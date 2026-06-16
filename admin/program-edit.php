<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/image-field.php';
require_once __DIR__ . '/includes/package-collections.php';
tt_require_admin();

$collections = tt_package_collections();
$collection = tt_package_collection_key($_GET['collection'] ?? $_POST['collection'] ?? 'programs');
$collectionMeta = $collections[$collection];

$data = tt_read_data();
$id = trim($_GET['id'] ?? $_POST['id'] ?? '');
$found = tt_package_find($collection, $id, $data);
$program = $found['item'];
$idx = $found['index'];
$isNew = $id === '' || $program === null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collection = tt_package_collection_key($_POST['collection'] ?? 'programs');
    $collectionMeta = $collections[$collection];
    $data = tt_read_data();
    $oldId = trim($_POST['old_id'] ?? '');
    $found = tt_package_find($collection, $oldId, $data);
    $idx = $found['index'];
    $existingItem = is_array($found['item'] ?? null) ? $found['item'] : [];

    $newId = trim($_POST['id'] ?? '');
    if ($newId === '') {
        $newId = tt_slugify($_POST['name'] ?? 'program');
    }
    $stops = tt_parse_lines($_POST['stops'] ?? '');
    $boats = array_values(array_filter(array_map('trim', explode(',', $_POST['boats'] ?? ''))));
    $gallery = tt_parse_gallery_post($_POST['gallery'] ?? []);
    $itinerary = tt_parse_itinerary($_POST['itinerary'] ?? '');
    $inclusions = tt_parse_lines($_POST['inclusions'] ?? '');
    $priceNotes = tt_parse_lines($_POST['priceNotes'] ?? '');
    $hotels = tt_parse_hotels($_POST['hotels'] ?? '');
    $childPriceRaw = trim($_POST['childPrice'] ?? '');
    $inclusionsByDay = tt_parse_inclusions_by_day($_POST['inclusionsByDay'] ?? '');

    $item = [
        'id' => $newId,
        'name' => trim($_POST['name'] ?? ''),
        'image' => trim($_POST['image'] ?? ''),
        'route' => trim($_POST['route'] ?? ''),
        'stops' => $stops,
        'duration' => trim($_POST['duration'] ?? ''),
        'basePrice' => (int)($_POST['basePrice'] ?? 0),
        'boats' => $boats,
        'desc' => trim($_POST['desc'] ?? ''),
        'ribbon' => trim($_POST['ribbon'] ?? '') ?: null,
        'rating' => (float)($_POST['rating'] ?? 4.8),
        'reviewCount' => trim($_POST['reviewCount'] ?? ''),
        'oldPrice' => (int)($_POST['oldPrice'] ?? 0),
        'stars' => (int)($_POST['stars'] ?? 5),
        'packageCode' => trim($_POST['packageCode'] ?? ''),
        'gallery' => $gallery,
        'galleryCaption' => trim($_POST['galleryCaption'] ?? ''),
        'licenseNo' => trim($_POST['licenseNo'] ?? ''),
        'itinerary' => $itinerary,
        'season' => trim($_POST['season'] ?? ''),
        'inclusions' => $inclusions,
        'hotels' => $hotels,
        'childPrice' => $childPriceRaw === '' ? null : (int)$childPriceRaw,
        'priceMeetingNote' => trim($_POST['priceMeetingNote'] ?? ''),
        'pickupSurcharge' => trim($_POST['pickupSurcharge'] ?? '') === '' ? null : (int)($_POST['pickupSurcharge'] ?? 0),
        'priceNotes' => $priceNotes,
        'warning' => trim($_POST['warning'] ?? ''),
    ];
    if ($item['ribbon'] === null) {
        unset($item['ribbon']);
    }
    foreach (['gallery', 'galleryCaption', 'licenseNo', 'itinerary', 'season', 'inclusions', 'hotels', 'priceMeetingNote', 'priceNotes', 'warning', 'boats', 'stops'] as $k) {
        if ($item[$k] === '' || $item[$k] === [] || $item[$k] === null) {
            unset($item[$k]);
        }
    }
    if ($item['childPrice'] === null) {
        unset($item['childPrice']);
    }
    if ($item['pickupSurcharge'] === null) {
        unset($item['pickupSurcharge']);
    }

    foreach ($existingItem as $k => $v) {
        if (!array_key_exists($k, $item)) {
            $item[$k] = $v;
        }
    }
    if ($collection === 'packages4d3n' || trim($_POST['inclusionsByDay'] ?? '') !== '' || !empty($existingItem['inclusionsByDay'])) {
        if ($inclusionsByDay) {
            $item['inclusionsByDay'] = $inclusionsByDay;
        } else {
            unset($item['inclusionsByDay']);
        }
    }

    $list = $data[$collection] ?? [];
    if ($idx >= 0) {
        if ($newId !== $oldId) {
            foreach ($list as $j => $p) {
                if ($j !== $idx && ($p['id'] ?? '') === $newId) {
                    tt_set_flash('ID นี้มีอยู่แล้ว — ใช้ ID อื่น');
                    header('Location: program-edit.php?collection=' . urlencode($collection) . '&id=' . urlencode($oldId));
                    exit;
                }
            }
        }
        $list[$idx] = $item;
    } else {
        foreach ($list as $p) {
            if (($p['id'] ?? '') === $newId) {
                tt_set_flash('ID นี้มีอยู่แล้ว — ใช้ ID อื่น');
                header('Location: program-edit.php?collection=' . urlencode($collection) . '&id=' . urlencode($newId));
                exit;
            }
        }
        $list[] = $item;
    }

    $data[$collection] = array_values($list);
    tt_write_data($data);
    tt_set_flash('บันทึกแพ็กเกจแล้ว');
    header('Location: program-edit.php?collection=' . urlencode($collection) . '&id=' . urlencode($newId));
    exit;
}

