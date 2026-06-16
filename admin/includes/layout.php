<?php
declare(strict_types=1);

require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/page-help.php';
require_once __DIR__ . '/boat-booking-layout.php';

function tt_admin_nav(): array
{
    $nav = [
        ['id' => 'dashboard', 'label' => 'ภาพรวม', 'href' => 'index.php'],
        ['id' => 'site', 'label' => 'ข้อมูลเว็บ', 'href' => 'site.php'],
        ['id' => 'nav', 'label' => 'เมนูนำทาง', 'href' => 'nav.php'],
        ['id' => 'hero', 'label' => 'แบนเนอร์หน้าแรก', 'href' => 'hero.php'],
        ['id' => 'services', 'label' => 'บริการ (Pills)', 'href' => 'services.php'],
        ['id' => 'boats', 'label' => 'ประเภทเรือ', 'href' => 'boats.php'],
        ['id' => 'boat-booking', 'label' => 'จองเรือเหมาลำ', 'href' => 'boat-booking-profiles.php', 'children' => tt_bb_sidebar_children()],
        ['id' => 'programs', 'label' => 'แพ็กเกจทัวร์', 'href' => 'programs.php'],
        ['id' => 'options', 'label' => 'ตัวเลือกจอง', 'href' => 'options.php'],
        ['id' => 'reviews', 'label' => 'รีวิว', 'href' => 'reviews.php'],
        ['id' => 'videos', 'label' => 'วิดีโอ', 'href' => 'videos.php'],
        ['id' => 'why', 'label' => 'จุดเด่น', 'href' => 'why.php'],
        ['id' => 'steps', 'label' => 'ขั้นตอนจอง', 'href' => 'steps.php'],
        ['id' => 'articles', 'label' => 'บทความ', 'href' => 'articles.php'],
        ['id' => 'about', 'label' => 'หน้าเกี่ยวกับเรา', 'href' => 'about.php'],
        ['id' => 'section-headings', 'label' => 'หัวข้อ Section', 'href' => 'section-headings.php'],
        ['id' => 'deals', 'label' => 'การ์ดหน้าแรก', 'href' => 'deals.php'],
        ['id' => 'seo', 'label' => 'SEO / Meta', 'href' => 'seo.php'],
        ['id' => 'images', 'label' => 'รูปภาพ (Registry)', 'href' => 'images.php'],
        ['id' => 'media', 'label' => 'อัปโหลดรูป', 'href' => 'media.php'],
        ['id' => 'backup', 'label' => 'สำรอง & กู้คืน', 'href' => 'backup.php'],
        ['id' => 'password', 'label' => 'เปลี่ยนรหัสผ่าน', 'href' => 'password.php'],
    ];

    return $nav;
}

/** @param list<array{id: string, href: string, label: string}> $children */
function tt_admin_nav_group_active(array $children, string $active): bool
{
    foreach ($children as $child) {
        if (($child['id'] ?? '') === $active) {
            return true;
        }
    }
    return false;
}

function tt_admin_header(string $title, string $active = ''): void
{
    $nav = tt_admin_nav();
    $user = htmlspecialchars($_SESSION['tt_admin_user'] ?? 'admin', ENT_QUOTES, 'UTF-8');
    ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> — Talay Trang Admin</title>
  <link rel="stylesheet" href="assets/admin.css"/>
</head>
<body class="admin-body">
  <aside class="admin-sidebar">
    <a class="admin-brand" href="index.php">Talay Trang<br><small>ระบบหลังบ้าน</small></a>
    <nav class="admin-nav">
      <?php foreach ($nav as $item):
        $children = $item['children'] ?? [];
        $hasChildren = $children !== [];
        $groupActive = $hasChildren && ($item['id'] === $active || tt_admin_nav_group_active($children, $active));
        if ($hasChildren): ?>
      <div class="admin-nav-group is-open">
        <a class="admin-nav-link admin-nav-parent<?= $item['id'] === $active ? ' is-active' : ($groupActive ? ' is-parent-active' : '') ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>">
          <span class="admin-nav-icon"><?= tt_admin_icon($item['id']) ?></span>
          <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
        </a>
        <div class="admin-nav-children">
          <?php foreach ($children as $child): ?>
          <a class="admin-nav-link admin-nav-sublink<?= ($child['id'] ?? '') === $active ? ' is-active' : '' ?>" href="<?= htmlspecialchars($child['href'], ENT_QUOTES, 'UTF-8') ?>">
            <span class="admin-nav-step"><?= (int) ($child['step'] ?? 0) ?></span>
            <?= htmlspecialchars($child['label'], ENT_QUOTES, 'UTF-8') ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
        <?php else: ?>
        <a class="admin-nav-link<?= $item['id'] === $active ? ' is-active' : '' ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>">
          <span class="admin-nav-icon"><?= tt_admin_icon($item['id']) ?></span>
          <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
        </a>
        <?php endif;
      endforeach; ?>
    </nav>
    <div class="admin-sidebar-foot">
      <a href="../index.html" target="_blank" rel="noopener"><span class="admin-foot-icon"><?= tt_admin_icon('external') ?></span>ดูเว็บไซต์</a>
      <a href="logout.php"><span class="admin-foot-icon"><?= tt_admin_icon('logout') ?></span>ออกจากระบบ</a>
    </div>
  </aside>
  <div class="admin-main">
    <header class="admin-topbar">
      <div class="admin-title-wrap">
        <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
        <?= tt_render_admin_page_help($active) ?>
      </div>
      <span class="admin-user"><?= $user ?></span>
    </header>
    <main class="admin-content">
<?php
}

function tt_admin_footer(): void
{
    ?>
    </main>
  </div>
  <div id="toast" class="toast" hidden></div>
  <script src="assets/admin.js"></script>
</body>
</html>
<?php
}

function tt_flash(): ?string
{
    if (!empty($_SESSION['tt_flash'])) {
        $m = $_SESSION['tt_flash'];
        unset($_SESSION['tt_flash']);
        return $m;
    }
    return null;
}

function tt_set_flash(string $msg, string $type = 'success'): void
{
    $_SESSION['tt_flash'] = $msg;
    $_SESSION['tt_flash_type'] = $type === 'error' ? 'error' : 'success';
}

function tt_flash_type(): string
{
    $type = $_SESSION['tt_flash_type'] ?? 'success';
    unset($_SESSION['tt_flash_type']);
    return $type === 'error' ? 'error' : 'success';
}
