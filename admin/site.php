<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$site = $data['site'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facebookUrl = trim($_POST['facebookUrl'] ?? '');
    $facebookPageUrl = trim($_POST['facebookPageUrl'] ?? '');

    $oldSite = $data['site'] ?? [];
    $phone = trim($_POST['phone'] ?? '');

    $site = array_merge($oldSite, [
        'brand' => trim($_POST['brand'] ?? ''),
        'brandTh' => trim($_POST['brandTh'] ?? ''),
        'tagline' => trim($_POST['tagline'] ?? ''),
        'phone' => $phone,
        'phoneDisplay' => $phone,
        'lineId' => trim($_POST['lineId'] ?? ''),
        'lineUrl' => trim($_POST['lineUrl'] ?? ''),
        'facebookUrl' => $facebookUrl,
        'facebookPageUrl' => $facebookPageUrl,
        'tiktokUrl' => trim($_POST['tiktokUrl'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'addressFull' => trim($_POST['addressFull'] ?? ''),
        'hours' => trim($_POST['hours'] ?? ''),
        'mapEmbed' => trim($_POST['mapEmbed'] ?? ''),
    ]);

    if ($site['facebookPageUrl'] === '' && $facebookUrl !== '') {
        $resolved = tt_resolve_facebook_page_url($facebookUrl);
        if ($resolved !== '') {
            $site['facebookPageUrl'] = $resolved;
        }
    }

    $data['site'] = $site;
    if (tt_write_data($data)) {
        tt_set_flash('บันทึกข้อมูลเว็บแล้ว — ข้อมูลจะอัปเดตบนหน้าเว็บทันที');
    } else {
        tt_set_flash('บันทึกไม่สำเร็จ — ตรวจสอบสิทธิ์เขียนไฟล์ data/site.json และ assets/js/data-fallback.js', 'error');
    }
    header('Location: site.php');
    exit;
}

tt_admin_header('ข้อมูลเว็บ', 'site');
$flash = tt_flash();
$flashType = tt_flash_type();
if ($flash): ?><div class="alert alert-<?= $flashType === 'error' ? 'error' : 'success' ?>"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post" class="card">
  <h2>ข้อมูลติดต่อ & แบรนด์</h2>
  <div class="grid-2">
    <div class="field"><label>ชื่อแบรนด์ (EN)</label><input name="brand" value="<?= htmlspecialchars($site['brand'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>ชื่อแบรนด์ (TH)</label><input name="brandTh" value="<?= htmlspecialchars($site['brandTh'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field" style="grid-column:1/-1"><label>Tagline</label><input name="tagline" value="<?= htmlspecialchars($site['tagline'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>เบอร์โทร</label><input name="phone" id="site-phone" value="<?= htmlspecialchars($site['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/><p class="field-hint">แสดงบนเว็บและใช้ในลิงก์กดโทร (tel:) ทุกหน้า</p></div>
    <div class="field"><label>LINE ID</label><input name="lineId" value="<?= htmlspecialchars($site['lineId'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>LINE URL</label><input name="lineUrl" value="<?= htmlspecialchars($site['lineUrl'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>Facebook URL (ลิงก์ทั่วไป)</label><input name="facebookUrl" value="<?= htmlspecialchars($site['facebookUrl'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>URL เพจ Facebook (Feed)</label><input name="facebookPageUrl" value="<?= htmlspecialchars($site['facebookPageUrl'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="https://www.facebook.com/talaytrang"/><p class="field-hint">ใช้ฝัง feed บนหน้าวิดีโอ — ว่างแล้วระบบจะหา URL เพจจากลิงก์ share ให้อัตโนมัติ</p></div>
    <div class="field"><label>TikTok URL</label><input name="tiktokUrl" value="<?= htmlspecialchars($site['tiktokUrl'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>ที่อยู่สั้น</label><input name="address" value="<?= htmlspecialchars($site['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>ที่อยู่เต็ม</label><input name="addressFull" value="<?= htmlspecialchars($site['addressFull'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field"><label>เวลาเปิด</label><input name="hours" value="<?= htmlspecialchars($site['hours'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    <div class="field" style="grid-column:1/-1"><label>Google Maps Embed URL</label><input name="mapEmbed" value="<?= htmlspecialchars($site['mapEmbed'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
  </div>
  <div class="form-actions"><button type="submit" class="btn btn-primary">บันทึก</button></div>
</form>

<?php tt_admin_footer(); ?>
