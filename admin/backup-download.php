<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
tt_require_admin();

$file = basename(trim($_GET['file'] ?? ''));
$path = tt_backup_path($file);

if ($path === null) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

$mime = str_ends_with($file, '.zip') ? 'application/zip' : 'application/json; charset=utf-8';
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . (string)filesize($path));
readfile($path);
exit;
