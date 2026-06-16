<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

function tt_read_data(): array
{
    if (!is_file(TT_DATA_FILE)) {
        return [];
    }
    $raw = file_get_contents(TT_DATA_FILE);
    if ($raw === false) {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function tt_write_data(array $data): bool
{
    tt_auto_backup_before_write();

    $data['meta'] = array_merge($data['meta'] ?? [], [
        'version' => 1,
        'updated' => date('c'),
    ]);

    $dir = dirname(TT_DATA_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($json === false) {
        return false;
    }

    $tmp = TT_DATA_FILE . '.tmp';
    if (file_put_contents($tmp, $json, LOCK_EX) === false) {
        return false;
    }
    if (!rename($tmp, TT_DATA_FILE)) {
        @unlink($tmp);
        return false;
    }

    tt_write_fallback_js($data);
    return true;
}

function tt_write_fallback_js(array $data): void
{
    $js = "/* Auto-generated — อัปเดตจากระบบหลังบ้าน */\nwindow.__TT_FALLBACK = " .
        json_encode($data, JSON_UNESCAPED_UNICODE) . ";\n";
    $tmp = TT_FALLBACK_JS . '.tmp';
    if (file_put_contents($tmp, $js, LOCK_EX) === false) {
        return;
    }
    if (!rename($tmp, TT_FALLBACK_JS)) {
        @unlink($tmp);
        return;
    }

    $updated = (string)($data['meta']['updated'] ?? date('c'));
    $versionJs = TT_ROOT . '/assets/js/tt-cache-version.js';
    $verContent = '/* Auto-generated */ window.__TT_SCRIPT_V = ' .
        json_encode($updated, JSON_UNESCAPED_UNICODE) . ";\n";
    $vtmp = $versionJs . '.tmp';
    if (file_put_contents($vtmp, $verContent, LOCK_EX) !== false) {
        rename($vtmp, $versionJs);
    }
}

function tt_api_to_frontend(array $data): array
{
    $data['homeSections'] = tt_home_sections($data);
    return $data;
}

/** @return array<string, array<string, string>> */
function tt_home_sections_defaults(): array
{
    return [
        'hero' => [
            'title' => 'แพ็คเกจทัวร์ตรัง',
            'lead' => 'รวมแพ็คเกจทัวร์เที่ยวเมืองตรัง หลากหลายบริการครบในที่เดียว ตั้งแต่เรือ ที่พัก รถ ตั๋วเรือ ไปจนถึงสัมมนาและสถานที่ถ่ายทำ',
        ],
        'boats' => [
            'eyebrow' => 'One-day trips',
            'title' => 'แพ็กเกจไปเช้า เย็นกลับ',
            'lead' => 'ไปเช้าเย็นกลับ ถ้ำมรกต เกาะกระดาน เกาะแหวน เกาะเชือก และเกาะรอก — เริ่มต้น 650 บาท ครบเรือ ไกด์ อาหาร และประกัน',
        ],
        'programs' => [
            'eyebrow' => 'พักค้างคืน',
            'title' => 'แพ็กเกจ 2 วัน 1 คืน',
            'lead' => 'เที่ยวทะเลเต็มอิ่ม 2 วัน 1 คืน รวมเรือ ที่พัก อาหาร และทีมงานดูแลตลอดทริป — ราคาเริ่มต้น 1,275 บาทต่อท่าน',
        ],
        'packages3d2n' => [
            'eyebrow' => 'พักยาวขึ้น',
            'title' => 'แพ็กเกจ 3 วัน 2 คืน',
            'lead' => 'เที่ยวทะเลตรังแบบสบาย ๆ 3 วัน 2 คืน ครบเรือ ที่พัก อาหาร กิจกรรมทะเล — ราคาเริ่มต้น 1,300 บาทต่อท่าน',
        ],
        'packages4d3n' => [
            'eyebrow' => 'ทริปยาวพิเศษ',
            'title' => 'แพ็กเกจ 4 วัน 3 คืน',
            'lead' => 'เที่ยวทะเลตรังครบจบ 4 วัน 3 คืน รวมทริปทะเล ที่พัก ทัวร์เมือง — ราคาเริ่มต้น 1,850 บาทต่อท่าน',
        ],
        'booking' => [
            'eyebrow' => 'จองง่ายใน 4 ขั้นตอน',
            'title' => 'จองเรือเที่ยวทะเลตรังใช้เวลาไม่ถึง 2 นาที',
            'lead' => 'ระบบคำนวณราคาให้อัตโนมัติ จากนั้นกดปุ่มเดียวเปิด LINE @talaytrang ติดต่อแอดมินได้ทันที',
        ],
        'reviews' => [
            'eyebrow' => 'รีวิวลูกค้า',
            'title' => 'เสียงจริงจากทริปจริง',
            'lead' => 'ลูกค้าหลายร้อยทริปบอกต่อ ทั้งครอบครัว คู่รัก เพื่อนกลุ่ม และบริษัท',
        ],
        'videos' => [
            'eyebrow' => 'TikTok ล่าสุด',
            'title' => 'ทะเลตรังในมุมที่คุณยังไม่เคยเห็น',
            'lead' => 'อัปเดตทุกสัปดาห์ที่',
            'tiktokHandle' => '@talaytrang',
        ],
        'office' => [
            'eyebrow' => 'ออฟฟิศมีตัวตนจริง',
            'title' => 'มาเจอเราได้จริงที่กันตัง จังหวัดตรัง',
            'infoTitle' => 'มั่นใจได้ทุกทริป',
            'infoLead' => 'เรามีออฟฟิศตั้งอยู่จริงที่อำเภอกันตัง เปิดบริการทุกวัน คุณสามารถนัดเข้ามาดูเรือก่อนตกลงจองได้',
        ],
        'cta' => [
            'eyebrow' => 'พร้อมออกทะเลแล้วใช่ไหม',
            'title' => 'เลือกเรือ คำนวณราคา และจองผ่าน LINE ได้เลย',
            'lead' => 'ทีมงานแอดมินตอบกลับใน LINE รวดเร็ว ดูแลคุณตั้งแต่ก่อนเที่ยวจนจบทริป',
        ],
    ];
}

/** @return array<string, array<string, string>> */
function tt_home_sections(?array $data = null): array
{
    if ($data === null) {
        $data = tt_read_data();
    }
    $stored = $data['homeSections'] ?? [];
    if (!is_array($stored)) {
        $stored = [];
    }
    return array_replace_recursive(tt_home_sections_defaults(), $stored);
}

/** แปลงลิงก์ share Facebook → URL เพจจริง (สำหรับ Page Plugin) */
function tt_resolve_facebook_page_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }

    if (!preg_match('#facebook\.com/share/#i', $url)) {
        if (preg_match('#facebook\.com/(?:profile\.php\?id=\d+|pages/[^/]+/\d+|people/[^/]+/\d+|[A-Za-z0-9.]+)/?#i', $url)) {
            return preg_replace('#\?.*$#', '', $url);
        }
        return '';
    }

    if (!function_exists('curl_init')) {
        return '';
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_NOBODY => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 12,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; TalayTrangCMS/1.0)',
    ]);
    curl_exec($ch);
    $final = (string)curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    if ($final === '' || !preg_match('#facebook\.com/([^/?&]+)#i', $final, $m)) {
        return '';
    }

    $slug = $m[1];
    $blocked = ['share', 'login', 'watch', 'sharer', 'dialog', 'plugins', 'l.php'];
    if (in_array(strtolower($slug), $blocked, true)) {
        return '';
    }

    return 'https://www.facebook.com/' . $slug;
}

