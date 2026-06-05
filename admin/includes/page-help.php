<?php
declare(strict_types=1);

/**
 * @return array<string, array{
 *   lead: string,
 *   items: list<string>,
 *   preview?: string,
 *   previewLabel?: string,
 *   visuals?: list<array{
 *     caption?: string,
 *     page: string,
 *     selector: string,
 *     maxHeight?: int,
 *     waitChildren?: bool,
 *     scope?: string
 *   }>
 * }>
 */
function tt_admin_page_help_map(): array
{
    return [
        'dashboard' => [
            'lead' => 'หน้านี้เป็นภาพรวมระบบหลังบ้าน — ไม่ได้แสดงบนหน้าเว็บโดยตรง',
            'items' => [
                'ใช้เมนูด้านซ้ายเพื่อแก้เนื้อหาแต่ละส่วนของเว็บ',
                'หลังบันทึก ข้อมูลจะอัปเดตบนเว็บทันที',
            ],
        ],
        'site' => [
            'lead' => 'ข้อมูลติดต่อ & แบรนด์ — ใช้หลายจุดบนเว็บ',
            'items' => [
                'Footer ทุกหน้า — เบอร์โทร, LINE, Facebook, TikTok, ที่อยู่, เวลเปิด',
                'ปุ่ม 「จอง LINE」 และ 「โทรเลย」 ในเมนู / แถบมือถือ',
                'หน้า About — แผนที่, ที่อยู่, เวลทำการ, LINE',
                'หน้าจอง (booking) — เปิด LINE หลังกดส่งคำขอ',
                'หน้าวิดีโอ — ฝัง Facebook feed (URL เพจ)',
            ],
            'preview' => '../about.html',
            'previewLabel' => 'ดู Footer / About',
            'visuals' => [
                [
                    'caption' => 'Footer ทุกหน้า — เบอร์, LINE, โซเชียล, ที่อยู่',
                    'page' => '../index.html',
                    'selector' => '#site-footer',
                    'maxHeight' => 200,
                    'waitChildren' => true,
                ],
                [
                    'caption' => 'หน้า About — แผนที่ & ข้อมูลติดต่อ',
                    'page' => '../about.html',
                    'selector' => '.office',
                    'maxHeight' => 220,
                ],
            ],
        ],
        'nav' => [
            'lead' => 'เมนูลิงก์ด้านบน (และเมนูมือถือ)',
            'items' => [
                'แสดงทุกหน้า — หน้าแรก, เรือ, โปรแกรม, จอง, บทความ, ฯลฯ',
                'ลำดับและชื่อเมนูตามที่ตั้งในรายการ',
            ],
            'preview' => '../index.html',
            'visuals' => [[
                'caption' => 'แถบเมนูด้านบนทุกหน้า',
                'page' => '../index.html',
                'selector' => '#site-header',
                'maxHeight' => 72,
            ]],
        ],
        'hero' => [
            'lead' => 'สไลด์แบนเนอร์ด้านบนหน้าแรก',
            'items' => [
                'รูปสไลด์หมุนอัตโนมัติใต้เมนู (index.html)',
                'แต่ละสไลด์ = รูป + คำอธิบาย alt',
            ],
            'preview' => '../index.html',
            'visuals' => [[
                'caption' => 'สไลด์แบนเนอร์หน้าแรก (ใต้เมนู)',
                'page' => '../index.html',
                'selector' => '#hero-slider',
                'scope' => 'section',
                'maxHeight' => 220,
                'waitChildren' => true,
            ]],
        ],
        'services' => [
            'lead' => 'แถบบริการ (Pills) บนหน้าแรก',
            'items' => [
                'ไอคอน + ชื่อบริการ เลื่อนแนวนอน',
                'กดแล้วไปหน้าที่กำหนด (เรือ, จอง, LINE ฯลฯ)',
            ],
            'preview' => '../index.html',
            'visuals' => [[
                'caption' => 'แถบบริการ (Pills) บนหน้าแรก',
                'page' => '../index.html',
                'selector' => '#home-services',
                'scope' => 'section',
                'maxHeight' => 280,
                'waitChildren' => true,
            ]],
        ],
        'boats' => [
            'lead' => 'ประเภทเรือ — หน้า boats.html + การ์ดหน้าแรก',
            'items' => [
                'หัวข้อ Section ประเภทเรือบนหน้าแรก — แก้ที่เมนู 「หัวข้อ Section」',
                'หน้า 「ประเภทเรือ」 — รายละเอียดเรือแต่ละแบบ',
                'การ์ดเรือในหน้าแรก — แก้รายการในตารางด้านล่าง',
            ],
            'preview' => '../boats.html',
            'visuals' => [
                [
                    'caption' => 'Hero + รายการเรือ — หน้า boats.html',
                    'page' => '../boats.html',
                    'selector' => '#boats-list',
                    'scope' => 'section',
                    'maxHeight' => 320,
                    'waitChildren' => true,
                ],
                [
                    'caption' => 'การ์ดเรือบนหน้าแรก',
                    'page' => '../index.html',
                    'selector' => '#home-boats',
                    'scope' => 'section',
                    'maxHeight' => 320,
                    'waitChildren' => true,
                ],
            ],
        ],
        'programs' => [
            'lead' => 'โปรแกรมทัวร์ — หน้า programs.html + การ์ดหน้าแรก',
            'items' => [
                'หน้า 「โปรแกรม」 — รายการโปรแกรมทั้งหมด',
                'การ์ดโปรแกรมบนหน้าแรก และลิงก์ไปหน้าจอง',
            ],
            'preview' => '../programs.html',
            'visuals' => [
                [
                    'caption' => 'การ์ดโปรแกรมบนหน้าแรก',
                    'page' => '../index.html',
                    'selector' => '#home-programs',
                    'scope' => 'section',
                    'maxHeight' => 320,
                    'waitChildren' => true,
                ],
                [
                    'caption' => 'รายการโปรแกรม — หน้า programs.html',
                    'page' => '../programs.html',
                    'selector' => '#programs-list',
                    'scope' => 'section',
                    'maxHeight' => 320,
                    'waitChildren' => true,
                ],
            ],
        ],
        'options' => [
            'lead' => 'ตัวเลือกเสริมในหน้าจองเรือ',
            'items' => [
                'อาหาร, ไกด์, ช่างภาพ, ดำน้ำ, รถรับส่ง ฯลฯ',
                'ลูกค้าเลือกเพิ่มตอนคำนวณราคา (booking.html)',
            ],
            'preview' => '../booking.html?step=trip',
            'visuals' => [[
                'caption' => 'ตัวเลือกเสริมในฟอร์มจอง',
                'page' => '../booking.html?step=trip',
                'selector' => '#option-choices',
                'scope' => 'section',
                'maxHeight' => 280,
                'waitChildren' => true,
            ]],
        ],
        'reviews' => [
            'lead' => 'รีวิวลูกค้า — ส่วนรีวิวบนหน้าแรก',
            'items' => [
                'การ์ดคำชมลูกค้า (สไลด์บนมือถือ)',
                'ชื่อ, ทริป, ข้อความ, ดาว',
            ],
            'preview' => '../index.html',
            'visuals' => [[
                'caption' => 'ส่วนรีวิวลูกค้าหน้าแรก',
                'page' => '../index.html',
                'selector' => '#home-reviews',
                'scope' => 'section',
                'maxHeight' => 340,
                'waitChildren' => true,
            ]],
        ],
        'videos' => [
            'lead' => 'วิดีโอ TikTok / Facebook — หน้า videos.html',
            'items' => [
                'การ์ดคลิป + ลิงก์ไป TikTok / Facebook',
                'ฝัง feed จาก TikTok และเพจ Facebook',
            ],
            'preview' => '../videos.html',
            'visuals' => [[
                'caption' => 'ส่วนวิดีโอ TikTok บนหน้าเว็บ',
                'page' => '../videos.html',
                'selector' => '#tiktok-feed',
                'scope' => 'section',
                'maxHeight' => 320,
                'waitChildren' => true,
            ]],
        ],
        'why' => [
            'lead' => 'จุดเด่น 「ทำไมต้องจองกับเรา」',
            'items' => [
                'แสดงบนหน้า About (และส่วนที่เกี่ยวข้องบนหน้าแรก)',
                'ไอคอน + หัวข้อ + คำอธิบายสั้น ๆ',
            ],
            'preview' => '../about.html',
            'visuals' => [[
                'caption' => '4 จุดเด่นบนหน้า About',
                'page' => '../about.html',
                'selector' => '#why-list',
                'scope' => 'section',
                'maxHeight' => 280,
                'waitChildren' => true,
            ]],
        ],
        'steps' => [
            'lead' => 'ขั้นตอนการจอง — หน้าแรก',
            'items' => [
                '4 ขั้นตอน: เลือกเรือ → โปรแกรม → คำนวณราคา → LINE',
                'อยู่เหนือส่วนรีวิวหรือ CTA บนหน้าแรก',
            ],
            'preview' => '../index.html',
            'visuals' => [[
                'caption' => '4 ขั้นตอนจองบนหน้าแรก',
                'page' => '../index.html',
                'selector' => '#home-steps',
                'scope' => 'section',
                'maxHeight' => 280,
                'waitChildren' => true,
            ]],
        ],
        'articles' => [
            'lead' => 'บทความ — หน้า articles.html + รายละเอียด',
            'items' => [
                'รายการบทความ (รูปย่อ, หัวข้อ, คำโปรย)',
                'หน้ article.html — เนื้อหาเต็มตาม slug',
            ],
            'preview' => '../articles.html',
            'visuals' => [[
                'caption' => 'รายการบทความบนหน้าเว็บ',
                'page' => '../articles.html',
                'selector' => '#articles-list',
                'scope' => 'section',
                'maxHeight' => 320,
                'waitChildren' => true,
            ]],
        ],
        'about' => [
            'lead' => 'เนื้อหาหน้า 「เกี่ยวกับเรา」',
            'items' => [
                'Hero, เรื่องราว, ตัวเลขความน่าเชื่อถือ, CTA',
                'ที่อยู่/แผนที่/โทร/LINE ดึงจาก 「ข้อมูลเว็บ」 อัตโนมัติ',
                'รูป story grid แก้ที่ 「รูปภาพ Registry」 (about1–3)',
            ],
            'preview' => '../about.html',
            'visuals' => [
                [
                    'caption' => 'Hero หน้า About',
                    'page' => '../about.html',
                    'selector' => '.hero-sub',
                    'maxHeight' => 180,
                ],
                [
                    'caption' => 'Story grid + รูป 3 ช่อง',
                    'page' => '../about.html',
                    'selector' => '#story-grid',
                    'scope' => 'section',
                    'maxHeight' => 280,
                    'waitChildren' => true,
                ],
            ],
        ],
        'deals' => [
            'lead' => 'การ์ดโปรโมชัน Custom Trip บนหน้าแรก',
            'items' => [
                'การ์ดพิเศษในส่วน deal (ถัดจากโปรแกรม/เรือ)',
                'เปิด/ปิดการแสดง, รูป, ราคา, ป้าย, ลิงก์ไปจอง',
            ],
            'preview' => '../index.html',
            'visuals' => [[
                'caption' => 'การ์ด Custom Trip ในหน้าแรก (ส่วนเรือ)',
                'page' => '../index.html',
                'selector' => '#home-boats',
                'scope' => 'section',
                'maxHeight' => 340,
                'waitChildren' => true,
            ]],
        ],
        'seo' => [
            'lead' => 'Title & Meta ของแต่ละหน้า (Google / แชร์โซเชียล)',
            'items' => [
                'ชื่อแท็บเบราว์เซอร์, คำอธิบาย, og:image',
                'ไม่เห็นบนหน้าเว็บโดยตรง แต่มีผลตอนค้นหาและแชร์ลิงก์',
            ],
            'preview' => '../index.html',
            'visuals' => [[
                'caption' => 'หน้าเว็บที่ใช้ title / meta (ตัวอย่างหน้าแรก)',
                'page' => '../index.html',
                'selector' => '#hero-slider',
                'maxHeight' => 160,
                'waitChildren' => true,
            ]],
        ],
        'images' => [
            'lead' => 'คลังรูป (Registry) — รูปพื้นหลอง Hero ย่อย & About',
            'items' => [
                'Hero หัวข้อย่อย: heroBoats, heroPrograms, heroAbout ฯลฯ',
                'รูป About: about1, about2, about3',
                'ใช้ URL หรือ path จาก 「อัปโหลดรูป」',
            ],
            'preview' => '../boats.html',
            'previewLabel' => 'ดู Hero หน้ารอง',
            'visuals' => [[
                'caption' => 'Hero หัวข้อย่อย (ตัวอย่างหน้าเรือ)',
                'page' => '../boats.html',
                'selector' => '.hero-sub',
                'maxHeight' => 180,
            ]],
        ],
        'media' => [
            'lead' => 'อัปโหลดรูปเข้าเว็บ — ใช้ร่วมกับช่อง URL ในเมนูอื่น',
            'items' => [
                'ไม่แสดงเป็นหน้าเว็บแยก — เป็นเครื่องมือเก็บไฟล์',
                'อัปโหลดแล้ว copy path ไปวางในฟอร์มแก้ไข',
                'หรือกดปุ่ม 「อัปโหลด」 ข้างช่อง URL โดยตรง',
            ],
            'visuals' => [[
                'caption' => 'รูปที่อัปโหลดจะไปแสดงในการ์ด/แบนเนอร์แบบนี้',
                'page' => '../index.html',
                'selector' => '#home-programs',
                'scope' => 'section',
                'maxHeight' => 320,
                'waitChildren' => true,
            ]],
        ],
    ];
}

