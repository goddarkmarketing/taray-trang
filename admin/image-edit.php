<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$images = $data['images'] ?? [];
$key = trim($_GET['key'] ?? '');
$entry = ($key !== '' && array_key_exists($key, $images)) ? $images[$key] : null;
$isNew = $entry === null;
$isArray = is_array($entry);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldKey = trim($_POST['old_key'] ?? '');
    $newKey = trim($_POST['key'] ?? '');
    if ($newKey === '') {
        tt_set_flash('กรุณาระบุ Key');
        header('Location: image-edit.php' . ($oldKey !== '' ? '?key=' . urlencode($oldKey) : ''));
        exit;
    }

    $type = $_POST['type'] ?? 'single';
    $raw = trim($_POST['value'] ?? '');

    if ($type === 'array') {
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $value = array_values(array_filter(array_map('trim', $lines)));
        if (!$value && str_contains($raw, ',')) {
            $value = array_values(array_filter(array_map('trim', explode(',', $raw))));
        }
    } else {
        $value = $raw;
    }

    if ($oldKey !== '' && $oldKey !== $newKey) {
        unset($images[$oldKey]);
    }
    if ($oldKey === '' || $oldKey !== $newKey) {
        if (array_key_exists($newKey, $images) && $oldKey !== $newKey) {
            tt_set_flash('Key นี้มีอยู่แล้ว — ใช้ชื่ออื่น');
            header('Location: image-edit.php?key=' . urlencode($oldKey !== '' ? $oldKey : $newKey));
            exit;
        }
    }

    $images[$newKey] = $value;
    $data['images'] = $images;
    tt_write_data($data);
    tt_set_flash('บันทึกรูปภาพแล้ว');
    header('Location: image-edit.php?key=' . urlencode($newKey));
    exit;
}

function tt_image_preview_url_edit(mixed $val): string
{
    $url = is_array($val) ? ($val[0] ?? '') : (string)$val;
    if ($url === '') {
        return '';
    }
    if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
        return $url;
    }
    return '../' . ltrim($url, '/');
}

$valueText = '';
if (is_array($entry)) {
    $valueText = implode("\n", $entry);
} elseif ($entry !== null) {
    $valueText = (string)$entry;
}

$pageTitle = $isNew ? 'เพิ่มรูปภาพ' : 'แก้ไขรูปภาพ';
tt_admin_header($pageTitle, 'images');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post">
  <div class="card">
    <h2>รูปภาพ (IMAGES Registry)</h2>
    <input type="hidden" name="old_key" value="<?= htmlspecialchars($isNew ? '' : $key, ENT_QUOTES, 'UTF-8') ?>"/>

    <?php
    $preview = $entry !== null ? tt_image_preview_url_edit($entry) : '';
    if ($preview): ?>
      <img src="<?= htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-width:280px;border-radius:10px;margin-bottom:16px;border:1px solid var(--adm-border)"/>
    <?php endif; ?>

    <div class="grid-2">
      <div class="field"><label>Key</label><input name="key" required value="<?= htmlspecialchars($isNew ? '' : $key, ENT_QUOTES, 'UTF-8') ?>" placeholder="heroHome, boatLongtail, videoThumbs"/></div>
      <div class="field"><label>ประเภท</label>
        <select name="type" id="image-type">
          <option value="single"<?= !$isArray ? ' selected' : '' ?>>URL เดียว</option>
          <option value="array"<?= $isArray ? ' selected' : '' ?>>หลาย URL (array)</option>
        </select>
      </div>
      <div class="field" style="grid-column:1/-1">
        <label id="image-value-label"><?= $isArray ? 'URL แต่ละบรรทัด' : 'URL / path' ?></label>
        <?php if ($isArray): ?>
          <textarea name="value" id="image-value" rows="8" placeholder="https://...&#10;assets/uploads/photo.jpg"><?= htmlspecialchars($valueText, ENT_QUOTES, 'UTF-8') ?></textarea>
        <?php else: ?>
          <input name="value" id="image-value" value="<?= htmlspecialchars($valueText, ENT_QUOTES, 'UTF-8') ?>" placeholder="https://... หรือ assets/uploads/photo.jpg"/>
        <?php endif; ?>
      </div>
    </div>

    <p class="field-hint">ใช้ URL จาก Pexels หรือ path ในเว็บ — อัปโหลดรูปใหม่ได้ที่ <a href="media.php">อัปโหลดรูป</a></p>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">บันทึก</button>
      <a class="btn btn-ghost" href="images.php">กลับ</a>
      <a class="btn btn-ghost" href="media.php">อัปโหลดรูป</a>
      <?php if ($preview): ?>
        <a class="btn btn-ghost" href="<?= htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">ดูรูป</a>
      <?php endif; ?>
      <?php if (!$isNew): ?>
        <a class="btn btn-danger" href="images.php?delete=<?= urlencode($key) ?>" onclick="return confirm('ลบ key นี้?')">ลบรายการนี้</a>
      <?php endif; ?>
    </div>
  </div>
</form>

<script>
(function () {
  const typeEl = document.getElementById('image-type');
  const valueEl = document.getElementById('image-value');
  const labelEl = document.getElementById('image-value-label');
  if (!typeEl || !valueEl) return;

  typeEl.addEventListener('change', () => {
    const isArray = typeEl.value === 'array';
    labelEl.textContent = isArray ? 'URL แต่ละบรรทัด' : 'URL / path';
    if (isArray && valueEl.tagName === 'INPUT') {
      const ta = document.createElement('textarea');
      ta.name = 'value';
      ta.id = 'image-value';
      ta.rows = 8;
      ta.placeholder = 'https://...\nassets/uploads/photo.jpg';
      ta.value = valueEl.value;
      valueEl.replaceWith(ta);
    } else if (!isArray && valueEl.tagName === 'TEXTAREA') {
      const input = document.createElement('input');
      input.name = 'value';
      input.id = 'image-value';
      input.placeholder = 'https://... หรือ assets/uploads/photo.jpg';
      input.value = valueEl.value.split('\n')[0] || valueEl.value.replace(/,/g, '').trim();
      valueEl.replaceWith(input);
    }
  });
})();
</script>

<?php tt_admin_footer(); ?>
