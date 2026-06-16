<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/boat-booking-helpers.php';
require_once __DIR__ . '/includes/boat-booking-layout.php';
require_once __DIR__ . '/includes/image-field.php';
tt_require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = tt_read_data();
    $bb = tt_bb_get($data);
    $bb['routes'] = tt_bb_parse_routes_post($_POST['routes'] ?? []);
    tt_bb_save($bb);
    tt_set_flash('บันทึกเส้นทางแล้ว');
    header('Location: boat-booking-routes.php');
    exit;
}

$data = tt_read_data();
$bb = tt_bb_get($data);
$routes = $bb['routes'] ?? [];
$routeRows = tt_bb_repeater_blank($routes, ['id' => '', 'name' => '', 'subtitle' => '', 'image' => '']);

tt_bb_page_start('② เส้นทางเดินเรือ', 'routes');
?>

<div class="card">
  <h2><span class="adm-step-badge adm-step-badge--lg">2</span> เส้นทางเดินเรือ</h2>
  <p class="field-hint">ขั้นตอนบนเว็บ: <strong>เลือกเส้นทาง</strong> — เพิ่มแถวว่างด้านล่างเพื่อเพิ่มเส้นทางใหม่</p>
  <form method="post">
    <div class="table-wrap">
      <table class="table table-form adm-routes-table">
        <thead>
          <tr><th class="col-id">ID</th><th class="col-name">ชื่อ</th><th class="col-subtitle">คำบรรยายย่อย</th><th class="col-image">URL รูป</th></tr>
        </thead>
        <tbody>
          <?php foreach ($routeRows as $i => $r): ?>
          <tr>
            <td class="col-id"><input name="routes[<?= $i ?>][id]" value="<?= htmlspecialchars((string) ($r['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="r1"/></td>
            <td class="col-name"><input name="routes[<?= $i ?>][name]" value="<?= htmlspecialchars((string) ($r['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="เส้นทางที่ 1"/></td>
            <td class="col-subtitle"><input name="routes[<?= $i ?>][subtitle]" value="<?= htmlspecialchars((string) ($r['subtitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"/></td>
            <td class="col-image"><?php tt_render_image_url_row("routes[{$i}][image]", (string) ($r['image'] ?? '')); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php tt_bb_form_actions('บันทึกเส้นทาง'); ?>
  </form>
</div>

<?php tt_admin_footer(); ?>
