<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/frontend-icons.php';
require_once __DIR__ . '/includes/boat-booking-helpers.php';
require_once __DIR__ . '/includes/boat-booking-layout.php';
tt_require_admin();

$iconOptions = ['cutlery', 'users', 'camera', 'scuba', 'carPick', 'compass', 'plus', 'anchor', 'chatBubble'];

$data = tt_read_data();
$boatId = trim($_GET['boat'] ?? $_POST['boat'] ?? '');
$boatIds = tt_bb_boat_ids($data);
if ($boatId === '' || !in_array($boatId, $boatIds, true)) {
    tt_set_flash('ไม่พบประเภทเรือ');
    header('Location: boat-booking-profiles.php');
    exit;
}

$bb = tt_bb_get($data);
$routes = $bb['routes'] ?? [];
$peopleTiers = $bb['peopleTiers'] ?? [];
$profile = $bb['profiles'][$boatId] ?? [];
$isSizeMode = ($profile['selectionMode'] ?? '') === 'size';
$sizes = $profile['sizes'] ?? [];
$charterPrices = $profile['charterPrices'] ?? ($bb['charterPrices'][$boatId] ?? []);
$profileAddons = $profile['addons'] ?? [];
$routeIds = $profile['routeIds'] ?? [];
$tierRules = $profile['tierRules'] ?? [];
$guideCounts = $profile['guideCountByTier'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = tt_read_data();
    $bb = tt_bb_get($data);
    $profile = [];

    $profile['selectionMode'] = trim($_POST['selectionMode'] ?? 'people') === 'size' ? 'size' : 'people';
    foreach (['tierProgressLabel', 'tierStepTitle', 'tierStepDesc', 'charterLabel', 'charterSummaryPrefix', 'includedBoxTitle', 'footerNote'] as $k) {
        $v = trim($_POST[$k] ?? '');
        if ($v !== '') {
            $profile[$k] = $v;
        }
    }
    $profile['insurancePerPerson'] = (int) ($_POST['insurancePerPerson'] ?? ($bb['insurancePerPerson'] ?? 50));
    $profile['guideFee'] = (int) ($_POST['guideFee'] ?? 0);
    $profile['safetyStaffRate'] = (int) ($_POST['safetyStaffRate'] ?? 0);
    $profile['safetyStaffRatio'] = max(1, (int) ($_POST['safetyStaffRatio'] ?? 20));
    $profile['askPax'] = isset($_POST['askPax']);

    $sizes = tt_bb_parse_sizes_post($_POST['sizes'] ?? []);
    if ($profile['selectionMode'] === 'size' && $sizes) {
        $profile['sizes'] = $sizes;
    }

    $matrix = tt_bb_parse_charter_matrix_post($_POST['charter_prices'] ?? []);
    if ($matrix) {
        $profile['charterPrices'] = $matrix;
    }

    $guideCounts = tt_bb_parse_guide_counts_post($_POST['guideCountByTier'] ?? []);
    if ($guideCounts) {
        $profile['guideCountByTier'] = $guideCounts;
    }

    $addons = tt_bb_parse_addons_post($_POST['addons'] ?? []);
    if ($addons) {
        $profile['addons'] = $addons;
    }

    $included = tt_parse_lines($_POST['includedItems'] ?? '');
    if ($included) {
        $profile['includedItems'] = $included;
    }

    $selectedRoutes = array_values(array_filter(array_map('trim', $_POST['routeIds'] ?? [])));
    if ($selectedRoutes) {
        $profile['routeIds'] = $selectedRoutes;
    }

    $tierRules = tt_bb_parse_tier_rules_post($_POST['tierRules'] ?? []);
    if ($tierRules) {
        $profile['tierRules'] = $tierRules;
    }

    $bb['profiles'] = $bb['profiles'] ?? [];
    $bb['profiles'][$boatId] = $profile;

    if ($profile['selectionMode'] !== 'size' && $matrix) {
        $bb['charterPrices'] = $bb['charterPrices'] ?? [];
        $bb['charterPrices'][$boatId] = $matrix;
    }

    $data['boatBooking'] = $bb;
    tt_write_data($data);
    tt_set_flash('บันทึกโปรไฟล์จองเรือแล้ว');
    header('Location: boat-booking-profile-edit.php?boat=' . urlencode($boatId));
    exit;
}

