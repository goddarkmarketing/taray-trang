<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/image-field.php';
tt_require_admin();

$data = tt_read_data();
$programs = $data['programs'] ?? [];
$id = trim($_GET['id'] ?? '');
$program = null;
$idx = -1;

foreach ($programs as $i => $p) {
    if (($p['id'] ?? '') === $id) {
        $program = $p;
        $idx = $i;
        break;
    }
}

$isNew = $id === '' || $program === null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newId = trim($_POST['id'] ?? '');
    if ($newId === '') {
        $newId = tt_slugify($_POST['name'] ?? 'program');
    }
    $stops = array_values(array_filter(array_map('trim', explode("\n", $_POST['stops'] ?? ''))));
    $boats = array_values(array_filter(array_map('trim', explode(',', $_POST['boats'] ?? ''))));

    $item = [
        'id' => $newId,
        'name' => trim($_POST['name'] ?? ''),
        'image' => trim($_POST['image'] ?? ''),
        'route' => trim($_POST['route'] ?? ''),
        'stops' => $stops,
        'duration' => trim($_POST['duration'] ?? ''),
        'basePrice' => (int)($_POST['basePrice'] ?? 0),
        'boats' => $boats,
        'desc' => trim($_POST['desc'] ?? ''),
        'ribbon' => trim($_POST['ribbon'] ?? '') ?: null,
        'rating' => (float)($_POST['rating'] ?? 4.8),
        'reviewCount' => trim($_POST['reviewCount'] ?? ''),
        'oldPrice' => (int)($_POST['oldPrice'] ?? 0),
        'stars' => (int)($_POST['stars'] ?? 5),
    ];
    if ($item['ribbon'] === null) {
        unset($item['ribbon']);
    }

    if ($idx >= 0) {
        if ($newId !== $id) {
            foreach ($programs as $j => $p) {
                if ($j !== $idx && ($p['id'] ?? '') === $newId) {
                    tt_set_flash('ID นี้มีอยู่แล้ว — ใช้ ID อื่น');
                    header('Location: program-edit.php?id=' . urlencode($id));
                    exit;
                }
            }
        }
        $programs[$idx] = $item;
    } else {
        foreach ($programs as $p) {
            if (($p['id'] ?? '') === $newId) {
                tt_set_flash('ID นี้มีอยู่แล้ว — ใช้ ID อื่น');
                header('Location: program-edit.php?id=' . urlencode($newId));
                exit;
            }
        }
        $programs[] = $item;
    }

    $data['programs'] = $programs;
    tt_write_data($data);
    tt_set_flash('บันทึกโปรแกรมแล้ว');
    header('Location: program-edit.php?id=' . urlencode($newId));
    exit;
}

$pageTitle = $isNew ? 'เพิ่มโปรแกรม' : 'แก้ไขโปรแกรม';
tt_admin_header($pageTitle, 'programs');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post">
  <div class="card">
    <h2>ข้อมูลหลัก</h2>
    <div class="grid-2">
      <div class="field"><label>ชื่อโปรแกรม</label><input name="name" required value="<?= htmlspecialchars($program['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ID (slug)</label><input name="id" value="<?= htmlspecialchars($program['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="four-islands, kradan"/></div>
      <?php tt_render_image_url_field('URL รูป', 'image', $program['image'] ?? '', ['context' => 'program']); ?>
      <div class="field"><label>ป้าย (ribbon)</label><input name="ribbon" value="<?= htmlspecialchars($program['ribbon'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ยอดนิยม"/></div>
      <div class="field"><label>เส้นทาง</label><input name="route" value="<?= htmlspecialchars($program['route'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ระยะเวลา</label><input name="duration" value="<?= htmlspecialchars($program['duration'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ราคาเริ่มต้น (บาท)</label><input name="basePrice" type="number" value="<?= (int)($program['basePrice'] ?? 0) ?>"/></div>
      <div class="field"><label>เรือที่รองรับ (คั่นด้วย ,)</label><input name="boats" value="<?= htmlspecialchars(implode(', ', $program['boats'] ?? []), ENT_QUOTES, 'UTF-8') ?>" placeholder="longtail, speedboat"/></div>
    </div>
  </div>

  <div class="card">
    <h2>การ์ดหน้าแรก (คะแนน & ราคาเดิม)</h2>
    <div class="grid-2">
      <div class="field"><label>คะแนน (0–5)</label><input name="rating" type="number" step="0.1" min="0" max="5" value="<?= htmlspecialchars((string)($program['rating'] ?? 4.8), ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>จำนวนรีวิว</label><input name="reviewCount" value="<?= htmlspecialchars($program['reviewCount'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ดาว (1–5)</label><input name="stars" type="number" min="1" max="5" value="<?= (int)($program['stars'] ?? 5) ?>"/></div>
      <div class="field"><label>ราคาเดิม (ขีดฆ่า)</label><input name="oldPrice" type="number" value="<?= (int)($program['oldPrice'] ?? 0) ?>" placeholder="0 = ไม่แสดง"/></div>
    </div>
  </div>

  <div class="card">
    <h2>รายละเอียด</h2>
    <div class="grid-2">
      <div class="field" style="grid-column:1/-1"><label>จุดแวะ (บรรทัดละจุด)</label><textarea name="stops"><?= htmlspecialchars(implode("\n", $program['stops'] ?? []), ENT_QUOTES, 'UTF-8') ?></textarea></div>
      <div class="field" style="grid-column:1/-1"><label>รายละเอียด</label><textarea name="desc"><?= htmlspecialchars($program['desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">บันทึก</button>
      <a class="btn btn-ghost" href="programs.php">กลับ</a>
      <?php if (!$isNew): ?>
        <a class="btn btn-ghost" href="../booking.html?program=<?= urlencode($program['id'] ?? '') ?>" target="_blank">ดูบนเว็บ</a>
      <?php endif; ?>
    </div>
  </div>
</form>

<?php tt_admin_footer(); ?>