/**
 * @return array<string, array{
 *   lead: string,
 *   items: list<string>,
 *   preview?: string,
 *   visual: array{page:string,selector:string,maxHeight?:int,waitChildren?:bool,scope?:string,caption?:string}
 * }>
 */
function tt_home_section_help_map(): array
{
    return [
        'hero' => [
            'lead' => 'หัวข้อบนแบนเนอร์สไลด์ — หน้าแรก',
            'items' => ['ข้อความทับรูปสไลด์ด้านบน', 'ไม่รวมปุ่มบริการเลื่อนด้านล่าง'],
            'preview' => '../index.html',
            'visual' => [
                'caption' => 'ตำแหน่งบนหน้าแรก',
                'page' => '../index.html',
                'selector' => '#hero-slider .overlay-head',
                'maxHeight' => 200,
                'waitChildren' => true,
            ],
        ],
        'boats' => [
            'lead' => 'หัวข้อ Section ประเภทเรือ — หน้าแรก',
            'items' => ['อยู่เหนือการ์ดเรือ 4 ใบ', 'การ์ดเรือแก้ที่เมนูประเภทเรือ'],
            'preview' => '../index.html#boats',
            'visual' => [
                'page' => '../index.html',
                'selector' => '[data-tt-home-section="boats"]',
                'scope' => 'section',
                'maxHeight' => 180,
            ],
        ],
        'programs' => [
            'lead' => 'หัวข้อ Section โปรแกรมยอดนิยม — หน้าแรก',
            'items' => ['อยู่เหนือการ์ดโปรแกรม', 'การ์ดโปรแกรมแก้ที่เมนูโปรแกรมทัวร์'],
            'preview' => '../index.html',
            'visual' => [
                'page' => '../index.html',
                'selector' => '#home-section-programs .section-head',
                'scope' => 'section',
                'maxHeight' => 200,
            ],
        ],
        'booking' => [
            'lead' => 'หัวข้อ Section ขั้นตอนจอง — หน้าแรก',
            'items' => ['อยู่เหนือ 4 ขั้นตอนจอง', 'ขั้นตอนแก้ที่เมนูขั้นตอนจอง'],
            'preview' => '../index.html',
            'visual' => [
                'page' => '../index.html',
                'selector' => '[data-tt-home-section="booking"]',
                'scope' => 'section',
                'maxHeight' => 180,
            ],
        ],
        'reviews' => [
            'lead' => 'หัวข้อ Section รีวิว — หน้าแรก',
            'items' => ['อยู่เหนือการ์ดรีวิว', 'รีวิวแก้ที่เมนูรีวิว'],
            'preview' => '../index.html',
            'visual' => [
                'page' => '../index.html',
                'selector' => '[data-tt-home-section="reviews"]',
                'scope' => 'section',
                'maxHeight' => 180,
            ],
        ],
        'videos' => [
            'lead' => 'หัวข้อ Section TikTok — หน้าแรก',
            'items' => ['อยู่เหนือคลิปวิดีโอ', 'คลิปแก้ที่เมนูวิดีโอ'],
            'preview' => '../index.html',
            'visual' => [
                'page' => '../index.html',
                'selector' => '[data-tt-home-section="videos"]',
                'scope' => 'section',
                'maxHeight' => 180,
            ],
        ],
        'office' => [
            'lead' => 'หัวข้อ Section ออฟฟิศ — หน้าแรก',
            'items' => ['หัวข้อด้านบน + กล่องข้อมูลออฟฟิศ', 'แผนที่/เบอร์ดึงจากข้อมูลเว็บ'],
            'preview' => '../index.html',
            'visual' => [
                'page' => '../index.html',
                'selector' => '[data-tt-home-section="office"]',
                'scope' => 'section',
                'maxHeight' => 200,
            ],
        ],
        'cta' => [
            'lead' => 'หัวข้อ Section CTA ท้ายหน้า — หน้าแรก',
            'items' => ['ข้อความก่อนปุ่มจองท้ายหน้าแรก'],
            'preview' => '../index.html',
            'visual' => [
                'page' => '../index.html',
                'selector' => '[data-tt-home-section="cta"]',
                'maxHeight' => 200,
            ],
        ],
    ];
}

