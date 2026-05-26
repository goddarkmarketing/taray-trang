<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$reviews = $data['reviews'] ?? [];

if (isset($_GET['delete']) && $_GET['delete'] !== '') {
    $idx = (int)$_GET['delete'];
    if (isset($reviews[$idx])) {
        array_splice($reviews, $idx, 1);
        $data['reviews'] = array_values($reviews);
        tt_write_data($data);
        tt_set_flash('ลบรีวิวแล้ว');
    }
    header('Location: reviews.php');
    exit;
}

function tt_review_excerpt(string $text, int $len = 60): string
{
    $text = trim($text);
    if (mb_strlen($text) <= $len) {
        return $text;
    }
    return mb_substr($text, 0, $len) . '…';
}

tt_admin_header('รีวิวลูกค้า', 'reviews');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="form-actions" style="border:0;margin-bottom:16px;padding:0">
  <a class="btn btn-primary" href="review-edit.php">+ เพิ่มรีวิว</a>
</div>

<div class="card">
  <?php if (!$reviews): ?>
    <p class="field-hint">ยังไม่มีรีวิว — กดปุ่มด้านบนเพื่อเพิ่มรายการแรก</p>
  <?php else: ?>
  <table class="table">
    <thead>
      <tr><th>ลูกค้า</th><th>ทริป</th><th>จาก</th><th>คะแนน</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($reviews as $i => $r): ?>
        <tr>
          <td>
            <strong><?= htmlspecialchars($r['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
            <?php if (!empty($r['initial'])): ?>
              <br><small class="field-hint"><?= htmlspecialchars($r['initial'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars(tt_review_excerpt($r['text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($r['trip'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($r['from'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= (int)($r['rating'] ?? 5) ?> ★</td>
          <td class="table-actions">
            <div class="table-actions-row">
              <a class="btn btn-ghost btn-sm" href="review-edit.php?i=<?= $i ?>">แก้ไข</a>
              <a class="btn btn-danger btn-sm" href="reviews.php?delete=<?= $i ?>" onclick="return confirm('ลบรีวิวนี้?')">ลบ</a>
              <a class="btn btn-ghost btn-sm" href="../index.html#home-reviews" target="_blank">ดู</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php tt_admin_footer(); ?>
