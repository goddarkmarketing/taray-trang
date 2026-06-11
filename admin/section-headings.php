<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$sections = tt_home_sections($data);
$keys = ['hero', 'boats', 'programs', 'packages3d2n', 'packages4d3n', 'booking', 'reviews', 'videos', 'office', 'cta'];

$labels = [
    'hero' => 'แบนเนอร์ — ข้อความบนรูปสไลด์',
    'boats' => 'Section แพ็กเกจไปเช้าเย็นกลับ (#boats)',
    'programs' => 'Section แพ็กเกจ 2 วัน 1 คืน (#home-section-programs)',
    'packages3d2n' => 'Section แพ็กเกจ 3 วัน 2 คืน (#home-section-packages3d2n)',
    'packages4d3n' => 'Section แพ็กเกจ 4 วัน 3 คืน (#home-section-packages4d3n)',
    'booking' => 'Section ขั้นตอนจอง (4 ขั้นตอน)',
    'reviews' => 'Section รีวิวลูกค้า',
    'videos' => 'Section TikTok / วิดีโอ',
    'office' => 'Section ออฟฟิศ / แผนที่',
    'cta' => 'Section CTA ท้ายหน้า',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $out = [];
    foreach ($keys as $key) {
        $out[$key] = [
            'eyebrow' => trim($_POST[$key . '_eyebrow'] ?? ''),
            'title' => trim($_POST[$key . '_title'] ?? ''),
            'lead' => trim($_POST[$key . '_lead'] ?? ''),
        ];
        if ($key === 'videos') {
            $out[$key]['tiktokHandle'] = trim($_POST['videos_tiktokHandle'] ?? '');
        }
        if ($key === 'office') {
            $out[$key]['infoTitle'] = trim($_POST['office_infoTitle'] ?? '');
            $out[$key]['infoLead'] = trim($_POST['office_infoLead'] ?? '');
        }
        if ($key === 'hero') {
            unset($out[$key]['eyebrow']);
        }
    }
    $data['homeSections'] = $out;
    if (tt_write_data($data)) {
        tt_set_flash('บันทึกหัวข้อ Section แล้ว — เปิดหน้าแรกแล้วกดรีเฟรชเพื่อดูผล');
    } else {
        tt_set_flash('บันทึกไม่สำเร็จ', 'error');
    }
    header('Location: section-headings.php');
    exit;
}

tt_admin_header('หัวข้อ Section', 'section-headings');
$flash = tt_flash();
$flashType = tt_flash_type();
if ($flash): ?>
  <div class="alert alert-<?= $flashType === 'error' ? 'error' : 'success' ?>"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="card">
  <h2>หัวข้อ Section หน้าแรก</h2>
  <p class="field-hint">แก้เฉพาะ <strong>หัวข้อใหญ่</strong> ของแต่ละ Section บน index.html (Eyebrow / H2 / คำอธิบาย) — ไม่รวมการ์ดรายการ การ์ดแพ็กเกจไปเช้าเย็นกลับ/โปรแกรม/รีวิว แก้ที่เมนู <a href="programs.php">โปรแกรมทัวร์</a> (รหัส # บนการ์ด), <a href="boats.php">ประเภทเรือ</a>, <a href="reviews.php">รีวิว</a></p>
</div>

<form method="post">
  <?php foreach ($keys as $key):
    $s = $sections[$key] ?? [];
    $label = $labels[$key] ?? $key;
  ?>
  <div class="card" style="margin-bottom:16px" id="section-<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>">
    <div class="admin-section-title">
      <h2><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></h2>
      <?= tt_render_admin_section_help($key) ?>
    </div>
    <div class="grid-2">
      <?php if ($key !== 'hero'): ?>
      <div class="field">
        <label>ข้อความเล็กด้านบน (Eyebrow)</label>
        <input name="<?= $key ?>_eyebrow" value="<?= htmlspecialchars($s['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/>
      </div>
      <?php endif; ?>
      <div class="field" style="<?= $key === 'hero' ? 'grid-column:1/-1' : '' ?>">
        <label>หัวข้อหลัก (H2)</label>
        <input name="<?= $key ?>_title" value="<?= htmlspecialchars($s['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/>
      </div>
      <?php if ($key === 'office'): ?>
      <div class="field" style="grid-column:1/-1">
        <label>หัวข้อในกล่องข้อมูลออฟฟิศ</label>
        <input name="office_infoTitle" value="<?= htmlspecialchars($s['infoTitle'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/>
      </div>
      <div class="field" style="grid-column:1/-1">
        <label>คำอธิบายในกล่องข้อมูลออฟฟิศ</label>
        <textarea name="office_infoLead" rows="2"><?= htmlspecialchars($s['infoLead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <?php endif; ?>
      <?php if ($key !== 'office'): ?>
      <div class="field" style="grid-column:1/-1">
        <label>คำอธิบาย (Lead)</label>
        <textarea name="<?= $key ?>_lead" rows="2"><?= htmlspecialchars($s['lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <?php endif; ?>
      <?php if ($key === 'videos'): ?>
      <div class="field">
        <label>ชื่อ TikTok ในลิงก์ (เช่น @talaytrang)</label>
        <input name="videos_tiktokHandle" value="<?= htmlspecialchars($s['tiktokHandle'] ?? '@talaytrang', ENT_QUOTES, 'UTF-8') ?>"/>
        <p class="field-hint">ลิงก์ไป URL TikTok จากเมนู <a href="site.php">ข้อมูลเว็บ</a></p>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <div class="form-actions">
    <button type="submit" class="btn btn-primary">บันทึกหัวข้อ Section ทั้งหมด</button>
    <a class="btn btn-ghost" href="../index.html" target="_blank" rel="noopener">เปิดดูหน้าแรก ↗</a>
  </div>
</form>

<?php tt_admin_footer(); ?>