function tt_json_response(array $payload, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function tt_ensure_credentials(): void
{
    if (is_file(TT_CREDENTIALS_FILE)) {
        return;
    }
    $hash = password_hash(TT_ADMIN_PASSWORD, PASSWORD_DEFAULT);
    $cred = [
        'username' => TT_ADMIN_USER,
        'password_hash' => $hash,
        'created' => date('c'),
    ];
    $dir = dirname(TT_CREDENTIALS_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(
        TT_CREDENTIALS_FILE,
        json_encode($cred, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        LOCK_EX
    );
}

function tt_verify_login(string $user, string $pass): bool
{
    tt_ensure_credentials();
    $raw = file_get_contents(TT_CREDENTIALS_FILE);
    if ($raw === false) {
        return false;
    }
    $cred = json_decode($raw, true);
    if (!is_array($cred)) {
        return false;
    }
    if (($cred['username'] ?? '') !== $user) {
        return false;
    }
    return password_verify($pass, $cred['password_hash'] ?? '');
}

function tt_slugify(string $text): string
{
    $text = trim(mb_strtolower($text, 'UTF-8'));
    $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text) ?? '';
    $text = preg_replace('/[\s-]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'item-' . time();
}

/** @return list<string> */
function tt_parse_lines(string $text): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text) ?: [])));
}

