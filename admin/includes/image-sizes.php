<?php
declare(strict_types=1);

/**
 * คำแนะนำขนาดรูป — อิงจาก CSS หน้าเว็บ (object-fit: cover)
 * ใช้ใน admin เป็นคำกำกับเท่านั้น
 */
function tt_image_size_hint(?string $key = null, ?string $context = null): string
{
    if ($key !== null && $key !== '') {
        $byKey = tt_image_size_by_key();
        if (isset($byKey[$key])) {
            return $byKey[$key];
        }
        if (str_starts_with($key, 'hero') && $key !== 'heroSlides') {
            return $byKey['heroSub'] ?? tt_image_size_by_context()['hero_sub'];
        }
        if (str_starts_with($key, 'program')) {
            return $byKey['programCard'] ?? tt_image_size_by_context()['program'];
        }
        if (str_starts_with($key, 'boat')) {
            return $byKey['boatDetail'] ?? tt_image_size_by_context()['boat'];
        }
        if (str_starts_with($key, 'svc')) {
            return $byKey['servicePill'] ?? tt_image_size_by_context()['service'];
        }
        if (str_starts_with($key, 'about')) {
            return $byKey[$key === 'about1' ? 'about1' : 'aboutSmall'] ?? '';
        }
    }

    if ($context !== null && isset(tt_image_size_by_context()[$context])) {
        return tt_image_size_by_context()[$context];
    }

    return 'JPG/PNG/WebP — แนะนำอัปโหลดแล้วคัดลอก path ไปใส่ในช่อง URL';
}

/** @return array<string, string> */
function tt_image_size_by_key(): array
{
    return [
        'heroSub' => '1920×720 px (กว้าง) — Hero หัวข้อย่อยทุกหน้า (crop แนวนอน)',
        'heroHome' => '1920×720 px — Hero หน้าแรก (Registry สำรอง)',
        'heroBoats' => '1920×720 px — Hero หน้า ประเภทเรือ',
        'heroPrograms' => '1920×720 px — Hero หน้า โปรแกรม',
        'heroBooking' => '1920×720 px — Hero หน้า จองเรือ',
        'heroArticles' => '1920×720 px — Hero หน้า บทความ',
        'heroVideos' => '1920×720 px — Hero หน้า วิดีโอ',
        'heroAbout' => '1920×720 px — Hero หน้า เกี่ยวกับเรา',
        'heroContact' => '1920×720 px — Hero หน้า ติดต่อ',
        'boatLongtail' => '1200×900 px (4:3) — รูปเรือหางยาว (Registry)',
        'boatSpeedboat' => '1200×900 px (4:3) — รูป Speed Boat (Registry)',
        'boatLarge' => '1200×900 px (4:3) — รูปเรือใหญ่ (Registry)',
        'programCard' => '1200×750 px (16:10) — การ์ดโปรแกรมบนหน้าแรก / โปรแกรม',
        'programFourIslands' => '1200×750 px (16:10) — โปรแกรม 4 เกาะ (Registry)',
        'programKradan' => '1200×750 px (16:10) — เกาะกระดาน (Registry)',
        'programDiving' => '1200×750 px (16:10) — ดำน้ำ (Registry)',
        'programEmerald' => '1200×750 px (16:10) — ถ้ำมรกต (Registry)',
        'programPrivate' => '1200×750 px (16:10) — เหมาลำ (Registry)',
        'about1' => '800×1000 px (แนวตั้ง) — รูปใหญ่ซ้ายใน story grid หน้า About',
        'about2' => '600×400 px — รูปเล็กบน story grid หน้า About',
        'about3' => '600×400 px — รูปเล็กล่าง story grid หน้า About',
        'servicePill' => '900×600 px (3:2) — รูปบริการ (Registry)',
        'videoThumbs' => '800×450 px — รูปย่อวิดีโอใน Registry (แต่ละบรรทัด)',
        'local' => '1200×800 px — แกลเลอรีท้องถิ่น (แต่ละบรรทัด)',
    ];
}

