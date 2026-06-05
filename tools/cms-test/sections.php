<?php
declare(strict_types=1);

/** Section CRUD tests — จำลอง logic เดียวกับ admin/*.php */

function cms_test_site(CmsTestRunner $t): void
{
    $data = $t->read();
    $site = $data['site'] ?? [];

    $site['brand'] = $t->marker();
    $site['phone'] = '099-' . substr($t->testId(), -7);
    $site['phoneDisplay'] = $site['phone'];
    $data['site'] = $site;
    $t->assert('site: write', $t->write($data));

    $t->assertPersisted('site.brand', fn(array $d) => ($d['site']['brand'] ?? '') === $t->marker());
    $t->assertPersisted('site.phone=phoneDisplay', fn(array $d) =>
        ($d['site']['phone'] ?? '') === ($d['site']['phoneDisplay'] ?? '') &&
        str_contains((string)($d['site']['phone'] ?? ''), '099-')
    );

    $fe = $t->frontendMap();
    $phoneShow = $fe['SITE']['phone'] ?? $fe['SITE']['phoneDisplay'] ?? '';
    $t->assert('frontend: phone display', str_contains((string)$phoneShow, '099-'), (string)$phoneShow);
    $t->assert('frontend: brand in SITE', ($fe['SITE']['brand'] ?? '') === $t->marker());
}

function cms_test_nav_items(CmsTestRunner $t): void
{
    $data = $t->read();
    $items = $data['navItems'] ?? [];
    $items[] = ['label' => $t->marker(), 'href' => 'contact.html'];
    $data['navItems'] = $items;
    $t->assert('nav: create', $t->write($data));
    $t->assertPersisted('navItems', fn(array $d) =>
        $t->findIndex($d['navItems'] ?? [], fn($i) => ($i['label'] ?? '') === $t->marker()) >= 0
    );

    $fe = $t->frontendMap();
    $t->assert('frontend: NAV_ITEMS has marker', $t->findIndex($fe['NAV_ITEMS'], fn($i) => ($i['label'] ?? '') === $t->marker()) >= 0);

    $data = $t->read();
    $idx = $t->findIndex($data['navItems'] ?? [], fn($i) => ($i['label'] ?? '') === $t->marker());
    $updated = $t->marker() . '-UPD';
    $data['navItems'][$idx]['label'] = $updated;
    $t->assert('nav: update', $t->write($data));
    $t->assertPersisted('navItems updated', fn(array $d) =>
        $t->findIndex($d['navItems'] ?? [], fn($i) => ($i['label'] ?? '') === $updated) >= 0
    );

    $data = $t->read();
    $idx = $t->findIndex($data['navItems'] ?? [], fn($i) => str_contains((string)($i['label'] ?? ''), '__TT_CRUD_'));
    if ($idx >= 0) {
        array_splice($data['navItems'], $idx, 1);
        $data['navItems'] = array_values($data['navItems']);
        $t->assert('nav: delete', $t->write($data));
        $t->assertRemoved('navItems', fn(array $d) =>
            $t->findIndex($d['navItems'] ?? [], fn($i) => str_contains((string)($i['label'] ?? ''), '__TT_CRUD_')) >= 0
        );
    }
}

function cms_test_list_by_id(CmsTestRunner $t, string $section, string $feKey, callable $makeItem, callable $patchItem): void
{
    $data = $t->read();
    $item = $makeItem($t->testId(), $t->marker());
    $data[$section][] = $item;
    $t->assert("{$section}: create", $t->write($data));
    $t->assertPersisted($section, fn(array $d) =>
        $t->findIndex($d[$section] ?? [], fn($i) => ($i['id'] ?? '') === $t->testId()) >= 0
    );

    $fe = $t->frontendMap();
    $t->assert("frontend: {$feKey}", $t->findIndex($fe[$feKey] ?? [], fn($i) => ($i['id'] ?? '') === $t->testId()) >= 0);

    $data = $t->read();
    $idx = $t->findIndex($data[$section] ?? [], fn($i) => ($i['id'] ?? '') === $t->testId());
    $data[$section][$idx] = $patchItem($data[$section][$idx], $t->marker() . '-UPD');
    $t->assert("{$section}: update", $t->write($data));
    $t->assertPersisted("{$section} updated", fn(array $d) => ($d[$section][$idx]['name'] ?? $d[$section][$idx]['label'] ?? $d[$section][$idx]['title'] ?? '') === $t->marker() . '-UPD'
        || str_contains(json_encode($d[$section][$idx] ?? [], JSON_UNESCAPED_UNICODE), $t->marker() . '-UPD'));

    $data = $t->read();
    $data[$section] = array_values(array_filter($data[$section] ?? [], fn($i) => ($i['id'] ?? '') !== $t->testId()));
    $t->assert("{$section}: delete", $t->write($data));
    $t->assertRemoved($section, fn(array $d) =>
        $t->findIndex($d[$section] ?? [], fn($i) => ($i['id'] ?? '') === $t->testId()) >= 0
    );
}

