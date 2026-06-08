/* =========================================================
   programs.html — catalog: search, filters, sections, load more
   ========================================================= */
(() => {
  const PAGE_SIZE = 8;
  const LOAD_STEP = 8;

  function esc(s) {
    return String(s ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/"/g, '&quot;');
  }

  function renderDealCard(p, { index = 0, fmt }) {
    const ICONS = window.TT?.ICONS || {};
    const r = {
      rating: p.rating ?? 4.8,
      reviews: p.reviewCount || '500',
      oldPrice: p.oldPrice ?? 0,
      stars: p.stars ?? 5,
    };
    const discount = r.oldPrice ? Math.round((1 - p.basePrice / r.oldPrice) * 100) : 0;
    const tr = p.ribbon
      ? `<span class="deal-badge-tr ${p.ribbon === 'ยอดนิยม' ? 'is-hot' : 'is-info'}">${esc(p.ribbon)}</span>`
      : discount > 0 ? `<span class="deal-badge-tr">ประหยัด ${discount}%</span>` : '';
    const code = p.packageCode ?? String(index + 1);
    const tl = `รหัส #${esc(code)}`;
    const starIcon = ICONS.star || '★';
    const starsSvg = (n) => Array.from({ length: n }).map(() => starIcon).join('');
    return `
      <a class="deal-card" href="program.html?id=${encodeURIComponent(p.id)}" data-reveal>
        <div class="deal-media">
          <img src="${esc(p.image)}" alt="ภาพประกอบ ${esc(p.name)}" loading="lazy"/>
          <span class="deal-badge-tl">${tl}</span>
          ${tr}
        </div>
        <div class="deal-body">
          <h3>${esc(p.name)}</h3>
          <p class="deal-sub">${esc(p.route)}</p>
          <div class="deal-stars" aria-label="${r.stars} ดาว">${starsSvg(r.stars)}</div>
          <div class="deal-rate"><strong>${Number(r.rating).toFixed(1)}</strong>/5 <span class="dot"></span> <span>${esc(r.reviews)} รีวิว</span></div>
          <div class="deal-price">
            <small>เริ่มต้น · ${esc(p.duration)}</small>
            ${r.oldPrice && r.oldPrice > p.basePrice ? `<s>THB ${fmt.format(r.oldPrice)}</s>` : ''}
            <strong>THB ${fmt.format(p.basePrice)}</strong>
          </div>
        </div>
      </a>`;
  }

  function packageHaystack(p) {
    return [
      p.name,
      p.route,
      p.desc,
      p.packageCode,
      p.duration,
      p.ribbon,
      ...(p.stops || []),
      ...(p.boats || []),
    ].join(' ').toLowerCase();
  }

  function matchesFilters(p, filters) {
    if (filters.q) {
      const hay = packageHaystack(p);
      const terms = filters.q.toLowerCase().split(/\s+/).filter(Boolean);
      if (!terms.every((t) => hay.includes(t))) return false;
    }
    if (filters.boat && filters.boat !== 'all') {
      const boats = p.boats || [];
      if (!boats.includes(filters.boat)) return false;
    }
    if (filters.price !== 'all') {
      const price = p.basePrice ?? 0;
      if (filters.price === 'under1000' && price >= 1000) return false;
      if (filters.price === '1000-2000' && (price < 1000 || price > 2000)) return false;
      if (filters.price === 'over2000' && price <= 2000) return false;
    }
    return true;
  }

  function sortItems(items, sortKey) {
    const list = [...items];
    if (sortKey === 'price-asc') list.sort((a, b) => (a.basePrice ?? 0) - (b.basePrice ?? 0));
    else if (sortKey === 'price-desc') list.sort((a, b) => (b.basePrice ?? 0) - (a.basePrice ?? 0));
    else if (sortKey === 'rating') list.sort((a, b) => (b.rating ?? 0) - (a.rating ?? 0));
    return list;
  }

  window.TT.initProgramsCatalog = function initProgramsCatalog(opts = {}) {
    const root = document.getElementById(opts.rootId || 'programs-catalog');
    if (!root) return;

    const fmt = new Intl.NumberFormat('th-TH');
    const sectionsHost = document.getElementById('programs-sections');
    const emptyEl = document.getElementById('programs-empty');
    const countEl = document.getElementById('programs-result-count');
    if (!sectionsHost) return;

    function catalogSections() {
      const hs = window.TT?.HOME_SECTIONS || {};
      return [
        {
          id: 'daytrip',
          eyebrow: hs.boats?.eyebrow || 'One-day trips',
          title: hs.boats?.title || 'แพ็กเกจไปเช้า เย็นกลับ',
          lead: hs.boats?.lead || '',
          items: window.TT?.PROGRAMS || [],
        },
        {
          id: 'overnight',
          eyebrow: hs.programs?.eyebrow || 'พักค้างคืน',
          title: hs.programs?.title || 'แพ็กเกจ 2 วัน 1 คืน',
          lead: hs.programs?.lead || '',
          items: window.TT?.PACKAGES_2D1N || [],
        },
        {
          id: 'packages3d2n',
          eyebrow: hs.packages3d2n?.eyebrow || 'พักยาวขึ้น',
          title: hs.packages3d2n?.title || 'แพ็กเกจ 3 วัน 2 คืน',
          lead: hs.packages3d2n?.lead || '',
          items: window.TT?.PACKAGES_3D2N || [],
        },
      ];
    }

    const visible = {};
    catalogSections().forEach((s) => {
      if (visible[s.id] == null) visible[s.id] = PAGE_SIZE;
    });

    function getFilters() {
      return {
        q: (document.getElementById('programs-search')?.value || '').trim(),
        category: document.getElementById('programs-filter-category')?.value || 'all',
        sort: document.getElementById('programs-filter-sort')?.value || 'default',
        price: document.getElementById('programs-filter-price')?.value || 'all',
        boat: document.getElementById('programs-filter-boat')?.value || 'all',
      };
    }

    function resetVisible() {
      catalogSections().forEach((s) => { visible[s.id] = PAGE_SIZE; });
    }

    function render() {
      const filters = getFilters();
      let totalShown = 0;
      let totalMatch = 0;
      let html = '';

      catalogSections().forEach((section) => {
        if (filters.category !== 'all' && filters.category !== section.id) return;

        let items = section.items.filter((p) => matchesFilters(p, filters));
        items = sortItems(items, filters.sort);
        totalMatch += items.length;

        if (!items.length) return;

        const showN = visible[section.id];
        const slice = items.slice(0, showN);
        totalShown += slice.length;
        const hasMore = items.length > showN;

        html += `
          <section class="programs-category" id="programs-${section.id}" data-category="${section.id}">
            <div class="section-head section-head-wide" data-reveal>
              <span class="eyebrow">${esc(section.eyebrow)}</span>
              <h2>${esc(section.title)}</h2>
              ${section.lead ? `<p class="lead">${esc(section.lead)}</p>` : ''}
            </div>
            <div class="grid grid-4 programs-category-grid">
              ${slice.map((p, i) => renderDealCard(p, { index: i, fmt })).join('')}
            </div>
            ${hasMore ? `
              <div class="btn-row center mt-4" data-reveal>
                <button type="button" class="btn btn-outline programs-load-more" data-section="${section.id}" data-remaining="${items.length - showN}">
                  ดูแพ็กเกจเพิ่มเติม (${items.length - showN} รายการ)
                </button>
              </div>` : ''}
          </section>`;
      });

      sectionsHost.innerHTML = html;
      if (emptyEl) emptyEl.hidden = totalMatch > 0;
      if (countEl) {
        countEl.textContent = filters.q || filters.category !== 'all' || filters.price !== 'all' || filters.boat !== 'all'
          ? `พบ ${totalMatch} แพ็กเกจ · แสดง ${totalShown} การ์ด`
          : `ทั้งหมด ${totalMatch} แพ็กเกจ`;
      }

      sectionsHost.querySelectorAll('.programs-load-more').forEach((btn) => {
        btn.addEventListener('click', () => {
          const sid = btn.dataset.section;
          const remaining = parseInt(btn.dataset.remaining, 10) || LOAD_STEP;
          visible[sid] = (visible[sid] || PAGE_SIZE) + Math.min(LOAD_STEP, remaining);
          render();
          window.TT.bindReveal?.(sectionsHost);
        });
      });

      window.TT.bindReveal?.(sectionsHost);
    }

    function populateBoatFilter() {
      const boatSelect = document.getElementById('programs-filter-boat');
      const BOATS = window.TT?.BOATS || [];
      if (!boatSelect || !BOATS.length) return;
      const current = boatSelect.value || 'all';
      boatSelect.innerHTML = '<option value="all">เรือทุกประเภท</option>'
        + BOATS.map((b) => `<option value="${esc(b.id)}">${esc(b.name)}</option>`).join('');
      if ([...boatSelect.options].some((o) => o.value === current)) {
        boatSelect.value = current;
      }
    }

    if (!root.dataset.catalogBound) {
      root.dataset.catalogBound = '1';
      ['programs-search', 'programs-filter-category', 'programs-filter-sort', 'programs-filter-price', 'programs-filter-boat']
        .forEach((id) => {
          const el = document.getElementById(id);
          if (!el) return;
          const evt = id === 'programs-search' ? 'input' : 'change';
          el.addEventListener(evt, () => {
            resetVisible();
            render();
          });
        });
    }

    function clearAllFilters() {
      const search = document.getElementById('programs-search');
      if (search) search.value = '';
      const cat = document.getElementById('programs-filter-category');
      if (cat) cat.value = 'all';
      const sort = document.getElementById('programs-filter-sort');
      if (sort) sort.value = 'default';
      const price = document.getElementById('programs-filter-price');
      if (price) price.value = 'all';
      const boat = document.getElementById('programs-filter-boat');
      if (boat) boat.value = 'all';
      resetVisible();
      render();
    }

    if (!root.dataset.clearBound) {
      root.dataset.clearBound = '1';
      document.getElementById('programs-clear-filters')?.addEventListener('click', (e) => {
        e.preventDefault();
        clearAllFilters();
      });
    }

    // Hash deep-link: programs.html#overnight
    if (!root.dataset.hashApplied) {
      const hash = location.hash.replace('#', '');
      if (hash === 'overnight' || hash === 'daytrip' || hash === 'packages3d2n') {
        const cat = document.getElementById('programs-filter-category');
        if (cat) cat.value = hash;
      }
      root.dataset.hashApplied = '1';
    }

    populateBoatFilter();
    render();

    const hash = location.hash.replace('#', '');
    if (hash && document.getElementById(`programs-${hash}`)) {
      setTimeout(() => {
        document.getElementById(`programs-${hash}`)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 100);
    }
  };
})();
