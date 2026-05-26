<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$updated = $data['meta']['updated'] ?? '—';

tt_admin_header('ภาพรวม', 'dashboard');
$flash = tt_flash();
if ($flash): ?>
  <div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="stats">
  <div class="stat"><div class="stat-num"><?= count($data['boats'] ?? []) ?></div><div class="stat-label">ประเภทเรือ</div></div>
  <div class="stat"><div class="stat-num"><?= count($data['programs'] ?? []) ?></div><div class="stat-label">โปรแกรม</div></div>
  <div class="stat"><div class="stat-num"><?= count($data['articles'] ?? []) ?></div><div class="stat-label">บทความ</div></div>
  <div class="stat"><div class="stat-num"><?= count($data['reviews'] ?? []) ?></div><div class="stat-label">รีวิว</div></div>
</div>

<div class="card">
  <h2>ข้อมูลล่าสุด</h2>
  <p>อัปเดตล่าสุด: <strong><?= htmlspecialchars($updated, ENT_QUOTES, 'UTF-8') ?></strong></p>
  <p class="field-hint">การบันทึกจากหลังบ้านจะอัปเดต <code>data/site.json</code> และ <code>assets/js/data-fallback.js</code> ทันที</p>
  <div class="form-actions" style="border:0;padding-top:8px">
    <a class="btn btn-primary" href="../index.html" target="_blank">เปิดหน้าเว็บ</a>
    <a class="btn btn-ghost" href="site.php">แก้ข้อมูลเว็บ</a>
    <a class="btn btn-ghost" href="articles.php">จัดการบทความ</a>
  </div>
</div>

<div class="card">
  <h2>แก้ไขตามส่วน</h2>
  <p class="field-hint" style="margin-bottom:16px">เลือกเมนูด้านซ้ายเพื่อแก้ไขแต่ละส่วนของเว็บ</p>
  <div class="grid-2">
    <?php foreach (tt_admin_nav() as $item): if ($item['id'] === 'dashboard') continue; ?>
      <a class="btn btn-ghost" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" style="justify-content:flex-start">
        <span class="admin-nav-icon"><?= tt_admin_icon($item['id']) ?></span>
        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<?php tt_admin_footer(); ?>
