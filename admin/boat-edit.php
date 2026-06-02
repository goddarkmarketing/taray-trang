<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/image-field.php';
tt_require_admin();

$data = tt_read_data();
$boats = $data['boats'] ?? [];
$id = trim($_GET['id'] ?? '');
$boat = null;
$idx = -1;

foreach ($boats as $i => $b) {
    if (($b['id'] ?? '') === $id) {
        $boat = $b;
        $idx = $i;
        break;
    }
}

$isNew = $id === '' || $boat === null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newId = trim($_POST['id'] ?? '');
    if ($newId === '') {
        $newId = tt_slugify($_POST['name'] ?? 'boat');
    }
    $highlights = array_values(array_filter(array_map('trim', explode("\n", $_POST['highlights'] ?? ''))));

    $item = [
        'id' => $newId,
        'name' => trim($_POST['name'] ?? ''),
        'tag' => trim($_POST['tag'] ?? ''),
        'image' => trim($_POST['image'] ?? ''),
        'capacity' => trim($_POST['capacity'] ?? ''),
        'capacityMax' => (int)($_POST['capacityMax'] ?? 0),
        'basePrice' => (int)($_POST['basePrice'] ?? 0),
        'short' => trim($_POST['short'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'suitable' => trim($_POST['suitable'] ?? ''),
        'highlights' => $highlights,
        'rating' => (float)($_POST['rating'] ?? 4.8),
        'reviewCount' => trim($_POST['reviewCount'] ?? ''),
        'stars' => (int)($_POST['stars'] ?? 5),
        'badgeTL' => trim($_POST['badgeTL'] ?? ''),
        'badgeTR' => trim($_POST['badgeTR'] ?? ''),
        'badgeTRClass' => trim($_POST['badgeTRClass'] ?? 'is-info'),
    ];

    if ($idx >= 0) {
        if ($newId !== $id) {
            foreach ($boats as $j => $b) {
                if ($j !== $idx && ($b['id'] ?? '') === $newId) {
                    tt_set_flash('ID นี้มีอยู่แล้ว — ใช้ ID อื่น');
                    header('Location: boat-edit.php?id=' . urlencode($id));
                    exit;
                }
            }
        }
        $boats[$idx] = $item;
    } else {
        foreach ($boats as $b) {
            if (($b['id'] ?? '') === $newId) {
                tt_set_flash('ID นี้มีอยู่แล้ว — ใช้ ID อื่น');
                header('Location: boat-edit.php?id=' . urlencode($newId));
                exit;
            }
        }
        $boats[] = $item;
    }

    $data['boats'] = $boats;
    tt_write_data($data);
    tt_set_flash('บันทึกประเภทเรือแล้ว');
    header('Location: boat-edit.php?id=' . urlencode($newId));
    exit;
}

$pageTitle = $isNew ? 'เพิ่มประเภทเรือ' : 'แก้ไขประเภทเรือ';
tt_admin_header($pageTitle, 'boats');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post">
  <div class="card">
    <h2>ข้อมูลหลัก</h2>
    <div class="grid-2">
      <div class="field"><label>ชื่อเรือ</label><input name="name" required value="<?= htmlspecialchars($boat['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ID (slug)</label><input name="id" value="<?= htmlspecialchars($boat['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="longtail, speedboat, bigboat"/></div>
      <div class="field"><label>Tag</label><input name="tag" value="<?= htmlspecialchars($boat['tag'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <?php tt_render_image_url_field('URL รูป', 'image', $boat['image'] ?? '', ['context' => 'boat']); ?>
      <div class="field"><label>ความจุ (ข้อความ)</label><input name="capacity" value="<?= htmlspecialchars($boat['capacity'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>จำนวนสูงสุด (ตัวเลข)</label><input name="capacityMax" type="number" value="<?= (int)($boat['capacityMax'] ?? 10) ?>"/></div>
      <div class="field"><label>ราคาเริ่มต้น (บาท)</label><input name="basePrice" type="number" value="<?= (int)($boat['basePrice'] ?? 0) ?>"/></div>
    </div>
  </div>

  <div class="card">
    <h2>การ์ดหน้าแรก (คะแนน & ป้าย)</h2>
    <div class="grid-2">
      <div class="field"><label>คะแนน (0–5)</label><input name="rating" type="number" step="0.1" min="0" max="5" value="<?= htmlspecialchars((string)($boat['rating'] ?? 4.8), ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>จำนวนรีวิว</label><input name="reviewCount" value="<?= htmlspecialchars($boat['reviewCount'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="2.1พัน"/></div>
      <div class="field"><label>ดาว (1–5)</label><input name="stars" type="number" min="1" max="5" value="<?= (int)($boat['stars'] ?? 5) ?>"/></div>
      <div class="field"><label>ป้ายซ้าย (badge TL)</label><input name="badgeTL" value="<?= htmlspecialchars($boat['badgeTL'] ?? 'กันตัง · ตรัง', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ป้ายขวา (badge TR)</label><input name="badgeTR" value="<?= htmlspecialchars($boat['badgeTR'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>Class ป้ายขวา</label><input name="badgeTRClass" value="<?= htmlspecialchars($boat['badgeTRClass'] ?? 'is-info', ENT_QUOTES, 'UTF-8') ?>" placeholder="is-hot / is-info"/></div>
    </div>
  </div>

  <div class="card">
    <h2>รายละเอียด</h2>
    <div class="grid-2">
      <div class="field" style="grid-column:1/-1"><label>คำอธิบายสั้น</label><input name="short" value="<?= htmlspecialchars($boat['short'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>รายละเอียด</label><textarea name="description"><?= htmlspecialchars($boat['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
      <div class="field" style="grid-column:1/-1"><label>เหมาะกับ</label><input name="suitable" value="<?= htmlspecialchars($boat['suitable'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>ไฮไลต์ (บรรทัดละ 1 ข้อ)</label><textarea name="highlights"><?= htmlspecialchars(implode("\n", $boat['highlights'] ?? []), ENT_QUOTES, 'UTF-8') ?></textarea></div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">บันทึก</button>
      <a class="btn btn-ghost" href="boats.php">กลับ</a>
      <?php if (!$isNew): ?>
        <a class="btn btn-ghost" href="../boats.html#<?= urlencode($boat['id'] ?? '') ?>" target="_blank">ดูบนเว็บ</a>
      <?php endif; ?>
    </div>
  </div>
</form>

<?php tt_admin_footer(); ?>
