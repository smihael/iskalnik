<?php

require_once __DIR__ . '/../core.php';

$body = ipz_parse_json_input();
$input = array_merge($_GET, $_POST, $body);

if ((!isset($input['q']) || trim((string) $input['q']) === '') && isset($input['query'])) {
    $input['q'] = $input['query'];
}

if (isset($input['filters']) && is_string($input['filters'])) {
    $decodedFilters = json_decode($input['filters'], true);
    $input['filters'] = is_array($decodedFilters) ? $decodedFilters : [];
}

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

$result = ipz_search($collection, $input);
ipz_json_response($result);