/** @return list<array{time: string, title: string, text: string}> */
function tt_parse_itinerary(string $text): array
{
    $out = [];
    foreach (tt_parse_lines($text) as $line) {
        $parts = array_map('trim', explode('|', $line));
        if (count($parts) < 2) {
            continue;
        }
        $time = $parts[0];
        if ($time === '') {
            continue;
        }
        if (count($parts) >= 3) {
            $title = $parts[1];
            $body = $parts[2];
            if ($title === '' && $body === '') {
                continue;
            }
            $out[] = ['time' => $time, 'title' => $title, 'text' => $body];
            continue;
        }
        $body = $parts[1];
        if ($body === '') {
            continue;
        }
        $out[] = ['time' => $time, 'title' => '', 'text' => $body];
    }
    return $out;
}

/** @return list<array{name: string, rooms: list<array{name: string, price?: int, book?: bool}>}> */
function tt_parse_hotels(string $text): array
{
    $hotels = [];
    $current = null;
    foreach (tt_parse_lines($text) as $line) {
        if (str_contains($line, '|')) {
            $parts = array_map('trim', explode('|', $line, 2));
            $roomName = $parts[0] ?? '';
            $priceRaw = $parts[1] ?? 'จอง';
            if ($roomName === '') {
                continue;
            }
            if ($current === null) {
                $current = ['name' => 'ที่พัก', 'rooms' => []];
                $hotels[] = $current;
            }
            $idx = count($hotels) - 1;
            if (count($hotels[$idx]['rooms']) >= 5) {
                continue;
            }
            $room = ['name' => $roomName];
            if (preg_match('/^(\d+)/', $priceRaw, $m)) {
                $room['price'] = (int) $m[1];
            } else {
                $room['book'] = true;
            }
            $hotels[$idx]['rooms'][] = $room;
            $current = $hotels[$idx];
            continue;
        }
        $current = ['name' => $line, 'rooms' => []];
        $hotels[] = $current;
    }
    return array_values(array_filter($hotels, static function ($h) {
        return ($h['name'] ?? '') !== '' && !empty($h['rooms']);
    }));
}

/** @param list<array{name: string, rooms: list<array{name: string, price?: int, book?: bool}>}> $hotels */
function tt_hotels_to_text(array $hotels): string
{
    $blocks = [];
    foreach ($hotels as $hotel) {
        $lines = [$hotel['name'] ?? ''];
        foreach ($hotel['rooms'] ?? [] as $room) {
            $price = isset($room['price']) ? (string) $room['price'] : 'จอง';
            $lines[] = ($room['name'] ?? '') . '|' . $price;
        }
        $blocks[] = implode("\n", $lines);
    }
    return implode("\n\n", $blocks);
}

/** @return list<array{day: int, label: string, items: list<string>}> */
function tt_parse_inclusions_by_day(string $text): array
{
    $blocks = [];
    $current = null;
    foreach (preg_split('/\r\n|\r|\n/', $text) ?: [] as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        if (preg_match('/^วันที่\s*(\d+)/u', $line, $m)) {
            if ($current) {
                $blocks[] = $current;
            }
            $current = [
                'day' => (int) $m[1],
                'label' => $line,
                'items' => [],
            ];
            continue;
        }
        if ($current) {
            $current['items'][] = $line;
        }
    }
    if ($current) {
        $blocks[] = $current;
    }
    return $blocks;
}

/** @param list<array{day?: int, label?: string, items?: list<string>}> $blocks */
function tt_inclusions_by_day_to_text(array $blocks): string
{
    $out = [];
    foreach ($blocks as $block) {
        $out[] = $block['label'] ?? ('วันที่ ' . ($block['day'] ?? ''));
        foreach ($block['items'] ?? [] as $item) {
            $out[] = $item;
        }
        $out[] = '';
    }
    return trim(implode("\n", $out));
}

