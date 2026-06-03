<?php

function ipz_config($name)
{
    static $configs = [];

    if (!isset($configs[$name])) {
        $path = __DIR__ . '/config/' . $name . '.php';
        if (!file_exists($path)) {
            throw new RuntimeException('Missing config: ' . $name);
        }
        $configs[$name] = require $path;
    }

    return $configs[$name];
}

function ipz_json_response($payload, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function ipz_normalize_collection_key($key)
{
    if ($key === null || $key === '') {
        return null;
    }

    $key = strtolower(trim((string) $key));
    $collections = ipz_config('collections');

    if (isset($collections[$key])) {
        return $key;
    }

    foreach ($collections as $collectionKey => $definition) {
        $aliases = isset($definition['aliases']) && is_array($definition['aliases'])
            ? $definition['aliases']
            : [];
        foreach ($aliases as $alias) {
            if (strtolower(trim((string) $alias)) === $key) {
                return $collectionKey;
            }
        }
    }

    return null;
}

function ipz_db_connect()
{
    $db = ipz_config('db');
    $mysqli = mysqli_init();
    $mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, (int) $db['connect_timeout']);

    $ok = $mysqli->real_connect(
        $db['host'],
        $db['user'],
        $db['password'],
        $db['name']
    );

    if (!$ok) {
        ipz_json_response([
            'ok' => false,
            'error' => 'DB connection failed',
            'details' => $mysqli->connect_error,
        ], 500);
    }

    $charsetSql = "SET NAMES '" . $mysqli->real_escape_string($db['charset']) .
        "' COLLATE '" . $mysqli->real_escape_string($db['collation']) . "'";
    $mysqli->query($charsetSql);

    return $mysqli;
}

function ipz_ref_values(array &$arr)
{
    $refs = [];
    foreach ($arr as $key => &$value) {
        $refs[$key] = &$value;
    }
    return $refs;
}

function ipz_bind_params($stmt, $types, array &$params)
{
    if ($types === '' || empty($params)) {
        return;
    }

    $bindArgs = array_merge([$types], ipz_ref_values($params));
    call_user_func_array([$stmt, 'bind_param'], $bindArgs);
}

function ipz_collection_definition($collectionKey)
{
    $collections = ipz_config('collections');
    if (!isset($collections[$collectionKey])) {
        return null;
    }

    return $collections[$collectionKey];
}

function ipz_humanize_field_name($field)
{
    $label = str_replace('_', ' ', (string) $field);
    return ucfirst($label);
}

function ipz_get_column_comments($mysqli, $table)
{
    $db = ipz_config('db');
    $comments = [];

    $sql = 'SELECT COLUMN_NAME, COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS '
        . 'WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?';
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        return $comments;
    }

    $schema = (string) $db['name'];
    $tableName = (string) $table;
    $types = 'ss';
    $params = [$schema, $tableName];
    ipz_bind_params($stmt, $types, $params);

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $name = $row['COLUMN_NAME'];
        $comment = trim((string) $row['COLUMN_COMMENT']);
        $comments[$name] = $comment;
    }

    $stmt->close();

    return $comments;
}

function ipz_resolved_fields($collectionDef, $columnComments)
{
    $resolved = [];
    $overrides = isset($collectionDef['label_overrides']) && is_array($collectionDef['label_overrides'])
        ? $collectionDef['label_overrides']
        : [];

    foreach ($collectionDef['fields'] as $name => $value) {
        if (is_array($value)) {
            $type = isset($value['type']) ? (string) $value['type'] : 'string';
        } else {
            $type = (string) $value;
        }

        $comment = isset($columnComments[$name]) ? trim((string) $columnComments[$name]) : '';
        $label = ipz_humanize_field_name($name);
        if ($comment !== '') {
            $label = $comment;
        }
        if (isset($overrides[$name]) && trim((string) $overrides[$name]) !== '') {
            $label = trim((string) $overrides[$name]);
        }

        $resolved[$name] = [
            'label' => $label,
            'type' => $type,
        ];
    }

    return $resolved;
}

