<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$options = $data['options'] ?? [];

if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $id = $_GET['delete'];
    $options = array_values(array_filter($options, fn($o) => ($o['id'] ?? '') !== $id));
    $data['options'] = $options;
    tt_write_data($data);
    tt_set_flash('ลบตัวเลือกจองแล้ว');
    header('Location: options.php');
    exit;
}

function tt_option_unit_label(string $unit): string
{
    return match ($unit) {
        'per_person' => 'ต่อคน',
        'flat' => 'ต่อทริป',
        default => $unit,
    };
}

tt_admin_header('ตัวเลือกจอง', 'options');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="form-actions" style="border:0;margin-bottom:16px;padding:0">
  <a class="btn btn-primary" href="option-edit.php">+ เพิ่มตัวเลือก</a>
</div>

<div class="card">
  <?php if (!$options): ?>
    <p class="field-hint">ยังไม่มีตัวเลือกจอง — กดปุ่มด้านบนเพื่อเพิ่มรายการแรก</p>
  <?php else: ?>
  <table class="table">
    <thead>
      <tr><th>ชื่อตัวเลือก</th><th>คำอธิบาย</th><th>ราคา</th><th>หน่วย</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($options as $o): ?>
        <tr>
          <td>
            <strong><?= htmlspecialchars($o['label'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
            <small class="field-hint"><?= htmlspecialchars($o['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
          </td>
          <td><?= htmlspecialchars($o['desc'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($o['priceLabel'] ?? (number_format((int)($o['price'] ?? 0)) . ' บาท'), ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars(tt_option_unit_label($o['unit'] ?? 'flat'), ENT_QUOTES, 'UTF-8') ?></td>
          <td class="table-actions">
            <div class="table-actions-row">
              <a class="btn btn-ghost btn-sm" href="option-edit.php?id=<?= urlencode($o['id'] ?? '') ?>">แก้ไข</a>
              <a class="btn btn-danger btn-sm" href="options.php?delete=<?= urlencode($o['id'] ?? '') ?>" onclick="return confirm('ลบตัวเลือกนี้?')">ลบ</a>
              <a class="btn btn-ghost btn-sm" href="../booking.html" target="_blank">ดู</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php tt_admin_footer(); ?>
