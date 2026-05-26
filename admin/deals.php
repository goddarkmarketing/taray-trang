<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$custom = $data['homeDeals']['customTrip'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['homeDeals'] = [
        'customTrip' => [
            'enabled' => !empty($_POST['enabled']),
            'href' => trim($_POST['href'] ?? 'booking.html'),
            'image' => trim($_POST['image'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'sub' => trim($_POST['sub'] ?? ''),
            'rating' => (float)($_POST['rating'] ?? 4.8),
            'reviewCount' => trim($_POST['reviewCount'] ?? ''),
            'stars' => (int)($_POST['stars'] ?? 5),
            'basePrice' => (int)($_POST['basePrice'] ?? 0),
            'badgeTL' => trim($_POST['badgeTL'] ?? ''),
            'badgeTR' => trim($_POST['badgeTR'] ?? ''),
            'badgeTRClass' => trim($_POST['badgeTRClass'] ?? 'is-info'),
        ],
    ];
    tt_write_data($data);
    tt_set_flash('บันทึกการ์ด Custom Trip แล้ว');
    header('Location: deals.php');
    exit;
}

tt_admin_header('การ์ดหน้าแรก (Deal)', 'deals');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="card">
  <p class="field-hint">คะแนน/รีวิวของเรือและโปรแกรม แก้ที่เมนู <a href="boats.php">ประเภทเรือ</a> และ <a href="programs.php">โปรแกรมทัวร์</a></p>
</div>

<form method="post" class="card">
  <h2>การ์ด Custom Trip (หน้าแรก)</h2>
  <div class="field">
    <label><input type="checkbox" name="enabled" value="1" <?= !empty($custom['enabled']) ? 'checked' : '' ?>/> แสดงการ์ดนี้บนหน้าแรก</label>
  </div>
  <div class="grid-2">
    <div class="field"><label>ชื่อ</label><input name="name" value="<?= htmlspecialchars($custom['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>คำบรรยาย</label><input name="sub" value="<?= htmlspecialchars($custom['sub'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>URL รูป</label><input name="image" value="<?= htmlspecialchars($custom['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>ลิงก์</label><input name="href" value="<?= htmlspecialchars($custom['href'] ?? 'booking.html', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>คะแนน (0–5)</label><input name="rating" type="number" step="0.1" min="0" max="5" value="<?= htmlspecialchars((string)($custom['rating'] ?? 5), ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>จำนวนรีวิว (ข้อความ)</label><input name="reviewCount" value="<?= htmlspecialchars($custom['reviewCount'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="850 หรือ 2.1พัน"/></div>
    <div class="field"><label>ดาว (1–5)</label><input name="stars" type="number" min="1" max="5" value="<?= (int)($custom['stars'] ?? 5) ?>"/></div>
    <div class="field"><label>ราคาเริ่มต้น</label><input name="basePrice" type="number" value="<?= (int)($custom['basePrice'] ?? 0) ?>"/></div>
    <div class="field"><label>ป้ายซ้าย (badge TL)</label><input name="badgeTL" value="<?= htmlspecialchars($custom['badgeTL'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>ป้ายขวา (badge TR)</label><input name="badgeTR" value="<?= htmlspecialchars($custom['badgeTR'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>Class ป้ายขวา</label><input name="badgeTRClass" value="<?= htmlspecialchars($custom['badgeTRClass'] ?? 'is-info', ENT_QUOTES, 'UTF-8') ?>" placeholder="is-hot หรือ is-info"/></div>
  </div>
  <div class="form-actions"><button type="submit" class="btn btn-primary">บันทึก</button></div>
</form>

<?php tt_admin_footer(); ?>