function ipz_operator_sql($operator)
{
    $normalized = strtolower(trim((string) $operator));

    $map = [
        'vsebuje' => 'like',
        'ne vsebuje' => 'not like',
        'je enako' => '=',
        'ni enako' => '<>',
        'večje kot' => '>',
        'manjše kot' => '<',
        'večje ali enako' => '>=',
        'manjše ali enako' => '<=',
    ];

    if (isset($map[$normalized])) {
        $normalized = $map[$normalized];
    }

    $allowed = ipz_config('search')['allowed_operators'];
    if (!in_array($normalized, $allowed, true)) {
        return null;
    }
    return $normalized;
}

function ipz_operator_label($operator)
{
    $labels = [
        'like' => 'vsebuje',
        'not like' => 'ne vsebuje',
        '=' => 'je enako',
        '<>' => 'ni enako',
        '>' => 'večje kot',
        '<' => 'manjše kot',
        '>=' => 'večje ali enako',
        '<=' => 'manjše ali enako',
    ];
    $normalized = strtolower(trim((string) $operator));
    return isset($labels[$normalized]) ? $labels[$normalized] : $operator;
}

function ipz_parse_filters($rawFilters, $collectionDef)
{
    $filters = [];
    $fieldMap = $collectionDef['fields'];
    $defaultOperator = ipz_config('search')['default_operator'];

    if (!is_array($rawFilters)) {
        return $filters;
    }

    foreach ($rawFilters as $filter) {
        if (!is_array($filter)) {
            continue;
        }

        $field = isset($filter['field']) ? (string) $filter['field'] : '';
        $value = isset($filter['value']) ? trim((string) $filter['value']) : '';
        $operator = isset($filter['operator']) ? $filter['operator'] : $defaultOperator;
        $operator = ipz_operator_sql($operator);

        if ($field === '' || !isset($fieldMap[$field]) || $value === '' || $operator === null) {
            continue;
        }

        $filters[] = [
            'field' => $field,
            'value' => $value,
            'operator' => $operator,
        ];
    }

    return $filters;
}

function ipz_parse_legacy_filters(array $requestData, $collectionDef)
{
    $filters = [];

    foreach ($requestData as $field => $value) {
        if (!is_array($value) || count($value) < 2) {
            continue;
        }

        $filters[] = [
            'field' => $field,
            'value' => isset($value[0]) ? $value[0] : '',
            'operator' => isset($value[1]) ? $value[1] : ipz_config('search')['default_operator'],
        ];
    }

    return ipz_parse_filters($filters, $collectionDef);
}

function ipz_extract_global_search_fields($collectionDef)
{
    $fields = [];

    if (!empty($collectionDef['summary']) && is_array($collectionDef['summary'])) {
        $fields = $collectionDef['summary'];
    } else {
        $fields = array_keys($collectionDef['fields']);
    }

    return array_values(array_filter($fields, function ($field) use ($collectionDef) {
        return isset($collectionDef['fields'][$field]);
    }));
}

function ipz_build_where($collectionDef, $filters, $globalQuery)
{
    $parts = [];
    $params = [];
    $types = '';

    foreach ($filters as $filter) {
        $field = $filter['field'];
        $operator = $filter['operator'];
        $value = $filter['value'];

        if ($operator === 'like' || $operator === 'not like') {
            $parts[] = "`$field` " . strtoupper($operator) . ' ?';
            $params[] = '%' . $value . '%';
        } else {
            $parts[] = "`$field` $operator ?";
            $params[] = $value;
        }
        $types .= 's';
    }

    $globalQuery = trim((string) $globalQuery);
    if ($globalQuery !== '') {
        $globalFields = ipz_extract_global_search_fields($collectionDef);
        if (!empty($globalFields)) {
            $globalParts = [];
            foreach ($globalFields as $field) {
                $globalParts[] = "`$field` LIKE ?";
                $params[] = '%' . $globalQuery . '%';
                $types .= 's';
            }
            $parts[] = '(' . implode(' OR ', $globalParts) . ')';
        }
    }

    if (empty($parts)) {
        return ['sql' => '1=1', 'types' => '', 'params' => []];
    }

    return [
        'sql' => implode(' AND ', $parts),
        'types' => $types,
        'params' => $params,
    ];
}

