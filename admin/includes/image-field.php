<?php
declare(strict_types=1);

require_once __DIR__ . '/image-sizes.php';

function tt_image_field_label(string $base, ?string $key = null, ?string $context = null): string
{
    $size = tt_image_size_label($key, $context);
    return $size !== '' ? $base . ' — ' . $size : $base;
}

/**
 * ช่อง URL/path รูป + ปุ่มอัปโหลด (ใช้ class image-url-row — init ใน admin.js)
 *
 * @param array{
 *   id?: string,
 *   placeholder?: string,
 *   required?: bool,
 *   multiline?: bool,
 *   rows?: int,
 *   style?: string,
 *   key?: string|null,
 *   context?: string|null,
 *   append?: bool
 * } $opts
 */
function tt_render_image_url_field(
    string $labelBase,
    string $name,
    string $value = '',
    array $opts = []
): void {
    $key = $opts['key'] ?? null;
    $context = $opts['context'] ?? null;
    $id = $opts['id'] ?? preg_replace('/[^a-zA-Z0-9_-]/', '-', $name);
    $placeholder = $opts['placeholder'] ?? 'https://... หรือ assets/uploads/photo.jpg';
    $required = !empty($opts['required']);
    $multiline = !empty($opts['multiline']);
    $rows = (int)($opts['rows'] ?? 8);
    $style = $opts['style'] ?? '';
    $append = !empty($opts['append']);
    $label = tt_image_field_label($labelBase, $key, $context);
    $reqAttr = $required ? ' required' : '';
    $appendAttr = $append ? ' data-image-append="1"' : '';
    ?>
<div class="field"<?= $style !== '' ? ' style="' . htmlspecialchars($style, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
  <label for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></label>
  <div class="image-url-row">
    <?php if ($multiline): ?>
      <textarea name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" class="image-url-input" rows="<?= $rows ?>" placeholder="<?= htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') ?>"<?= $appendAttr ?>><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?></textarea>
    <?php else: ?>
      <input type="text" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" class="image-url-input" value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') ?>"<?= $reqAttr ?><?= $appendAttr ?>/>
    <?php endif; ?>
    <button type="button" class="btn btn-ghost image-upload-btn">อัปโหลด</button>
    <input type="file" class="image-upload-file" accept="image/*" hidden/>
  </div>
</div>
<?php
}