/** @return list<string> */
function tt_program_inclusion_options(): array
{
    return [
        'boat' => 'เรือนำเที่ยว',
        'food' => 'อาหารบุฟเฟ่ต์',
        'guide' => 'พนักงานนำเที่ยว',
        'snorkel' => 'อุปกรณ์ดำน้ำตื้น',
        'park' => 'ค่าเข้าอุทยาน',
        'insurance' => 'ประกันอุบัติเหตุ',
        'coffee' => 'กาแฟ',
        'snack' => 'ของว่าง',
        'lifejacket' => 'เสื้อชูชีพ',
    ];
}

function tt_upload_image(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'อัปโหลดไม่สำเร็จ'];
    }
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : ($file['type'] ?? '');
    if ($finfo) {
        finfo_close($finfo);
    }
    if (!in_array($mime, $allowed, true)) {
        return ['ok' => false, 'error' => 'รองรับเฉพาะ JPG, PNG, WebP, GIF'];
    }
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        return ['ok' => false, 'error' => 'ไฟล์ใหญ่เกิน 5MB'];
    }

    if (!is_dir(TT_UPLOAD_DIR)) {
        mkdir(TT_UPLOAD_DIR, 0755, true);
    }

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        default => 'jpg',
    };
    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = TT_UPLOAD_DIR . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['ok' => false, 'error' => 'ย้ายไฟล์ไม่สำเร็จ'];
    }

    return ['ok' => true, 'url' => 'assets/uploads/' . $name, 'name' => $name];
}

function tt_list_uploads(): array
{
    if (!is_dir(TT_UPLOAD_DIR)) {
        return [];
    }
    $files = glob(TT_UPLOAD_DIR . '*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE) ?: [];
    usort($files, fn($a, $b) => filemtime($b) <=> filemtime($a));
    return array_map(function ($path) {
        $name = basename($path);
        return [
            'name' => $name,
            'url' => 'assets/uploads/' . $name,
            'size' => filesize($path),
            'mtime' => filemtime($path),
        ];
    }, $files);
}

/** แปลง Markdown ง่าย ๆ เป็น article body blocks */
function tt_markdown_to_body(string $md): array
{
    $md = trim(str_replace("\r\n", "\n", $md));
    if ($md === '') {
        return [];
    }

    $blocks = [];
    $lines = explode("\n", $md);
    $i = 0;
    $n = count($lines);

    while ($i < $n) {
        $line = $lines[$i];

        if (preg_match('/^##\s+(.+)$/', $line, $m)) {
            $blocks[] = ['type' => 'h2', 'text' => trim($m[1])];
            $i++;
            continue;
        }

        if (preg_match('/^>\s*(.+)$/', $line, $m)) {
            $blocks[] = ['type' => 'quote', 'text' => trim($m[1])];
            $i++;
            continue;
        }

        if (preg_match('/^!\[([^\]]*)\]\(([^)]+)\)$/', $line, $m)) {
            $blocks[] = ['type' => 'img', 'src' => trim($m[2]), 'alt' => trim($m[1])];
            $i++;
            continue;
        }

        if (preg_match('/^-\s+(.+)$/', $line)) {
            $items = [];
            while ($i < $n && preg_match('/^-\s+(.+)$/', $lines[$i], $lm)) {
                $items[] = trim($lm[1]);
                $i++;
            }
            $blocks[] = ['type' => 'list', 'items' => $items];
            continue;
        }

        if (trim($line) === '') {
            $i++;
            continue;
        }

        $para = [];
        while ($i < $n && trim($lines[$i]) !== '' && !preg_match('/^(##|>|-\s|!\[)/', $lines[$i])) {
            $para[] = $lines[$i];
            $i++;
        }
        if ($para) {
            $blocks[] = ['type' => 'p', 'text' => implode(' ', $para)];
        }
    }

    return $blocks;
}

function tt_body_to_markdown(array $body): string
{
    $out = [];
    foreach ($body as $b) {
        $type = $b['type'] ?? '';
        switch ($type) {
            case 'h2':
                $out[] = '## ' . ($b['text'] ?? '');
                break;
            case 'quote':
                $out[] = '> ' . ($b['text'] ?? '');
                break;
            case 'list':
                foreach ($b['items'] ?? [] as $it) {
                    $out[] = '- ' . $it;
                }
                break;
            case 'img':
                $alt = $b['alt'] ?? '';
                $out[] = '![' . $alt . '](' . ($b['src'] ?? '') . ')';
                break;
            case 'p':
            default:
                $out[] = $b['text'] ?? '';
        }
        $out[] = '';
    }
    return trim(implode("\n", $out));
}

function tt_backup_ensure_dir(): string
{
    if (!is_dir(TT_BACKUP_DIR)) {
        mkdir(TT_BACKUP_DIR, 0755, true);
    }
    return TT_BACKUP_DIR;
}

function tt_backup_is_valid_filename(string $name): bool
{
    return (bool)preg_match('/^\d{8}-\d{6}_(manual|auto)(-\d+)?\.(json|zip)$/', $name);
}

function tt_backup_format_bytes(int $bytes): string
{
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }
    return round($bytes / (1024 * 1024), 1) . ' MB';
}

