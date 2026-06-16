<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/frontend-icons.php';
tt_require_admin();

$iconOptions = ['cutlery', 'users', 'camera', 'scuba', 'carPick', 'chatBubble', 'anchor', 'car', 'bed', 'ticket', 'briefcase', 'compass'];

$data = tt_read_data();
$options = $data['options'] ?? [];
$id = trim($_GET['id'] ?? '');
$option = null;
$idx = -1;

foreach ($options as $i => $o) {
    if (($o['id'] ?? '') === $id) {
        $option = $o;
        $idx = $i;
        break;
    }
}

$isNew = $id === '' || $option === null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newId = trim($_POST['id'] ?? '');
    if ($newId === '') {
        $newId = tt_slugify($_POST['label'] ?? 'option');
    }

    $unit = trim($_POST['unit'] ?? 'flat');
    $price = (int)($_POST['price'] ?? 0);
    $priceLabel = trim($_POST['priceLabel'] ?? '');
    if ($priceLabel === '') {
        $suffix = $unit === 'per_person' ? '/คน' : '/ทริป';
        $priceLabel = '+' . number_format($price) . ' บาท' . $suffix;
    }

    $item = [
        'id' => $newId,
        'icon' => trim($_POST['icon'] ?? 'cutlery'),
        'label' => trim($_POST['label'] ?? ''),
        'desc' => trim($_POST['desc'] ?? ''),
        'price' => $price,
        'unit' => $unit,
        'priceLabel' => $priceLabel,
    ];

    if ($idx >= 0) {
        if ($newId !== $id) {
            foreach ($options as $j => $o) {
                if ($j !== $idx && ($o['id'] ?? '') === $newId) {
                    tt_set_flash('ID นี้มีอยู่แล้ว — ใช้ ID อื่น');
                    header('Location: option-edit.php?id=' . urlencode($id));
                    exit;
                }
            }
        }
        $options[$idx] = $item;
    } else {
        foreach ($options as $o) {
            if (($o['id'] ?? '') === $newId) {
                tt_set_flash('ID นี้มีอยู่แล้ว — ใช้ ID อื่น');
                header('Location: option-edit.php');
                exit;
            }
        }
        $options[] = $item;
    }

    $data['options'] = $options;
    tt_write_data($data);
    tt_set_flash('บันทึกตัวเลือกจองแล้ว');
    header('Location: option-edit.php?id=' . urlencode($newId));
    exit;
}

$pageTitle = $isNew ? 'เพิ่มตัวเลือกจอง' : 'แก้ไขตัวเลือกจอง';
tt_admin_header($pageTitle, 'options');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post">
  <div class="card">
    <h2>ข้อมูลตัวเลือก</h2>
    <div class="grid-2">
      <div class="field"><label>ชื่อที่แสดง</label><input name="label" required value="<?= htmlspecialchars($option['label'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="อาหารเที่ยง"/></div>
      <div class="field"><label>ID (slug)</label><input name="id" value="<?= htmlspecialchars($option['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="lunch, guide, photo"/></div>
      <div class="field"><label>ไอคอน</label>
        <?php tt_icon_select('icon', $iconOptions, $option['icon'] ?? 'cutlery'); ?>
      </div>
      <div class="field"><label>ราคา (บาท)</label><input name="price" type="number" min="0" value="<?= (int)($option['price'] ?? 0) ?>"/></div>
      <div class="field"><label>หน่วยคิดราคา</label>
        <select name="unit">
          <option value="flat"<?= ($option['unit'] ?? 'flat') === 'flat' ? ' selected' : '' ?>>ต่อทริป (flat)</option>
          <option value="per_person"<?= ($option['unit'] ?? '') === 'per_person' ? ' selected' : '' ?>>ต่อคน (per_person)</option>
        </select>
      </div>
      <div class="field"><label>ข้อความราคา</label><input name="priceLabel" value="<?= htmlspecialchars($option['priceLabel'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="+250 บาท/คน (ว่าง = สร้างอัตโนมัติ)"/></div>
      <div class="field" style="grid-column:1/-1"><label>คำอธิบายสั้น</label><input name="desc" value="<?= htmlspecialchars($option['desc'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="เซ็ตอาหารทะเลสด"/></div>
    </div>
    <p class="field-hint">ตัวเลือกนี้จะแสดงในหน้า <strong>booking.html</strong> (จองแบบเก่า) — หน้า <strong>boat-book.html</strong> แก้ที่เมนู <a href="boat-booking-profiles.php">จองเรือเหมาลำ</a></p>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">บันทึก</button>
      <a class="btn btn-ghost" href="options.php">กลับ</a>
      <a class="btn btn-ghost" href="../booking.html" target="_blank">ดูหน้าจอง</a>
    </div>
  </div>
</form>

<?php tt_admin_footer(); ?>
