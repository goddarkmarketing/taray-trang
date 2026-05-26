<?php
declare(strict_types=1);

/** ไอคอนเดียวกับที่หน้าเว็บใช้ (assets/js/main.js → window.TT.ICONS) */
function tt_frontend_icons(): array
{
    return [
        'cutlery' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 2v8a2 2 0 0 0 2 2v9"/><path d="M11 2v8a2 2 0 0 1-2 2"/><path d="M17 14V2c-2 0-3 2-3 4v5c0 2 1 3 3 3v7"/></svg>',
        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'camera' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8a2 2 0 0 1 2-2h2l2-3h6l2 3h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><circle cx="12" cy="13" r="4"/></svg>',
        'scuba' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="9" r="4"/><path d="M8 9h-2a2 2 0 0 0-2 2v3"/><path d="M16 9h2a2 2 0 0 1 2 2v3"/><path d="M3 20c2 0 3-1 4.5-1S10 20 12 20s3-1 4.5-1S19 20 21 20"/></svg>',
        'carPick' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 15h2l1-5a2 2 0 0 1 2-1.6h8.7a2 2 0 0 1 1.7 1l2 3.6h1.6a1 1 0 0 1 1 1v2H18"/><circle cx="7" cy="17" r="2"/><circle cx="15" cy="17" r="2"/><path d="M9 15h-2"/><path d="M11 9V5"/><path d="M9 7l2-2 2 2"/></svg>',
        'chatBubble' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.5 7.2L4 21l1.7-5.4A8 8 0 1 1 21 12Z"/><path d="M12 11v.01M12 14h0M11 9a1 1 0 1 1 1.5.9c-.4.2-.5.4-.5.8"/></svg>',
        'anchor' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="5" r="2"/><path d="M12 7v15"/><path d="M5 12H3a9 9 0 0 0 18 0h-2"/><path d="M8 12h8"/></svg>',
        'car' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 17h14"/><path d="M5 17a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm18 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/><path d="M3 17v-5l2-5h12l2 5v5"/><path d="M5 12h14"/></svg>',
        'bed' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18v-7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v7"/><path d="M3 14h18"/><path d="M3 20v-2M21 20v-2"/><path d="M7 9V7a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>',
        'ticket' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4Z"/><path d="M13 7v2M13 12v2M13 17v0"/></svg>',
        'briefcase' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 13h18"/></svg>',
        'compass' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor" stroke="none"/></svg>',
        'bigBoat' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18c2 1 3 1 4.5 1s2.5 0 4.5-1 3-1 4.5-1 2.5 0 4.5 1"/><path d="M5 16V9a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v7"/><path d="M9 8V5h6v3"/><path d="M12 11v3"/></svg>',
        'longtail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 18c2 1 3 1 4.5 1s2.5 0 4.5-1 3-1 4.5-1 2.5 0 4.5 1"/><path d="M4 16l1.5-4h12L19 16"/><path d="M11 12V7l9 3"/></svg>',
        'speedboat' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 17c2 1 3 1 4.5 1s2.5 0 4.5-1 3-1 4.5-1 2.5 0 4.5 1"/><path d="M5 15l2-5h8l3 5"/><path d="M9 10V6h4"/><path d="M19 8l2-1"/></svg>',
        'edit' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="m18.5 2.5 3 3L12 15l-4 1 1-4Z"/></svg>',
        'route' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/><circle cx="18" cy="5" r="3"/></svg>',
        'calculator' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M8 6h8M8 10h2M12 10h2M16 10h0M8 14h2M12 14h2M16 14h0M8 18h2M12 18h6"/></svg>',
        'line' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.36 10.62c0-3.78-3.79-6.85-8.45-6.85S2.46 6.84 2.46 10.62c0 3.39 3 6.23 7.06 6.77.28.06.65.18.74.42.08.21.05.55.03.77l-.12.72c-.04.21-.17.83.73.45.9-.38 4.84-2.85 6.61-4.88 1.22-1.34 1.85-2.7 1.85-4.25Zm-11.4 2.02H6.28c-.24 0-.43-.2-.43-.43V9.05c0-.24.2-.43.43-.43.24 0 .43.19.43.43v2.74h1.25c.24 0 .43.19.43.43 0 .23-.19.42-.43.42Zm1.69-.43c0 .23-.19.43-.43.43-.24 0-.43-.2-.43-.43V9.05c0-.24.19-.43.43-.43.24 0 .43.19.43.43v3.16Zm3.79 0c0 .18-.12.35-.3.41a.44.44 0 0 1-.13.02c-.14 0-.27-.07-.35-.18l-1.72-2.35v2.1c0 .23-.19.43-.43.43-.24 0-.43-.2-.43-.43V9.05c0-.18.12-.35.3-.41.05-.01.09-.02.13-.02.14 0 .27.07.35.17l1.72 2.35v-2.1c0-.24.19-.43.43-.43.24 0 .43.19.43.43v3.16Zm3.05-1.6h-1.25v.74h1.25c.23 0 .43.2.43.43 0 .23-.2.43-.43.43h-1.69c-.23 0-.42-.2-.42-.43V9.05c0-.23.19-.43.43-.43h1.69c.23 0 .43.19.43.43 0 .23-.2.43-.43.43h-1.25v.74h1.25c.23 0 .43.19.43.43 0 .23-.2.42-.44.42Z"/></svg>',
        'info' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>',
        'chat' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg>',
        'shield' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>',
        'star' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>',
        'plus' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>',
    ];
}

function tt_frontend_icon(string $name): string
{
    $icons = tt_frontend_icons();
    return $icons[$name] ?? '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="2" fill="currentColor"/></svg>';
}

function tt_icon_select(string $fieldName, array $iconNames, string $selected = ''): void
{
    if ($selected === '' && $iconNames) {
        $selected = $iconNames[0];
    }
    $uid = 'icon-select-' . preg_replace('/[^a-z0-9-]/i', '', $fieldName);
    ?>
    <div class="icon-select" id="<?= htmlspecialchars($uid, ENT_QUOTES, 'UTF-8') ?>" data-icon-select>
      <input type="hidden" name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($selected, ENT_QUOTES, 'UTF-8') ?>"/>
      <button type="button" class="icon-select-trigger" aria-haspopup="listbox" aria-expanded="false">
        <span class="icon-select-item-icon"><?= tt_frontend_icon($selected) ?></span>
        <span class="icon-select-item-label"><?= htmlspecialchars($selected, ENT_QUOTES, 'UTF-8') ?></span>
        <span class="icon-select-chevron" aria-hidden="true"></span>
      </button>
      <ul class="icon-select-menu" role="listbox" hidden>
        <?php foreach ($iconNames as $ic): ?>
          <li class="icon-select-option<?= $selected === $ic ? ' is-selected' : '' ?>" role="option" data-value="<?= htmlspecialchars($ic, ENT_QUOTES, 'UTF-8') ?>" aria-selected="<?= $selected === $ic ? 'true' : 'false' ?>">
            <span class="icon-select-item-icon"><?= tt_frontend_icon($ic) ?></span>
            <span class="icon-select-item-label"><?= htmlspecialchars($ic, ENT_QUOTES, 'UTF-8') ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
}
