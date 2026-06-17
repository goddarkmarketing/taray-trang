<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/frontend-icons.php';
require_once __DIR__ . '/includes/boat-booking-helpers.php';
require_once __DIR__ . '/includes/boat-booking-layout.php';
tt_require_admin();

$iconOptions = tt_bb_icon_options();

$data = tt_read_data();
$bb = tt_bb_get($data);
$addons = $bb['addons'] ?? [];
$addonId = trim($_GET['id'] ?? $_POST['original_id'] ?? '');
$idx = $addonId !== '' ? tt_bb_find_addon_index($addons, $addonId) : -1;
$addon = $idx >= 0 ? $addons[$idx] : null;
$isNew = $addon === null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = tt_read_data();
    $bb = tt_bb_get($data);
    $addons = $bb['addons'] ?? [];
    $originalId = trim($_POST['original_id'] ?? '');
    $idx = $originalId !== '' ? tt_bb_find_addon_index($addons, $originalId) : -1;

    $row = [
        'id' => trim($_POST['id'] ?? ''),
        'icon' => trim($_POST['icon'] ?? 'plus'),
        'label' => trim($_POST['label'] ?? ''),
        'desc' => trim($_POST['desc'] ?? ''),
        'price' => $_POST['price'] ?? 0,
        'unit' => $_POST['unit'] ?? 'flat',
        'priceLabel' => trim($_POST['priceLabel'] ?? ''),
        'qtyLabel' => trim($_POST['qtyLabel'] ?? ''),
    ];
    if ($row['id'] === '' && $row['label'] !== '') {
        $row['id'] = 'menu-' . tt_slugify($row['label']);
    }

    $item = tt_bb_parse_addon_post($row);
    if ($item === null) {
        tt_set_flash('กรุณากรอก ID และชื่อที่แสดง', 'error');
        $redirectId = $originalId !== '' ? '?id=' . urlencode($originalId) : '';
        header('Location: boat-booking-addon-edit.php' . $redirectId);
        exit;
    }

    $newId = (string) $item['id'];
    if ($idx >= 0) {
        if ($newId !== $originalId) {
            $addons = array_values(array_filter($addons, static fn($a) => ($a['id'] ?? '') !== $originalId));
            if (tt_bb_find_addon_index($addons, $newId) >= 0) {
                tt_set_flash('มี ID นี้อยู่แล้ว — ใช้ชื่ออื่น', 'error');
                header('Location: boat-booking-addon-edit.php?id=' . urlencode($originalId));
                exit;
            }
            $addons[] = $item;
        } else {
            $addons[$idx] = $item;
        }
    } else {
        if (tt_bb_find_addon_index($addons, $newId) >= 0) {
            tt_set_flash('มี ID นี้อยู่แล้ว', 'error');
            header('Location: boat-booking-addon-edit.php');
            exit;
        }
        $addons[] = $item;
    }

    $bb['addons'] = array_values($addons);
    tt_bb_save($bb);
    tt_set_flash('บันทึกบริการเสริมแล้ว');
    header('Location: boat-booking-addon-edit.php?id=' . urlencode($newId));
    exit;
}

$addon = $addon ?? [
    'id' => '',
    'icon' => 'plus',
    'label' => '',
    'desc' => '',
    'price' => 0,
    'unit' => 'flat',
    'priceLabel' => '',
];

$pageTitle = $isNew ? 'เพิ่มบริการเสริม' : 'แก้ไขบริการเสริม';
tt_bb_page_start($pageTitle, 'addons');
?>

<div class="card admin-preview-bar adm-bb-profile-bar">
  <a class="btn btn-ghost btn-sm" href="boat-booking-addons.php">← รายการบริการเสริม</a>
  <a class="btn btn-ghost btn-sm" href="../boat-book.html" target="_blank" rel="noopener">ทดสอบหน้าจอง ↗</a>
</div>

<form method="post">
  <input type="hidden" name="original_id" value="<?= htmlspecialchars($isNew ? '' : (string) ($addon['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"/>

  <div class="card">
    <h2>ข้อมูลบริการเสริม</h2>
    <div class="grid-2">
      <div class="field">
        <label>ชื่อที่แสดง</label>
        <input name="label" required value="<?= htmlspecialchars((string) ($addon['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="อาหารกลางวัน"/>
      </div>
      <div class="field">
        <label>ID</label>
        <input name="id" value="<?= htmlspecialchars((string) ($addon['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="menu-food"/>
        <p class="field-hint">ว่างไว้จะสร้างจากชื่ออัตโนมัติ เช่น menu-food</p>
      </div>
      <div class="field">
        <label>ไอคอน</label>
        <?php tt_icon_select('icon', $iconOptions, (string) ($addon['icon'] ?? 'plus')); ?>
      </div>
      <div class="field">
        <label>ราคา (บาท)</label>
        <input name="price" type="number" min="0" value="<?= (int) ($addon['price'] ?? 0) ?>"/>
      </div>
      <div class="field">
        <label>หน่วย</label>
        <?php tt_bb_render_addon_unit_select('unit', (string) ($addon['unit'] ?? 'flat')); ?>
      </div>
      <div class="field">
        <label>ข้อความราคา</label>
        <input name="priceLabel" value="<?= htmlspecialchars((string) ($addon['priceLabel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="100 บาท/ท่าน"/>
        <p class="field-hint">แสดงบนการ์ดจอง — ว่างได้ ระบบจะใช้ตัวเลขราคา</p>
      </div>
      <div class="field">
        <label>ป้ายจำนวน (perQty/perVan)</label>
        <input name="qtyLabel" value="<?= htmlspecialchars((string) ($addon['qtyLabel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="คน / ชุด"/>
      </div>
      <div class="field" style="grid-column:1/-1">
        <label>คำอธิบาย</label>
        <input name="desc" value="<?= htmlspecialchars((string) ($addon['desc'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="อาหารกลางวัน 1 ชุด/ท่าน"/>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">บันทึกบริการเสริม</button>
    <a class="btn btn-ghost" href="boat-booking-addons.php">กลับ</a>
  </div>
</form>

<?php tt_admin_footer(); ?>
