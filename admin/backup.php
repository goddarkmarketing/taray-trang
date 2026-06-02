<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $includeUploads = !empty($_POST['include_uploads']);
        $result = tt_backup_create($includeUploads);
        if ($result['ok']) {
            tt_set_flash('สำรองข้อมูลเรียบร้อยแล้ว');
        } else {
            tt_set_flash($result['error'] ?? 'สำรองไม่สำเร็จ ลองอีกครั้ง', 'error');
        }
        header('Location: backup.php');
        exit;
    }

    if ($action === 'restore') {
        $file = basename(trim($_POST['file'] ?? ''));
        $confirm = trim($_POST['confirm'] ?? '');
        if ($confirm !== 'กู้คืน') {
            tt_set_flash('พิมพ์คำว่า กู้คืน (ตามที่แสดง) เพื่อยืนยัน', 'error');
            header('Location: backup.php');
            exit;
        }
        $result = tt_backup_restore($file);
        if ($result['ok']) {
            tt_set_flash('กู้คืนข้อมูลเรียบร้อยแล้ว — เปิดหน้าเว็บแล้วกดรีเฟรชเพื่อดูผล');
        } else {
            tt_set_flash($result['error'] ?? 'กู้คืนไม่สำเร็จ', 'error');
        }
        header('Location: backup.php');
        exit;
    }

    if ($action === 'delete') {
        $file = basename(trim($_POST['file'] ?? ''));
        if (tt_backup_delete($file)) {
            tt_set_flash('ลบแบ็คอัพแล้ว');
        } else {
            tt_set_flash('ลบไม่สำเร็จ', 'error');
        }
        header('Location: backup.php');
        exit;
    }
}

$backups = tt_backup_list();
$hasZip = class_exists('ZipArchive');
$autoCount = count(array_filter($backups, fn($b) => $b['type'] === 'auto'));

tt_admin_header('สำรอง & กู้คืนข้อมูล', 'backup');
$flash = tt_flash();
$flashType = tt_flash_type();
if ($flash): ?>
  <div class="alert alert-<?= $flashType === 'error' ? 'error' : 'success' ?>"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="card backup-guide">
  <h2>สำรองข้อมูลคืออะไร?</h2>
  <p>เหมือน<strong>ถ่ายรูปหน้าจอเก็บไว้</strong> — เก็บข้อความ เบอร์โทร ราคา รูป และทุกอย่างที่แก้ในหลังบ้านไว้ชุดหนึ่ง ถ้าแก้ผิดหรือข้อมูลหาย สามารถ<strong>ย้อนกลับ</strong>ได้</p>

  <h3 style="margin-top:20px;font-size:1rem">ขั้นตอนใช้งาน (อ่านตามลำดับ)</h3>
  <ol class="backup-steps">
    <li><strong>ก่อนแก้เว็บครั้งใหญ่</strong> — กดปุ่มสีน้ำเงิน 「สำรองข้อมูลตอนนี้」 ด้านล่าง</li>
    <li><strong>เก็บไฟล์ไว้เครื่อง</strong> (แนะนำ) — ในตารางด้านล่าง กด 「ดาวน์โหลด」 เก็บใน Google Drive หรือคอมพิวเตอร์</li>
    <li><strong>แก้เว็บตามปกติ</strong> — ใช้เมนูอื่นในหลังบ้านได้เลย</li>
    <li><strong>ถ้าแก้ผิด</strong> — เลือกวันที่ต้องการในตาราง กด 「กู้คืน」 แล้วพิมพ์คำว่า <strong>กู้คืน</strong> เพื่อยืนยัน</li>
  </ol>
  <p class="field-hint" style="margin-top:12px">💡 ทุกครั้งที่กด 「บันทึก」 ในหลังบ้าน ระบบจะสำรองให้อัตโนมัติ (เก็บล่าสุด <?= (int)TT_BACKUP_MAX_AUTO ?> ชุด) — ไม่ต้องทำเองทุกครั้ง</p>
</div>

<div class="card backup-create">
  <div class="backup-create__head">
    <h2>สำรองข้อมูลตอนนี้</h2>
    <p class="field-hint">บันทึกข้อมูลทั้งหมดบนเว็บ ณ ขณะนี้</p>
  </div>

  <form method="post" class="backup-create__form">
    <input type="hidden" name="action" value="create"/>

    <div class="backup-create__includes">
      <span class="backup-create__includes-label">ข้อมูลที่จะเก็บ</span>
      <ul class="backup-create__tags">
        <li>เบอร์โทร & ติดต่อ</li>
        <li>โปรแกรมทัวร์</li>
        <li>บทความ</li>
        <li>รีวิว</li>
        <li>เมนู & ข้อความอื่นๆ</li>
      </ul>
    </div>

    <?php if ($hasZip): ?>
      <label class="backup-create__option">
        <input type="checkbox" name="include_uploads" value="1"/>
        <span class="backup-create__option-body">
          <strong>รวมรูปที่อัปโหลดในระบบด้วย</strong>
          <span class="field-hint">ไฟล์จะใหญ่ขึ้น — เหมาะตอนย้ายเว็บไปโฮสใหม่</span>
        </span>
      </label>
    <?php else: ?>
      <div class="backup-create__notice">โฮสนี้สำรองได้เฉพาะข้อความและข้อมูล — รูปแยกดาวน์โหลดจากเมนู 「อัปโหลดรูป」</div>
    <?php endif; ?>

    <div class="backup-create__actions">
      <button type="submit" class="btn btn-primary">สำรองข้อมูลตอนนี้</button>
    </div>
  </form>