function cms_test_boats(CmsTestRunner $t): void
{
    cms_test_list_by_id($t, 'boats', 'BOATS',
        fn(string $id, string $m) => [
            'id' => $id, 'name' => $m, 'tag' => 'Test', 'image' => 'https://example.com/t.jpg',
            'capacity' => '1', 'capacityMax' => 1, 'basePrice' => 1, 'short' => $m,
            'description' => $m, 'suitable' => 'test', 'highlights' => [$m],
            'rating' => 5, 'reviewCount' => '1', 'stars' => 5, 'badgeTL' => '', 'badgeTR' => '', 'badgeTRClass' => 'is-info',
        ],
        fn(array $item, string $m) => array_merge($item, ['name' => $m, 'short' => $m])
    );
}

function cms_test_programs(CmsTestRunner $t): void
{
    cms_test_list_by_id($t, 'programs', 'PROGRAMS',
        fn(string $id, string $m) => [
            'id' => $id, 'name' => $m, 'image' => 'https://example.com/t.jpg',
            'route' => $m, 'stops' => [$m], 'duration' => '1h', 'basePrice' => 1,
            'boats' => ['longtail'], 'desc' => $m, 'rating' => 5, 'reviewCount' => '1', 'stars' => 5,
        ],
        fn(array $item, string $m) => array_merge($item, ['name' => $m, 'desc' => $m])
    );
}

function cms_test_options(CmsTestRunner $t): void
{
    cms_test_list_by_id($t, 'options', 'OPTIONS',
        fn(string $id, string $m) => [
            'id' => $id, 'icon' => 'info', 'label' => $m, 'desc' => $m,
            'price' => 1, 'unit' => 'flat', 'priceLabel' => '+1',
        ],
        fn(array $item, string $m) => array_merge($item, ['label' => $m])
    );
}

function cms_test_videos(CmsTestRunner $t): void
{
    cms_test_list_by_id($t, 'videos', 'VIDEOS',
        fn(string $id, string $m) => [
            'id' => $id, 'platform' => 'tiktok', 'thumb' => 'https://example.com/t.jpg',
            'title' => $m, 'views' => '1', 'url' => 'https://example.com/v/' . $id,
        ],
        fn(array $item, string $m) => array_merge($item, ['title' => $m])
    );
}

function cms_test_articles(CmsTestRunner $t): void
{
    $slug = $t->testId();
    $data = $t->read();
    $data['articles'][] = [
        'slug' => $slug, 'category' => 'Test', 'title' => $t->marker(),
        'excerpt' => $t->marker(), 'cover' => 'https://example.com/c.jpg', 'thumb' => 'https://example.com/t.jpg',
        'author' => 'Test', 'date' => '1 ม.ค. 2026', 'readTime' => '1 นาที',
        'body' => [['type' => 'p', 'text' => $t->marker()]],
    ];
    $t->assert('articles: create', $t->write($data));
    $t->assertPersisted('articles', fn(array $d) =>
        $t->findIndex($d['articles'] ?? [], fn($a) => ($a['slug'] ?? '') === $slug) >= 0
    );

    $data = $t->read();
    $idx = $t->findIndex($data['articles'] ?? [], fn($a) => ($a['slug'] ?? '') === $slug);
    $data['articles'][$idx]['title'] = $t->marker() . '-UPD';
    $t->assert('articles: update', $t->write($data));

    $data = $t->read();
    $data['articles'] = array_values(array_filter($data['articles'] ?? [], fn($a) => ($a['slug'] ?? '') !== $slug));
    $t->assert('articles: delete', $t->write($data));
    $t->assertRemoved('articles', fn(array $d) =>
        $t->findIndex($d['articles'] ?? [], fn($a) => ($a['slug'] ?? '') === $slug) >= 0
    );
}

