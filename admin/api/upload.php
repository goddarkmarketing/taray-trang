<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
tt_require_admin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    tt_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

if (empty($_FILES['file'])) {
    tt_json_response(['ok' => false, 'error' => 'No file'], 400);
}

$result = tt_upload_image($_FILES['file']);
if (!$result['ok']) {
    tt_json_response(['ok' => false, 'error' => $result['error'] ?? 'Upload failed'], 400);
}

tt_json_response(['ok' => true, 'url' => $result['url'], 'name' => $result['name']]);