function ipz_search($collectionKey, array $input)
{
    $collectionDef = ipz_collection_definition($collectionKey);
    if ($collectionDef === null) {
        ipz_json_response(['ok' => false, 'error' => 'Unknown collection'], 404);
    }

    $searchCfg = ipz_config('search');

    $page = isset($input['page']) ? max(1, (int) $input['page']) : 1;
    $perPage = isset($input['per_page']) ? (int) $input['per_page'] : (int) $searchCfg['default_page_size'];
    if ($perPage < 1) {
        $perPage = (int) $searchCfg['default_page_size'];
    }
    $perPage = min($perPage, (int) $searchCfg['max_page_size']);

    $sort = isset($input['sort']) ? (string) $input['sort'] : (string) $collectionDef['order_key'];
    if (!isset($collectionDef['fields'][$sort])) {
        $sort = (string) $collectionDef['order_key'];
    }

    $dir = isset($input['dir']) ? strtoupper((string) $input['dir']) : 'ASC';
    $dir = $dir === 'DESC' ? 'DESC' : 'ASC';

    $filters = [];
    if (isset($input['filters']) && is_array($input['filters'])) {
        $filters = ipz_parse_filters($input['filters'], $collectionDef);
    }

    if (empty($filters)) {
        $filters = ipz_parse_legacy_filters($input, $collectionDef);
    }

    $globalQuery = isset($input['q']) ? $input['q'] : '';
    $whereData = ipz_build_where($collectionDef, $filters, $globalQuery);

    $offset = ($page - 1) * $perPage;
    $table = $collectionDef['table'];

    $mysqli = ipz_db_connect();

    $resolvedFields = ipz_resolved_fields($collectionDef, ipz_get_column_comments($mysqli, $table));

    $countSql = "SELECT COUNT(*) AS total FROM `$table` WHERE " . $whereData['sql'];
    $countStmt = $mysqli->prepare($countSql);
    if (!$countStmt) {
        ipz_json_response(['ok' => false, 'error' => 'Failed to prepare COUNT query'], 500);
    }

    $countParams = $whereData['params'];
    ipz_bind_params($countStmt, $whereData['types'], $countParams);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $total = (int) $countResult->fetch_assoc()['total'];
    $countStmt->close();

    $dataSql = "SELECT * FROM `$table` WHERE " . $whereData['sql'] .
        " ORDER BY `$sort` $dir LIMIT ? OFFSET ?";
    $dataStmt = $mysqli->prepare($dataSql);
    if (!$dataStmt) {
        ipz_json_response(['ok' => false, 'error' => 'Failed to prepare data query'], 500);
    }

    $dataTypes = $whereData['types'] . 'ii';
    $dataParams = $whereData['params'];
    $dataParams[] = $perPage;
    $dataParams[] = $offset;
    ipz_bind_params($dataStmt, $dataTypes, $dataParams);

    $dataStmt->execute();
    $result = $dataStmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $dataStmt->close();
    $mysqli->close();

    return [
        'ok' => true,
        'collection' => $collectionKey,
        'meta' => [
            'title' => $collectionDef['title'],
            'description' => isset($collectionDef['description']) ? $collectionDef['description'] : '',
            'intro' => isset($collectionDef['intro']) ? $collectionDef['intro'] : '',
            'about' => isset($collectionDef['about']) ? $collectionDef['about'] : '',
            'credits' => isset($collectionDef['credits']) ? $collectionDef['credits'] : '',
            'last_update' => isset($collectionDef['last_update']) ? $collectionDef['last_update'] : '',
            'n_entries' => isset($collectionDef['n_entries']) ? $collectionDef['n_entries'] : '',
            'base_url' => isset($collectionDef['base_url']) ? $collectionDef['base_url'] : '',
            'table' => $collectionDef['table'],
            'summary' => $collectionDef['summary'],
            'fields' => $resolvedFields,
            'links' => $collectionDef['links'],
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'pages' => $perPage > 0 ? (int) ceil($total / $perPage) : 0,
            'sort' => $sort,
            'dir' => $dir,
            'filters' => $filters,
            'q' => (string) $globalQuery,
        ],
        'rows' => $rows,
    ];
}