/** เวลาจากชื่อไฟล์แบ็คอัพ เช่น 20260602-163045_manual.zip */
function tt_backup_created_from_filename(string $file): ?int
{
    if (!preg_match('/^(\d{4})(\d{2})(\d{2})-(\d{2})(\d{2})(\d{2})_(manual|auto)/', $file, $m)) {
        return null;
    }
    $dt = DateTimeImmutable::createFromFormat(
        'Y-m-d H:i:s',
        sprintf('%s-%s-%s %s:%s:%s', $m[1], $m[2], $m[3], $m[4], $m[5], $m[6]),
        new DateTimeZone('Asia/Bangkok')
    );
    return $dt ? $dt->getTimestamp() : null;
}

function tt_backup_format_datetime(int $timestamp): string
{
    return date('d/m/Y H:i:s', $timestamp);
}

/** @return list<array{file:string,type:string,created:string,size:int,label:string}> */
function tt_backup_list(): array
{
    $dir = tt_backup_ensure_dir();
    $items = [];
    foreach (glob($dir . '/*.{json,zip}', GLOB_BRACE) ?: [] as $path) {
        $file = basename($path);
        if (!tt_backup_is_valid_filename($file)) {
            continue;
        }
        $type = str_contains($file, '_auto.') ? 'auto' : 'manual';
        $created = tt_backup_created_from_filename($file) ?? (filemtime($path) ?: time());
        $items[] = [
            'file' => $file,
            'type' => $type,
            'created' => date('c', $created),
            'createdDisplay' => tt_backup_format_datetime($created),
            'size' => (int)filesize($path),
            'label' => $type === 'auto'
                ? 'ระบบสำรองให้ (ตอนกดบันทึก — ข้อความอย่างเดียว)'
                : (str_ends_with($file, '.zip') ? 'สำรองเอง (ครบทุกส่วน)' : 'สำรองเอง (ข้อความอย่างเดียว)'),
            'isZip' => str_ends_with($file, '.zip'),
        ];
    }
    usort($items, fn($a, $b) => strcmp($b['file'], $a['file']));
    return $items;
}

function tt_backup_prune(string $suffix, int $max): void
{
    $dir = tt_backup_ensure_dir();
    $files = [];
    foreach (glob($dir . '/*' . $suffix) ?: [] as $path) {
        $file = basename($path);
        if (tt_backup_is_valid_filename($file)) {
            $files[] = $file;
        }
    }
    rsort($files);
    foreach (array_slice($files, $max) as $old) {
        @unlink($dir . '/' . $old);
    }
}

/** แฮชเนื้อหาจริง (ไม่รวม meta.updated) — กันสำรองซ้ำเมื่อกดบันทึกหลายครั้งโดยไม่ได้แก้อะไร */
function tt_backup_content_hash(string $jsonRaw): string
{
    $data = json_decode($jsonRaw, true);
    if (!is_array($data)) {
        return md5($jsonRaw);
    }
    unset($data['meta']);
    $encoded = json_encode($data, JSON_UNESCAPED_UNICODE);
    return md5($encoded !== false ? $encoded : $jsonRaw);
}

