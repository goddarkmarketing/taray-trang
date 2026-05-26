<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$uploads = tt_list_uploads();

tt_admin_header('อัปโหลดรูป', 'media');
?>

<div class="card">
  <h2>อัปโหลดรูปใหม่</h2>
  <p class="field-hint">JPG, PNG, WebP, GIF — สูงสุด 5MB</p>
  <form id="upload-form" enctype="multipart/form-data">
    <div class="field">
      <input type="file" name="file" id="file" accept="image/*" required/>
    </div>
    <button type="submit" class="btn btn-primary">อัปโหลด</button>
  </form>
  <div id="upload-result" class="field-hint" style="margin-top:12px"></div>
</div>

<div class="card">
  <h2>รูปที่อัปโหลดแล้ว (<?= count($uploads) ?>)</h2>
  <?php if (!$uploads): ?>
    <p class="field-hint">ยังไม่มีรูป — อัปโหลดรูปแรกได้ด้านบน</p>
  <?php else: ?>
    <div class="media-grid">
      <?php foreach ($uploads as $u): ?>
        <div class="media-item">
          <img src="../<?= htmlspecialchars($u['url'], ENT_QUOTES, 'UTF-8') ?>" alt=""/>
          <code><?= htmlspecialchars($u['url'], ENT_QUOTES, 'UTF-8') ?></code>
          <button type="button" class="btn btn-ghost btn-sm" style="margin:8px;width:calc(100% - 16px)" data-copy="<?= htmlspecialchars($u['url'], ENT_QUOTES, 'UTF-8') ?>">คัดลอก path</button>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
document.getElementById('upload-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData();
  fd.append('file', document.getElementById('file').files[0]);
  const res = await fetch('api/upload.php', { method: 'POST', body: fd });
  const json = await res.json();
  const el = document.getElementById('upload-result');
  if (json.ok) {
    el.innerHTML = 'สำเร็จ: <code>' + json.url + '</code> — <a href="media.php">รีเฟรช</a>';
    ttToast('อัปโหลดสำเร็จ');
  } else {
    el.textContent = json.error || 'ผิดพลาด';
    ttToast(json.error || 'ผิดพลาด', true);
  }
});
</script>

<?php tt_admin_footer(); ?>
