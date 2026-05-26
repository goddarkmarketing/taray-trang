<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$programs = $data['programs'] ?? [];

if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $id = $_GET['delete'];
    $programs = array_values(array_filter($programs, fn($p) => ($p['id'] ?? '') !== $id));
    $data['programs'] = $programs;
    tt_write_data($data);
    tt_set_flash('ลบโปรแกรมแล้ว');
    header('Location: programs.php');
    exit;
}

tt_admin_header('โปรแกรมทัวร์', 'programs');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="form-actions" style="border:0;margin-bottom:16px;padding:0">
  <a class="btn btn-primary" href="program-edit.php">+ เพิ่มโปรแกรม</a>
</div>

<div class="card">
  <?php if (!$programs): ?>
    <p class="field-hint">ยังไม่มีโปรแกรม — กดปุ่มด้านบนเพื่อเพิ่มรายการแรก</p>
  <?php else: ?>
  <table class="table">
    <thead>
      <tr><th>ชื่อโปรแกรม</th><th>เส้นทาง</th><th>ระยะเวลา</th><th>ราคาเริ่มต้น</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($programs as $p): ?>
        <tr>
          <td>
            <strong><?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
            <small class="field-hint"><?= htmlspecialchars($p['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
            <?php if (!empty($p['ribbon'])): ?>
              <br><small class="field-hint"><?= htmlspecialchars($p['ribbon'], ENT_QUOTES, 'UTF-8') ?></small>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($p['route'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($p['duration'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= number_format((int)($p['basePrice'] ?? 0)) ?> บาท</td>
          <td class="table-actions">
            <div class="table-actions-row">
              <a class="btn btn-ghost btn-sm" href="program-edit.php?id=<?= urlencode($p['id'] ?? '') ?>">แก้ไข</a>
              <a class="btn btn-danger btn-sm" href="programs.php?delete=<?= urlencode($p['id'] ?? '') ?>" onclick="return confirm('ลบโปรแกรมนี้?')">ลบ</a>
              <a class="btn btn-ghost btn-sm" href="../booking.html?program=<?= urlencode($p['id'] ?? '') ?>" target="_blank">ดู</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php tt_admin_footer(); ?>
