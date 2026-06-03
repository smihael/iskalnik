<?php

require_once __DIR__ . '/../core.php';

$body = ipz_parse_json_input();
$input = array_merge($_GET, $_POST, $body);

$collection = null;
if (isset($input['collection'])) {
    $collection = ipz_normalize_collection_key($input['collection']);
}
if ($collection === null && isset($input['baza'])) {
    $collection = ipz_normalize_collection_key($input['baza']);
}

if ($collection === null) {
    ipz_json_response(['ok' => false, 'error' => 'Missing or invalid collection'], 400);
}

$id = isset($input['id']) ? $input['id'] : null;
$result = ipz_details($collection, $id);
ipz_json_response($result);
