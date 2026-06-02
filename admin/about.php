<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/image-sizes.php';
tt_require_admin();

$data = tt_read_data();
$about = $data['about'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paragraphs = array_values(array_filter(array_map('trim', explode("\n", $_POST['story_paragraphs'] ?? ''))));

    $trust = [];
    $trustValues = $_POST['trust_value'] ?? [];
    $trustLabels = $_POST['trust_label'] ?? [];
    foreach ($trustValues as $i => $val) {
        $val = trim($val);
        $label = trim($trustLabels[$i] ?? '');
        if ($val === '' && $label === '') continue;
        $trust[] = ['value' => $val, 'label' => $label];
    }

    $about = [
        'hero' => [
            'crumb' => trim($_POST['hero_crumb'] ?? ''),
            'title' => trim($_POST['hero_title'] ?? ''),
            'lead' => trim($_POST['hero_lead'] ?? ''),
            'heroAlt' => trim($_POST['hero_alt'] ?? ''),
        ],
        'story' => [
            'eyebrow' => trim($_POST['story_eyebrow'] ?? ''),
            'title' => trim($_POST['story_title'] ?? ''),
            'paragraphs' => $paragraphs,
            'ctaPrimary' => [
                'label' => trim($_POST['story_cta1_label'] ?? ''),
                'href' => trim($_POST['story_cta1_href'] ?? ''),
            ],
            'ctaSecondary' => [
                'label' => trim($_POST['story_cta2_label'] ?? ''),
                'href' => trim($_POST['story_cta2_href'] ?? ''),
            ],
        ],
        'whySection' => [
            'eyebrow' => trim($_POST['why_eyebrow'] ?? ''),
            'title' => trim($_POST['why_title'] ?? ''),
        ],
        'trust' => $trust,
        'office' => [
            'eyebrow' => trim($_POST['office_eyebrow'] ?? ''),
            'title' => trim($_POST['office_title'] ?? ''),
            'infoTitle' => trim($_POST['office_info_title'] ?? ''),
            'infoText' => trim($_POST['office_info_text'] ?? ''),
        ],
        'cta' => [
            'title' => trim($_POST['cta_title'] ?? ''),
            'lead' => trim($_POST['cta_lead'] ?? ''),
            'lineLabel' => trim($_POST['cta_line_label'] ?? ''),
            'bookingLabel' => trim($_POST['cta_booking_label'] ?? ''),
        ],
    ];

    $data['about'] = $about;
    tt_write_data($data);
    tt_set_flash('บันทึกหน้าเกี่ยวกับเราแล้ว');
    header('Location: about.php');
    exit;
}

$hero = $about['hero'] ?? [];
$story = $about['story'] ?? [];
$whySection = $about['whySection'] ?? [];
$trust = $about['trust'] ?? [];
$office = $about['office'] ?? [];
$cta = $about['cta'] ?? [];
$ctaPrimary = $story['ctaPrimary'] ?? [];
$ctaSecondary = $story['ctaSecondary'] ?? [];

