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
    file_put_contents(TT_FALLBACK_JS, $js, LOCK_EX);
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