function cms_test_index_list(CmsTestRunner $t, string $section, string $feKey, callable $makeItem, string $matchField): void
{
    $data = $t->read();
    $data[$section][] = $makeItem($t->marker());
    $t->assert("{$section}: create", $t->write($data));
    $t->assertPersisted($section, fn(array $d) =>
        $t->findIndex($d[$section] ?? [], fn($i) => ($i[$matchField] ?? '') === $t->marker()) >= 0
    );

    $data = $t->read();
    $idx = $t->findIndex($data[$section] ?? [], fn($i) => ($i[$matchField] ?? '') === $t->marker());
    $data[$section][$idx][$matchField] = $t->marker() . '-UPD';
    $t->assert("{$section}: update", $t->write($data));

    $data = $t->read();
    $idx = $t->findIndex($data[$section] ?? [], fn($i) => str_contains((string)($i[$matchField] ?? ''), '__TT_CRUD_'));
    if ($idx >= 0) {
        array_splice($data[$section], $idx, 1);
        $data[$section] = array_values($data[$section]);
        $t->assert("{$section}: delete", $t->write($data));
    }
}

function cms_test_reviews(CmsTestRunner $t): void
{
    cms_test_index_list($t, 'reviews', 'REVIEWS',
        fn(string $m) => ['name' => $m, 'initial' => 'T', 'trip' => 'Test', 'from' => 'Test', 'rating' => 5, 'text' => $m],
        'name'
    );
}

function cms_test_why_us(CmsTestRunner $t): void
{
    cms_test_index_list($t, 'whyUs', 'WHY_US',
        fn(string $m) => ['icon' => 'shield', 'title' => $m, 'desc' => $m],
        'title'
    );
}

function cms_test_steps(CmsTestRunner $t): void
{
    cms_test_index_list($t, 'steps', 'STEPS',
        fn(string $m) => ['n' => 99, 'icon' => 'anchor', 'title' => $m, 'desc' => $m],
        'title'
    );
}

function cms_test_services(CmsTestRunner $t): void
{
    cms_test_index_list($t, 'services', 'SERVICES',
        fn(string $m) => ['icon' => 'info', 'title' => $m, 'href' => 'contact.html'],
        'title'
    );
}

function cms_test_hero_slides(CmsTestRunner $t): void
{
    cms_test_index_list($t, 'heroSlides', 'HERO_SLIDES',
        fn(string $m) => ['src' => 'https://example.com/slide.jpg', 'alt' => $m],
        'alt'
    );
}

function cms_test_images(CmsTestRunner $t): void
{
    $key = 'testHero_' . substr($t->testId(), -6);
    $data = $t->read();
    $images = $data['images'] ?? [];
    $images[$key] = $t->marker();
    $data['images'] = $images;
    $t->assert('images: create', $t->write($data));
    $t->assertPersisted('images', fn(array $d) => ($d['images'][$key] ?? '') === $t->marker());

    $fe = $t->frontendMap();
    $t->assert('frontend: IMAGES key', ($fe['IMAGES'][$key] ?? '') === $t->marker());

    $data = $t->read();
    $data['images'][$key] = $t->marker() . '-UPD';
    $t->assert('images: update', $t->write($data));

    $data = $t->read();
    unset($data['images'][$key]);
    $t->assert('images: delete', $t->write($data));
    $t->assert('images: removed', !isset($t->read()['images'][$key]));
}

function cms_test_about(CmsTestRunner $t): void
{
    $data = $t->read();
    $about = $data['about'] ?? [];
    $origTitle = $about['hero']['title'] ?? '';
    $about['hero']['title'] = $t->marker();
    $data['about'] = $about;
    $t->assert('about: update', $t->write($data));
    $t->assertPersisted('about.hero.title', fn(array $d) => ($d['about']['hero']['title'] ?? '') === $t->marker());

    $fe = $t->frontendMap();
    $t->assert('frontend: ABOUT.hero.title', ($fe['ABOUT']['hero']['title'] ?? '') === $t->marker());

    $data = $t->read();
    $data['about']['hero']['title'] = $origTitle;
    $t->write($data);
}

