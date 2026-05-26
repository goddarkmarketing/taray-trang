<?php
/** One-time patch: about, seo, homeDeals, boat/program deal fields */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$data = tt_read_data();
if (!$data) {
    fwrite(STDERR, "site.json not found\n");
    exit(1);
}

$boatDeals = [
    'longtail' => [
        'rating' => 4.9,
        'reviewCount' => '2.1พัน',
        'stars' => 5,
        'badgeTL' => 'กันตัง · ตรัง',
        'badgeTR' => 'ยอดนิยม',
        'badgeTRClass' => 'is-hot',
    ],
    'speedboat' => [
        'rating' => 4.9,
        'reviewCount' => '2.1พัน',
        'stars' => 5,
        'badgeTL' => 'กันตัง · ตรัง',
        'badgeTR' => '',
        'badgeTRClass' => 'is-info',
    ],
    'bigboat' => [
        'rating' => 4.9,
        'reviewCount' => '2.1พัน',
        'stars' => 5,
        'badgeTL' => 'กันตัง · ตรัง',
        'badgeTR' => 'กรุ๊ปใหญ่',
        'badgeTRClass' => 'is-info',
    ],
];

foreach ($data['boats'] ?? [] as $i => $boat) {
    $id = $boat['id'] ?? '';
    $deal = $boatDeals[$id] ?? [
        'rating' => 4.8,
        'reviewCount' => '500',
        'stars' => 5,
        'badgeTL' => 'กันตัง · ตรัง',
        'badgeTR' => '',
        'badgeTRClass' => 'is-info',
    ];
    $data['boats'][$i] = array_merge($boat, $deal);
}

$programDeals = [
    'four-islands' => ['rating' => 4.9, 'reviewCount' => '3.2พัน', 'oldPrice' => 4200, 'stars' => 5],
    'kradan' => ['rating' => 4.8, 'reviewCount' => '1.8พัน', 'oldPrice' => 5500, 'stars' => 5],
    'diving' => ['rating' => 4.9, 'reviewCount' => '1.4พัน', 'oldPrice' => 6500, 'stars' => 5],
    'emerald-cave' => ['rating' => 4.9, 'reviewCount' => '2.5พัน', 'oldPrice' => 3800, 'stars' => 5],
    'private-charter' => ['rating' => 5.0, 'reviewCount' => '760', 'oldPrice' => 9500, 'stars' => 5],
];

foreach ($data['programs'] ?? [] as $i => $prog) {
    $id = $prog['id'] ?? '';
    $deal = $programDeals[$id] ?? [
        'rating' => 4.8,
        'reviewCount' => '500',
        'oldPrice' => 0,
        'stars' => 5,
    ];
    $data['programs'][$i] = array_merge($prog, $deal);
}

$data['homeDeals'] = [
    'customTrip' => [
        'enabled' => true,
        'href' => 'booking.html',
        'image' => $data['images']['programPrivate'] ?? '',
        'name' => 'เหมาลำพิเศษ Custom Trip',
        'sub' => 'ออกแบบเส้นทาง เลือก Option ได้เอง',
        'rating' => 5.0,
        'reviewCount' => '850',
        'stars' => 5,
        'basePrice' => 8000,
        'badgeTL' => 'Tailor-made',
        'badgeTR' => 'Custom',
        'badgeTRClass' => 'is-info',
    ],
];

