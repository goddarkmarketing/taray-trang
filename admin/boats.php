<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$boats = $data['boats'] ?? [];
$boatsHead = tt_home_sections($data)['boats'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'section_heading') {
    $sections = $data['homeSections'] ?? [];
    if (!is_array($sections)) {
        $sections = [];
    }
    $sections['boats'] = [
        'eyebrow' => trim($_POST['boats_eyebrow'] ?? ''),
        'title' => trim($_POST['boats_title'] ?? ''),
        'lead' => trim($_POST['boats_lead'] ?? ''),
    ];
    $data['homeSections'] = $sections;
    if (tt_write_data($data)) {
        tt_set_flash('บันทึกหัวข้อส่วนประเภทเรือบนหน้าแรกแล้ว');
    } else {
        tt_set_flash('บันทึกไม่สำเร็จ', 'error');
    }
    header('Location: boats.php');
    exit;
}

if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $id = $_GET['delete'];
    $boats = array_values(array_filter($boats, fn($b) => ($b['id'] ?? '') !== $id));
    $data['boats'] = $boats;
    tt_write_data($data);
    tt_set_flash('ลบประเภทเรือแล้ว');
    header('Location: boats.php');
    exit;
}

tt_admin_header('ประเภทเรือ', 'boats');
$flash = tt_flash();
$flashType = tt_flash_type();
if ($flash): ?>
  <div class="alert alert-<?= $flashType === 'error' ? 'error' : 'success' ?>"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form method="post" class="card" style="margin-bottom:16px">
  <input type="hidden" name="action" value="section_heading"/>
  <h2>หัวข้อส่วนประเภทเรือบนหน้าแรก</h2>
  <p class="field-hint">แก้เฉพาะข้อความด้านบนการ์ดเรือบนหน้าแรก (ไม่ใช่รายการเรือด้านล่าง)</p>
  <div class="grid-2">
    <div class="field">
      <label>ข้อความเล็กด้านบน (Eyebrow)</label>
      <input name="boats_eyebrow" value="<?= htmlspecialchars($boatsHead['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ประเภทเรือ"/>
    </div>
    <div class="field">
      <label>หัวข้อหลัก (H2)</label>
      <input name="boats_title" value="<?= htmlspecialchars($boatsHead['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="เลือกเรือให้เหมาะกับทริปของคุณ"/>
    </div>
    <div class="field" style="grid-column:1/-1">
      <label>คำอธิบาย (Lead)</label>
      <textarea name="boats_lead" rows="2" placeholder="มีให้เลือก 3 ประเภท..."><?= htmlspecialchars($boatsHead['lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
  </div>
  <div class="form-actions" style="border:0;padding-top:12px">
    <button type="submit" class="btn btn-primary">บันทึกหัวข้อหน้าแรก</button>
    <a class="btn btn-ghost" href="../index.html#boats" target="_blank" rel="noopener">ดูบนหน้าแรก ↗</a>
  </div>
</form>

<div class="form-actions" style="border:0;margin-bottom:16px;padding:0">
  <a class="btn btn-primary" href="boat-edit.php">+ เพิ่มประเภทเรือ</a>
</div>

<div class="card">
  <?php if (!$boats): ?>
    <p class="field-hint">ยังไม่มีประเภทเรือ — กดปุ่มด้านบนเพื่อเพิ่มรายการแรก</p>
  <?php else: ?>
  <table class="table">
    <thead>
      <tr><th>ชื่อเรือ</th><th>Tag</th><th>ความจุ</th><th>ราคาเริ่มต้น</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($boats as $b): ?>
        <tr>
          <td>
            <strong><?= htmlspecialchars($b['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
            <small class="field-hint"><?= htmlspecialchars($b['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
          </td>
          <td><?= htmlspecialchars($b['tag'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($b['capacity'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= number_format((int)($b['basePrice'] ?? 0)) ?> บาท</td>
          <td class="table-actions">
            <div class="table-actions-row">
              <a class="btn btn-ghost btn-sm" href="boat-edit.php?id=<?= urlencode($b['id'] ?? '') ?>">แก้ไข</a>
              <a class="btn btn-danger btn-sm" href="boats.php?delete=<?= urlencode($b['id'] ?? '') ?>" onclick="return confirm('ลบประเภทเรือนี้?')">ลบ</a>
              <a class="btn btn-ghost btn-sm" href="../boats.html#<?= urlencode($b['id'] ?? '') ?>" target="_blank">ดู</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php tt_admin_footer(); ?>