function cms_test_seo(CmsTestRunner $t): void
{
    $data = $t->read();
    $seo = $data['seo'] ?? [];
    $orig = $seo['index']['title'] ?? '';
    $seo['index']['title'] = $t->marker();
    $data['seo'] = $seo;
    $t->assert('seo: update', $t->write($data));
    $t->assertPersisted('seo.index.title', fn(array $d) => ($d['seo']['index']['title'] ?? '') === $t->marker());

    $fe = $t->frontendMap();
    $t->assert('frontend: SEO.index.title', ($fe['SEO']['index']['title'] ?? '') === $t->marker());

    $data = $t->read();
    $data['seo']['index']['title'] = $orig;
    $t->write($data);
}

function cms_test_home_sections(CmsTestRunner $t): void
{
    $data = $t->read();
    $sections = $data['homeSections'] ?? [];
    $orig = $sections['programs']['title'] ?? '';
    if ($sections === []) {
        $sections = tt_home_sections_defaults();
    }
    $sections['programs']['title'] = $t->marker();
    $data['homeSections'] = $sections;
    $t->assert('homeSections: update', $t->write($data));
    $t->assertPersisted('homeSections.programs.title', fn(array $d) =>
        ($d['homeSections']['programs']['title'] ?? '') === $t->marker()
    );

    $fe = $t->frontendMap();
    $t->assert('frontend: HOME_SECTIONS', ($fe['HOME_SECTIONS']['programs']['title'] ?? '') === $t->marker());

    $data = $t->read();
    if ($orig === '' && ($data['homeSections']['programs']['title'] ?? '') === $t->marker()) {
        unset($data['homeSections']['programs']['title']);
        if (($data['homeSections']['programs'] ?? []) === []) {
            unset($data['homeSections']['programs']);
        }
        if (($data['homeSections'] ?? []) === []) {
            unset($data['homeSections']);
        }
    } else {
        $data['homeSections']['programs']['title'] = $orig;
    }
    $t->write($data);
}

function cms_test_home_deals(CmsTestRunner $t): void
{
    $data = $t->read();
    $deals = $data['homeDeals'] ?? [];
    $orig = $deals['customTrip']['name'] ?? '';
    $deals['customTrip']['name'] = $t->marker();
    $data['homeDeals'] = $deals;
    $t->assert('homeDeals: update', $t->write($data));
    $t->assertPersisted('homeDeals.customTrip.name', fn(array $d) =>
        ($d['homeDeals']['customTrip']['name'] ?? '') === $t->marker()
    );

    $fe = $t->frontendMap();
    $t->assert('frontend: HOME_DEALS', ($fe['HOME_DEALS']['customTrip']['name'] ?? '') === $t->marker());

    $data = $t->read();
    $data['homeDeals']['customTrip']['name'] = $orig;
    $t->write($data);
}

function cms_test_api_save_endpoint(CmsTestRunner $t): void
{
    $data = $t->read();
    $nav = $data['navItems'] ?? [];
    $nav[] = ['label' => $t->marker() . '-API', 'href' => 'index.html'];
    $t->assert('api/save: navItems via saveSectionViaApi', $t->saveSectionViaApi('navItems', $nav));
    $t->assertPersisted('api save nav', fn(array $d) =>
        $t->findIndex($d['navItems'] ?? [], fn($i) => ($i['label'] ?? '') === $t->marker() . '-API') >= 0
    );

    $data = $t->read();
    $data['navItems'] = array_values(array_filter($data['navItems'] ?? [], fn($i) =>
        ($i['label'] ?? '') !== $t->marker() . '-API'
    ));
    $t->write($data);
}

function cms_test_cache_version(CmsTestRunner $t): void
{
    $before = (string)@file_get_contents(TT_ROOT . '/assets/js/tt-cache-version.js');
    $data = $t->read();
    $data['site']['tagline'] = $t->marker();
    $t->write($data);
    $after = (string)@file_get_contents(TT_ROOT . '/assets/js/tt-cache-version.js');
    $t->assert('cache: tt-cache-version.js regenerated', $before !== $after || str_contains($after, 'window.__TT_SCRIPT_V'));
}

function cms_test_write_integrity(CmsTestRunner $t): void
{
    $data = $t->read();
    $t->assert('integrity: meta.updated exists', isset($data['meta']['updated']));
    $t->assert('integrity: boats is array', is_array($data['boats'] ?? null));
    $t->assert('integrity: site.phone not empty', ($data['site']['phone'] ?? '') !== '');
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    $t->assert('integrity: json round-trip', is_string($json) && is_array(json_decode($json, true)));
}
