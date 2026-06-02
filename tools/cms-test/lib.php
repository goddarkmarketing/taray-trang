<?php
declare(strict_types=1);

/**
 * CMS integration test harness — backup/restore, assertions, frontend mapping.
 * ใช้ร่วมกับ run.php เท่านั้น
 */

require_once dirname(__DIR__, 2) . '/includes/functions.php';

final class CmsTestRunner
{
    private string $marker;
    private string $testId;
    private array $backup = [];
    private int $passed = 0;
    private int $failed = 0;
    /** @var list<array{name:string,ok:bool,detail:string}> */
    private array $results = [];
    private bool $restored = false;

    public function __construct()
    {
        $this->marker = '__TT_CRUD_' . date('YmdHis') . '__';
        $this->testId = 'tt-test-' . substr(md5($this->marker), 0, 8);
    }

    public function marker(): string
    {
        return $this->marker;
    }

    public function testId(): string
    {
        return $this->testId;
    }

    public function backup(): void
    {
        foreach ([TT_DATA_FILE, TT_FALLBACK_JS, TT_ROOT . '/assets/js/tt-cache-version.js'] as $path) {
            if (is_file($path)) {
                $this->backup[$path] = file_get_contents($path);
            }
        }
        $this->assert('backup: site.json readable', is_file(TT_DATA_FILE) && $this->backup[TT_DATA_FILE] !== false);
    }

    public function restore(): void
    {
        if ($this->restored) {
            return;
        }
        foreach ($this->backup as $path => $content) {
            file_put_contents($path, $content, LOCK_EX);
        }
        $this->restored = true;
        $this->assert('restore: site.json unchanged', is_file(TT_DATA_FILE) && file_get_contents(TT_DATA_FILE) === $this->backup[TT_DATA_FILE], '', true);
    }

    public function assert(string $name, bool $ok, string $detail = '', bool $silent = false): void
    {
        if ($ok) {
            $this->passed++;
        } else {
            $this->failed++;
        }
        if (!$silent) {
            $this->results[] = ['name' => $name, 'ok' => $ok, 'detail' => $detail];
        }
    }

    public function write(array $data): bool
    {
        return tt_write_data($data);
    }

    public function read(): array
    {
        return tt_read_data();
    }

    public function apiPayload(): array
    {
        return tt_api_to_frontend($this->read());
    }

    public function fallbackRaw(): string
    {
        return (string)file_get_contents(TT_FALLBACK_JS);
    }

    /** @return array<string, mixed> */
    public function frontendMap(?array $data = null): array
    {
        $d = $data ?? $this->read();
        return [
            'SITE' => $d['site'] ?? [],
            'IMAGES' => $d['images'] ?? [],
            'NAV_ITEMS' => $d['navItems'] ?? [],
            'BOATS' => $d['boats'] ?? [],
            'OPTIONS' => $d['options'] ?? [],
            'PROGRAMS' => $d['programs'] ?? [],
            'REVIEWS' => $d['reviews'] ?? [],
            'VIDEOS' => $d['videos'] ?? [],
            'WHY_US' => $d['whyUs'] ?? [],
            'STEPS' => $d['steps'] ?? [],
            'SERVICES' => $d['services'] ?? [],
            'HERO_SLIDES' => $d['heroSlides'] ?? [],
            'ARTICLES' => $d['articles'] ?? [],
            'ABOUT' => $d['about'] ?? [],
            'SEO' => $d['seo'] ?? [],
            'HOME_DEALS' => $d['homeDeals'] ?? [],
        ];
    }

    public function assertPersisted(string $label, callable $check): void
    {
        $data = $this->read();
        $ok = (bool)$check($data);
        $this->assert("persist: {$label}", $ok, $ok ? '' : 'ไม่พบใน site.json');
        $this->assert("fallback: {$label}", str_contains($this->fallbackRaw(), $this->marker), 'ไม่พบใน data-fallback.js');
        $api = $this->apiPayload();
        $this->assert("api: {$label}", (bool)$check($api), 'ไม่พบใน api payload');
    }

    public function fallbackData(): array
    {
        $raw = $this->fallbackRaw();
        if (preg_match('/window\.__TT_FALLBACK\s*=\s*(\{.*\})\s*;\s*$/s', $raw, $m)) {
            $decoded = json_decode($m[1], true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    public function assertRemoved(string $label, callable $checkStillExists): void
    {
        $gone = !(bool)$checkStillExists($this->read());
        $this->assert("removed json: {$label}", $gone);
        $goneFb = !(bool)$checkStillExists($this->fallbackData());
        $this->assert("removed fallback: {$label}", $goneFb, 'ยังพบใน data-fallback.js');
    }

    /** @param list<array<string, mixed>> $items */
    public function findIndex(array $items, callable $match): int
    {
        foreach ($items as $i => $item) {
            if ($match($item)) {
                return $i;
            }
        }
        return -1;
    }

    public function saveSectionViaApi(string $section, mixed $payload): bool
    {
        $allowed = [
            'site', 'navItems', 'images', 'heroSlides', 'services',
            'boats', 'programs', 'options', 'reviews', 'videos',
            'whyUs', 'steps', 'articles', 'about', 'seo', 'homeDeals',
        ];
        if (!in_array($section, $allowed, true)) {
            return false;
        }
        $all = $this->read();
        $all[$section] = $payload;
        return $this->write($all);
    }

    public function hasFailures(): bool
    {
        return $this->failed > 0;
    }

    public function printReport(): void
    {
        $width = 72;
        echo str_repeat('=', $width) . PHP_EOL;
        echo ' CMS Integration Test — Talay Trang' . PHP_EOL;
        echo ' Marker: ' . $this->marker . PHP_EOL;
        echo str_repeat('=', $width) . PHP_EOL;

        foreach ($this->results as $r) {
            $icon = $r['ok'] ? 'PASS' : 'FAIL';
            $line = sprintf(' [%s] %s', $icon, $r['name']);
            echo $line . PHP_EOL;
            if (!$r['ok'] && $r['detail'] !== '') {
                echo '       → ' . $r['detail'] . PHP_EOL;
            }
        }

        echo str_repeat('-', $width) . PHP_EOL;
        echo sprintf(" ผลรวม: %d ผ่าน, %d ไม่ผ่าน\n", $this->passed, $this->failed);
        echo $this->restored ? " ข้อมูลจริงถูก restore แล้ว — ไม่กระทบหน้าเว็บ\n" : " ⚠ ยังไม่ได้ restore\n";
        echo str_repeat('=', $width) . PHP_EOL;
    }

    public function tryLiveHttp(string $baseUrl): void
    {
        if (!function_exists('curl_init')) {
            $this->assert('live: curl available', false, 'ไม่มี cURL');
            return;
        }

        $apiUrl = rtrim($baseUrl, '/') . '/api/data.php';
        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assert('live: api/data.php HTTP 200', $code === 200, "got {$code}");
        $json = json_decode((string)$body, true);
        $this->assert('live: api JSON valid', is_array($json));
        $this->assert('live: api has site.phone', is_array($json) && isset($json['site']['phone']));
    }
}
