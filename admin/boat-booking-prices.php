<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/boat-booking-helpers.php';
require_once __DIR__ . '/includes/boat-booking-layout.php';
tt_require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = tt_read_data();
    $bb = tt_bb_get($data);
    $bb['charterPrices'] = $bb['charterPrices'] ?? [];
    $bb['charterPrices']['longtail'] = tt_bb_parse_charter_matrix_post($_POST['charter_longtail'] ?? []);
    tt_bb_save($bb);
    tt_set_flash('บันทึกราคาเรือหางยาวแล้ว');
    header('Location: boat-booking-prices.php');
    exit;
}

$data = tt_read_data();
$bb = tt_bb_get($data);
$routes = $bb['routes'] ?? [];
$peopleTiers = $bb['peopleTiers'] ?? [];
$charterLongtail = $bb['charterPrices']['longtail'] ?? [];

tt_bb_page_start('④ ราคาเหมาเรือหางยาว', 'prices');
?>

<div class="card">
  <h2><span class="adm-step-badge adm-step-badge--lg">4</span> ราคาเหมาเรือหางยาว</h2>
  <p class="field-hint">แสดงในขั้นเลือกจำนวนคน — ตาราง <strong>เส้นทาง × ช่วงคน</strong> · ราคาสปีดโบ๊ท/เรือทัวร์แก้ใน <a href="boat-booking-profiles.php">โปรไฟล์เรือ</a></p>
  <?php if (!$routes || !$peopleTiers): ?>
    <p class="field-hint">กรุณาตั้งค่า <a href="boat-booking-routes.php">เส้นทาง</a> และ <a href="boat-booking-tiers.php">ช่วงจำนวนคน</a> ก่อน</p>
  <?php else: ?>
  <form method="post">
    <div class="table-wrap">
      <table class="table table-form adm-charter-matrix">
        <thead>
          <tr>
            <th>เส้นทาง</th>
            <?php foreach ($peopleTiers as $t): ?>
              <th><?= htmlspecialchars((string) ($t['label'] ?? $t['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($routes as $r):
            $rid = (string) ($r['id'] ?? '');
          ?>
          <tr>
            <td><strong><?= htmlspecialchars((string) ($r['name'] ?? $rid), ENT_QUOTES, 'UTF-8') ?></strong><br><small><?= htmlspecialchars($rid, ENT_QUOTES, 'UTF-8') ?></small></td>
            <?php foreach ($peopleTiers as $t):
              $tid = (string) ($t['id'] ?? '');
              $val = $charterLongtail[$rid][$tid] ?? '';
            ?>
            <td><input name="charter_longtail[<?= htmlspecialchars($rid, ENT_QUOTES, 'UTF-8') ?>][<?= htmlspecialchars($tid, ENT_QUOTES, 'UTF-8') ?>]" type="number" min="0" value="<?= $val !== '' ? (int) $val : '' ?>" placeholder="—"/></td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php tt_bb_form_actions('บันทึกราคาเรือหางยาว'); ?>
  </form>
  <?php endif; ?>
</div>

<?php tt_admin_footer(); ?>
