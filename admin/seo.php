<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/image-field.php';
tt_require_admin();

$data = tt_read_data();
$seo = $data['seo'] ?? [];

$pageLabels = [
    'index' => 'หน้าแรก',
    'about' => 'เกี่ยวกับเรา',
    'boats' => 'ประเภทเรือ',
    'programs' => 'โปรแกรมทัวร์',
    'booking' => 'จองเรือ',
    'articles' => 'บทความ (รายการ)',
    'article' => 'บทความ (รายละเอียด — fallback)',
    'videos' => 'วิดีโอ',
    'contact' => 'ติดต่อเรา',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSeo = [];
    foreach ($pageLabels as $key => $label) {
        $newSeo[$key] = [
            'title' => trim($_POST["title_{$key}"] ?? ''),
            'description' => trim($_POST["description_{$key}"] ?? ''),
            'ogTitle' => trim($_POST["ogTitle_{$key}"] ?? ''),
            'ogDescription' => trim($_POST["ogDescription_{$key}"] ?? ''),
            'ogImage' => trim($_POST["ogImage_{$key}"] ?? ''),
        ];
    }
    $data['seo'] = $newSeo;
    tt_write_data($data);
    tt_set_flash('บันทึก SEO แล้ว');
    header('Location: seo.php');
    exit;
}

tt_admin_header('SEO / Meta Tags', 'seo');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post">
  <?php foreach ($pageLabels as $key => $label):
    $s = $seo[$key] ?? [];
  ?>
    <div class="card">
      <h2><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> <small class="field-hint">(<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>)</small></h2>
      <div class="grid-2">
        <div class="field" style="grid-column:1/-1"><label>&lt;title&gt;</label><input name="title_<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($s['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
        <div class="field" style="grid-column:1/-1"><label>meta description</label><textarea name="description_<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" rows="2"><?= htmlspecialchars($s['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
        <div class="field"><label>og:title</label><input name="ogTitle_<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($s['ogTitle'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ว่าง = ใช้ title"/></div>
        <?php tt_render_image_url_field('og:image URL', 'ogImage_' . $key, $s['ogImage'] ?? '', [
            'context' => 'og',
            'id' => 'ogImage-' . $key,
        ]); ?>
        <div class="field" style="grid-column:1/-1"><label>og:description</label><textarea name="ogDescription_<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" rows="2" placeholder="ว่าง = ใช้ description"><?= htmlspecialchars($s['ogDescription'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
      </div>
    </div>
  <?php endforeach; ?>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">บันทึก SEO ทั้งหมด</button>
  </div>
</form>

<?php tt_admin_footer(); ?>
