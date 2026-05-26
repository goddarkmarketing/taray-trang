<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$items = $data['navItems'] ?? [];
$idx = isset($_GET['i']) ? (int)$_GET['i'] : -1;
$item = ($idx >= 0 && isset($items[$idx])) ? $items[$idx] : null;
$isNew = $item === null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postIdx = isset($_POST['idx']) ? (int)$_POST['idx'] : -1;
    $label = trim($_POST['label'] ?? '');
    $href = trim($_POST['href'] ?? '');

    if ($label === '') {
        tt_set_flash('กรุณาระบุชื่อเมนู');
        header('Location: nav-edit.php' . ($postIdx >= 0 ? '?i=' . $postIdx : ''));
        exit;
    }

    $entry = ['label' => $label, 'href' => $href];

    if ($postIdx >= 0 && isset($items[$postIdx])) {
        $items[$postIdx] = $entry;
    } else {
        $items[] = $entry;
        $postIdx = count($items) - 1;
    }

    $data['navItems'] = $items;
    tt_write_data($data);
    tt_set_flash('บันทึกเมนูแล้ว');
    header('Location: nav-edit.php?i=' . $postIdx);
    exit;
}

$pageTitle = $isNew ? 'เพิ่มเมนูนำทาง' : 'แก้ไขเมนูนำทาง';
tt_admin_header($pageTitle, 'nav');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post">
  <div class="card">
    <h2>รายการเมนู</h2>
    <p class="field-hint">ลำดับในเว็บเรียงตามลำดับในหน้ารายการ — ใช้ลิงก์ในเว็บ เช่น <code>boats.html</code> หรือ URL ภายนอก</p>

    <input type="hidden" name="idx" value="<?= $isNew ? -1 : $idx ?>"/>

    <div class="grid-2">
      <div class="field"><label>ชื่อเมนู</label><input name="label" required value="<?= htmlspecialchars($item['label'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ประเภทเรือ"/></div>
      <div class="field"><label>ลิงก์</label><input name="href" value="<?= htmlspecialchars($item['href'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="boats.html"/></div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">บันทึก</button>
      <a class="btn btn-ghost" href="nav.php">กลับ</a>
      <?php if (!$isNew && !empty($item['href'])): ?>
        <a class="btn btn-ghost" href="../<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">ดูหน้าเว็บ</a>
      <?php endif; ?>
      <?php if (!$isNew): ?>
        <a class="btn btn-danger" href="nav.php?delete=<?= $idx ?>" onclick="return confirm('ลบเมนูนี้?')">ลบเมนูนี้</a>
      <?php endif; ?>
    </div>
  </div>
</form>

<?php tt_admin_footer(); ?>
