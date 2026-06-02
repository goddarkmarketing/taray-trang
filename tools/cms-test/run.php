<?php
declare(strict_types=1);

/**
 * CMS Integration Test — ทดสอบ CRUD ทุกส่วนหลังบ้าน + ตรวจว่าข้อมูลไหลไปหน้าเว็บ (JSON / fallback / API map)
 *
 * ปลอดภัย: backup site.json + data-fallback.js ก่อนทดสอบ และ restore ทุกครั้ง (แม้ error)
 *
 * Usage:
 *   php tools/cms-test/run.php
 *   php tools/cms-test/run.php --live http://localhost/1495
 */

require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/sections.php';

$liveUrl = null;
foreach ($argv as $i => $arg) {
    if ($arg === '--live' && isset($argv[$i + 1])) {
        $liveUrl = $argv[$i + 1];
    }
}

$runner = new CmsTestRunner();

try {
    $runner->backup();

    cms_test_write_integrity($runner);
    cms_test_site($runner);
    cms_test_nav_items($runner);
    cms_test_boats($runner);
    cms_test_programs($runner);
    cms_test_options($runner);
    cms_test_videos($runner);
    cms_test_articles($runner);
    cms_test_reviews($runner);
    cms_test_why_us($runner);
    cms_test_steps($runner);
    cms_test_services($runner);
    cms_test_hero_slides($runner);
    cms_test_images($runner);
    cms_test_about($runner);
    cms_test_seo($runner);
    cms_test_home_deals($runner);
    cms_test_api_save_endpoint($runner);
    cms_test_cache_version($runner);

    if ($liveUrl !== null) {
        $runner->tryLiveHttp($liveUrl);
    }
} catch (Throwable $e) {
    $runner->assert('FATAL: uncaught exception', false, $e->getMessage());
} finally {
    $runner->restore();
}

$runner->printReport();
exit($runner->hasFailures() ? 1 : 0);