$gallerySlots = tt_gallery_slots_for_form($program['gallery'] ?? []);
$itinLines = array_map(function ($row) {
    $time = $row['time'] ?? '';
    $title = $row['title'] ?? '';
    $text = $row['text'] ?? $row['desc'] ?? '';
    if ($title !== '') {
        return $time . '|' . $title . '|' . $text;
    }
    return $time . '|' . $text;
}, $program['itinerary'] ?? []);
$hotelsText = tt_hotels_to_text($program['hotels'] ?? []);
$inclusionsByDayText = tt_inclusions_by_day_to_text($program['inclusionsByDay'] ?? []);
$showInclusionsByDay = $collection === 'packages4d3n' || !empty($program['inclusionsByDay']);

$pageTitle = $isNew ? 'เพิ่มแพ็กเกจ' : 'แก้ไขหน้ารายละเอียด';
tt_admin_header($pageTitle, 'programs');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<?php if (!$isNew): ?>
<div class="card admin-preview-bar">
  <div>
    <strong>แก้ไขหน้า:</strong>
    <a href="../program.html?id=<?= urlencode($program['id'] ?? '') ?>" target="_blank" rel="noopener">program.html?id=<?= htmlspecialchars($program['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
  </div>
  <a class="btn btn-ghost btn-sm" href="../program.html?id=<?= urlencode($program['id'] ?? '') ?>" target="_blank" rel="noopener">เปิดดูหน้ารายละเอียด ↗</a>
</div>
<?php endif; ?>

<form method="post" class="admin-package-form">
  <input type="hidden" name="collection" value="<?= htmlspecialchars($collection, ENT_QUOTES, 'UTF-8') ?>"/>
  <input type="hidden" name="old_id" value="<?= htmlspecialchars($program['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/>

  <div class="card">
    <h2>ประเภทแพ็กเกจ</h2>
    <div class="field">
      <label for="collection-select">หมวด</label>
      <select id="collection-select" class="input" onchange="location.href='program-edit.php?collection='+encodeURIComponent(this.value)+<?= $isNew ? "''" : "'&id='+encodeURIComponent('" . htmlspecialchars($program['id'] ?? '', ENT_QUOTES, 'UTF-8') . "')" ?>">
        <?php foreach ($collections as $key => $meta): ?>
          <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"<?= $key === $collection ? ' selected' : '' ?>><?= htmlspecialchars($meta['label'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
      </select>
      <p class="field-hint">แพ็กเกจ <?= htmlspecialchars($collectionMeta['label'], ENT_QUOTES, 'UTF-8') ?> แสดงบนหน้าแรกและหน้า programs.html</p>
    </div>
  </div>

  <div class="card">
    <h2>ภาพปกการ์ด</h2>
    <p class="field-hint section-lead">รูปหลักบนการ์ดแพ็กเกจ (หน้าแรก · programs.html) — แยกจากแกลเลอรี 6 รูปในหน้ารายละเอียด</p>
    <?php tt_render_card_image_field('image', $program['image'] ?? '', ['context' => 'program', 'label' => 'ภาพปกการ์ด']); ?>
  </div>

  <div class="card">
    <h2>ข้อมูลการ์ด</h2>
    <p class="field-hint section-lead">ชื่อ ราคา และรายละเอียดที่แสดงบนการ์ด</p>
    <div class="grid-2">
      <div class="field"><label>ชื่อแพ็กเกจ</label><input name="name" required value="<?= htmlspecialchars($program['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ID (slug)</label><input name="id" value="<?= htmlspecialchars($program['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="dl001, p1-001"/></div>
      <div class="field"><label>รหัสแพ็กเกจ</label><input name="packageCode" value="<?= htmlspecialchars((string)($program['packageCode'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="DL001, D-002"/><p class="field-hint">แสดงบนการ์ดและแถบ meta หน้ารายละเอียด</p></div>
      <div class="field"><label>ป้าย (ribbon)</label><input name="ribbon" value="<?= htmlspecialchars($program['ribbon'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="650 ฿ หรือ ยอดนิยม"/></div>
      <div class="field"><label>เส้นทาง</label><input name="route" value="<?= htmlspecialchars($program['route'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ระยะเวลา</label><input name="duration" value="<?= htmlspecialchars($program['duration'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ประมาณ 8 ชั่วโมง"/></div>
      <div class="field"><label>ราคาผู้ใหญ่ (บาท)</label><input name="basePrice" type="number" value="<?= (int)($program['basePrice'] ?? 0) ?>"/></div>
      <div class="field"><label>เรือที่รองรับ (คั่นด้วย ,)</label><input name="boats" value="<?= htmlspecialchars(implode(', ', $program['boats'] ?? []), ENT_QUOTES, 'UTF-8') ?>" placeholder="longtail, bigboat, speedboat"/></div>
      <div class="field"><label>คะแนน (0–5)</label><input name="rating" type="number" step="0.1" min="0" max="5" value="<?= htmlspecialchars((string)($program['rating'] ?? 4.8), ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>จำนวนรีวิว</label><input name="reviewCount" value="<?= htmlspecialchars($program['reviewCount'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ดาว (1–5)</label><input name="stars" type="number" min="1" max="5" value="<?= (int)($program['stars'] ?? 5) ?>"/></div>
      <div class="field"><label>ราคาเดิม (ขีดฆ่า)</label><input name="oldPrice" type="number" value="<?= (int)($program['oldPrice'] ?? 0) ?>" placeholder="0 = ไม่แสดง"/></div>
      <div class="field" style="grid-column:1/-1"><label>จุดแวะ (บรรทัดละจุด)</label><textarea name="stops" rows="3"><?= htmlspecialchars(implode("\n", $program['stops'] ?? []), ENT_QUOTES, 'UTF-8') ?></textarea></div>
      <div class="field" style="grid-column:1/-1"><label>รายละเอียดสั้น</label><textarea name="desc" rows="3"><?= htmlspecialchars($program['desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
    </div>
  </div>

  <div class="card">
    <h2>แกลเลอรี 6 รูป — ด้านบนหน้ารายละเอียด</h2>
    <p class="field-hint section-lead">จัดเรียงตามหน้า program.html: 3 รูปแถวบน · แถบชื่อกลาง · 3 รูปแถวล่าง · ว่างช่องใดจะใช้รูปปกการ์ดแทน</p>
    <div class="field" style="grid-column:1/-1">
      <label>รูปแกลเลอรี 6 ช่อง</label>
      <?php tt_render_gallery_grid($gallerySlots, ['name' => 'gallery', 'context' => 'program']); ?>
    </div>
    <div class="field" style="margin-top:16px">
      <label>แถบชื่อทับกลางรูป (galleryCaption)</label>
      <input name="galleryCaption" value="<?= htmlspecialchars($program['galleryCaption'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ถ้ำมรกต เกาะกระดาน เกาะแหวน เกาะเชือก"/>
    </div>
    <div class="field">
      <label>ใบอนุญาตนำเที่ยว</label>
      <input name="licenseNo" value="<?= htmlspecialchars($program['licenseNo'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="43/00540"/>
    </div>
  </div>

  <div class="card">
    <h2>โปรแกรมทัวร์ — คอลัมน์ซ้าย</h2>
    <div class="field" style="grid-column:1/-1">
      <label>ตารางเดินทาง (บรรทัดละ 1 รายการ — รูปแบบ เวลา|หัวข้อ|รายละเอียด)</label>
      <textarea name="itinerary" rows="10" placeholder="07:30|ท่าเรือปากเมง|ลูกค้าเจอกันที่ท่าเรือ&#10;09:30|ถ้ำมรกต|ลงเรือเดินทางสู่เกาะมุก"><?= htmlspecialchars(implode("\n", $itinLines), ENT_QUOTES, 'UTF-8') ?></textarea>
      <p class="field-hint">ว่างไว้จะสร้างจากจุดแวะอัตโนมัติ · ใช้ 2 ส่วน (เวลา|ข้อความ) หรือ 3 ส่วน (เวลา|หัวข้อ|รายละเอียด)</p>
    </div>
  </div>

  <div class="card">
    <h2>การ์ดเลือกซื้อ &amp; ราคา — คอลัมน์ขวา</h2>
    <div class="grid-2">
      <div class="field"><label>ราคาเด็ก (บาท)</label><input name="childPrice" type="number" value="<?= htmlspecialchars((string)($program['childPrice'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="ว่าง = คำนวณ ~74% ของผู้ใหญ่"/></div>
      <div class="field"><label>ค่ารับในเมือง (บาท/ท่าน)</label><input name="pickupSurcharge" type="number" value="<?= htmlspecialchars((string)($program['pickupSurcharge'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="200"/></div>
      <div class="field" style="grid-column:1/-1"><label>หมายเหตุราคา / จุดนัดพบ</label><input name="priceMeetingNote" value="<?= htmlspecialchars($program['priceMeetingNote'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="เจอกันที่ท่าเรือปากเมง · ผู้ใหญ่ 650 บาท · เด็ก 550 บาท"/></div>
      <div class="field" style="grid-column:1/-1"><label>ช่วงเวลาเปิดทัวร์</label><input name="season" value="<?= htmlspecialchars($program['season'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="1 ต.ค. – 31 พ.ค. · ออกเดินทางทุกวัน"/></div>
      <div class="field" style="grid-column:1/-1"><label>สิ่งที่รวมในแพ็กเกจ (บรรทัดละข้อ)</label><textarea name="inclusions" rows="6"><?= htmlspecialchars(implode("\n", $program['inclusions'] ?? []), ENT_QUOTES, 'UTF-8') ?></textarea></div>
      <?php if ($showInclusionsByDay): ?>
      <div class="field" style="grid-column:1/-1">
        <label>สิ่งที่รวมแยกตามวัน (แพ็ก 4 วัน 3 คืน)</label>
        <textarea name="inclusionsByDay" rows="12" placeholder="วันที่ 1&#10;ห้องพัก 3 คืน&#10;เช็กอินตามโปรแกรม&#10;&#10;วันที่ 2&#10;เรือนำเที่ยวทะเลตรัง"><?= htmlspecialchars($inclusionsByDayText, ENT_QUOTES, 'UTF-8') ?></textarea>
        <p class="field-hint">บรรทัดขึ้นต้นด้วย <strong>วันที่ N</strong> แล้วตามด้วยรายการของวันนั้น · เว้นบรรทัดว่างคั่นแต่ละวัน</p>
      </div>
      <?php endif; ?>
      <div class="field" style="grid-column:1/-1">
        <label>โรงแรม &amp; ประเภทห้อง (แพ็กหลายวัน)</label>
        <textarea name="hotels" rows="12" placeholder="โรงแรมชมตรัง&#10;ห้องดีลักซ์|2150&#10;ห้องสุพีเรีย|2250&#10;&#10;โรงแรมเมซอง&#10;ห้องสุพีเรีย|2250&#10;ห้องดีลักซ์|จอง"><?= htmlspecialchars($hotelsText, ENT_QUOTES, 'UTF-8') ?></textarea>
        <p class="field-hint">บรรทัดแรกของแต่ละโรงแรม = ชื่อโรงแรม · บรรทัดถัดไป = ชื่อห้อง|ราคาต่อท่าน (ตัวเลข) หรือ จอง · สูงสุด 5 ห้อง/โรงแรม · เว้นบรรทัดว่างคั่นแต่ละโรงแรม</p>
      </div>
      <div class="field" style="grid-column:1/-1"><label>หมายเหตุอายุ / เงื่อนไขราคา (บรรทัดละข้อ)</label><textarea name="priceNotes" rows="4"><?= htmlspecialchars(implode("\n", $program['priceNotes'] ?? []), ENT_QUOTES, 'UTF-8') ?></textarea></div>
      <div class="field" style="grid-column:1/-1"><label>ข้อความเตือน/เงื่อนไข (แถบล่างสุด)</label><textarea name="warning" rows="3"><?= htmlspecialchars($program['warning'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">บันทึก</button>
    <a class="btn btn-ghost" href="<?= htmlspecialchars($collectionMeta['listHref'], ENT_QUOTES, 'UTF-8') ?>">กลับรายการ</a>
    <?php if (!$isNew): ?>
      <a class="btn btn-ghost" href="../program.html?id=<?= urlencode($program['id'] ?? '') ?>" target="_blank" rel="noopener">ดูหน้ารายละเอียด</a>
      <a class="btn btn-ghost" href="../program.html?id=<?= urlencode($program['id'] ?? '') ?>#pkg-book" target="_blank" rel="noopener">ทดสอบจอง</a>
    <?php endif; ?>
  </div>
</form>

<?php tt_admin_footer(); ?>
