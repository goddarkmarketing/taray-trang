/* =========================================================
   Talay Trang — Main JS
   Shared header / footer / sticky CTA / scroll reveal
   ========================================================= */

(() => {
  const site = () => window.TT?.SITE || {};
  const navItems = () => window.TT?.NAV_ITEMS || [];
  const brandEn = () => site().brand || 'Talay Trang';
  const brandTh = () => site().brandTh || 'เช่าเรือตรัง';
  const brandAlt = () => {
    const tag = site().tagline || 'เช่าเรือเที่ยวทะเลตรัง';
    return `${brandEn()} — ${tag}`;
  };
  const esc = (s) => String(s ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/"/g, '&quot;');

  // Lucide icons (inline SVG) — regenerate: node tools/generate-lucide-icons.js
/* Auto-generated from Lucide — node tools/generate-lucide-icons.js */
/* Auto-generated from Lucide — node tools/generate-lucide-icons.js */
/* Auto-generated from Lucide — node tools/generate-lucide-icons.js */
  const ICONS = {
    line: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.36 10.62c0-3.78-3.79-6.85-8.45-6.85S2.46 6.84 2.46 10.62c0 3.39 3 6.23 7.06 6.77.28.06.65.18.74.42.08.21.05.55.03.77l-.12.72c-.04.21-.17.83.73.45.9-.38 4.84-2.85 6.61-4.88 1.22-1.34 1.85-2.7 1.85-4.25Zm-11.4 2.02H6.28c-.24 0-.43-.2-.43-.43V9.05c0-.24.2-.43.43-.43.24 0 .43.19.43.43v2.74h1.25c.24 0 .43.19.43.43 0 .23-.19.42-.43.42Zm1.69-.43c0 .23-.19.43-.43.43-.24 0-.43-.2-.43-.43V9.05c0-.24.19-.43.43-.43.24 0 .43.19.43.43v3.16Zm3.79 0c0 .18-.12.35-.3.41a.44.44 0 0 1-.13.02c-.14 0-.27-.07-.35-.18l-1.72-2.35v2.1c0 .23-.19.43-.43.43-.24 0-.43-.2-.43-.43V9.05c0-.18.12-.35.3-.41.05-.01.09-.02.13-.02.14 0 .27.07.35.17l1.72 2.35v-2.1c0-.24.19-.43.43-.43.24 0 .43.19.43.43v3.16Zm3.05-1.6h-1.25v.74h1.25c.23 0 .43.2.43.43 0 .23-.2.43-.43.43h-1.69c-.23 0-.42-.2-.42-.43V9.05c0-.23.19-.43.43-.43h1.69c.23 0 .43.19.43.43 0 .23-.2.43-.43.43h-1.25v.74h1.25c.23 0 .43.19.43.43 0 .23-.2.42-.44.42Z"/></svg>',
    fb: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12Z"/></svg>',
    tt: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.6 6.32a4.83 4.83 0 0 1-3.77-2.16h-2.99v13.05a2.83 2.83 0 1 1-2.83-2.83c.21 0 .42.03.62.07v-3.05a5.94 5.94 0 1 0 5.21 5.9V9.43a7.96 7.96 0 0 0 4.45 1.37V7.81a4.86 4.86 0 0 1-.69-1.49Z"/></svg>',
    phone: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>',
    menu: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>',
    close: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>',
    arrow: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>',
    play: '<svg viewBox="0 0 24 24" fill="currentColor" stroke="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"/></svg>',
    star: '<svg viewBox="0 0 24 24" fill="currentColor" stroke="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>',
    map: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>',
    clock: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
    sun: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>',
    moon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"/></svg>',
    calendar: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>',
    shield: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>',
    starOutline: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>',
    compass: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z"/></svg>',
    chat: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"/></svg>',
    users: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/></svg>',
    user: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    route: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/><circle cx="18" cy="5" r="3"/></svg>',
    info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
    alert: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
    calculator: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="16" x2="16" y1="14" y2="18"/><path d="M16 10h.01"/><path d="M12 10h.01"/><path d="M8 10h.01"/><path d="M12 14h.01"/><path d="M8 14h.01"/><path d="M12 18h.01"/><path d="M8 18h.01"/></svg>',
    anchor: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 6v16"/><path d="m19 13 2-1a9 9 0 0 1-18 0l2 1"/><path d="M9 11h6"/><circle cx="12" cy="4" r="2"/></svg>',
    bigBoat: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 10.189V14"/><path d="M12 2v3"/><path d="M19 13V7a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v6"/><path d="M19.38 20A11.6 11.6 0 0 0 21 14l-8.188-3.639a2 2 0 0 0-1.624 0L3 14a11.6 11.6 0 0 0 2.81 7.76"/><path d="M2 21c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1s1.2 1 2.5 1c2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>',
    longtail: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 2v15"/><path d="M7 22a4 4 0 0 1-4-4 1 1 0 0 1 1-1h16a1 1 0 0 1 1 1 4 4 0 0 1-4 4z"/><path d="M9.159 2.46a1 1 0 0 1 1.521-.193l9.977 8.98A1 1 0 0 1 20 13H4a1 1 0 0 1-.824-1.567z"/></svg>',
    speedboat: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 10.189V14"/><path d="M12 2v3"/><path d="M19 13V7a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v6"/><path d="M19.38 20A11.6 11.6 0 0 0 21 14l-8.188-3.639a2 2 0 0 0-1.624 0L3 14a11.6 11.6 0 0 0 2.81 7.76"/><path d="M2 21c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1s1.2 1 2.5 1c2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>',
    bed: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>',
    ticket: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg>',
    car: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>',
    briefcase: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg>',
    scuba: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12q2.5 2 5 0t5 0 5 0 5 0"/><path d="M2 19q2.5 2 5 0t5 0 5 0 5 0"/><path d="M2 5q2.5 2 5 0t5 0 5 0 5 0"/></svg>',
    cutlery: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>',
    edit: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>',
    camera: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z"/><circle cx="12" cy="13" r="3"/></svg>',
    carSelf: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>',
    carPick: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 6v6"/><path d="M15 6v6"/><path d="M2 12h19.6"/><path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3"/><circle cx="7" cy="18" r="2"/><path d="M9 18h5"/><circle cx="16" cy="18" r="2"/></svg>',
    chatBubble: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>',
    check: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>',
    copy: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>',
    minus: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/></svg>',
    plus: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="M12 5v14"/></svg>',
    arrowLeft: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>',
  };
  window.TT.ICONS = ICONS;

  /* ---------- Header / Nav ---------- */
  function renderHeader() {
    const host = document.getElementById('site-header');
    if (!host) return;
    const phoneTel = (site().phone || '').replace(/[^\d+]/g, '');
    const path = location.pathname.split('/').pop() || 'index.html';
    const linksHtml = navItems()
      .map(item => `<a class="nav-link${item.href === path ? ' active' : ''}" href="${item.href}">${item.label}</a>`)
      .join('');
    const drawerLinksHtml = navItems()
      .map(item => `<a class="drawer-link${item.href === path ? ' active' : ''}" href="${item.href}">${item.label}${ICONS.arrow}</a>`)
      .join('');

    const transparent = host.classList.contains('transparent') ? 'is-transparent' : '';

    /* IMPORTANT: drawer ต้องอยู่ "นอก" .site-header เพราะ header มี backdrop-filter
       ซึ่งสร้าง containing block ใหม่ ทำให้ position: fixed ลูกของ header ถูกขังในกรอบ header (เตี้ยมาก) */
    host.outerHTML = `
      <header class="site-header ${transparent}" id="site-header" data-transparent="${transparent ? 1 : 0}">
        <div class="container nav">
          <a href="index.html" class="brand" aria-label="กลับสู่หน้าแรก ${esc(brandEn())}">
            <img class="brand-logo" src="assets/logo/logo.png" alt="${esc(brandAlt())}" />
            <span class="brand-text"><strong>${esc(brandEn())}</strong><small>${esc(brandTh())}</small></span>
          </a>
          <nav class="nav-menu" aria-label="เมนูหลัก">${linksHtml}</nav>
          <a class="btn btn-line btn-sm nav-cta" href="${site().lineUrl}" target="_blank" rel="noopener">
            ${ICONS.line} จองผ่าน LINE
          </a>
          <button class="nav-toggle" type="button" aria-label="เปิดเมนู" id="nav-toggle">${ICONS.menu}</button>
        </div>
      </header>
    `;

    // ลบ drawer เก่า (ถ้ามี) แล้ว append ใหม่เป็นลูกตรงของ body — กัน backdrop-filter ของ header ขังกรอบ
    const oldDrawer = document.getElementById('nav-drawer');
    if (oldDrawer) oldDrawer.remove();
    const drawerEl = document.createElement('div');
    drawerEl.className = 'nav-drawer';
    drawerEl.id = 'nav-drawer';
    drawerEl.setAttribute('aria-hidden', 'true');
    drawerEl.innerHTML = `
      <div class="nav-drawer-panel" role="dialog" aria-label="เมนูนำทาง">
        <button class="nav-drawer-close" type="button" aria-label="ปิดเมนู" id="nav-drawer-close">${ICONS.close}</button>
        <div>${drawerLinksHtml}</div>
        <div class="drawer-foot">
          <a class="btn btn-line btn-block" href="${site().lineUrl}" target="_blank" rel="noopener">${ICONS.line} จอง LINE</a>
          <a class="btn btn-call btn-block" href="tel:${phoneTel}">${ICONS.phone} โทรเลย</a>
        </div>
      </div>
    `;
    document.body.appendChild(drawerEl);

    // Bind interactions (querying after outerHTML replacement)
    const headerEl = document.querySelector('.site-header');
    const drawer   = document.getElementById('nav-drawer');
    const toggle   = document.getElementById('nav-toggle');
    const closeBtn = document.getElementById('nav-drawer-close');

    // เผื่อค้างจาก state เดิม
    document.body.style.overflow = '';

    const openDrawer = (ev) => {
      ev?.preventDefault?.();
      ev?.stopPropagation?.();
      if (!drawer) return;
      drawer.classList.add('is-open');
      drawer.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    };
    const closeDrawer = () => {
      if (!drawer) return;
      drawer.classList.remove('is-open');
      drawer.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    };

    // รองรับทั้ง click และ touchend (กันเคส iOS ที่ click delay/ghost click)
    if (toggle) {
      toggle.addEventListener('click', openDrawer);
      toggle.addEventListener('touchend', openDrawer, { passive: false });
    }
    if (closeBtn) {
      closeBtn.addEventListener('click', closeDrawer);
      closeBtn.addEventListener('touchend', (e) => { e.preventDefault(); closeDrawer(); }, { passive: false });
    }
    drawer?.addEventListener('click', (e) => { if (e.target === drawer) closeDrawer(); });
    drawer?.querySelectorAll('.drawer-link').forEach(a => a.addEventListener('click', closeDrawer));
    // ESC ปิด drawer
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDrawer(); });

    // Transparent → solid on scroll
    const wasTransparent = headerEl.dataset.transparent === '1';
    const hasFlushBanner = !!document.getElementById('hero-slider');
    const onScroll = () => {
      const y = window.scrollY;
      if (wasTransparent) {
        headerEl.classList.toggle('is-transparent', y < 80);
        headerEl.classList.toggle('is-flush-top', hasFlushBanner && y < 80);
      }
      headerEl.classList.toggle('is-scrolled', y > 8);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ---------- Footer ---------- */
  function renderFooter() {
    const host = document.getElementById('site-footer');
    if (!host) return;

    const phoneTel = (site().phone || '').replace(/[^\d+]/g, '');
    const phoneShow = site().phone || site().phoneDisplay || '';

    const aboutLinks = [
      { label: 'วิธีจองเรือ',     href: 'booking.html' },
      { label: 'ติดต่อเรา',        href: 'contact.html' },
      { label: `เกี่ยวกับ ${brandEn()}`, href: 'about.html' },
      { label: 'บทความและคู่มือ',    href: 'articles.html' },
      { label: 'วิดีโอจาก TikTok', href: 'videos.html' },
    ];
    const serviceLinks = [
      { label: 'เช่าเรือหางยาว',       href: 'boats.html#longtail' },
      { label: 'เช่าเรือ Speed Boat',  href: 'boats.html#speedboat' },
      { label: 'เช่าเรือใหญ่ / เหมาลำ', href: 'boats.html#bigboat' },
      { label: 'โปรแกรม 4 เกาะ',        href: 'programs.html#four-islands' },
      { label: 'ถ้ำมรกต · เกาะกระดาน', href: 'programs.html' },
      { label: 'สอนดำน้ำ Scuba',        href: 'programs.html#diving' },
      { label: 'จองห้องพัก / รถเช่า',   href: site().lineUrl, external: true },
      { label: 'ตั๋วเรือเข้าเกาะ',      href: site().lineUrl, external: true },
    ];
    const moreLinks = [
      { label: 'นโยบายความเป็นส่วนตัว', href: '#' },
      { label: 'ข้อกำหนดและเงื่อนไข',   href: '#' },
      { label: 'นโยบายการคืนเงิน',      href: '#' },
      { label: 'คำถามที่พบบ่อย (FAQ)',  href: 'booking.html#faq' },
      { label: 'ร่วมเป็นพาร์ทเนอร์',     href: site().lineUrl, external: true },
    ];

    const renderLinks = (arr) => arr.map(i => {
      const ext = i.external ? ' target="_blank" rel="noopener"' : '';
      return `<li><a href="${i.href}"${ext}>${i.label}</a></li>`;
    }).join('');

    const ICO = 'assets/icon/';
    const payList = [
      { src: '1736409068278-213734dd376baa9642331bf4620bcb41.png', label: 'Visa' },
      { src: '1736479220958-0c6dae87da47bf1bbf6ae51ab5150617.png', label: 'Mastercard' },
      { src: '1736479521542-46cd24bde2c3910f5fa4e2d653eb6c23.png', label: 'JCB' },
      { src: '1736479507703-3399918fb9e71924ef2dddd4f8ccd947.png', label: 'American Express' },
      { src: '1736479511814-8264416bfb67d4a902623644c401b394.png', label: 'ttb' },
      { src: '1736480762727-aef62311a0107a1e58dac9b3492b4f4c.png', label: 'K+ (KBank)' },
      { src: '1736480772015-8e13294a9e031c44af797d68c3a36299.png', label: 'Bangkok Bank' },
      { src: '1736480775437-d4b01a73d59cf9b29a2197aaf6a8d26f.png', label: 'Krungsri' },
      { src: '1736480778815-598c66ec3869ba9cd2275e123b9aaaac.png', label: 'TrueMoney' },
      { src: '1736480766464-76cce31dad31e2f72b89f4aea6c934b3.png', label: '7-Eleven' },
      { src: '1736480785634-7bd7a1c50dc24a0853b644f46c8b01df.png', label: "Lotus's" },
    ];
    const payChips = payList.map(p =>
      `<span class="pay-chip" title="${p.label}" aria-label="${p.label}"><img src="${ICO}${p.src}" alt="${p.label}" loading="lazy"/></span>`
    ).join('');

    host.outerHTML = `
      <footer class="site-footer" id="site-footer">
        <div class="container">
          <div class="footer-grid">
            <!-- Col 1: Brand + Trust + Payment -->
            <div class="footer-col footer-col-brand">
              <a href="index.html" class="brand brand-footer" aria-label="กลับสู่หน้าแรก ${esc(brandEn())}">
                <img class="brand-logo" src="assets/logo/logo.png" alt="${esc(brandEn())}" />
                <span class="brand-text brand-text--footer"><strong>${esc(brandEn())}</strong><small>${esc(brandTh())}</small></span>
              </a>
              <a class="footer-cta" href="${site().lineUrl}" target="_blank" rel="noopener">
                ลงทะเบียนเป็นคู่ค้ากับ ${esc(brandEn())} ${ICONS.arrow}
              </a>
              <div class="pay-block">
                <h5>ช่องทางชำระเงิน</h5>
                <div class="pay-chips">${payChips}</div>
              </div>
            </div>

            <!-- Col 2: About + Social -->
            <div class="footer-col">
              <h4>เกี่ยวกับ ${esc(brandEn())}</h4>
              <ul class="footer-links">${renderLinks(aboutLinks)}</ul>
              <h4 class="mt-block">ติดตามเราได้ทาง</h4>
              <ul class="footer-social">
                <li><a href="${site().facebookUrl}" target="_blank" rel="noopener"><span class="ico ico-fb">${ICONS.fb}</span>Facebook</a></li>
                <li><a href="${site().tiktokUrl}"   target="_blank" rel="noopener"><span class="ico ico-tt">${ICONS.tt}</span>TikTok</a></li>
                <li><a href="${site().lineUrl}"     target="_blank" rel="noopener"><span class="ico ico-line">${ICONS.line}</span>LINE Official</a></li>
                <li><a href="tel:${phoneTel}"><span class="ico ico-call">${ICONS.phone}</span>โทร ${phoneShow}</a></li>
              </ul>
            </div>

            <!-- Col 3: Services -->
            <div class="footer-col">
              <h4>บริการของเรา</h4>
              <ul class="footer-links">${renderLinks(serviceLinks)}</ul>
            </div>

            <!-- Col 4: Other + Contact card -->
            <div class="footer-col">
              <h4>อื่น ๆ</h4>
              <ul class="footer-links">${renderLinks(moreLinks)}</ul>
              <div class="footer-contact-card">
                <h5>จองด่วน 24 ชั่วโมง</h5>
                <p>${site().addressFull}</p>
                <p class="muted-thin">${site().hours}</p>
                <div class="footer-contact-buttons">
                  <a class="btn btn-line btn-sm" href="${site().lineUrl}" target="_blank" rel="noopener">${ICONS.line} แอด LINE</a>
                  <a class="btn btn-ghost btn-sm" href="tel:${phoneTel}">${ICONS.phone} โทร</a>
                </div>
              </div>
            </div>
          </div>

          <div class="footer-bot">
            <span>© ${new Date().getFullYear()} ${esc(brandEn())}. All rights reserved.</span>
            <span class="footer-bot-meta">เลขประจำตัวผู้เสียภาษี xxx-xxxx-xxxxx · จ.ตรัง</span>
          </div>
        </div>
      </footer>
    `;
  }

  /* ---------- Site contact blocks (index / contact) ---------- */
  function hydrateSiteOffices() {
    const s = site();
    const phoneTel = (s.phone || '').replace(/[^\d+]/g, '');
    const phoneDisplay = s.phone || s.phoneDisplay || '';
    const address = s.addressFull || s.address || '';
    const tiktokLabel = (() => {
      const m = (s.tiktokUrl || '').match(/@([\w.]+)/i);
      return m ? '@' + m[1] : 'TikTok';
    })();

    document.querySelectorAll('[data-tt-site-office]').forEach((office) => {
      const mode = office.dataset.ttSiteOffice || 'basic';
      const mapIframe = office.querySelector('.office-map iframe');
      if (mapIframe && s.mapEmbed) {
        mapIframe.src = s.mapEmbed;
      }

      const list = office.querySelector('.contact-list');
      if (list) {
        let html = '';
        if (mode === 'basic') {
          html = `
          <li><span class="ico" data-icon="map"></span><span><strong>ที่ตั้งออฟฟิศ</strong><span>${address}</span></span></li>
          <li><span class="ico" data-icon="clock"></span><span><strong>เวลาทำการ</strong><span>${s.hours || ''}</span></span></li>
          <li><span class="ico" data-icon="phone"></span><span><strong>โทรหาเรา</strong><span><a href="tel:${phoneTel}">${phoneDisplay}</a></span></span></li>
          <li><span class="ico" data-icon="line"></span><span><strong>LINE Official</strong><span><a href="${s.lineUrl || '#'}" target="_blank" rel="noopener">${s.lineId || ''}</a></span></span></li>`;
        } else {
          html = `
          <li><span class="ico" data-icon="line"></span><span><strong>LINE Official Account</strong><span><a href="${s.lineUrl || '#'}" target="_blank" rel="noopener">${s.lineId || ''}</a> · ตอบไวที่สุด</span></span></li>
          <li><span class="ico" data-icon="phone"></span><span><strong>โทรศัพท์</strong><span><a href="tel:${phoneTel}">${phoneDisplay}</a> · เปิดทุกวัน</span></span></li>
          <li><span class="ico" data-icon="map"></span><span><strong>ที่ตั้งออฟฟิศ</strong><span>${address}</span></span></li>
          <li><span class="ico" data-icon="clock"></span><span><strong>เวลาทำการ</strong><span>${s.hours || ''}</span></span></li>
          <li><span class="ico" data-icon="fb"></span><span><strong>Facebook</strong><span><a href="${s.facebookUrl || '#'}" target="_blank" rel="noopener">เพจ ${esc(brandEn())}</a></span></span></li>
          <li><span class="ico" data-icon="tt"></span><span><strong>TikTok</strong><span><a href="${s.tiktokUrl || '#'}" target="_blank" rel="noopener">${tiktokLabel}</a></span></span></li>`;
        }
        list.innerHTML = html;
      }

      office.querySelectorAll('[data-tt-line-btn]').forEach((a) => {
        a.href = s.lineUrl || '#';
      });
      office.querySelectorAll('[data-tt-call-btn]').forEach((a) => {
        a.href = 'tel:' + phoneTel;
      });
    });
  }

  function refreshSiteShell() {
    const pageKey = document.body.dataset.ttPage;
    if (pageKey) applySeo(pageKey);
    renderHeader();
    renderFooter();
    document.querySelector('.sticky-cta')?.remove();
    document.body.classList.remove('has-sticky');
    renderStickyCta();
    hydrateSiteOffices();
    injectIcons();
  }
  window.TT.refreshSiteShell = refreshSiteShell;

  /* ---------- Welcome popup (contact on entry) ---------- */
  function initWelcomePopup() {
    if (window.__TT_POPUP_DONE) return;
    if (/\/admin(\/|$)/.test(location.pathname)) return;

    const s = site();
    if (s.popupEnabled === false) return;
    if (sessionStorage.getItem('tt-welcome-popup')) return;

    const phoneTel = (s.phone || '').replace(/[^\d+]/g, '');
    const phoneDisplay = s.phoneDisplay || s.phone || '';
    const lineId = s.lineId || '@talaytrang';
    const lineUrl = s.lineUrl || '#';
    const hoursWeekday = s.popupHoursWeekday || s.hours || 'จันทร์ - เสาร์ 07:00-21:00 น.';
    const hoursSunday = s.popupHoursSunday || '';
    const qrSrc = s.lineQrImage
      || `https://api.qrserver.com/v1/create-qr-code/?size=112x112&data=${encodeURIComponent(lineUrl)}`;

    document.getElementById('welcome-popup')?.remove();

    const el = document.createElement('div');
    el.className = 'welcome-popup';
    el.id = 'welcome-popup';
    el.setAttribute('role', 'dialog');
    el.setAttribute('aria-label', 'ติดต่อเรา');
    el.innerHTML = `
      <div class="welcome-popup-backdrop" aria-hidden="true"></div>
      <button type="button" class="welcome-popup-fab" aria-label="เปิดติดต่อเรา" aria-expanded="false">${ICONS.chat}</button>
      <div class="welcome-popup-panel">
        <div class="welcome-popup-head">
          <span class="welcome-popup-label">ติดต่อเรา</span>
          <button type="button" class="welcome-popup-close" aria-label="ปิด">${ICONS.close}</button>
        </div>
        <div class="welcome-popup-body">
          <a class="welcome-popup-call" href="tel:${phoneTel}">
            <span class="welcome-popup-call-icon">${ICONS.phone}</span>
            <span class="welcome-popup-call-num">${esc(phoneDisplay)}</span>
          </a>
          <a class="welcome-popup-line" href="${esc(lineUrl)}" target="_blank" rel="noopener">
            <span class="welcome-popup-line-icon">${ICONS.line}</span>
            <span class="welcome-popup-line-text">
              <span class="welcome-popup-line-title">แชท LINE</span>
              <span class="welcome-popup-line-id">${esc(lineId)}</span>
            </span>
          </a>
          <div class="welcome-popup-qr">
            <p class="welcome-popup-qr-hint">สแกนเพิ่มเพื่อน</p>
            <div class="welcome-popup-qr-frame">
              <img src="${esc(qrSrc)}" width="100" height="100" alt="QR Code ${esc(lineId)}" loading="lazy"/>
            </div>
          </div>
          <div class="welcome-popup-hours-block">
            <span>${esc(hoursWeekday)}</span>
            ${hoursSunday ? `<span>${esc(hoursSunday)}</span>` : ''}
          </div>
        </div>
      </div>`;

    const fab = el.querySelector('.welcome-popup-fab');
    const mqMobile = window.matchMedia('(max-width: 767px)');

    const setExpanded = (open) => {
      el.classList.toggle('is-expanded', open);
      if (fab) fab.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    const close = () => {
      el.classList.remove('is-visible', 'is-expanded');
      if (fab) fab.setAttribute('aria-expanded', 'false');
      sessionStorage.setItem('tt-welcome-popup', '1');
      window.__TT_POPUP_DONE = true;
      setTimeout(() => el.remove(), 320);
    };

    fab?.addEventListener('click', (e) => {
      e.stopPropagation();
      setExpanded(!el.classList.contains('is-expanded'));
    });

    el.querySelector('.welcome-popup-backdrop')?.addEventListener('click', () => setExpanded(false));
    el.querySelector('.welcome-popup-close')?.addEventListener('click', close);
    document.addEventListener('click', (e) => {
      if (!el.isConnected || !el.classList.contains('is-expanded')) return;
      if (el.contains(e.target)) return;
      setExpanded(false);
    });
    document.addEventListener('keydown', function onKey(e) {
      if (!el.isConnected) return;
      if (e.key === 'Escape') {
        if (mqMobile.matches && el.classList.contains('is-expanded')) {
          setExpanded(false);
        } else if (el.classList.contains('is-visible')) {
          close();
          document.removeEventListener('keydown', onKey);
        }
      }
    });

    document.body.appendChild(el);
    window.__TT_POPUP_DONE = true;
    setTimeout(() => {
      el.classList.add('is-visible');
      if (!mqMobile.matches) el.classList.add('is-expanded');
    }, 700);
  }
  window.TT.initWelcomePopup = initWelcomePopup;

  /* ---------- Sticky bottom CTA (mobile only) ---------- */
  function renderStickyCta() {
    if (document.body.dataset.sticky === 'off') return;
    document.querySelector('.sticky-cta')?.remove();
    const el = document.createElement('div');
    el.className = 'sticky-cta';
    el.innerHTML = `
      <a class="btn btn-call" href="tel:${(site().phone || '').replace(/[^\d+]/g,'')}">${ICONS.phone} โทรเลย</a>
      <a class="btn btn-line" href="${site().lineUrl}" target="_blank" rel="noopener">${ICONS.line} จอง LINE</a>
    `;
    document.body.appendChild(el);
    document.body.classList.add('has-sticky');
    // Reveal after small scroll so it doesn't overlay hero too much
    const reveal = () => {
      if (window.scrollY > 120) el.classList.add('is-visible');
      else el.classList.remove('is-visible');
    };
    reveal();
    window.addEventListener('scroll', reveal, { passive: true });
  }

  /* ---------- Scroll reveal ---------- */
  let revealObserver = null;

  function bindReveal(root = document) {
    const els = root.querySelectorAll('[data-reveal]:not([data-reveal-bound])');
    if (!els.length) return;

    if (!('IntersectionObserver' in window)) {
      els.forEach(el => {
        el.classList.add('is-in');
        el.dataset.revealBound = '1';
      });
      return;
    }

    if (!revealObserver) {
      revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(en => {
          if (en.isIntersecting) {
            en.target.classList.add('is-in');
            revealObserver.unobserve(en.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
    }

    els.forEach(el => {
      el.dataset.revealBound = '1';
      revealObserver.observe(el);
    });
  }
  window.TT.bindReveal = bindReveal;

  /* ---------- Toast ---------- */
  function toast(msg, ms = 2400) {
    let t = document.querySelector('.toast');
    if (!t) {
      t = document.createElement('div');
      t.className = 'toast';
      document.body.appendChild(t);
    }
    t.textContent = msg;
    requestAnimationFrame(() => t.classList.add('is-visible'));
    clearTimeout(t._tid);
    t._tid = setTimeout(() => t.classList.remove('is-visible'), ms);
  }
  window.TT.toast = toast;

  /* ---------- Auto inject inline SVG icons ---------- */
  function injectIcons() {
    document.querySelectorAll('[data-icon]').forEach(el => {
      const name = el.getAttribute('data-icon');
      if (ICONS[name] && !el.dataset.injected) {
        el.innerHTML = ICONS[name];
        el.dataset.injected = '1';
      }
    });
  }
  window.TT.injectIcons = injectIcons;

  /* ---------- Hero Banner Slider ---------- */
  function initBannerSlider() {
    const root = document.getElementById('hero-slider');
    if (!root) return;
    const slides = (window.TT && window.TT.HERO_SLIDES) || [];
    if (!slides.length) { root.style.display = 'none'; return; }

    const track = root.querySelector('#banner-track');
    const dotsWrap = root.querySelector('#banner-dots');
    const prevBtn = root.querySelector('.banner-arrow.prev');
    const nextBtn = root.querySelector('.banner-arrow.next');

    root.setAttribute('data-count', String(slides.length));

    track.innerHTML = slides.map((s, i) => `
      <div class="banner-slide${i === 0 ? ' is-active' : ''}" role="group" aria-roledescription="slide" aria-label="${i + 1} จาก ${slides.length}">
        <img src="${s.src}" alt="${s.alt || ''}" ${i === 0 ? '' : 'loading="lazy"'} decoding="async" />
      </div>
    `).join('');

    dotsWrap.innerHTML = slides.map((_, i) => `
      <button type="button" class="${i === 0 ? 'is-active' : ''}" role="tab" aria-label="ภาพที่ ${i + 1}" data-i="${i}"></button>
    `).join('');

    const dotEls = dotsWrap.querySelectorAll('button');
    const slideEls = track.querySelectorAll('.banner-slide');
    let cur = 0;
    let timer = null;
    const DURATION = 5500;

    function go(i) {
      i = ((i % slides.length) + slides.length) % slides.length;
      if (i === cur) return;
      slideEls[cur].classList.remove('is-active');
      dotEls[cur].classList.remove('is-active');
      cur = i;
      slideEls[cur].classList.add('is-active');
      dotEls[cur].classList.add('is-active');
    }
    function next() { go(cur + 1); }
    function prev() { go(cur - 1); }
    function start() {
      stop();
      if (window.__TT_PREVIEW || slides.length <= 1) return;
      timer = setInterval(next, DURATION);
    }
    function stop() { if (timer) { clearInterval(timer); timer = null; } }

    nextBtn.addEventListener('click', () => { next(); start(); });
    prevBtn.addEventListener('click', () => { prev(); start(); });
    dotEls.forEach(d => d.addEventListener('click', () => {
      go(Number(d.dataset.i));
      start();
    }));

    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', start);

    let touchX = null;
    root.addEventListener('touchstart', (e) => { touchX = e.touches[0].clientX; stop(); }, { passive: true });
    root.addEventListener('touchend', (e) => {
      if (touchX === null) return start();
      const dx = e.changedTouches[0].clientX - touchX;
      if (Math.abs(dx) > 40) { dx < 0 ? next() : prev(); }
      touchX = null;
      start();
    });

    document.addEventListener('visibilitychange', () => {
      document.hidden ? stop() : start();
    });

    start();
  }
  window.TT.initBannerSlider = initBannerSlider;

  /* Pills marquee ถูกควบคุมด้วย CSS @keyframes ทั้งหมด ไม่ต้องใช้ JS
     ฟังก์ชันนี้คงไว้เผื่อหน้าอื่นยังเรียกใช้ (no-op) */
  window.TT.initAutoScrollPills = function () {};

  /* ---------- Mobile slider (เลื่อนทีละ 1 ใบ + dots) ----------
     ใช้กับ container ที่มี class `.mobile-slider` คู่กับ `.slider-dots` ที่มี id ตรงกัน+'-dots'
     เปิดทำงานเฉพาะ ≤767px; resize แล้วปรับให้เอง */
  function initMobileSlider(containerSelector, dotsSelector) {
    const container = typeof containerSelector === 'string'
      ? document.querySelector(containerSelector) : containerSelector;
    const dotsWrap = typeof dotsSelector === 'string'
      ? document.querySelector(dotsSelector) : dotsSelector;
    if (!container || !dotsWrap) return;

    const mq = window.matchMedia('(max-width: 767px)');
    let dotEls = [];
    let scrollHandler = null;

    function clearDots() {
      dotsWrap.innerHTML = '';
      dotEls = [];
    }

    function activate(i) {
      dotEls.forEach((d, idx) => d.classList.toggle('is-active', idx === i));
    }

    function buildDots() {
      const items = Array.from(container.children);
      if (!items.length) { clearDots(); return; }

      dotsWrap.innerHTML = items.map((_, i) =>
        `<button type="button" role="tab" data-i="${i}" class="${i === 0 ? 'is-active' : ''}" aria-label="ไปที่รายการที่ ${i + 1}"></button>`
      ).join('');
      dotEls = Array.from(dotsWrap.querySelectorAll('button'));

      dotEls.forEach(d => d.addEventListener('click', () => {
        const i = Number(d.dataset.i);
        const target = items[i];
        if (!target) return;
        // คำนวณ scroll position ที่ทำให้ item อยู่ที่ตำแหน่งซ้ายของ container (เผื่อ padding)
        const targetLeft = target.offsetLeft - container.offsetLeft - 20;
        container.scrollTo({ left: Math.max(0, targetLeft), behavior: 'smooth' });
      }));

      // sync ตอน scroll
      let raf = null;
      scrollHandler = () => {
        if (raf) return;
        raf = requestAnimationFrame(() => {
          raf = null;
          const cardWidth = items[0].getBoundingClientRect().width;
          const gap = parseFloat(getComputedStyle(container).gap) || 14;
          const step = cardWidth + gap;
          const idx = Math.round(container.scrollLeft / step);
          activate(Math.max(0, Math.min(items.length - 1, idx)));
        });
      };
      container.addEventListener('scroll', scrollHandler, { passive: true });
    }

    function teardown() {
      if (scrollHandler) {
        container.removeEventListener('scroll', scrollHandler);
        scrollHandler = null;
      }
      clearDots();
    }

    function apply() {
      teardown();
      if (mq.matches) buildDots();
    }

    apply();
    mq.addEventListener
      ? mq.addEventListener('change', apply)
      : mq.addListener(apply);
  }
  window.TT.initMobileSlider = initMobileSlider;

  /* ---------- SEO meta from CMS ---------- */
  function setMeta(attr, name, content) {
    if (!content) return;
    let el = document.querySelector(`meta[${attr}="${name}"]`);
    if (!el) {
      el = document.createElement('meta');
      el.setAttribute(attr, name);
      document.head.appendChild(el);
    }
    el.setAttribute('content', content);
  }

  function applySeo(pageKey) {
    const seo = window.TT?.SEO?.[pageKey];
    if (!seo) return;
    if (seo.title) document.title = seo.title;
    setMeta('name', 'description', seo.description);
    setMeta('property', 'og:title', seo.ogTitle || seo.title);
    setMeta('property', 'og:description', seo.ogDescription || seo.description);
    setMeta('property', 'og:image', seo.ogImage);
    setMeta('property', 'og:type', 'website');
  }
  window.TT.applySeo = applySeo;

  /* ---------- Init ---------- */
  window.__TT_PREVIEW = new URLSearchParams(location.search).has('tt_preview');

  document.addEventListener('DOMContentLoaded', () => {
    refreshSiteShell();
    bindReveal();
    initBannerSlider();
    initWelcomePopup();
  });

  document.addEventListener('tt:data-updated', () => {
    refreshSiteShell();
  });
})();