function tt_admin_page_help_visual_figure(array $visual): string
{
    $caption = trim((string)($visual['caption'] ?? ''));
    $page = (string)($visual['page'] ?? '');
    $selector = (string)($visual['selector'] ?? '');
    if ($page === '' || $selector === '') {
        return '';
    }
    $maxHeight = (int)($visual['maxHeight'] ?? 200);
    $waitChildren = !empty($visual['waitChildren']) ? '1' : '0';
    $scope = trim((string)($visual['scope'] ?? ''));

    $html = '<figure class="admin-page-help-figure admin-page-help-live-wrap"'
        . ' data-page="' . htmlspecialchars($page, ENT_QUOTES, 'UTF-8') . '"'
        . ' data-selector="' . htmlspecialchars($selector, ENT_QUOTES, 'UTF-8') . '"'
        . ' data-max-height="' . $maxHeight . '"'
        . ' data-wait-children="' . $waitChildren . '"'
        . ($scope !== '' ? ' data-scope="' . htmlspecialchars($scope, ENT_QUOTES, 'UTF-8') . '"' : '')
        . '>'
        . '<div class="admin-page-help-live">'
        . '<div class="admin-page-help-live-clip">'
        . '<div class="admin-page-help-live-stage">'
        . '<iframe class="admin-page-help-iframe" title="ตัวอย่างหน้าเว็บ" tabindex="-1" loading="eager"></iframe>'
        . '</div></div>'
        . '<div class="admin-page-help-live-loading">กำลังโหลดตัวอย่าง…</div>'
        . '</div>';
    if ($caption !== '') {
        $html .= '<figcaption>' . htmlspecialchars($caption, ENT_QUOTES, 'UTF-8') . '</figcaption>';
    }
    return $html . '</figure>';
}

