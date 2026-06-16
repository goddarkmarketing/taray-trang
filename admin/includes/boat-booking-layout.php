<?php
declare(strict_types=1);

/** @return list<array{id: string, href: string, step: int, label: string}> */
function tt_bb_nav_items(): array
{
    return [
        ['id' => 'profiles', 'href' => 'boat-booking-profiles.php', 'step' => 1, 'label' => 'โปรไฟล์เรือ'],
        ['id' => 'routes', 'href' => 'boat-booking-routes.php', 'step' => 2, 'label' => 'เส้นทาง'],
        ['id' => 'tiers', 'href' => 'boat-booking-tiers.php', 'step' => 3, 'label' => 'ช่วงจำนวนคน'],
        ['id' => 'prices', 'href' => 'boat-booking-prices.php', 'step' => 4, 'label' => 'ราคาเหมา (หางยาว)'],
        ['id' => 'insurance', 'href' => 'boat-booking-insurance.php', 'step' => 5, 'label' => 'ประกัน & ค่าบังคับ'],
        ['id' => 'addons', 'href' => 'boat-booking-addons.php', 'step' => 6, 'label' => 'บริการเสริม'],
    ];
}

/** @return list<array{id: string, href: string, step: int, label: string}> */
function tt_bb_sidebar_children(): array
{
    $out = [];
    foreach (tt_bb_nav_items() as $item) {
        $out[] = [
            'id' => 'boat-booking-' . $item['id'],
            'href' => $item['href'],
            'step' => $item['step'],
            'label' => $item['label'],
        ];
    }
    return $out;
}

function tt_bb_active_nav_id(string $activeId): string
{
    if (str_starts_with($activeId, 'boat-booking-')) {
        return $activeId;
    }
    return 'boat-booking-' . $activeId;
}

function tt_bb_render_nav(string $activeId): void
{
    $activeId = tt_bb_active_nav_id($activeId);
    ?>
<nav class="admin-subnav card adm-bb-subnav" aria-label="ลำดับตามขั้นตอนจองบนเว็บ">
  <?php foreach (tt_bb_nav_items() as $item):
    $navId = 'boat-booking-' . $item['id'];
    $isActive = $navId === $activeId;
    ?>
  <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"<?= $isActive ? ' class="is-active"' : '' ?>>
    <span class="adm-step-badge"><?= (int) $item['step'] ?></span>
    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
  </a>
  <?php endforeach; ?>
</nav>
<?php
}

function tt_bb_render_preview_bar(): void
{
    ?>
<div class="card admin-preview-bar">
  <div>
    <strong>หน้าจอง:</strong>
    <a href="../boat-book.html" target="_blank" rel="noopener">boat-book.html</a>
    — การ์ดชื่อ/รูปเรือที่เมนู <a href="boats.php">ประเภทเรือ</a>
  </div>
  <a class="btn btn-ghost btn-sm" href="../boat-book.html" target="_blank" rel="noopener">ทดสอบจองเรือ ↗</a>
</div>
<?php
}

function tt_bb_render_flash(): void
{
    $flash = tt_flash();
    if ($flash):
        $type = tt_flash_type();
        $class = $type === 'error' ? 'alert-error' : 'alert-success';
        ?>
<div class="alert <?= $class ?>"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif;
}

function tt_bb_save(array $bb): void
{
    $data = tt_read_data();
    $data['boatBooking'] = $bb;
    tt_write_data($data);
}

/** @param array<int, mixed> $rows */
function tt_bb_repeater_blank(array $rows, array $template): array
{
    $rows = array_values($rows);
    $rows[] = $template;
    return $rows;
}

/** @return list<string> */
function tt_bb_icon_options(): array
{
    return ['cutlery', 'users', 'camera', 'scuba', 'carPick', 'compass', 'plus', 'anchor', 'chatBubble'];
}

function tt_bb_page_start(string $title, string $activeId): void
{
    tt_admin_header($title, tt_bb_active_nav_id($activeId));
    tt_bb_render_flash();
    tt_bb_render_preview_bar();
}

function tt_bb_form_actions(string $label): void
{
    ?>
<div class="form-actions">
  <button type="submit" class="btn btn-primary"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></button>
</div>
<?php
}
