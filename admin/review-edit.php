<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$reviews = $data['reviews'] ?? [];
$idx = isset($_GET['i']) ? (int)$_GET['i'] : -1;
$review = ($idx >= 0 && isset($reviews[$idx])) ? $reviews[$idx] : null;
$isNew = $review === null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postIdx = isset($_POST['idx']) ? (int)$_POST['idx'] : -1;
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        tt_set_flash('กรุณาระบุชื่อลูกค้า');
        header('Location: review-edit.php' . ($postIdx >= 0 ? '?i=' . $postIdx : ''));
        exit;
    }

    $initial = trim($_POST['initial'] ?? '');
    if ($initial === '') {
        $initial = mb_substr($name, 0, 1);
    }

    $item = [
        'name' => $name,
        'initial' => $initial,
        'trip' => trim($_POST['trip'] ?? ''),
        'from' => trim($_POST['from'] ?? ''),
        'rating' => max(1, min(5, (int)($_POST['rating'] ?? 5))),
        'text' => trim($_POST['text'] ?? ''),
    ];

    if ($postIdx >= 0 && isset($reviews[$postIdx])) {
        $reviews[$postIdx] = $item;
    } else {
        $reviews[] = $item;
        $postIdx = count($reviews) - 1;
    }

    $data['reviews'] = $reviews;
    tt_write_data($data);
    tt_set_flash('บันทึกรีวิวแล้ว');
    header('Location: review-edit.php?i=' . $postIdx);
    exit;
}

$pageTitle = $isNew ? 'เพิ่มรีวิว' : 'แก้ไขรีวิว';
tt_admin_header($pageTitle, 'reviews');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post">
  <div class="card">
    <h2>ข้อมูลรีวิว</h2>
    <input type="hidden" name="idx" value="<?= $isNew ? -1 : $idx ?>"/>

    <div class="grid-2">
      <div class="field"><label>ชื่อลูกค้า</label><input name="name" required value="<?= htmlspecialchars($review['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="คุณพิม ขจร"/></div>
      <div class="field"><label>ตัวอักษร (วงกลม)</label><input name="initial" maxlength="2" value="<?= htmlspecialchars($review['initial'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ว่าง = ใช้ตัวแรกของชื่อ"/></div>
      <div class="field"><label>ทริป</label><input name="trip" value="<?= htmlspecialchars($review['trip'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="โปรแกรม 4 เกาะ"/></div>
      <div class="field"><label>จากจังหวัด</label><input name="from" value="<?= htmlspecialchars($review['from'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="กรุงเทพฯ"/></div>
      <div class="field"><label>คะแนน (1–5)</label><input name="rating" type="number" min="1" max="5" value="<?= (int)($review['rating'] ?? 5) ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>ข้อความรีวิว</label><textarea name="text" rows="5"><?= htmlspecialchars($review['text'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
    </div>

    <p class="field-hint">รีวิวจะแสดงบนหน้าแรกในส่วนรีวิวลูกค้า</p>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">บันทึก</button>
      <a class="btn btn-ghost" href="reviews.php">กลับ</a>
      <a class="btn btn-ghost" href="../index.html#home-reviews" target="_blank">ดูหน้าแรก</a>
      <?php if (!$isNew): ?>
        <a class="btn btn-danger" href="reviews.php?delete=<?= $idx ?>" onclick="return confirm('ลบรีวิวนี้?')">ลบรีวิวนี้</a>
      <?php endif; ?>
    </div>
  </div>
</form>

<?php tt_admin_footer(); ?>
