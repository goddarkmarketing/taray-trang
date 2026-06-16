<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
tt_require_admin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    tt_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$input = json_decode($raw ?: '', true);
if (!is_array($input)) {
    tt_json_response(['ok' => false, 'error' => 'Invalid JSON'], 400);
}

$section = $input['section'] ?? '';
$data = $input['data'] ?? null;

$allowed = [
    'site', 'navItems', 'images', 'heroSlides', 'services',
    'boats', 'programs', 'options', 'reviews', 'videos',
    'whyUs', 'steps', 'articles', 'about', 'seo', 'homeDeals',
    'boatBooking', 'packages2d1n', 'packages3d2n', 'packages4d3n', 'homeSections',
];

if (!in_array($section, $allowed, true)) {
    tt_json_response(['ok' => false, 'error' => 'Unknown section'], 400);
}

$all = tt_read_data();
if (!$all) {
    tt_json_response(['ok' => false, 'error' => 'Data file missing'], 500);
}

$all[$section] = $data;

if (!tt_write_data($all)) {
    tt_json_response(['ok' => false, 'error' => 'Write failed'], 500);
}

tt_json_response(['ok' => true, 'section' => $section, 'updated' => date('c')]);
