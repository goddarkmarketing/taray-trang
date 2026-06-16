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

  // SVG icons (inline so we keep no external deps)
  const ICONS = {
    line: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.36 10.62c0-3.78-3.79-6.85-8.45-6.85S2.46 6.84 2.46 10.62c0 3.39 3 6.23 7.06 6.77.28.06.65.18.74.42.08.21.05.55.03.77l-.12.72c-.04.21-.17.83.73.45.9-.38 4.84-2.85 6.61-4.88 1.22-1.34 1.85-2.7 1.85-4.25Zm-11.4 2.02H6.28c-.24 0-.43-.2-.43-.43V9.05c0-.24.2-.43.43-.43.24 0 .43.19.43.43v2.74h1.25c.24 0 .43.19.43.43 0 .23-.19.42-.43.42Zm1.69-.43c0 .23-.19.43-.43.43-.24 0-.43-.2-.43-.43V9.05c0-.24.19-.43.43-.43.24 0 .43.19.43.43v3.16Zm3.79 0c0 .18-.12.35-.3.41a.44.44 0 0 1-.13.02c-.14 0-.27-.07-.35-.18l-1.72-2.35v2.1c0 .23-.19.43-.43.43-.24 0-.43-.2-.43-.43V9.05c0-.18.12-.35.3-.41.05-.01.09-.02.13-.02.14 0 .27.07.35.17l1.72 2.35v-2.1c0-.24.19-.43.43-.43.24 0 .43.19.43.43v3.16Zm3.05-1.6h-1.25v.74h1.25c.23 0 .43.2.43.43 0 .23-.2.43-.43.43h-1.69c-.23 0-.42-.2-.42-.43V9.05c0-.23.19-.43.43-.43h1.69c.23 0 .43.19.43.43 0 .23-.2.43-.43.43h-1.25v.74h1.25c.23 0 .43.19.43.43 0 .23-.2.42-.44.42Z"/></svg>',
    phone: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57a1 1 0 0 0-1.02.24l-2.2 2.2a15.05 15.05 0 0 1-6.59-6.58l2.2-2.21a1 1 0 0 0 .25-1A11.36 11.36 0 0 1 8.5 4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1c0 9.39 7.61 17 17 17a1 1 0 0 0 1-1v-3.5a1 1 0 0 0-1-1Z"/></svg>',
    menu: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
    close: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>',
    arrow: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>',
    play: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>',
    star: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>',
    map: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7Zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5Z"/></svg>',
    clock: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm.5 11H7v-2h4.5V6h2v7Z"/></svg>',
    sun: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>',
    moon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 14.5A8.5 8.5 0 0 1 10.5 4 9 9 0 0 0 21 14.5Z"/></svg>',
    calendar: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>',
    fb: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12Z"/></svg>',
    tt: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.6 6.32a4.83 4.83 0 0 1-3.77-2.16h-2.99v13.05a2.83 2.83 0 1 1-2.83-2.83c.21 0 .42.03.62.07v-3.05a5.94 5.94 0 1 0 5.21 5.9V9.43a7.96 7.96 0 0 0 4.45 1.37V7.81a4.86 4.86 0 0 1-.69-1.49Z"/></svg>',
    shield: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>',
    starOutline: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14 2 9.27l6.91-1.01L12 2Z"/></svg>',
    compass: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor" stroke="none"/></svg>',
    chat: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg>',
    users: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    route: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/><circle cx="18" cy="5" r="3"/></svg>',
    /* Service pill icons */
    info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>',
    alert: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>',
    calculator: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M8 6h8M8 10h2M12 10h2M16 10h0M8 14h2M12 14h2M16 14h0M8 18h2M12 18h6"/></svg>',
    anchor: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="2"/><path d="M12 7v15"/><path d="M5 12H3a9 9 0 0 0 18 0h-2"/><path d="M8 12h8"/></svg>',
    bigBoat: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18c2 1 3 1 4.5 1s2.5 0 4.5-1 3-1 4.5-1 2.5 0 4.5 1"/><path d="M5 16V9a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v7"/><path d="M9 8V5h6v3"/><path d="M12 11v3"/></svg>',
    longtail: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 18c2 1 3 1 4.5 1s2.5 0 4.5-1 3-1 4.5-1 2.5 0 4.5 1"/><path d="M4 16l1.5-4h12L19 16"/><path d="M11 12V7l9 3"/></svg>',
    speedboat: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17c2 1 3 1 4.5 1s2.5 0 4.5-1 3-1 4.5-1 2.5 0 4.5 1"/><path d="M5 15l2-5h8l3 5"/><path d="M9 10V6h4"/><path d="M19 8l2-1"/></svg>',
    bed: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v7"/><path d="M3 14h18"/><path d="M3 20v-2M21 20v-2"/><path d="M7 9V7a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>',
    ticket: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4Z"/><path d="M13 7v2M13 12v2M13 17v0"/></svg>',
    car: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h14"/><path d="M5 17a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm18 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/><path d="M3 17v-5l2-5h12l2 5v5"/><path d="M5 12h14"/></svg>',
    briefcase: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 13h18"/></svg>',
    scuba: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="4"/><path d="M8 9h-2a2 2 0 0 0-2 2v3"/><path d="M16 9h2a2 2 0 0 1 2 2v3"/><path d="M3 20c2 0 3-1 4.5-1S10 20 12 20s3-1 4.5-1S19 20 21 20"/></svg>',
    cutlery: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 2v8a2 2 0 0 0 2 2v9"/><path d="M11 2v8a2 2 0 0 1-2 2"/><path d="M17 14V2c-2 0-3 2-3 4v5c0 2 1 3 3 3v7"/></svg>',
    edit: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="m18.5 2.5 3 3L12 15l-4 1 1-4Z"/></svg>',
    camera: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 1 2-2h2l2-3h6l2 3h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><circle cx="12" cy="13" r="4"/></svg>',
    /* Booking icons */
    carSelf: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 13l2-5a2 2 0 0 1 1.9-1.3h10.2A2 2 0 0 1 19 8l2 5"/><path d="M3 13h18v5a1 1 0 0 1-1 1h-1.5a1 1 0 0 1-1-1v-1H6.5v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Z"/><circle cx="7" cy="16" r="1"/><circle cx="17" cy="16" r="1"/></svg>',
    carPick: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 15h2l1-5a2 2 0 0 1 2-1.6h8.7a2 2 0 0 1 1.7 1l2 3.6h1.6a1 1 0 0 1 1 1v2H18"/><circle cx="7" cy="17" r="2"/><circle cx="15" cy="17" r="2"/><path d="M9 15h-2"/><path d="M11 9V5"/><path d="M9 7l2-2 2 2"/></svg>',
    chatBubble: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.5 7.2L4 21l1.7-5.4A8 8 0 1 1 21 12Z"/><path d="M12 11v.01M12 14h0M11 9a1 1 0 1 1 1.5.9c-.4.2-.5.4-.5.8"/></svg>',
    check: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>',
    copy: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>',
    minus: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14"/></svg>',
    plus: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>',
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
