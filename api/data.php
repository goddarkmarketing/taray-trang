<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=60');

$data = tt_read_data();
if (!$data) {
    http_response_code(503);
    echo json_encode(['error' => 'Data not available'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(tt_api_to_frontend($data), JSON_UNESCAPED_UNICODE);
