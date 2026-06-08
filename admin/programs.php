<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/package-collections.php';
tt_require_admin();

$data = tt_read_data();
$collections = tt_package_collections();

if (isset($_GET['delete'], $_GET['collection'])) {
    $collection = tt_package_collection_key($_GET['collection']);
    $id = trim((string)$_GET['delete']);
    if ($id !== '') {
        tt_package_delete($collection, $id, $data);
        tt_set_flash('ลบแพ็กเกจแล้ว');
    }
    header('Location: programs.php#' . ($collection === 'programs' ? 'daytrip' : ($collection === 'packages2d1n' ? 'overnight' : 'packages3d2n')));
    exit;
}

tt_admin_header('แพ็กเกจทัวร์', 'programs');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="card">
  <p class="field-hint" style="margin:0">จัดการการ์ดหน้าแรกและหน้ารายละเอียด <code>program.html</code> — แก้ไขเนื้อหา + รูปแกลเลอรี 6 ช่องได้ที่ปุ่ม 「แก้หน้ารายละเอียด」</p>
</div>

<?php
function tt_render_package_table(string $collection, array $items, string $sectionId, string $addLabel): void
{
    $collections = tt_package_collections();
    $meta = $collections[$collection];
    ?>
<section class="card admin-package-section" id="<?= htmlspecialchars($sectionId, ENT_QUOTES, 'UTF-8') ?>">
  <div class="admin-section-head">
    <div>
      <h2><?= htmlspecialchars($meta['label'], ENT_QUOTES, 'UTF-8') ?></h2>
      <p class="field-hint"><?= count($items) ?> แพ็กเกจ</p>
    </div>
    <a class="btn btn-primary btn-sm" href="program-edit.php?collection=<?= urlencode($collection) ?>">+ <?= htmlspecialchars($addLabel, ENT_QUOTES, 'UTF-8') ?></a>
  </div>
  <?php if (!$items): ?>
    <p class="field-hint">ยังไม่มีแพ็กเกจในหมวดนี้</p>
  <?php else: ?>
  <table class="table">
    <thead>
      <tr><th>ชื่อแพ็กเกจ</th><th>รหัส</th><th>เส้นทาง</th><th>ราคา</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($items as $p):
          $pid = $p['id'] ?? '';
          ?>
        <tr>
          <td><strong><?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></td>
          <td><?= ($p['packageCode'] ?? '') !== '' ? htmlspecialchars((string)$p['packageCode'], ENT_QUOTES, 'UTF-8') : '—' ?></td>
          <td><?= htmlspecialchars($p['route'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= number_format((int)($p['basePrice'] ?? 0)) ?> ฿</td>
          <td class="table-actions">
            <div class="table-actions-row">
              <a class="btn btn-primary btn-sm" href="program-edit.php?collection=<?= urlencode($collection) ?>&amp;id=<?= urlencode($pid) ?>" title="แก้หน้ารายละเอียด">แก้ไข</a>
              <a class="btn btn-ghost btn-sm" href="../program.html?id=<?= urlencode($pid) ?>" target="_blank" rel="noopener">ดู</a>
              <a class="btn btn-danger btn-sm" href="programs.php?collection=<?= urlencode($collection) ?>&amp;delete=<?= urlencode($pid) ?>" onclick="return confirm('ลบแพ็กเกจนี้?')">ลบ</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</section>
<?php
}

tt_render_package_table('programs', $data['programs'] ?? [], 'daytrip', 'เพิ่มไปเช้าเย็นกลับ');
tt_render_package_table('packages2d1n', $data['packages2d1n'] ?? [], 'overnight', 'เพิ่ม 2 วัน 1 คืน');
tt_render_package_table('packages3d2n', $data['packages3d2n'] ?? [], 'packages3d2n', 'เพิ่ม 3 วัน 2 คืน');
?>

<?php tt_admin_footer(); ?>
