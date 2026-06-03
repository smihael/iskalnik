<?php

require_once __DIR__ . '/../core.php';

$all = ipz_all_collections_payload();

if (isset($_GET['collection'])) {
    $key = ipz_normalize_collection_key($_GET['collection']);
    if ($key === null) {
        ipz_json_response(['ok' => false, 'error' => 'Unknown collection'], 404);
    }

    $definition = ipz_collection_definition($key);
    $mysqli = ipz_db_connect();
    $resolvedFields = ipz_resolved_fields($definition, ipz_get_column_comments($mysqli, $definition['table']));
    $mysqli->close();

    ipz_json_response([
        'ok' => true,
        'collection' => [
            'key' => $key,
            'title' => $definition['title'],
            'description' => isset($definition['description']) ? $definition['description'] : '',
            'intro' => isset($definition['intro']) ? $definition['intro'] : '',
            'about' => isset($definition['about']) ? $definition['about'] : '',
            'credits' => isset($definition['credits']) ? $definition['credits'] : '',
            'last_update' => isset($definition['last_update']) ? $definition['last_update'] : '',
            'n_entries' => isset($definition['n_entries']) ? $definition['n_entries'] : '',
            'base_url' => isset($definition['base_url']) ? $definition['base_url'] : '',
            'aliases' => ipz_aliases_for_collection($key),
            'search_hint' => $definition['search_hint'],
            'summary' => $definition['summary'],
            'table' => $definition['table'],
            'fields' => $resolvedFields,
            'links' => $definition['links'],
        ],
    ]);
}

ipz_json_response($all);