</div>

<div class="card">
  <h2>ระบบสำรองให้อัตโนมัติ</h2>
  <p class="field-hint">ทุกครั้งที่กด 「บันทึก」 ในหน้าต่างๆ ของหลังบ้าน ระบบจะเก็บสำเนาไว้ให้ — ใช้ตอนกดบันทึกผิดหรือลบข้อมูลผิด</p>
  <p>ตอนนี้มีสำเนาอัตโนมัติ: <strong><?= $autoCount ?></strong> ชุด (เก็บได้สูงสุด <?= (int)TT_BACKUP_MAX_AUTO ?> ชุด)</p>
</div>

<div class="card">
  <h2>รายการที่สำรองไว้ (<?= count($backups) ?> ชุด)</h2>
  <?php if (!$backups): ?>
    <p class="field-hint">ยังไม่มีรายการ — กดปุ่ม 「สำรองข้อมูลตอนนี้」 ด้านบนก่อน</p>
  <?php else: ?>
  <p class="field-hint" style="margin-bottom:16px">เลือกวันที่ใกล้เคียงกับช่วงที่ต้องการย้อนกลับ</p>
  <table class="table">
    <thead>
      <tr>
        <th>วันเวลา</th>
        <th>แบบไหน</th>
        <th>ขนาดไฟล์</th>
        <th>ทำอะไรได้</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($backups as $b): ?>
        <tr>
          <td>
            <strong><?= htmlspecialchars($b['createdDisplay'], ENT_QUOTES, 'UTF-8') ?></strong>
            <?php if ($b['isZip']): ?><br><span class="field-hint">มีรูปรวมอยู่ด้วย</span><?php endif; ?>
          </td>
          <td><?= htmlspecialchars($b['label'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars(tt_backup_format_bytes($b['size']), ENT_QUOTES, 'UTF-8') ?></td>
          <td class="table-actions">
            <div class="table-actions-row">
              <a class="btn btn-ghost btn-sm" href="backup-download.php?file=<?= urlencode($b['file']) ?>" title="เก็บไฟล์ไว้คอมหรือ Google Drive">ดาวน์โหลด</a>
              <button type="button" class="btn btn-ghost btn-sm" data-restore="<?= htmlspecialchars($b['file'], ENT_QUOTES, 'UTF-8') ?>" data-date="<?= htmlspecialchars($b['createdDisplay'], ENT_QUOTES, 'UTF-8') ?>" title="ย้อนเว็บกลับไปตามวันนี้">กู้คืน</button>
              <form method="post" style="display:inline" onsubmit="return confirm('ลบรายการสำรองนี้? (เว็บปัจจุบันไม่เปลี่ยน)')">
                <input type="hidden" name="action" value="delete"/>
                <input type="hidden" name="file" value="<?= htmlspecialchars($b['file'], ENT_QUOTES, 'UTF-8') ?>"/>
                <button type="submit" class="btn btn-danger btn-sm">ลบ</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<div class="card" id="restore-panel" hidden>
  <h2>ยืนยันการกู้คืน (ย้อนกลับ)</h2>
  <p class="field-hint" style="color:#b45309">
    ข้อมูลบนเว็บ<strong>ตอนนี้</strong>จะถูกแทนที่ด้วนข้อมูลจากวันที่เลือก<br>
    ระบบจะสำรองข้อมูลปัจจุบันไว้ให้อีกชุดก่อนกู้คืน — ปลอดภัย
  </p>
  <form method="post">
    <input type="hidden" name="action" value="restore"/>
    <input type="hidden" name="file" id="restore-file" value=""/>
    <div class="field">
      <label>ย้อนกลับไปวันที่</label>
      <input id="restore-file-display" readonly/>
    </div>
    <div class="field">
      <label>พิมพ์คำว่า <strong>กู้คืน</strong> เพื่อยืนยัน (พิมพ์ภาษาไทย)</label>
      <input name="confirm" required placeholder="กู้คืน" autocomplete="off"/>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">ยืนยัน — กู้คืนข้อมูล</button>
      <button type="button" class="btn btn-ghost" id="restore-cancel">ยกเลิก</button>
    </div>
  </form>
</div>

<script>
(function () {
  const panel = document.getElementById('restore-panel');
  const fileInput = document.getElementById('restore-file');
  const fileDisplay = document.getElementById('restore-file-display');
  document.querySelectorAll('[data-restore]').forEach((btn) => {
    btn.addEventListener('click', () => {
      fileInput.value = btn.getAttribute('data-restore');
      fileDisplay.value = btn.getAttribute('data-date') || btn.getAttribute('data-restore');
      panel.hidden = false;
      panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });
  document.getElementById('restore-cancel')?.addEventListener('click', () => {
    panel.hidden = true;
  });
})();
</script>

<?php tt_admin_footer(); ?>
