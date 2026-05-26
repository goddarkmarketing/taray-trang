<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/frontend-icons.php';
tt_require_admin();

$iconOptions = ['anchor', 'route', 'calculator', 'line', 'chat', 'users', 'compass', 'shield'];

$data = tt_read_data();
$steps = $data['steps'] ?? [];
$idx = isset($_GET['i']) ? (int)$_GET['i'] : -1;
$step = ($idx >= 0 && isset($steps[$idx])) ? $steps[$idx] : null;
$isNew = $step === null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postIdx = isset($_POST['idx']) ? (int)$_POST['idx'] : -1;
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        tt_set_flash('กรุณาระบุหัวข้อ');
        header('Location: step-edit.php' . ($postIdx >= 0 ? '?i=' . $postIdx : ''));
        exit;
    }

    $n = (int)($_POST['n'] ?? 0);
    if ($n <= 0) {
        $n = $postIdx >= 0 ? ($steps[$postIdx]['n'] ?? $postIdx + 1) : count($steps) + 1;
    }

    $entry = [
        'n' => $n,
        'icon' => trim($_POST['icon'] ?? 'anchor'),
        'title' => $title,
        'desc' => trim($_POST['desc'] ?? ''),
    ];

    if ($postIdx >= 0 && isset($steps[$postIdx])) {
        $steps[$postIdx] = $entry;
    } else {
        $steps[] = $entry;
        $postIdx = count($steps) - 1;
    }

    $data['steps'] = $steps;
    tt_write_data($data);
    tt_set_flash('บันทึกขั้นตอนแล้ว');
    header('Location: step-edit.php?i=' . $postIdx);
    exit;
}

$pageTitle = $isNew ? 'เพิ่มขั้นตอนจอง' : 'แก้ไขขั้นตอนจอง';
tt_admin_header($pageTitle, 'steps');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post">
  <div class="card">
    <h2>ขั้นตอนการจอง</h2>
    <input type="hidden" name="idx" value="<?= $isNew ? -1 : $idx ?>"/>

    <div class="grid-2">
      <div class="field"><label>ลำดับ (ตัวเลข)</label><input name="n" type="number" min="1" value="<?= (int)($step['n'] ?? ($isNew ? count($steps) + 1 : $idx + 1)) ?>"/></div>
      <div class="field"><label>ไอคอน</label>
        <?php tt_icon_select('icon', $iconOptions, $step['icon'] ?? 'anchor'); ?>
      </div>
      <div class="field" style="grid-column:1/-1"><label>หัวข้อ</label><input name="title" required value="<?= htmlspecialchars($step['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>คำอธิบาย</label><textarea name="desc"><?= htmlspecialchars($step['desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
    </div>

    <p class="field-hint">แสดงบนหน้าแรกในส่วนขั้นตอนการจอง</p>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">บันทึก</button>
      <a class="btn btn-ghost" href="steps.php">กลับ</a>
      <a class="btn btn-ghost" href="../index.html#home-steps" target="_blank">ดูหน้าแรก</a>
      <?php if (!$isNew): ?>
        <a class="btn btn-danger" href="steps.php?delete=<?= $idx ?>" onclick="return confirm('ลบขั้นตอนนี้?')">ลบขั้นตอนนี้</a>
      <?php endif; ?>
    </div>
  </div>
</form>

<?php tt_admin_footer(); ?>