function tt_auto_backup_before_write(): void
{
    if (!is_file(TT_DATA_FILE)) {
        return;
    }
    $current = file_get_contents(TT_DATA_FILE);
    if ($current === false || $current === '') {
        return;
    }
    $hash = tt_backup_content_hash($current);
    $dir = tt_backup_ensure_dir();
    foreach (glob($dir . '/*_auto.json') ?: [] as $path) {
        $prev = file_get_contents($path);
        if ($prev !== false && tt_backup_content_hash($prev) === $hash) {
            return;
        }
    }
    $name = date('Ymd-His') . '_auto.json';
    file_put_contents($dir . '/' . $name, $current, LOCK_EX);
    tt_backup_prune('_auto.json', TT_BACKUP_MAX_AUTO);
}

/** ให้ไฟล์ fallback บนหน้าเว็บตรงกับ site.json ก่อนสำรอง */
function tt_backup_sync_frontend_cache(): void
{
    $data = tt_read_data();
    if ($data !== []) {
        tt_write_fallback_js($data);
    }
}

/** @return list<string> absolute paths under uploads */
function tt_backup_collect_upload_files(): array
{
    if (!is_dir(TT_UPLOAD_DIR)) {
        return [];
    }
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(TT_UPLOAD_DIR, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $base = $file->getFilename();
        if ($base === '.gitkeep') {
            continue;
        }
        $files[] = $file->getPathname();
    }
    sort($files);
    return $files;
}

/**
 * @return array{ok:bool,error?:string,uploadCount?:int}
 */
function tt_backup_pack_full_zip(string $zipPath): array
{
    if (!class_exists('ZipArchive')) {
        return ['ok' => false, 'error' => 'เซิร์ฟเวอร์ไม่รองรับ ZipArchive'];
    }
    if (!is_file(TT_DATA_FILE)) {
        return ['ok' => false, 'error' => 'ไม่พบ data/site.json'];
    }

    tt_backup_sync_frontend_cache();

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return ['ok' => false, 'error' => 'สร้างไฟล์ ZIP ไม่สำเร็จ'];
    }

    $zip->addFile(TT_DATA_FILE, 'site.json');

    $frontendFiles = [
        'assets/js/data-fallback.js' => TT_FALLBACK_JS,
        'assets/js/tt-cache-version.js' => TT_ROOT . '/assets/js/tt-cache-version.js',
    ];
    foreach ($frontendFiles as $entry => $abs) {
        if (is_file($abs)) {
            $zip->addFile($abs, $entry);
        }
    }

    $uploadCount = 0;
    $uploadRoot = rtrim(str_replace('\\', '/', TT_UPLOAD_DIR), '/') . '/';
    foreach (tt_backup_collect_upload_files() as $uploadPath) {
        $normalized = str_replace('\\', '/', $uploadPath);
        $rel = str_starts_with($normalized, $uploadRoot)
            ? substr($normalized, strlen($uploadRoot))
            : basename($uploadPath);
        $zip->addFile($uploadPath, 'uploads/' . $rel);
        $uploadCount++;
    }

    $manifest = json_encode([
        'created' => date('c'),
        'type' => 'full',
        'hasUploads' => true,
        'hasFallback' => true,
        'uploadCount' => $uploadCount,
        'php' => PHP_VERSION,
    ], JSON_UNESCAPED_UNICODE);
    $zip->addFromString('manifest.json', (string)$manifest);
    $zip->close();

    return ['ok' => true, 'uploadCount' => $uploadCount];
}

/** @return array{ok:bool,error?:string,file?:string,uploadCount?:int,partial?:bool} */
function tt_backup_create(): array
{
    if (!is_file(TT_DATA_FILE)) {
        return ['ok' => false, 'error' => 'ไม่พบ data/site.json'];
    }

    $dir = tt_backup_ensure_dir();
    $stamp = date('Ymd-His');

    if (class_exists('ZipArchive')) {
        $zipName = $stamp . '_manual.zip';
        $packed = tt_backup_pack_full_zip($dir . '/' . $zipName);
        if (!$packed['ok']) {
            return ['ok' => false, 'error' => $packed['error'] ?? 'สร้างแบ็คอัพไม่สำเร็จ'];
        }
        tt_backup_prune('_manual.zip', TT_BACKUP_MAX_MANUAL);
        return [
            'ok' => true,
            'file' => $zipName,
            'uploadCount' => (int)($packed['uploadCount'] ?? 0),
        ];
    }

    tt_backup_sync_frontend_cache();
    $jsonName = $stamp . '_manual.json';
    if (!copy(TT_DATA_FILE, $dir . '/' . $jsonName)) {
        return ['ok' => false, 'error' => 'คัดลอก site.json ไม่สำเร็จ'];
    }
    tt_backup_prune('_manual.json', TT_BACKUP_MAX_MANUAL);
    return ['ok' => true, 'file' => $jsonName, 'partial' => true];
}