function ipz_details($collectionKey, $id)
{
    $collectionDef = ipz_collection_definition($collectionKey);
    if ($collectionDef === null) {
        ipz_json_response(['ok' => false, 'error' => 'Unknown collection'], 404);
    }

    if ($id === null || $id === '') {
        ipz_json_response(['ok' => false, 'error' => 'Missing id'], 400);
    }

    $table = $collectionDef['table'];
    $mysqli = ipz_db_connect();
    $resolvedFields = ipz_resolved_fields($collectionDef, ipz_get_column_comments($mysqli, $table));

    $sql = "SELECT * FROM `$table` WHERE `id` = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        ipz_json_response(['ok' => false, 'error' => 'Failed to prepare detail query'], 500);
    }

    $idParam = (string) $id;
    $types = 's';
    $params = [$idParam];
    ipz_bind_params($stmt, $types, $params);

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();
    $mysqli->close();

    if ($row === null) {
        ipz_json_response(['ok' => false, 'error' => 'Item not found'], 404);
    }

    return [
        'ok' => true,
        'collection' => $collectionKey,
        'meta' => [
            'title' => $collectionDef['title'],
            'description' => isset($collectionDef['description']) ? $collectionDef['description'] : '',
            'intro' => isset($collectionDef['intro']) ? $collectionDef['intro'] : '',
            'about' => isset($collectionDef['about']) ? $collectionDef['about'] : '',
            'credits' => isset($collectionDef['credits']) ? $collectionDef['credits'] : '',
            'last_update' => isset($collectionDef['last_update']) ? $collectionDef['last_update'] : '',
            'n_entries' => isset($collectionDef['n_entries']) ? $collectionDef['n_entries'] : '',
            'base_url' => isset($collectionDef['base_url']) ? $collectionDef['base_url'] : '',
            'fields' => $resolvedFields,
            'links' => $collectionDef['links'],
        ],
        'row' => $row,
    ];
}

function ipz_aliases_for_collection($collectionKey)
{
    $definition = ipz_collection_definition($collectionKey);
    if ($definition === null || !isset($definition['aliases']) || !is_array($definition['aliases'])) {
        return [];
    }

    $aliases = array_filter($definition['aliases'], function ($alias) use ($collectionKey) {
        return trim((string) $alias) !== '' && (string) $alias !== (string) $collectionKey;
    });

    return array_values(array_unique($aliases));
}

function ipz_all_collections_payload()
{
    $collections = ipz_config('collections');
    $payload = [];
    $mysqli = ipz_db_connect();

    foreach ($collections as $key => $definition) {
        $resolvedFields = ipz_resolved_fields($definition, ipz_get_column_comments($mysqli, $definition['table']));
        
        $nEntries = isset($definition['n_entries']) ? $definition['n_entries'] : '';
        if ($nEntries === '') {
            $countRes = $mysqli->query("SELECT COUNT(*) as total FROM `" . $mysqli->real_escape_string($definition['table']) . "`");
            if ($countRes) {
                $countRow = $countRes->fetch_assoc();
                $nEntries = $countRow['total'];
            }
        }

        $payload[] = [
            'key' => $key,
            'title' => $definition['title'],
            'description' => isset($definition['description']) ? $definition['description'] : '',
            'intro' => isset($definition['intro']) ? $definition['intro'] : '',
            'about' => isset($definition['about']) ? $definition['about'] : '',
            'credits' => isset($definition['credits']) ? $definition['credits'] : '',
            'last_update' => isset($definition['last_update']) ? $definition['last_update'] : '',
            'n_entries' => $nEntries,
            'base_url' => isset($definition['base_url']) ? $definition['base_url'] : '',
            'aliases' => ipz_aliases_for_collection($key),
            'search_hint' => $definition['search_hint'],
            'summary' => $definition['summary'],
            'table' => $definition['table'],
            'fields' => $resolvedFields,
            'links' => $definition['links'],
        ];
    }

    $mysqli->close();

    return [
        'ok' => true,
        'collections' => $payload,
    ];
}

function ipz_parse_json_input()
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [];
    }

    return $decoded;
}
