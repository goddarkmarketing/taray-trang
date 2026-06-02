<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/functions.php';

$data = tt_read_data();
$site = $data['site'] ?? [];
$marker = '__TT_TEST_' . date('His') . '__';

$testSite = array_merge($site, [
    'phoneDisplay' => $marker,
]);
$data['site'] = $testSite;

if (!tt_write_data($data)) {
    fwrite(STDERR, "FAIL: tt_write_data returned false\n");
    exit(1);
}

$readBack = tt_read_data();
$api = tt_api_to_frontend($readBack);
$fallbackRaw = file_get_contents(TT_FALLBACK_JS);
$hasMarkerJson = str_contains(file_get_contents(TT_DATA_FILE), $marker);
$hasMarkerFallback = str_contains($fallbackRaw, $marker);

// restore original
$data['site'] = $site;
tt_write_data($data);

echo json_encode([
    'ok' => true,
    'marker' => $marker,
    'saved_to_site_json' => $hasMarkerJson,
    'saved_to_fallback_js' => $hasMarkerFallback,
    'read_back_phoneDisplay' => $readBack['site']['phoneDisplay'] ?? null,
    'api_site_phoneDisplay' => $api['site']['phoneDisplay'] ?? null,
    'meta_updated' => $readBack['meta']['updated'] ?? null,
    'site_fields' => array_keys($readBack['site'] ?? []),
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
