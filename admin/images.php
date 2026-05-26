<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$images = $data['images'] ?? [];

if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $key = $_GET['delete'];
    if (array_key_exists($key, $images)) {
        unset($images[$key]);
        $data['images'] = $images;
        tt_write_data($data);
        tt_set_flash('ลบรูปภาพแล้ว');
    }
    header('Location: images.php');
    exit;
}

function tt_image_preview_url(mixed $val): string
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

function tt_image_value_summary(mixed $val): string
{
    if (is_array($val)) {
        return count($val) . ' รูป';
    }
    $text = (string)$val;
    if (mb_strlen($text) > 48) {
        return mb_substr($text, 0, 48) . '…';
    }
    return $text;
}

tt_admin_header('รูปภาพ (Registry)', 'images');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="form-actions" style="border:0;margin-bottom:16px;padding:0">
  <a class="btn btn-primary" href="image-edit.php">+ เพิ่มรูปภาพ</a>
  <a class="btn btn-ghost" href="media.php">อัปโหลดรูป</a>
</div>

<div class="card">
  <?php if (!$images): ?>
    <p class="field-hint">ยังไม่มีรูปใน Registry — กดปุ่มด้านบนเพื่อเพิ่ม key ใหม่</p>
  <?php else: ?>
  <p class="field-hint" style="margin-top:0;margin-bottom:16px">คลังรูป IMAGES ที่หน้าเว็บใช้ — URL เต็ม (Pexels) หรือ path ในเว็บ (assets/...)</p>
  <table class="table">
    <thead>
      <tr><th>ตัวอย่าง</th><th>Key</th><th>ประเภท</th><th>ค่า</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($images as $key => $val):
        $preview = tt_image_preview_url($val);
      ?>
        <tr>
          <td>
            <?php if ($preview): ?>
              <img src="<?= htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') ?>" alt=""/>
            <?php else: ?>
              <span class="field-hint">—</span>
            <?php endif; ?>
          </td>
          <td><strong><?= htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8') ?></strong></td>
          <td><?= is_array($val) ? 'หลาย URL' : 'URL เดียว' ?></td>
          <td><small class="field-hint"><?= htmlspecialchars(tt_image_value_summary($val), ENT_QUOTES, 'UTF-8') ?></small></td>
          <td class="table-actions">
            <div class="table-actions-row">
              <a class="btn btn-ghost btn-sm" href="image-edit.php?key=<?= urlencode((string)$key) ?>">แก้ไข</a>
              <a class="btn btn-danger btn-sm" href="images.php?delete=<?= urlencode((string)$key) ?>" onclick="return confirm('ลบ key นี้?')">ลบ</a>
              <?php if ($preview): ?>
                <a class="btn btn-ghost btn-sm" href="<?= htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">ดู</a>
              <?php else: ?>
                <span class="btn btn-ghost btn-sm" style="opacity:.4;pointer-events:none">ดู</span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php tt_admin_footer(); ?>
