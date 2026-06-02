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
    return $data;
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
        $mtime = filemtime($path) ?: time();
        $items[] = [
            'file' => $file,
            'type' => $type,
            'created' => date('c', $mtime),
            'createdDisplay' => date('d/m/Y H:i:s', $mtime),
            'size' => (int)filesize($path),
            'label' => $type === 'auto' ? 'ระบบสำรองให้ (ตอนกดบันทึก)' : 'สำรองเอง',
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

function tt_auto_backup_before_write(): void
{
    if (!is_file(TT_DATA_FILE)) {
        return;
    }
    $current = file_get_contents(TT_DATA_FILE);
    if ($current === false || $current === '') {
        return;
    }
    $hash = md5($current);
    $dir = tt_backup_ensure_dir();
    foreach (glob($dir . '/*_auto.json') ?: [] as $path) {
        $prev = file_get_contents($path);
        if ($prev !== false && md5($prev) === $hash) {
            return;
        }
    }
    $name = date('Ymd-His') . '_auto.json';
    file_put_contents($dir . '/' . $name, $current, LOCK_EX);
    tt_backup_prune('_auto.json', TT_BACKUP_MAX_AUTO);
}

/** @return array{ok:bool,error?:string,file?:string} */
function tt_backup_create(bool $includeUploads = false): array
{
    if (!is_file(TT_DATA_FILE)) {
        return ['ok' => false, 'error' => 'ไม่พบ data/site.json'];
    }
    $dir = tt_backup_ensure_dir();
    $stamp = date('Ymd-His');
    $jsonName = $stamp . '_manual.json';
    $jsonPath = $dir . '/' . $jsonName;

    if (!copy(TT_DATA_FILE, $jsonPath)) {
        return ['ok' => false, 'error' => 'คัดลอก site.json ไม่สำเร็จ'];
    }

    if (!$includeUploads) {
        tt_backup_prune('_manual.json', TT_BACKUP_MAX_MANUAL);
        return ['ok' => true, 'file' => $jsonName];
    }

    if (!class_exists('ZipArchive')) {
        @unlink($jsonPath);
        return ['ok' => false, 'error' => 'เซิร์ฟเวอร์ไม่รองรับ ZipArchive — ใช้แบ็คอัพเนื้อหาอย่างเดียว'];
    }

    $zipName = $stamp . '_manual.zip';
    $zipPath = $dir . '/' . $zipName;
    @unlink($jsonPath);

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return ['ok' => false, 'error' => 'สร้างไฟล์ ZIP ไม่สำเร็จ'];
    }
    $zip->addFile(TT_DATA_FILE, 'site.json');
    $manifest = json_encode([
        'created' => date('c'),
        'type' => 'full',
        'hasUploads' => true,
        'php' => PHP_VERSION,
    ], JSON_UNESCAPED_UNICODE);
    $zip->addFromString('manifest.json', (string)$manifest);

    if (is_dir(TT_UPLOAD_DIR)) {
        $uploadFiles = glob(TT_UPLOAD_DIR . '*') ?: [];
        foreach ($uploadFiles as $uploadPath) {
            if (!is_file($uploadPath)) {
                continue;
            }
            $base = basename($uploadPath);
            if ($base === '.gitkeep') {
                continue;
            }
            $zip->addFile($uploadPath, 'uploads/' . $base);
        }
    }
    $zip->close();

    tt_backup_prune('_manual.zip', TT_BACKUP_MAX_MANUAL);
    return ['ok' => true, 'file' => $zipName];
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

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $entry = $zip->getNameIndex($i);
        if (!is_string($entry) || !str_starts_with($entry, 'uploads/')) {
            continue;
        }
        $base = basename($entry);
        if ($base === '' || $base === '.gitkeep') {
            continue;
        }
        $content = $zip->getFromIndex($i);
        if ($content === false) {
            continue;
        }
        if (!is_dir(TT_UPLOAD_DIR)) {
            mkdir(TT_UPLOAD_DIR, 0755, true);
        }
        file_put_contents(TT_UPLOAD_DIR . $base, $content, LOCK_EX);
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