$bb = tt_bb_get($data);
$profile = $bb['profiles'][$boatId] ?? [];
$isSizeMode = ($profile['selectionMode'] ?? '') === 'size';
$sizes = $profile['sizes'] ?? [];
if (!$sizes && $isSizeMode) {
    $sizes = [['id' => '', 'label' => '', 'capacity' => 0, 'capacityLabel' => '']];
}
$sizes[] = ['id' => '', 'label' => '', 'capacity' => 0, 'capacityLabel' => ''];
$charterPrices = $profile['charterPrices'] ?? ($bb['charterPrices'][$boatId] ?? []);
$profileAddons = $profile['addons'] ?? [];
$profileAddons[] = ['id' => '', 'icon' => 'plus', 'label' => '', 'desc' => '', 'price' => 0, 'unit' => 'flat', 'priceLabel' => ''];
$routeIds = $profile['routeIds'] ?? [];
$tierRules = $profile['tierRules'] ?? [];
$guideCounts = $profile['guideCountByTier'] ?? [];

$tierCols = $isSizeMode ? $sizes : $peopleTiers;
$tierCols = array_values(array_filter($tierCols, static fn($t) => ($t['id'] ?? '') !== ''));
$routeList = $routeIds
    ? array_values(array_filter($routes, static fn($r) => in_array($r['id'] ?? '', $routeIds, true)))
    : $routes;
if (!$routeList) {
    $routeList = $routes;
}

$boatName = $boatId;
foreach ($data['boats'] ?? [] as $b) {
    if (($b['id'] ?? '') === $boatId) {
        $boatName = (string) ($b['name'] ?? $boatId);
        break;
    }
}

$pageTitle = 'โปรไฟล์จอง: ' . $boatName;
tt_admin_header($pageTitle, 'boat-booking-profiles');
tt_bb_render_flash();
?>

<div class="card admin-preview-bar adm-bb-profile-bar">
  <a class="btn btn-ghost btn-sm" href="boat-booking-profiles.php">← รายการโปรไฟล์</a>
  <a class="btn btn-ghost btn-sm" href="../boat-book.html?boat=<?= urlencode($boatId) ?>" target="_blank" rel="noopener">ทดสอบจอง <?= htmlspecialchars($boatName, ENT_QUOTES, 'UTF-8') ?> ↗</a>
</div>