$data['about'] = [
    'hero' => [
        'crumb' => 'เกี่ยวกับ Talay Trang',
        'title' => 'คนตรังพาเที่ยวทะเลตรัง',
        'lead' => 'เราคือทีมงานเจ้าถิ่นกันตังโดยกำเนิด ที่อยากให้ทุกคนที่มาเที่ยวตรัง ได้สัมผัสทะเลของเราอย่างปลอดภัย สะดวก และประทับใจ',
        'heroAlt' => 'ทีมงาน Talay Trang ที่ทะเลตรัง',
    ],
    'story' => [
        'eyebrow' => 'เรื่องราวของเรา',
        'title' => 'เริ่มจากเรือลำเดียว สู่ทีมงานที่บริการกว่า 1,000 ทริป',
        'paragraphs' => [
            'Talay Trang เริ่มต้นจากครอบครัวชาวกันตังที่หาเลี้ยงชีพด้วยเรือมาตั้งแต่รุ่นพ่อ เรารู้จักทุกเกาะ ทุกถ้ำ ทุกฤดูลม และอยากให้ลูกค้าทุกคนได้สัมผัสทะเลตรังในแบบที่คนท้องถิ่นรู้จริง',
            'วันนี้เรามีเรือทั้ง 3 ประเภทคือเรือหางยาว Speed Boat และเรือใหญ่ พร้อมทีมกัปตัน ไกด์ และช่างภาพที่ผ่านการทำงานกับลูกค้ามาแล้วกว่า 1,000 ทริป ตั้งแต่ครอบครัว คู่รัก กรุ๊ปเพื่อน ไปจนถึงสัมมนาบริษัทขนาดใหญ่',
        ],
        'ctaPrimary' => ['label' => 'เริ่มจองเรือ', 'href' => 'booking.html'],
        'ctaSecondary' => ['label' => 'อ่านบทความ', 'href' => 'articles.html'],
    ],
    'whySection' => [
        'eyebrow' => 'ทำไมต้องจองกับเรา',
        'title' => '4 เหตุผลที่ลูกค้ากลับมาใช้บริการซ้ำ',
    ],
    'trust' => [
        ['value' => '1,000+', 'label' => 'ทริปที่บริการแล้ว'],
        ['value' => '5+ ปี', 'label' => 'ประสบการณ์ในตรัง'],
        ['value' => '3', 'label' => 'ประเภทเรือ'],
        ['value' => '4.9 ★', 'label' => 'คะแนนเฉลี่ย'],
    ],
    'office' => [
        'eyebrow' => 'มีออฟฟิศจริง',
        'title' => 'มาเจอเราได้ที่กันตัง',
        'infoTitle' => 'โปร่งใส ตรวจสอบได้',
        'infoText' => 'ก่อนตกลงจอง คุณสามารถเข้ามาดูเรือ ดูออฟฟิศจริง พบทีมงานได้โดยตรง เพราะเราอยากให้ลูกค้าทุกคนมั่นใจ 100% ก่อนชำระเงิน',
    ],
    'cta' => [
        'title' => 'เตรียมทริปครั้งต่อไปกับเรา',
        'lead' => 'ทักไลน์มาได้เลย เราจะแนะนำเรือ โปรแกรม และเวลาที่เหมาะกับคุณที่สุด',
        'lineLabel' => 'คุยกับเรา',
        'bookingLabel' => 'เริ่มจองเรือ',
    ],
];

$ogImage = $data['images']['heroHome'] ?? 'https://images.pexels.com/photos/14573822/pexels-photo-14573822.jpeg?auto=compress&cs=tinysrgb&w=1200';