function tt_admin_page_help(?string $pageId): ?array
{
    if ($pageId === '' || $pageId === 'password' || $pageId === 'section-headings') {
        return null;
    }
    $map = tt_admin_page_help_map();
    return $map[$pageId] ?? null;
}

function tt_render_admin_section_help(string $sectionKey): string
{
    $map = tt_home_section_help_map();
    $help = $map[$sectionKey] ?? null;
    if ($help === null) {
        return '';
    }

    $visualsHtml = tt_admin_page_help_visual_figure($help['visual'] ?? []);
    $hasVisuals = $visualsHtml !== '';
    $popoverClass = 'admin-page-help-popover' . ($hasVisuals ? ' has-visuals' : '');

    $items = '';
    foreach ($help['items'] as $item) {
        $items .= '<li>' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</li>';
    }

    $preview = '';
    if (!empty($help['preview'])) {
        $preview = '<p class="admin-page-help-foot"><a href="'
            . htmlspecialchars($help['preview'], ENT_QUOTES, 'UTF-8')
            . '" target="_blank" rel="noopener">เปิดดูบนเว็บ ↗</a></p>';
    }

    return '<span class="admin-page-help admin-page-help--inline">'
        . '<button type="button" class="admin-page-help-btn" aria-label="ดูว่าแสดงที่ไหนบนเว็บ">?</button>'
        . '<div class="' . $popoverClass . '" role="tooltip">'
        . $visualsHtml
        . '<p class="admin-page-help-lead">' . htmlspecialchars($help['lead'], ENT_QUOTES, 'UTF-8') . '</p>'
        . '<ul class="admin-page-help-list">' . $items . '</ul>'
        . $preview
        . '</div></span>';
}