/** @return array<string, string> */
function tt_image_size_by_context(): array
{
    return [
        'hero_slide' => '1920×700 px (~2.75:1) — สไลด์แบนเนอร์หน้าแรก (เต็มความกว้าง)',
        'hero_sub' => '1920×720 px — แบนเนอร์หัวข้อย่อย (สูง ~360px บน desktop)',
        'program' => '1200×750 px (16:10) — การ์ดโปรแกรม (crop อัตโนมัติ)',
        'boat' => '1200×900 px (4:3) — รูปเรือในหน้า ประเภทเรือ',
        'deal' => '1200×900 px (4:3) — การ์ดโปรโมชันหน้าแรก',
        'article_cover' => '1600×1000 px (16:10) — รูปปกบทความ (หน้ารายละเอียด)',
        'article_thumb' => '800×500 px (16:10) — รูปย่อในรายการบทความ',
        'video_thumb' => '720×1280 px (9:16) — ปกคลิป TikTok / วิดีโอ (แนวตั้ง)',
        'og' => '1200×630 px — รูปแชร์ Facebook / LINE (og:image)',
        'service' => '900×600 px (3:2) — รูปบริการ',
        'upload' => 'ขึ้นกับการใช้งาน — ดูตารางด้านล่างก่อนอัปโหลด',
    ];
}

/** @return list<array{label: string, size: string, ratio: string, where: string}> */
function tt_image_size_guide_rows(): array
{
    return [
        [
            'label' => 'สไลด์แบนเนอร์หน้าแรก',
            'size' => '1920×700 px',
            'ratio' => '~2.75 : 1',
            'where' => 'เมนู แบนเนอร์หน้าแรก → hero-edit',
        ],
        [
            'label' => 'Hero หัวข้อย่อย (ทุกหน้า)',
            'size' => '1920×720 px',
            'ratio' => 'กว้าง ~8:3',
            'where' => 'รูปภาพ (Registry) → heroBoats, heroPrograms, …',
        ],
        [
            'label' => 'การ์ดโปรแกรม',
            'size' => '1200×750 px',
            'ratio' => '16 : 10',
            'where' => 'เมนู โปรแกรมทัวร์ → แก้ไข → URL รูป',
        ],
        [
            'label' => 'รูปเรือ (รายละเอียด)',
            'size' => '1200×900 px',
            'ratio' => '4 : 3',
            'where' => 'เมนู ประเภทเรือ → แก้ไข → URL รูป',
        ],
        [
            'label' => 'การ์ดโปรโมชันหน้าแรก',
            'size' => '1200×900 px',
            'ratio' => '4 : 3',
            'where' => 'เมนู การ์ดหน้าแรก → Custom Trip',
        ],
        [
            'label' => 'บทความ — รูปปก',
            'size' => '1600×1000 px',
            'ratio' => '16 : 10',
            'where' => 'เมนู บทความ → แก้ไข → รูปปก',
        ],
        [
            'label' => 'บทความ — รูปย่อ',
            'size' => '800×500 px',
            'ratio' => '16 : 10',
            'where' => 'เมนู บทความ → แก้ไข → รูปย่อ',
        ],
        [
            'label' => 'ปกวิดีโอ / TikTok',
            'size' => '720×1280 px',
            'ratio' => '9 : 16',
            'where' => 'เมนู วิดีโอ → แก้ไข → รูปปก',
        ],
        [
            'label' => 'About — รูปใหญ่',
            'size' => '800×1000 px',
            'ratio' => '4 : 5',
            'where' => 'Registry → about1',
        ],
        [
            'label' => 'About — รูปเล็ก',
            'size' => '600×400 px',
            'ratio' => '3 : 2',
            'where' => 'Registry → about2, about3',
        ],
        [
            'label' => 'SEO / แชร์โซเชียล',
            'size' => '1200×630 px',
            'ratio' => '1.91 : 1',
            'where' => 'เมนู SEO / Meta → og:image',
        ],
    ];
}

