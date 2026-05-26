/* =========================================================
   Talay Trang — Data loader
   โหลดจาก api/data.php (แก้ผ่าน admin) + fallback จาก data-fallback.js
   ========================================================= */
(function () {
  const PX = (id, w = 1200) =>
    `https://images.pexels.com/photos/${id}/pexels-photo-${id}.jpeg?auto=compress&cs=tinysrgb&w=${w}`;

  function scriptBase() {
    const s = document.currentScript;
    if (!s || !s.src) return '';
    return s.src.replace(/assets\/js\/data\.js.*$/, '');
  }

  function loadFromApi() {
    try {
      const xhr = new XMLHttpRequest();
      xhr.open('GET', scriptBase() + 'api/data.php', false);
      xhr.send(null);
      if (xhr.status >= 200 && xhr.status < 300) {
        return JSON.parse(xhr.responseText);
      }
    } catch (e) {
      console.warn('[TT] API load failed', e);
    }
    return null;
  }

  function apply(d) {
    window.TT = {
      SITE: d.site,
      IMAGES: d.images,
      PX,
      NAV_ITEMS: d.navItems,
      BOATS: d.boats,
      OPTIONS: d.options,
      PROGRAMS: d.programs,
      REVIEWS: d.reviews,
      VIDEOS: d.videos,
      WHY_US: d.whyUs,
      STEPS: d.steps,
      SERVICES: d.services,
      HERO_SLIDES: d.heroSlides,
      ARTICLES: d.articles,
      ABOUT: d.about || {},
      SEO: d.seo || {},
      HOME_DEALS: d.homeDeals || {},
    };
  }

  const payload = loadFromApi() || window.__TT_FALLBACK;
  if (!payload) {
    console.error('[TT] No data — ตรวจสอบ api/data.php และ data-fallback.js');
    window.TT = { SITE: {}, IMAGES: {}, PX, NAV_ITEMS: [], BOATS: [], OPTIONS: [], PROGRAMS: [], REVIEWS: [], VIDEOS: [], WHY_US: [], STEPS: [], SERVICES: [], HERO_SLIDES: [], ARTICLES: [], ABOUT: {}, SEO: {}, HOME_DEALS: {} };
  } else {
    apply(payload);
  }
})();
