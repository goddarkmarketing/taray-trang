<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$boats = $data['boats'] ?? [];

if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $id = $_GET['delete'];
    $boats = array_values(array_filter($boats, fn($b) => ($b['id'] ?? '') !== $id));
    $data['boats'] = $boats;
    tt_write_data($data);
    tt_set_flash('ลบประเภทเรือแล้ว');
    header('Location: boats.php');
    exit;
}

tt_admin_header('ประเภทเรือ', 'boats');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="form-actions" style="border:0;margin-bottom:16px;padding:0">
  <a class="btn btn-primary" href="boat-edit.php">+ เพิ่มประเภทเรือ</a>
</div>

<div class="card">
  <?php if (!$boats): ?>
    <p class="field-hint">ยังไม่มีประเภทเรือ — กดปุ่มด้านบนเพื่อเพิ่มรายการแรก</p>
  <?php else: ?>
  <table class="table">
    <thead>
      <tr><th>ชื่อเรือ</th><th>Tag</th><th>ความจุ</th><th>ราคาเริ่มต้น</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($boats as $b): ?>
        <tr>
          <td>
            <strong><?= htmlspecialchars($b['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
            <small class="field-hint"><?= htmlspecialchars($b['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
          </td>
          <td><?= htmlspecialchars($b['tag'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($b['capacity'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= number_format((int)($b['basePrice'] ?? 0)) ?> บาท</td>
          <td class="table-actions">
            <div class="table-actions-row">
              <a class="btn btn-ghost btn-sm" href="boat-edit.php?id=<?= urlencode($b['id'] ?? '') ?>">แก้ไข</a>
              <a class="btn btn-danger btn-sm" href="boats.php?delete=<?= urlencode($b['id'] ?? '') ?>" onclick="return confirm('ลบประเภทเรือนี้?')">ลบ</a>
              <a class="btn btn-ghost btn-sm" href="../boats.html#<?= urlencode($b['id'] ?? '') ?>" target="_blank">ดู</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php tt_admin_footer(); ?>
