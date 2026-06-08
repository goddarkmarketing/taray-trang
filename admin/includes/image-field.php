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

/**
 * แกลเลอรี 6 ช่อง — ตรง layout หน้า program.html (3 บน + 3 ล่าง)
 *
 * @param list<string> $values URL รูป (0–6 รายการ)
 * @param array{name?: string, context?: string|null} $opts
 */
function tt_render_gallery_grid(array $values = [], array $opts = []): void
{
    $name = $opts['name'] ?? 'gallery';
    $context = $opts['context'] ?? 'program';
    $labels = [
        'รูป 1 — แถวบนซ้าย',
        'รูป 2 — แถวบนหลัง',
        'รูป 3 — แถวบนขวา',
        'รูป 4 — แถวล่างซ้าย',
        'รูป 5 — แถวล่างกลาง',
        'รูป 6 — แถวล่างขวา',
    ];
    $sizeHint = tt_image_size_label(null, $context);
    ?>
<div class="gallery-grid-field">
  <?php if ($sizeHint !== ''): ?>
    <p class="field-hint gallery-grid-hint"><?= htmlspecialchars($sizeHint, ENT_QUOTES, 'UTF-8') ?> · แนะนำ 1200×800 px ขึ้นไป</p>
  <?php endif; ?>
  <div class="gallery-grid">
    <?php for ($i = 0; $i < 6; $i++):
        $val = $values[$i] ?? '';
        $slotId = $name . '-' . ($i + 1);
        $label = $labels[$i];
        ?>
    <div class="gallery-slot" data-gallery-slot>
      <span class="gallery-slot-label"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
      <div class="gallery-slot-preview" data-gallery-preview>
        <?php if ($val !== ''): ?>
          <img src="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy"/>
        <?php else: ?>
          <span class="gallery-slot-empty">ยังไม่มีรูป</span>
        <?php endif; ?>
      </div>
      <div class="image-url-row">
        <input type="text"
               name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>[<?= $i ?>]"
               id="<?= htmlspecialchars($slotId, ENT_QUOTES, 'UTF-8') ?>"
               class="image-url-input gallery-slot-input"
               value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="https://... หรือ assets/uploads/..."/>
        <button type="button" class="btn btn-ghost image-upload-btn">อัปโหลด</button>
        <input type="file" class="image-upload-file" accept="image/*" hidden/>
      </div>
    </div>
    <?php endfor; ?>
  </div>
</div>
<?php
}

/** @return list<string> */
function tt_parse_gallery_post(mixed $raw, int $max = 6): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    for ($i = 0; $i < $max; $i++) {
        $url = trim((string)($raw[$i] ?? ''));
        if ($url !== '') {
            $out[] = $url;
        }
    }
    return $out;
}

/** @return list<string> */
function tt_gallery_slots_for_form(?array $gallery): array
{
    $slots = array_values($gallery ?? []);
    while (count($slots) < 6) {
        $slots[] = '';
    }
    return array_slice($slots, 0, 6);
}

/**
 * ช่องรูปปกการ์ด — preview ใหญ่ + URL + อัปโหลด
 *
 * @param array{id?: string, context?: string|null, label?: string} $opts
 */
function tt_render_card_image_field(string $name, string $value = '', array $opts = []): void
{
    $context = $opts['context'] ?? 'program';
    $label = $opts['label'] ?? 'ภาพปกการ์ด';
    $id = $opts['id'] ?? preg_replace('/[^a-zA-Z0-9_-]/', '-', $name);
    $sizeHint = tt_image_size_label(null, $context);
    ?>
<div class="card-image-field" data-card-image-field>
  <div class="card-image-preview" data-card-image-preview>
    <?php if ($value !== ''): ?>
      <img src="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy"/>
    <?php else: ?>
      <span class="card-image-empty">ยังไม่มีภาพปกการ์ด</span>
    <?php endif; ?>
  </div>
  <div class="field card-image-input-wrap">
    <label for="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?><?= $sizeHint !== '' ? ' — ' . htmlspecialchars($sizeHint, ENT_QUOTES, 'UTF-8') : '' ?></label>
    <div class="image-url-row">
      <input type="text"
             name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
             id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
             class="image-url-input card-image-input"
             value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>"
             placeholder="https://... หรือ assets/uploads/..."/>
      <button type="button" class="btn btn-ghost image-upload-btn">อัปโหลด</button>
      <input type="file" class="image-upload-file" accept="image/*" hidden/>
    </div>
    <p class="field-hint">แสดงบนการ์ดหน้าแรกและหน้า programs.html · แนะนำ 1200×750 px (16:10)</p>
  </div>
</div>
<?php
}
