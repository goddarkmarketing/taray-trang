<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$items = $data['whyUs'] ?? [];

if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $idx = (int)$_GET['delete'];
    if (isset($items[$idx])) {
        array_splice($items, $idx, 1);
        $data['whyUs'] = array_values($items);
        tt_write_data($data);
        tt_set_flash('ลบจุดเด่นแล้ว');
    }
    header('Location: why.php');
    exit;
}

function tt_why_excerpt(string $text, int $len = 50): string
{
    $text = trim($text);
    if (mb_strlen($text) <= $len) {
        return $text;
    }
    return mb_substr($text, 0, $len) . '…';
}

tt_admin_header('จุดเด่นบริการ', 'why');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="form-actions" style="border:0;margin-bottom:16px;padding:0">
  <a class="btn btn-primary" href="why-edit.php">+ เพิ่มจุดเด่น</a>
</div>

<div class="card">
  <?php if (!$items): ?>
    <p class="field-hint">ยังไม่มีจุดเด่น — กดปุ่มด้านบนเพื่อเพิ่มรายการแรก</p>
  <?php else: ?>
  <table class="table">
    <thead>
      <tr><th>หัวข้อ</th><th>ไอคอน</th><th>คำอธิบาย</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($items as $i => $w): ?>
        <tr>
          <td><strong><?= htmlspecialchars($w['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></td>
          <td><?= htmlspecialchars($w['icon'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars(tt_why_excerpt($w['desc'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
          <td class="table-actions">
            <div class="table-actions-row">
              <a class="btn btn-ghost btn-sm" href="why-edit.php?i=<?= $i ?>">แก้ไข</a>
              <a class="btn btn-danger btn-sm" href="why.php?delete=<?= $i ?>" onclick="return confirm('ลบจุดเด่นนี้?')">ลบ</a>
              <a class="btn btn-ghost btn-sm" href="../about.html" target="_blank">ดู</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php tt_admin_footer(); ?>
