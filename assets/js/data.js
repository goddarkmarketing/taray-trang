/* =========================================================
   Talay Trang — Data loader
   โหลดจาก fallback ทันที (ไม่ block หน้า) แล้วอัปเดตจาก api/data.php แบบ async
   ========================================================= */
(function () {
  const PX = (id, w = 1200) =>
    `https://images.pexels.com/photos/${id}/pexels-photo-${id}.jpeg?auto=compress&cs=tinysrgb&w=${w}`;

  function scriptBase() {
    const s = document.currentScript;
    if (s && s.src) {
      return s.src.replace(/assets\/js\/data\.js.*$/, '');
    }
    const el = document.querySelector('script[src*="assets/js/data.js"]');
    if (el && el.src) {
      return el.src.replace(/assets\/js\/data\.js.*$/, '');
    }
    const path = location.pathname;
    const i = path.lastIndexOf('/');
    return i >= 0 ? path.slice(0, i + 1) : './';
  }

  function apply(d) {
    if (!d || typeof d !== 'object' || Array.isArray(d)) {
      return false;
    }
    window.TT = {
      SITE: d.site || {},
      IMAGES: d.images || {},
      PX,
      NAV_ITEMS: d.navItems || [],
      BOATS: d.boats || [],
      OPTIONS: d.options || [],
      PROGRAMS: d.programs || [],
      REVIEWS: d.reviews || [],
      VIDEOS: d.videos || [],
      WHY_US: d.whyUs || [],
      STEPS: d.steps || [],
      SERVICES: d.services || [],
      HERO_SLIDES: d.heroSlides || [],
      ARTICLES: d.articles || [],
      ABOUT: d.about || {},
      SEO: d.seo || {},
      HOME_DEALS: d.homeDeals || {},
      HOME_SECTIONS: d.homeSections || {},
    };
    return true;
  }

  const emptyPayload = {
    site: {},
    images: {},
    navItems: [],
    boats: [],
    options: [],
    programs: [],
    reviews: [],
    videos: [],
    whyUs: [],
    steps: [],
    services: [],
    heroSlides: [],
    articles: [],
    about: {},
    seo: {},
    homeDeals: {},
    homeSections: {},
  };

  if (!apply(window.__TT_FALLBACK)) {
    console.warn('[TT] data-fallback.js missing or invalid — using empty defaults');
    apply(emptyPayload);
  }

  const base = scriptBase();
  if (!base || typeof fetch !== 'function') {
    return;
  }

  fetch(base + 'api/data.php', { credentials: 'same-origin', cache: 'no-store' })
    .then((res) => (res.ok ? res.json() : null))
    .then((data) => {
      if (!data || data.error) {
        return;
      }
      if (apply(data)) {
        document.dispatchEvent(new CustomEvent('tt:data-updated'));
      }
    })
    .catch((err) => {
      console.warn('[TT] API refresh skipped', err);
    });
})();