/** @return array{ok:bool,error?:string} */
function tt_backup_restore(string $filename): array
{
    if (!tt_backup_is_valid_filename($filename)) {
        return ['ok' => false, 'error' => 'ชื่อไฟล์ไม่ถูกต้อง'];
    }
    $path = tt_backup_ensure_dir() . '/' . $filename;
    if (!is_file($path)) {
        return ['ok' => false, 'error' => 'ไม่พบไฟล์แบ็คอัพ'];
    }

    if (str_ends_with($filename, '.json')) {
        $raw = file_get_contents($path);
        if ($raw === false) {
            return ['ok' => false, 'error' => 'อ่านไฟล์ไม่สำเร็จ'];
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return ['ok' => false, 'error' => 'JSON ไม่ถูกต้อง'];
        }
        if (!tt_write_data($data)) {
            return ['ok' => false, 'error' => 'เขียน site.json ไม่สำเร็จ'];
        }
        return ['ok' => true];
    }

    if (!class_exists('ZipArchive')) {
        return ['ok' => false, 'error' => 'เซิร์ฟเวอร์ไม่รองรับ ZipArchive'];
    }

    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        return ['ok' => false, 'error' => 'เปิด ZIP ไม่สำเร็จ'];
    }

    $jsonRaw = $zip->getFromName('site.json');
    if ($jsonRaw === false) {
        $zip->close();
        return ['ok' => false, 'error' => 'ไม่พบ site.json ใน ZIP'];
    }
    $data = json_decode($jsonRaw, true);
    if (!is_array($data)) {
        $zip->close();
        return ['ok' => false, 'error' => 'site.json ใน ZIP ไม่ถูกต้อง'];
    }
    if (!tt_write_data($data)) {
        $zip->close();
        return ['ok' => false, 'error' => 'เขียน site.json ไม่สำเร็จ'];
    }

    $restoreMap = [
        'assets/js/data-fallback.js' => TT_FALLBACK_JS,
        'assets/js/tt-cache-version.js' => TT_ROOT . '/assets/js/tt-cache-version.js',
    ];

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $entry = $zip->getNameIndex($i);
        if (!is_string($entry) || str_ends_with($entry, '/')) {
            continue;
        }

        if (isset($restoreMap[$entry])) {
            $content = $zip->getFromIndex($i);
            if ($content !== false) {
                $dest = $restoreMap[$entry];
                $destDir = dirname($dest);
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                file_put_contents($dest, $content, LOCK_EX);
            }
            continue;
        }

        if (!str_starts_with($entry, 'uploads/')) {
            continue;
        }
        $rel = substr($entry, strlen('uploads/'));
        if ($rel === '' || $rel === '.gitkeep' || str_contains($rel, '..')) {
            continue;
        }
        $content = $zip->getFromIndex($i);
        if ($content === false) {
            continue;
        }
        if (!is_dir(TT_UPLOAD_DIR)) {
            mkdir(TT_UPLOAD_DIR, 0755, true);
        }
        $dest = TT_UPLOAD_DIR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        $destDir = dirname($dest);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        file_put_contents($dest, $content, LOCK_EX);
    }
    $zip->close();

    return ['ok' => true];
}

function tt_backup_delete(string $filename): bool
{
    if (!tt_backup_is_valid_filename($filename)) {
        return false;
    }
    $path = tt_backup_ensure_dir() . '/' . $filename;
    return is_file($path) && unlink($path);
}

function tt_backup_path(string $filename): ?string
{
    if (!tt_backup_is_valid_filename($filename)) {
        return null;
    }
    $path = tt_backup_ensure_dir() . '/' . $filename;
    return is_file($path) ? $path : null;
}
