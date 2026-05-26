<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
tt_require_admin();

$data = tt_read_data();
$site = $data['site'] ?? [];
$videos = $data['videos'] ?? [];
$idx = isset($_GET['i']) ? (int)$_GET['i'] : -1;
$video = ($idx >= 0 && isset($videos[$idx])) ? $videos[$idx] : null;
$isNew = $video === null;
$platform = ($video['platform'] ?? '') === 'facebook' ? 'facebook' : 'tiktok';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postIdx = isset($_POST['idx']) ? (int)$_POST['idx'] : -1;
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        tt_set_flash('กรุณาระบุชื่อวิดีโอ');
        header('Location: video-edit.php' . ($postIdx >= 0 ? '?i=' . $postIdx : ''));
        exit;
    }

    $vid = (int)($_POST['vid'] ?? 0);
    if ($vid <= 0) {
        $maxId = 0;
        foreach ($videos as $v) {
            $maxId = max($maxId, (int)($v['id'] ?? 0));
        }
        $vid = $maxId + 1;
    }

    $postPlatform = $_POST['platform'] ?? 'tiktok';
    $postPlatform = $postPlatform === 'facebook' ? 'facebook' : 'tiktok';
    $defaultUrl = $postPlatform === 'facebook'
        ? ($site['facebookUrl'] ?? '')
        : ($site['tiktokUrl'] ?? '');

    $item = [
        'id' => $vid,
        'platform' => $postPlatform,
        'thumb' => trim($_POST['thumb'] ?? ''),
        'title' => $title,
        'views' => trim($_POST['views'] ?? ''),
        'url' => trim($_POST['url'] ?? $defaultUrl),
    ];

    if ($postIdx >= 0 && isset($videos[$postIdx])) {
        $videos[$postIdx] = $item;
    } else {
        $videos[] = $item;
        $postIdx = count($videos) - 1;
    }

    $data['videos'] = $videos;
    tt_write_data($data);
    tt_set_flash('บันทึกวิดีโอแล้ว');
    header('Location: video-edit.php?i=' . $postIdx);
    exit;
}

$pageTitle = $isNew ? 'เพิ่มวิดีโอ' : 'แก้ไขวิดีโอ';
tt_admin_header($pageTitle, 'videos');
$flash = tt_flash();
if ($flash): ?><div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<form method="post">
  <div class="card">
    <h2>วิดีโอ Social (TikTok / Facebook)</h2>
    <input type="hidden" name="idx" value="<?= $isNew ? -1 : $idx ?>"/>

    <?php if (!empty($video['thumb'])): ?>
      <img src="<?= htmlspecialchars($video['thumb'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-width:240px;border-radius:10px;margin-bottom:16px;border:1px solid var(--adm-border)"/>
    <?php endif; ?>

    <div class="grid-2">
      <div class="field"><label>แพลตฟอร์ม</label>
        <select name="platform" id="video-platform">
          <option value="tiktok"<?= $platform === 'tiktok' ? ' selected' : '' ?>>TikTok</option>
          <option value="facebook"<?= $platform === 'facebook' ? ' selected' : '' ?>>Facebook</option>
        </select>
      </div>
      <div class="field"><label>ชื่อวิดีโอ</label><input name="title" required value="<?= htmlspecialchars($video['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
      <div class="field"><label>ID (ตัวเลข)</label><input name="vid" type="number" min="1" value="<?= (int)($video['id'] ?? 0) ?>" placeholder="ว่าง = สร้างอัตโนมัติ"/></div>
      <div class="field"><label>ยอดวิว (ข้อความ)</label><input name="views" value="<?= htmlspecialchars($video['views'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="128K"/></div>
      <div class="field" style="grid-column:1/-1"><label>ลิงก์คลิป</label><input name="url" value="<?= htmlspecialchars($video['url'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="วางลิงก์คลิปจาก Share → Copy link"/></div>
      <div class="field" style="grid-column:1/-1"><label>URL รูปปก (ถ้าไม่มีลิงก์คลิปเฉพาะ)</label><input name="thumb" value="<?= htmlspecialchars($video['thumb'] ?? '', ENT_QUOTES, 'UTF-8') ?>"/></div>
    </div>

    <p class="field-hint" id="video-url-hint">
      <strong>TikTok:</strong> ใช้ลิงก์คลิป เช่น <code>https://www.tiktok.com/@user/video/123...</code> (ไม่ใช่ลิงก์โปรไฟล์)<br/>
      <strong>Facebook:</strong> ใช้ลิงก์คลิป/Reels จากเพจ เช่น <code>facebook.com/.../videos/...</code> หรือ <code>fb.watch/...</code>
    </p>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">บันทึก</button>
      <a class="btn btn-ghost" href="videos.php">กลับ</a>
      <a class="btn btn-ghost" href="media.php">อัปโหลดรูป</a>
      <a class="btn btn-ghost" href="../videos.html" target="_blank">ดูหน้าวิดีโอ</a>
      <?php if (!$isNew): ?>
        <a class="btn btn-danger" href="videos.php?delete=<?= $idx ?>" onclick="return confirm('ลบวิดีโอนี้?')">ลบวิดีโอนี้</a>
      <?php endif; ?>
    </div>
  </div>
</form>

<?php tt_admin_footer(); ?>