function tt_render_admin_page_help(?string $pageId): string
{
    $help = tt_admin_page_help($pageId);
    if ($help === null) {
        return '';
    }

    $visualsHtml = '';
    foreach ($help['visuals'] ?? [] as $visual) {
        $visualsHtml .= tt_admin_page_help_visual_figure($visual);
    }

    $hasVisuals = $visualsHtml !== '';
    $popoverClass = 'admin-page-help-popover' . ($hasVisuals ? ' has-visuals' : '');

    $items = '';
    foreach ($help['items'] as $item) {
        $items .= '<li>' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</li>';
    }

    $preview = '';
    if (!empty($help['preview'])) {
        $label = $help['previewLabel'] ?? 'เปิดดูบนเว็บ';
        $preview = '<p class="admin-page-help-foot"><a href="'
            . htmlspecialchars($help['preview'], ENT_QUOTES, 'UTF-8')
            . '" target="_blank" rel="noopener">'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            . ' ↗</a></p>';
    }

    return '<span class="admin-page-help">'
        . '<button type="button" class="admin-page-help-btn" aria-label="ดูว่าแสดงที่ไหนบนเว็บ">?</button>'
        . '<div class="' . $popoverClass . '" role="tooltip">'
        . $visualsHtml
        . '<p class="admin-page-help-lead">' . htmlspecialchars($help['lead'], ENT_QUOTES, 'UTF-8') . '</p>'
        . '<ul class="admin-page-help-list">' . $items . '</ul>'
        . $preview
        . '</div></span>';
}