$data['seo'] = [
    'index' => [
        'title' => 'Talay Trang — เช่าเรือเที่ยวทะเลตรัง เลือกเรือได้ตามสไตล์คุณ',
        'description' => 'บริการเช่าเรือเที่ยวทะเลตรัง โดยทีมงานเจ้าถิ่น เลือกได้ทั้งเรือหางยาว Speed Boat และเรือใหญ่ พร้อมโปรแกรม 4 เกาะ ถ้ำมรกต เกาะกระดาน จองง่ายผ่าน LINE @talaytrang',
        'ogTitle' => 'Talay Trang — เช่าเรือเที่ยวทะเลตรัง',
        'ogDescription' => 'เช่าเรือทะเลตรัง 3 ประเภท หางยาว / Speed Boat / เรือใหญ่ พร้อมโปรแกรมเที่ยวยอดนิยม',
        'ogImage' => $ogImage,
    ],
    'about' => [
        'title' => 'เกี่ยวกับเรา — Talay Trang เช่าเรือตรัง โดยเจ้าถิ่นกันตัง',
        'description' => 'Talay Trang ทีมงานเจ้าถิ่นกันตัง บริการเช่าเรือเที่ยวทะเลตรัง มีออฟฟิศจริง รู้เส้นทาง ดูแลตั้งแต่ก่อนเดินทางจนจบทริป',
        'ogTitle' => 'เกี่ยวกับ Talay Trang — เช่าเรือตรัง',
        'ogDescription' => 'ทีมงานเจ้าถิ่นกันตัง บริการเช่าเรือเที่ยวทะเลตรัง มีออฟฟิศจริง',
        'ogImage' => $data['images']['heroAbout'] ?? $ogImage,
    ],
    'boats' => [
        'title' => 'ประเภทเรือ — เรือหางยาว Speed Boat เรือใหญ่ | Talay Trang',
        'description' => 'เลือกเช่าเรือเที่ยวทะเลตรังให้เหมาะกับทริปคุณ มี 3 ประเภท: เรือหางยาว Speed Boat และเรือใหญ่ พร้อมราคาและจำนวนคนรองรับชัดเจน',
        'ogTitle' => 'ประเภทเรือ — Talay Trang',
        'ogDescription' => 'เรือหางยาว Speed Boat และเรือใหญ่ เลือกได้ตามสไตล์ทริป',
        'ogImage' => $data['images']['heroBoats'] ?? $ogImage,
    ],
    'programs' => [
        'title' => 'โปรแกรมเที่ยวทะเลตรัง — 4 เกาะ ถ้ำมรกต เกาะกระดาน | Talay Trang',
        'description' => 'โปรแกรมเช่าเรือเที่ยวทะเลตรังยอดนิยม 4 เกาะ ถ้ำมรกต เกาะกระดาน ดำน้ำดูปะการัง และเหมาลำส่วนตัว ราคาเริ่มต้น 3,200 บาท',
        'ogTitle' => 'โปรแกรมเที่ยวทะเลตรัง — Talay Trang',
        'ogDescription' => 'โปรแกรม 4 เกาะ ถ้ำมรกต เกาะกระดาน และเหมาลำส่วนตัว',
        'ogImage' => $data['images']['heroPrograms'] ?? $ogImage,
    ],
    'booking' => [
        'title' => 'จองเรือเที่ยวทะเลตรัง — คำนวณราคาก่อนจอง | Talay Trang',
        'description' => 'เลือกเรือ เลือกโปรแกรม กรอกข้อมูล ระบบคำนวณราคาทันที พร้อมส่งจองผ่าน LINE — ใช้เวลาไม่ถึง 2 นาที',
        'ogTitle' => 'จองเรือเที่ยวทะเลตรัง — Talay Trang',
        'ogDescription' => 'คำนวณราคาและส่งจองผ่าน LINE ได้ทันที',
        'ogImage' => $data['images']['heroBooking'] ?? $ogImage,
    ],
    'articles' => [
        'title' => 'บทความ — เคล็ดลับเที่ยวทะเลตรัง | Talay Trang',
        'description' => 'รวมบทความและคู่มือเที่ยวทะเลตรัง — แนะนำเกาะยอดนิยม การเลือกเรือ ฤดูที่เหมาะสม และ Checklist เตรียมตัวก่อนออกเดินทาง',
        'ogTitle' => 'บทความเที่ยวทะเลตรัง — Talay Trang',
        'ogDescription' => 'คู่มือและเคล็ดลับเที่ยวทะเลตรังจากทีมงาน Talay Trang',
        'ogImage' => $data['images']['heroArticles'] ?? $ogImage,
    ],
    'article' => [
        'title' => 'บทความ | Talay Trang',
        'description' => 'บทความเที่ยวทะเลตรัง จาก Talay Trang',
        'ogTitle' => 'บทความ — Talay Trang',
        'ogDescription' => 'บทความและคู่มือเที่ยวทะเลตรัง',
        'ogImage' => $data['images']['heroArticles'] ?? $ogImage,
    ],
    'videos' => [
        'title' => 'วิดีโอ TikTok ทะเลตรัง — ทริปจริงจาก @talaytrang | Talay Trang',
        'description' => 'รวมวิดีโอ TikTok ทะเลตรังจากช่อง @talaytrang ดูบรรยากาศจริงก่อนตัดสินใจจอง อัปเดตล่าสุดทุกสัปดาห์',
        'ogTitle' => 'วิดีโอ TikTok — Talay Trang',
        'ogDescription' => 'ทริปจริงจากช่อง @talaytrang',
        'ogImage' => $data['images']['heroVideos'] ?? $ogImage,
    ],
    'contact' => [
        'title' => 'ติดต่อ Talay Trang — โทร / LINE / แผนที่ออฟฟิศกันตัง',
        'description' => 'ติดต่อ Talay Trang เช่าเรือเที่ยวทะเลตรัง โทร 081-234-5678 LINE @talaytrang ออฟฟิศตั้งอยู่ที่อำเภอกันตัง จังหวัดตรัง',
        'ogTitle' => 'ติดต่อ Talay Trang',
        'ogDescription' => 'โทร LINE แผนที่ออฟฟิศกันตัง',
        'ogImage' => $data['images']['heroContact'] ?? $ogImage,
    ],
];

if (!tt_write_data($data)) {
    fwrite(STDERR, "write failed\n");
    exit(1);
}

echo "Patched site.json + data-fallback.js\n";
