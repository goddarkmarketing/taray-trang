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
    $bb['insurancePerPerson'] = (int) ($_POST['insurancePerPerson'] ?? 50);
    tt_bb_save($bb);
    tt_set_flash('บันทึกประกันแล้ว');
    header('Location: boat-booking-insurance.php');
    exit;
}

$data = tt_read_data();
$bb = tt_bb_get($data);

tt_bb_page_start('⑤ ประกัน & ค่าบังคับ', 'insurance');
?>

<div class="card">
  <h2><span class="adm-step-badge adm-step-badge--lg">5</span> ประกัน &amp; ค่าบังคับ</h2>
  <p class="field-hint">ขั้นตอนบนเว็บ: <strong>ค่าประกันอุบัติเหตุ</strong> — ค่าเริ่มต้นทุกเรือ (โปรไฟล์แต่ละเรือ override ได้) · มัคคุเทศก์/สต๊าฟแก้ใน <a href="boat-booking-profiles.php">โปรไฟล์เรือ</a></p>
  <form method="post">
    <div class="grid-2">
      <div class="field">
        <label>ประกันอุบัติเหตุ (บาท/ท่าน)</label>
        <input name="insurancePerPerson" type="number" min="0" value="<?= (int) ($bb['insurancePerPerson'] ?? 50) ?>"/>
        <p class="field-hint">คำนวณ × จำนวนผู้โดยสารจริง</p>
      </div>
    </div>
    <?php tt_bb_form_actions('บันทึกประกัน'); ?>
  </form>
</div>

<?php tt_admin_footer(); ?>