<form method="post" class="admin-package-form">
  <input type="hidden" name="boat" value="<?= htmlspecialchars($boatId, ENT_QUOTES, 'UTF-8') ?>"/>

  <div class="card">
    <h2>โหมดการเลือก &amp; ข้อความ</h2>
    <div class="grid-2">
      <div class="field">
        <label>โหมดเลือก</label>
        <select name="selectionMode">
          <option value="people"<?= !$isSizeMode ? ' selected' : '' ?>>ช่วงจำนวนคน (people)</option>
          <option value="size"<?= $isSizeMode ? ' selected' : '' ?>>ขนาดเรือ (size)</option>
        </select>
      </div>
      <div class="field">
        <label>ประกันอุบัติเหตุ (บาท/ท่าน)</label>
        <input name="insurancePerPerson" type="number" min="0" value="<?= (int) ($profile['insurancePerPerson'] ?? $bb['insurancePerPerson'] ?? 50) ?>"/>
      </div>
      <div class="field"><label>ป้ายขั้นตอน (progress)</label><input name="tierProgressLabel" value="<?= htmlspecialchars((string) ($profile['tierProgressLabel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="จำนวนคน / ขนาดเรือ"/></div>
      <div class="field"><label>หัวข้อขั้นเลือก</label><input name="tierStepTitle" value="<?= htmlspecialchars((string) ($profile['tierStepTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1"><label>คำอธิบายขั้นเลือก</label><textarea name="tierStepDesc" rows="2"><?= htmlspecialchars((string) ($profile['tierStepDesc'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></div>
      <div class="field"><label>ป้ายราคาเรือ</label><input name="charterLabel" value="<?= htmlspecialchars((string) ($profile['charterLabel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>คำนำหน้าสรุปราคาเรือ</label><input name="charterSummaryPrefix" value="<?= htmlspecialchars((string) ($profile['charterSummaryPrefix'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>หัวข้อกล่องรวมในราคา</label><input name="includedBoxTitle" value="<?= htmlspecialchars((string) ($profile['includedBoxTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field" style="grid-column:1/-1">
        <label><input type="checkbox" name="askPax" value="1"<?= ($profile['askPax'] ?? true) ? ' checked' : '' ?>/> ให้กรอกจำนวนผู้โดยสารจริง (คำนวณประกัน/ต่อท่าน)</label>
      </div>
      <div class="field" style="grid-column:1/-1"><label>หมายเหตุท้ายสรุป</label><textarea name="footerNote" rows="2"><?= htmlspecialchars((string) ($profile['footerNote'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></div>
      <div class="field" style="grid-column:1/-1"><label>รายการรวมในราคาเรือ (บรรทัดละข้อ)</label><textarea name="includedItems" rows="4" placeholder="อุปกรณ์ดำน้ำ&#10;น้ำดื่ม"><?= htmlspecialchars(implode("\n", $profile['includedItems'] ?? []), ENT_QUOTES, 'UTF-8') ?></textarea></div>
    </div>
  </div>

  <div class="card">
    <h2>ค่าบังคับ (มัคคุเทศก์ / สต๊าฟ)</h2>
    <div class="grid-2">
      <div class="field"><label>ค่ามัคคุเทศก์ (บาท/คน)</label><input name="guideFee" type="number" min="0" value="<?= (int) ($profile['guideFee'] ?? 0) ?>"/></div>
      <div class="field"><label>สต๊าฟความปลอดภัย (บาท/คน)</label><input name="safetyStaffRate" type="number" min="0" value="<?= (int) ($profile['safetyStaffRate'] ?? 0) ?>"/></div>
      <div class="field"><label>อัตราสต๊าฟ (คนลูกค้าต่อสต๊าฟ 1)</label><input name="safetyStaffRatio" type="number" min="1" value="<?= (int) ($profile['safetyStaffRatio'] ?? 20) ?>"/></div>
    </div>
    <?php if ($isSizeMode && $sizes): ?>
    <p class="field-hint" style="margin-top:12px">จำนวนมัคคุเทศก์ตามขนาดเรือ (ว่าง = 1 คน)</p>
    <table class="table table-form">
      <thead><tr><th>ขนาด (ID)</th><th>จำนวนมัคคุเทศก์</th></tr></thead>
      <tbody>
        <?php foreach (array_slice($sizes, 0, -1) as $s):
          $sid = (string) ($s['id'] ?? '');
          if ($sid === '') continue;
        ?>
        <tr>
          <td><?= htmlspecialchars((string) ($s['label'] ?? $sid), ENT_QUOTES, 'UTF-8') ?> <small>(<?= htmlspecialchars($sid, ENT_QUOTES, 'UTF-8') ?>)</small></td>
          <td><input name="guideCountByTier[<?= htmlspecialchars($sid, ENT_QUOTES, 'UTF-8') ?>]" type="number" min="1" value="<?= (int) ($guideCounts[$sid] ?? 1) ?>" style="width:100px"/></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

  <?php if ($isSizeMode): ?>
  <div class="card">
    <h2>ขนาดเรือ</h2>
    <table class="table table-form">
      <thead><tr><th>ID</th><th>ชื่อ</th><th>จุคน (ตัวเลข)</th><th>ข้อความจุ</th></tr></thead>
      <tbody>
        <?php foreach ($sizes as $i => $s): ?>
        <tr>
          <td><input name="sizes[<?= $i ?>][id]" value="<?= htmlspecialchars((string) ($s['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="s1 / t1"/></td>
          <td><input name="sizes[<?= $i ?>][label]" value="<?= htmlspecialchars((string) ($s['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"/></td>
          <td><input name="sizes[<?= $i ?>][capacity]" type="number" min="1" value="<?= (int) ($s['capacity'] ?? 0) ?>"/></td>
          <td><input name="sizes[<?= $i ?>][capacityLabel]" value="<?= htmlspecialchars((string) ($s['capacityLabel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="จุ 30 คน"/></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <div class="card">
    <h2>เส้นทางที่ใช้ได้</h2>
    <p class="field-hint">ไม่เลือก = แสดงทุกเส้นทาง · เลือกเฉพาะเรือหางยาวที่มี 5 เส้นทาง</p>
    <div class="grid-2">
      <?php foreach ($routes as $r):
        $rid = (string) ($r['id'] ?? '');
      ?>
      <label class="field" style="display:flex;align-items:center;gap:8px">
        <input type="checkbox" name="routeIds[]" value="<?= htmlspecialchars($rid, ENT_QUOTES, 'UTF-8') ?>"<?= in_array($rid, $routeIds, true) ? ' checked' : '' ?>/>
        <?= htmlspecialchars((string) ($r['name'] ?? $rid), ENT_QUOTES, 'UTF-8') ?>
      </label>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if (!$isSizeMode && $routes): ?>
  <div class="card">
    <h2>กฎช่วงคนเฉพาะเส้นทาง</h2>
    <p class="field-hint">ซ่อนช่วง (เช่น p4) หรือจำกัดสูงสุดในช่วง (เช่น 9–12 สูงสุด 10 คน)</p>
    <table class="table table-form">
      <thead><tr><th>เส้นทาง</th><th>ซ่อนช่วง (คั่น ,)</th><th>จำกัดสูงสุด (tierId=จำนวน)</th></tr></thead>
      <tbody>
        <?php foreach ($routes as $r):
          $rid = (string) ($r['id'] ?? '');
          $rule = $tierRules[$rid] ?? [];
          $hide = implode(', ', $rule['hideTiers'] ?? []);
          $caps = $rule['tierCaps'] ?? [];
        ?>
        <tr>
          <td><?= htmlspecialchars((string) ($r['name'] ?? $rid), ENT_QUOTES, 'UTF-8') ?></td>
          <td><input name="tierRules[<?= htmlspecialchars($rid, ENT_QUOTES, 'UTF-8') ?>][hideTiers]" value="<?= htmlspecialchars($hide, ENT_QUOTES, 'UTF-8') ?>" placeholder="p4"/></td>
          <td>
            <?php foreach ($peopleTiers as $t):
              $tid = (string) ($t['id'] ?? '');
            ?>
            <span style="display:inline-flex;align-items:center;gap:4px;margin-right:8px">
              <small><?= htmlspecialchars($tid, ENT_QUOTES, 'UTF-8') ?></small>
              <input name="tierRules[<?= htmlspecialchars($rid, ENT_QUOTES, 'UTF-8') ?>][tierCaps][<?= htmlspecialchars($tid, ENT_QUOTES, 'UTF-8') ?>]" type="number" min="0" value="<?= isset($caps[$tid]) ? (int) $caps[$tid] : '' ?>" placeholder="—" style="width:70px"/>
            </span>
            <?php endforeach; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <div class="card">
    <h2>ราคาเหมาลำ (เส้นทาง × <?= $isSizeMode ? 'ขนาดเรือ' : 'ช่วงคน' ?>)</h2>
    <?php if (!$tierCols): ?>
      <p class="field-hint">กรุณากำหนด<?= $isSizeMode ? 'ขนาดเรือ' : 'ช่วงจำนวนคน' ?>ก่อน</p>
    <?php else: ?>
    <div class="table-wrap" style="overflow-x:auto">
      <table class="table table-form adm-charter-matrix">
        <thead>
          <tr>
            <th>เส้นทาง</th>
            <?php foreach ($tierCols as $t): ?>
              <th><?= htmlspecialchars((string) ($t['label'] ?? $t['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($routeList as $r):
            $rid = (string) ($r['id'] ?? '');
          ?>
          <tr>
            <td><strong><?= htmlspecialchars((string) ($r['name'] ?? $rid), ENT_QUOTES, 'UTF-8') ?></strong></td>
            <?php foreach ($tierCols as $t):
              $tid = (string) ($t['id'] ?? '');
              if ($tid === '') continue;
              $val = $charterPrices[$rid][$tid] ?? '';
            ?>
            <td><input name="charter_prices[<?= htmlspecialchars($rid, ENT_QUOTES, 'UTF-8') ?>][<?= htmlspecialchars($tid, ENT_QUOTES, 'UTF-8') ?>]" type="number" min="0" value="<?= $val !== '' ? (int) $val : '' ?>" placeholder="—"/></td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>บริการเสริม (เฉพาะเรือนี้)</h2>
    <p class="field-hint">ว่างทั้งหมด = ใช้บริการเสริมทั่วไปจากหน้าจองเรือเหมาลำ</p>
    <div class="adm-repeater">
      <?php foreach ($profileAddons as $i => $a): ?>
      <article class="adm-repeater-card">
        <div class="adm-repeater-grid">
          <div class="field">
            <label>ID</label>
            <input name="addons[<?= $i ?>][id]" value="<?= htmlspecialchars((string) ($a['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="menu-park"/>
          </div>
          <div class="field">
            <label>ไอคอน</label>
            <?php tt_icon_select("addons[{$i}][icon]", $iconOptions, (string) ($a['icon'] ?? 'plus')); ?>
          </div>
          <div class="field">
            <label>ราคา (บาท)</label>
            <input name="addons[<?= $i ?>][price]" type="number" min="0" value="<?= (int) ($a['price'] ?? 0) ?>"/>
          </div>
          <div class="field">
            <label>หน่วย</label>
            <select name="addons[<?= $i ?>][unit]">
              <option value="flat"<?= ($a['unit'] ?? 'flat') === 'flat' ? ' selected' : '' ?>>ต่อลำ</option>
              <option value="perPerson"<?= ($a['unit'] ?? '') === 'perPerson' ? ' selected' : '' ?>>ต่อท่าน</option>
            </select>
          </div>
          <div class="field span-2">
            <label>ชื่อที่แสดง</label>
            <input name="addons[<?= $i ?>][label]" value="<?= htmlspecialchars((string) ($a['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"/>
          </div>
          <div class="field span-2">
            <label>ข้อความราคา</label>
            <input name="addons[<?= $i ?>][priceLabel]" value="<?= htmlspecialchars((string) ($a['priceLabel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="40 บาท/ท่าน"/>
          </div>
          <div class="field span-full">
            <label>คำอธิบาย</label>
            <input name="addons[<?= $i ?>][desc]" value="<?= htmlspecialchars((string) ($a['desc'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"/>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary">บันทึกโปรไฟล์</button>
    <a class="btn btn-ghost" href="boat-booking-profiles.php">กลับ</a>
    <a class="btn btn-ghost" href="boat-edit.php?id=<?= urlencode($boatId) ?>">แก้การ์ดเรือ (หน้า boats)</a>
  </div>
</form>

<?php tt_admin_footer(); ?>
