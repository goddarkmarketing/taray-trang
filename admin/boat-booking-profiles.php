<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/boat-booking-helpers.php';
require_once __DIR__ . '/includes/boat-booking-layout.php';
tt_require_admin();

$data = tt_read_data();
$bb = tt_bb_get($data);
$profiles = $bb['profiles'] ?? [];
$boatIds = tt_bb_boat_ids($data);

tt_bb_page_start('① โปรไฟล์เรือ', 'profiles');
?>

<div class="card">
  <h2><span class="adm-step-badge adm-step-badge--lg">1</span> โปรไฟล์เรือ</h2>
  <p class="field-hint">ขั้นตอนบนเว็บ: <strong>เลือกประเภทเรือ</strong> — แก้ขนาดเรือ ราคาเหมา ใบเสนอ LINE หลายลำ/แยก ผญ-เด็ก และบริการเสริมเฉพาะเรือ</p>
  <table class="table">
    <thead><tr><th>เรือ</th><th>โหมดบนเว็บ</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($boatIds as $bid):
        $p = $profiles[$bid] ?? [];
        $mode = ($p['selectionMode'] ?? '') === 'size' ? 'เลือกขนาดเรือ' : ($bid === 'longtail' ? 'เลือกช่วงคน + จำนวนจริง' : 'เลือกช่วงคน');
      ?>
      <tr>
        <td><strong><?= htmlspecialchars($bid, ENT_QUOTES, 'UTF-8') ?></strong></td>
        <td><?= htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') ?></td>
        <td class="table-actions">
          <a class="btn btn-primary btn-sm" href="boat-booking-profile-edit.php?boat=<?= urlencode($bid) ?>">แก้ไขโปรไฟล์จอง</a>
          <a class="btn btn-ghost btn-sm" href="boat-edit.php?id=<?= urlencode($bid) ?>">การ์ดเรือ</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="form-actions" style="border:0;padding-top:16px">
    <a class="btn btn-primary" href="boat-booking-routes.php">ถัดไป: เส้นทาง →</a>
  </div>
</div>

<?php tt_admin_footer(); ?>