/** @return array<string, array{size: string, ratio: string, note: string}> */
function tt_image_size_meta_map(): array
{
    return [
        'hero_slide' => ['size' => '1920×700 px', 'ratio' => '~2.75 : 1', 'note' => 'สไลด์แบนเนอร์หน้าแรก — กว้างเต็มจอ'],
        'hero_sub' => ['size' => '1920×720 px', 'ratio' => 'กว้าง ~8:3', 'note' => 'แบนเนอร์หัวข้อย่อยทุกหน้า'],
        'program' => ['size' => '1200×750 px', 'ratio' => '16 : 10', 'note' => 'การ์ดโปรแกรมบนหน้าแรก / หน้าโปรแกรม'],
        'boat' => ['size' => '1200×900 px', 'ratio' => '4 : 3', 'note' => 'รูปเรือในหน้า ประเภทเรือ'],
        'deal' => ['size' => '1200×900 px', 'ratio' => '4 : 3', 'note' => 'การ์ดโปรโมชัน Custom Trip หน้าแรก'],
        'article_cover' => ['size' => '1600×1000 px', 'ratio' => '16 : 10', 'note' => 'รูปปกบทความ (หน้ารายละเอียด)'],
        'article_thumb' => ['size' => '800×500 px', 'ratio' => '16 : 10', 'note' => 'รูปย่อในรายการบทความ'],
        'video_thumb' => ['size' => '720×1280 px', 'ratio' => '9 : 16', 'note' => 'ปกคลิป TikTok / วิดีโอ (แนวตั้ง)'],
        'og' => ['size' => '1200×630 px', 'ratio' => '1.91 : 1', 'note' => 'รูปแชร์ Facebook / LINE'],
        'service' => ['size' => '900×600 px', 'ratio' => '3 : 2', 'note' => 'รูปบริการ'],
    ];
}

/** @return array{size: string, ratio: string, note: string}|null */
function tt_image_size_meta(?string $key = null, ?string $context = null): ?array
{
    if ($context !== null && isset(tt_image_size_meta_map()[$context])) {
        return tt_image_size_meta_map()[$context];
    }

    if ($key !== null && $key !== '') {
        if (str_starts_with($key, 'hero') && $key !== 'heroSlides') {
            return tt_image_size_meta_map()['hero_sub'];
        }
        if (str_starts_with($key, 'program')) {
            return tt_image_size_meta_map()['program'];
        }
        if (str_starts_with($key, 'boat')) {
            return tt_image_size_meta_map()['boat'];
        }
        if ($key === 'about1') {
            return ['size' => '800×1000 px', 'ratio' => '4 : 5', 'note' => 'รูปใหญ่ซ้ายใน story grid หน้า About'];
        }
        if ($key === 'about2' || $key === 'about3') {
            return ['size' => '600×400 px', 'ratio' => '3 : 2', 'note' => 'รูปเล็กใน story grid หน้า About'];
        }
    }

    return null;
}

function tt_image_size_label(?string $key = null, ?string $context = null): string
{
    $meta = tt_image_size_meta($key, $context);
    return $meta ? 'แนะนำ ' . $meta['size'] : '';
}

function tt_render_image_size_hint(?string $key = null, ?string $context = null): string
{
    $meta = tt_image_size_meta($key, $context);
    if ($meta === null) {
        $text = tt_image_size_hint($key, $context);
        if ($text === '') {
            return '';
        }
        return '<div class="alert alert-info image-size-note"><strong>ขนาดรูป:</strong> '
            . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    return '<div class="alert alert-info image-size-note">'
        . '<strong>📐 ขนาดรูปที่แนะนำ:</strong> '
        . '<span class="image-size-dim">' . htmlspecialchars($meta['size'], ENT_QUOTES, 'UTF-8') . '</span>'
        . ' <span class="image-size-ratio">(' . htmlspecialchars($meta['ratio'], ENT_QUOTES, 'UTF-8') . ')</span>'
        . '<br><span class="image-size-desc">' . htmlspecialchars($meta['note'], ENT_QUOTES, 'UTF-8') . '</span>'
        . ' · JPG/PNG/WebP · crop กลางอัตโนมัติ'
        . ' · <a href="media.php">ดูตารางขนาดทั้งหมด</a>'
        . '</div>';
}

/** @return string JSON สำหรับอัปเดต hint ตาม key ใน image-edit */
function tt_image_size_hints_json(): string
{
    return json_encode(tt_image_size_by_key(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
}
