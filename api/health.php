<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$root = dirname(__DIR__);
$checks = [
    'phpVersion' => PHP_VERSION,
    'phpOk' => version_compare(PHP_VERSION, '8.0.0', '>='),
    'config' => is_file($root . '/config.php'),
    'includes/functions.php' => is_file($root . '/includes/functions.php'),
    'admin/login.php' => is_file($root . '/admin/login.php'),
    'data/site.json' => is_file($root . '/data/site.json'),
    'dataWritable' => is_dir($root . '/data') && is_writable($root . '/data'),
    'uploadsWritable' => is_dir($root . '/assets/uploads') && is_writable($root . '/assets/uploads'),
];

$ok = $checks['phpOk'] && $checks['config'] && $checks['includes/functions.php']
    && $checks['admin/login.php'] && $checks['data/site.json'];

http_response_code($ok ? 200 : 503);
echo json_encode([
    'ok' => $ok,
    'checks' => $checks,
    'hint' => $ok
        ? 'CMS files look ready — open /admin/login.php'
        : 'Upload missing folders (includes/, admin/, config.php, data/) or set PHP 8.0+ in cPanel',
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
