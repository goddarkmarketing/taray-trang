<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/image-field.php';
tt_require_admin();

$data = tt_read_data();
$articles = $data['articles'] ?? [];
$slug = $_GET['slug'] ?? '';
$article = null;
$idx = -1;

foreach ($articles as $i => $a) {
    if (($a['slug'] ?? '') === $slug) {
        $article = $a;
        $idx = $i;
        break;
    }
}

$isNew = $article === null;
$markdown = $article ? tt_body_to_markdown($article['body'] ?? []) : "## หัวข้อย่อย\n\nย่อหน้าแรกของบทความ\n\n- รายการที่ 1\n- รายการที่ 2";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSlug = trim($_POST['slug'] ?? '');
    if ($newSlug === '') {
        $newSlug = tt_slugify($_POST['title'] ?? 'article');
    }
    $body = tt_markdown_to_body($_POST['markdown'] ?? '');

    $item = [
        'slug' => $newSlug,
        'category' => trim($_POST['category'] ?? ''),
        'title' => trim($_POST['title'] ?? ''),
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'cover' => trim($_POST['cover'] ?? ''),
        'thumb' => trim($_POST['thumb'] ?? ''),
        'author' => trim($_POST['author'] ?? 'ทีมงาน Talay Trang'),
        'date' => trim($_POST['date'] ?? date('j M Y')),
        'readTime' => trim($_POST['readTime'] ?? '5 นาที'),
        'body' => $body,
    ];

    if ($idx >= 0) {
        $articles[$idx] = $item;
    } else {
        $articles[] = $item;
    }

    $data['articles'] = $articles;
    tt_write_data($data);
    tt_set_flash('บันทึกบทความแล้ว');
    header('Location: article-edit.php?slug=' . urlencode($newSlug));
    exit;
}

$title = $isNew ? 'เขียนบทความใหม่' : 'แก้ไขบทความ';
tt_admin_header($title, 'articles');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="card">
  <div class="alert alert-info">
    <strong>Markdown ง่าย ๆ:</strong><br>
    <code>## หัวข้อ</code> · <code>&gt; คำคม</code> · <code>- รายการ</code> · <code>![alt](url)</code> · ย่อหน้าปกติ
  </div>
</div>

<form method="post">
  <div class="card">
    <h2>ข้อมูลบทความ</h2>
    <div class="grid-2">
      <div class="field"><label>หัวข้อ</label><input name="title" required value="<?= htmlspecialchars($article['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>Slug (URL)</label><input name="slug" value="<?= htmlspecialchars($article['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="five-must-see-islands-trang"/></div>
      <div class="field"><label>หมวด</label><input name="category" value="<?= htmlspecialchars($article['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ผู้เขียน</label><input name="author" value="<?= htmlspecialchars($article['author'] ?? 'ทีมงาน Talay Trang', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>วันที่</label><input name="date" value="<?= htmlspecialchars($article['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>เวลาอ่าน</label><input name="readTime" value="<?= htmlspecialchars($article['readTime'] ?? '5 นาที', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>คำโปรย (excerpt)</label><textarea name="excerpt"><?= htmlspecialchars($article['excerpt'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
      <?php tt_render_image_url_field('รูปปก (cover URL)', 'cover', $article['cover'] ?? '', ['context' => 'article_cover']); ?>
      <?php tt_render_image_url_field('รูปย่อ (thumb URL)', 'thumb', $article['thumb'] ?? '', ['context' => 'article_thumb']); ?>
    </div>
  </div>

  <div class="card">
    <h2>เนื้อหา (Markdown)</h2>
    <div class="field">
      <textarea name="markdown" class="tall"><?= htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">บันทึกบทความ</button>
      <a class="btn btn-ghost" href="articles.php">กลับ</a>
      <a class="btn btn-ghost" href="media.php">อัปโหลดรูป</a>
      <?php if (!$isNew): ?>
        <a class="btn btn-ghost" href="../article.html?slug=<?= urlencode($article['slug'] ?? '') ?>" target="_blank">ดูบนเว็บ</a>
      <?php endif; ?>
    </div>
  </div>
</form>

<?php tt_admin_footer(); ?>