tt_admin_header('หน้าเกี่ยวกับเรา', 'about');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post">
  <div class="card">
    <h2>Hero</h2>
    <div class="grid-2">
      <div class="field"><label>Crumb</label><input name="hero_crumb" value="<?= htmlspecialchars($hero['crumb'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>Alt รูป Hero</label><input name="hero_alt" value="<?= htmlspecialchars($hero['heroAlt'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>หัวข้อหลัก</label><input name="hero_title" value="<?= htmlspecialchars($hero['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>Lead</label><textarea name="hero_lead"><?= htmlspecialchars($hero['lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
    </div>
    <p class="field-hint">รูป Hero แก้ที่เมนู <a href="images.php">รูปภาพ (Registry)</a> → key <code>heroAbout</code> (<?= htmlspecialchars(tt_image_size_hint('heroAbout'), ENT_QUOTES, 'UTF-8') ?>)</p>
  </div>

  <div class="card">
    <h2>เรื่องราว</h2>
    <div class="grid-2">
      <div class="field"><label>Eyebrow</label><input name="story_eyebrow" value="<?= htmlspecialchars($story['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>หัวข้อ</label><input name="story_title" value="<?= htmlspecialchars($story['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>ย่อหน้า (บรรทัดละ 1 ย่อหน้า)</label><textarea name="story_paragraphs" rows="5"><?= htmlspecialchars(implode("\n", $story['paragraphs'] ?? []), ENT_QUOTES, 'UTF-8') ?></textarea></div>
      <div class="field"><label>ปุ่มหลัก — ข้อความ</label><input name="story_cta1_label" value="<?= htmlspecialchars($ctaPrimary['label'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ปุ่มหลัก — ลิงก์</label><input name="story_cta1_href" value="<?= htmlspecialchars($ctaPrimary['href'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ปุ่มรอง — ข้อความ</label><input name="story_cta2_label" value="<?= htmlspecialchars($ctaSecondary['label'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ปุ่มรอง — ลิงก์</label><input name="story_cta2_href" value="<?= htmlspecialchars($ctaSecondary['href'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    </div>
    <p class="field-hint">รูป 3 รูปใน story grid แก้ที่ <code>about1</code> (<?= htmlspecialchars(tt_image_size_hint('about1'), ENT_QUOTES, 'UTF-8') ?>), <code>about2</code> / <code>about3</code> (<?= htmlspecialchars(tt_image_size_hint('about2'), ENT_QUOTES, 'UTF-8') ?>) ในเมนู <a href="images.php">รูปภาพ</a></p>
  </div>

  <div class="card">
    <h2>ส่วน Why us (หัวข้อ)</h2>
    <p class="field-hint">รายการจุดเด่นแก้ที่เมนู <a href="why.php">จุดเด่น</a></p>
    <div class="grid-2">
      <div class="field"><label>Eyebrow</label><input name="why_eyebrow" value="<?= htmlspecialchars($whySection['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>หัวข้อ</label><input name="why_title" value="<?= htmlspecialchars($whySection['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    </div>
  </div>

  <div class="card">
    <h2>Trust strip (ตัวเลขความน่าเชื่อถือ)</h2>
    <?php
    $trustRows = $trust ?: [['value' => '', 'label' => '']];
    foreach ($trustRows as $i => $t): ?>
      <div class="grid-2 item-card" style="margin-bottom:12px">
        <div class="field"><label>ตัวเลข #<?= $i + 1 ?></label><input name="trust_value[]" value="<?= htmlspecialchars($t['value'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
        <div class="field"><label>คำอธิบาย</label><input name="trust_label[]" value="<?= htmlspecialchars($t['label'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      </div>
    <?php endforeach; ?>
    <p class="field-hint">เพิ่มแถวได้โดยกรอกช่องว่างแล้วบันทึก — ลบแถวโดยลบข้อความทั้งสองช่อง</p>
  </div>

  <div class="card">
    <h2>ออฟฟิศ / แผนที่</h2>
    <p class="field-hint">ที่อยู่ เบอร์โทร LINE และแผนที่ ดึงจาก <a href="site.php">ข้อมูลเว็บ</a> อัตโนมัติ</p>
    <div class="grid-2">
      <div class="field"><label>Eyebrow</label><input name="office_eyebrow" value="<?= htmlspecialchars($office['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>หัวข้อ</label><input name="office_title" value="<?= htmlspecialchars($office['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>หัวข้อ info</label><input name="office_info_title" value="<?= htmlspecialchars($office['infoTitle'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>ข้อความ info</label><textarea name="office_info_text"><?= htmlspecialchars($office['infoText'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
    </div>
  </div>

  <div class="card">
    <h2>CTA ท้ายหน้า</h2>
    <div class="grid-2">
      <div class="field" style="grid-column:1/-1"><label>หัวข้อ</label><input name="cta_title" value="<?= htmlspecialchars($cta['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>Lead</label><textarea name="cta_lead"><?= htmlspecialchars($cta['lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
      <div class="field"><label>ปุ่ม LINE</label><input name="cta_line_label" value="<?= htmlspecialchars($cta['lineLabel'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ปุ่มจอง</label><input name="cta_booking_label" value="<?= htmlspecialchars($cta['bookingLabel'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="../about.html" target="_blank">ดูหน้าเว็บ</a>
    <button type="submit" class="btn btn-primary">บันทึก</button>
  </div>
</form>

<?php tt_admin_footer(); ?>
