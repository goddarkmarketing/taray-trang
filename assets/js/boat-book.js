/* Talay Trang — Boat charter booking wizard (per-boat profiles) */
(() => {
  const ready = (fn) => (document.readyState !== 'loading'
    ? fn() : document.addEventListener('DOMContentLoaded', fn));

  const checkSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>';
  const shieldSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
  const arrowNext = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
  const arrowBack = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 5l-7 7 7 7"/></svg>';

  const STEPS = [
    { id: 'boat', label: 'เรือ' },
    { id: 'route', label: 'เส้นทาง' },
    { id: 'tier', label: 'จำนวนคน' },
    { id: 'insurance', label: 'ประกัน' },
    { id: 'addons', label: 'เสริม' },
    { id: 'summary', label: 'สรุป' },
    { id: 'contact', label: 'จอง' },
  ];

  ready(() => {
    const form = document.getElementById('bb-form');
    const wizardEl = document.getElementById('bb-wizard');
    const progressEl = document.getElementById('bb-progress');
    if (!form || !wizardEl || !progressEl) return;

    const { SITE, BOATS, BOAT_BOOKING, ICONS } = window.TT || {};
    const cfg = BOAT_BOOKING || {};
    const routes = cfg.routes || [];
    const peopleTiers = cfg.peopleTiers || [];
    const addons = cfg.addons || [];
    const fmt = new Intl.NumberFormat('th-TH');
    const lineUrl = SITE?.lineUrl || 'https://line.me/R/ti/p/%40talaytrang';

    const params = new URLSearchParams(location.search);
    let defaultBoatId = params.get('boat') || BOATS?.[0]?.id || 'longtail';
    if (!BOATS?.find((b) => b.id === defaultBoatId)) defaultBoatId = BOATS?.[0]?.id || 'longtail';

    let currentStep = 'boat';
    let steps = [];
    let pips = [];
    let pipLines = [];

    document.getElementById('hero-img').src = window.TT?.IMAGES?.heroBooking || '';
    document.getElementById('bb-page-title').textContent = 'จองเรือเหมาลำ';
    document.getElementById('bb-page-lead').textContent = 'เลือกเรือ เส้นทาง และบริการเสริม ทีละขั้นตอน';

    function getBoat(boatId) {
      return BOATS.find((b) => b.id === boatId) || BOATS[0];
    }

    function getProfile(boatId) {
      const custom = cfg.profiles?.[boatId];
      if (custom) {
        const options = custom.selectionMode === 'size'
          ? (custom.sizes || [])
          : peopleTiers;
        return {
          selectionMode: custom.selectionMode || 'people',
          tierProgressLabel: custom.tierProgressLabel || 'จำนวนคน',
          tierStepTitle: custom.tierStepTitle || 'เลือกจำนวนคน',
          tierStepDesc: custom.tierStepDesc || '',
          charterLabel: custom.charterLabel || 'ราคาค่าเรือ (เหมาลำ)',
          insurancePerPerson: Number(custom.insurancePerPerson ?? cfg.insurancePerPerson) || 40,
          guideFee: Number(custom.guideFee) || 0,
          safetyStaffRate: Number(custom.safetyStaffRate) || 0,
          safetyStaffRatio: Number(custom.safetyStaffRatio) || 20,
          charterSummaryPrefix: custom.charterSummaryPrefix || '',
          includedBoxTitle: custom.includedBoxTitle || '',
          footerNote: custom.footerNote || '',
          options,
          charterPrices: custom.charterPrices || cfg.charterPrices?.[boatId] || {},
        };
      }
      return {
        selectionMode: 'people',
        tierProgressLabel: 'จำนวนคน',
        tierStepTitle: 'เลือกจำนวนคน',
        tierStepDesc: 'เมื่อเลือกเส้นทางเสร็จแล้ว เลือกจำนวนคน ราคาค่าเรือจะแสดงทันที',
        charterLabel: 'ราคาค่าเรือ (เหมาลำ)',
        insurancePerPerson: Number(cfg.insurancePerPerson) || 40,
        guideFee: 0,
        safetyStaffRate: 0,
        safetyStaffRatio: 20,
        charterSummaryPrefix: '',
        includedBoxTitle: '',
        footerNote: '',
        options: peopleTiers,
        charterPrices: cfg.charterPrices?.[boatId] || {},
      };
    }

    function getSelectedOption(state) {
      const profile = getProfile(state.boatId);
      return profile.options.find((o) => o.id === state.tierId) || null;
    }

    function optionPeople(option) {
      if (!option) return 0;
      return option.capacity ?? option.max ?? 0;
    }

    function charterPrice(state) {
      const profile = getProfile(state.boatId);
      const matrix = profile.charterPrices?.[state.routeId]?.[state.tierId]
        ?? cfg.charterPrices?.[state.boatId]?.[state.routeId]?.[state.tierId];
      if (matrix != null) return matrix;
      const boat = getBoat(state.boatId);
      const routeIdx = routes.findIndex((r) => r.id === state.routeId);
      const routeMul = 1 + Math.max(routeIdx, 0) * 0.08;
      if (profile.selectionMode === 'size') {
        const sizeMul = { s1: 1, s2: 1.42, s3: 1.85 }[state.tierId] || 1;
        return Math.round((boat?.basePrice || 10000) * sizeMul * routeMul);
      }
      const tierMul = { p1: 1, p2: 1.25, p3: 1.5, p4: 1.8 }[state.tierId] || 1;
      return Math.round((boat?.basePrice || 4000) * tierMul * routeMul);
    }

    function getState() {
      const fd = new FormData(form);
      return {
        boatId: fd.get('boat')?.toString() || defaultBoatId,
        routeId: fd.get('route')?.toString() || '',
        tierId: fd.get('tier')?.toString() || '',
        addonIds: fd.getAll('addon').map(String),
        date: fd.get('date')?.toString() || '',
        name: fd.get('name')?.toString().trim() || '',
        phone: fd.get('phone')?.toString().trim() || '',
        note: fd.get('note')?.toString().trim() || '',
      };
    }

    function calculate(state) {
      const profile = getProfile(state.boatId);
      const boat = getBoat(state.boatId);
      const route = routes.find((r) => r.id === state.routeId);
      const option = getSelectedOption(state);
      const people = optionPeople(option);
      const charter = state.routeId && state.tierId ? charterPrice(state) : 0;
      const guideFee = profile.guideFee || 0;
      const insurance = people > 0 ? profile.insurancePerPerson * people : 0;
      const safetyStaffCount = profile.safetyStaffRate && people > 0
        ? Math.ceil(people / (profile.safetyStaffRatio || 20))
        : 0;
      const safetyStaffTotal = safetyStaffCount * (profile.safetyStaffRate || 0);
      const selectedAddons = addons.filter((a) => state.addonIds.includes(a.id));
      const addonTotal = selectedAddons.reduce((s, a) => s + (Number(a.price) || 0), 0);
      return {
        profile, boat, route, option, people, charter, guideFee, insurance,
        insuranceRate: profile.insurancePerPerson,
        safetyStaffCount, safetyStaffTotal,
        safetyStaffRate: profile.safetyStaffRate || 0,
        safetyStaffRatio: profile.safetyStaffRatio || 20,
        selectedAddons, addonTotal,
        total: charter + guideFee + safetyStaffTotal + insurance + addonTotal,
      };
    }

    function formatThaiDate(d) {
      if (!d) return '-';
      try {
        const dt = new Date(d + 'T12:00:00');
        const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        return `${dt.getDate()} ${months[dt.getMonth()]} ${dt.getFullYear() + 543}`;
      } catch { return d; }
    }

    function stepHead(num, title, desc) {
      return `
        <div class="bb-wizard-head">
          <h3><span class="bb-step-num-lg">${num}</span>${title}</h3>
          ${desc ? `<p class="bb-wizard-desc">${desc}</p>` : ''}
        </div>`;
    }

    function wizardNav(back, next, nextLabel, extra = '') {
      const forwardOnly = !back && !extra;
      return `
        <div class="wizard-nav${forwardOnly ? ' is-forward-only' : ''}">
          ${back
            ? `<button type="button" class="btn btn-ghost wizard-back" data-back="${back}">${arrowBack} ย้อนกลับ</button>`
            : ''}
          ${extra || `<button type="button" class="btn btn-primary wizard-next" data-next="${next}">${nextLabel || 'ถัดไป'} ${arrowNext}</button>`}
        </div>`;
    }

    function tierRangeText(option) {
      if (option.min != null && option.max != null) return `${option.min}–${option.max}`;
      return (option.label || '').replace(/\s*คน\s*$/, '').trim();
    }

    function tierOptionsHtml(profile, selectedId) {
      const opts = profile.options;
      const firstId = opts[0]?.id || '';
      const sel = selectedId || firstId;
      if (profile.selectionMode === 'size') {
        return `
          <div class="bb-size-grid">
            ${opts.map((s) => `
              <label class="bb-size-card${s.id === sel ? ' is-selected' : ''}">
                <input type="radio" name="tier" value="${s.id}"${s.id === sel ? ' checked' : ''}/>
                <span class="bb-size-icon">${ICONS?.users || '👥'}</span>
                <strong>${s.label}</strong>
                <span>${s.capacityLabel || `จุ ${s.capacity} คน`}</span>
              </label>`).join('')}
          </div>`;
      }
      return `
        <div class="bb-tier-grid bb-tier-grid--people">
          ${opts.map((t) => `
            <label class="bb-tier-card${t.id === sel ? ' is-selected' : ''}">
              <input type="radio" name="tier" value="${t.id}"${t.id === sel ? ' checked' : ''}/>
              <span class="bb-tier-card-body">
                <span class="bb-tier-range">${tierRangeText(t)}</span>
                <span class="bb-tier-unit">คน</span>
              </span>
            </label>`).join('')}
        </div>`;
    }

    function includedBoxHtml(profile) {
      if (!profile.includedBoxTitle) return '';
      return `
        <div class="bb-included-box">
          <strong>${profile.includedBoxTitle}</strong>
          <ul>
            ${profile.guideFee ? `<li>ค่ามัคคุเทศก์ <strong>฿${fmt.format(profile.guideFee)}</strong></li>` : ''}
            ${profile.safetyStaffRate ? `<li>สต๊าฟดูแลความปลอดภัย คนละ <strong>฿${fmt.format(profile.safetyStaffRate)}</strong> (คำนวณจากลูกค้า ${profile.safetyStaffRatio || 20} คน ต่อสต๊าฟ 1 คน)</li>` : ''}
            <li>ประกันอุบัติเหตุ คนละ <strong>฿${fmt.format(profile.insurancePerPerson)}</strong></li>
          </ul>
          <p class="muted-sm">(คำนวณอัตโนมัติตามจำนวนคน)</p>
        </div>`;
    }

    function tierStepBody(profile) {
      const fieldLabel = profile.selectionMode === 'size' ? 'เลือกขนาดเรือ' : 'เลือกจำนวนคน';
      return `
        <div class="bb-tier-step">
          <div class="bb-tier-main">
            <p class="bb-field-label">${fieldLabel} <span class="req">*</span></p>
            <div id="bb-tier-options">${tierOptionsHtml(profile)}</div>
          </div>
          <aside class="bb-tier-aside">
            <div class="bb-charter-price" aria-live="polite">
              <span class="bb-charter-price-label" id="bb-charter-label">${profile.charterLabel}</span>
              <strong class="bb-charter-price-value" id="bb-charter-amount">฿0</strong>
            </div>
            ${includedBoxHtml(profile)}
          </aside>
        </div>`;
    }

    function insuranceStepBody(profile) {
      const guideBlock = profile.guideFee ? `
        <div class="bb-guide-fee">
          <span>ค่ามัคคุเทศก์ (บังคับ)</span>
          <strong id="bb-guide-amount">฿${fmt.format(profile.guideFee)}</strong>
        </div>` : '';
      const safetyBlock = profile.safetyStaffRate ? `
        <div class="bb-guide-fee bb-safety-staff">
          <div class="bb-safety-staff-text">
            <span>สต๊าฟดูแลความปลอดภัย (บังคับ)</span>
            <small id="bb-safety-staff-formula" class="muted-sm">คำนวณจากจำนวนคนบนเรือ</small>
          </div>
          <strong id="bb-safety-staff-amount">฿0</strong>
        </div>` : '';
      return `
        ${guideBlock}
        ${safetyBlock}
        <div class="bb-insurance" aria-live="polite">
          <span class="bb-insurance-icon" aria-hidden="true">${shieldSvg}</span>
          <div class="bb-insurance-text">
            <strong>ค่าประกันอุบัติเหตุ (บังคับ)</strong>
            <span id="bb-insurance-formula">${profile.insurancePerPerson} บาท × จำนวนคน</span>
          </div>
          <strong class="bb-insurance-amount" id="bb-insurance-amount">฿0</strong>
        </div>`;
    }

    function buildWizard(activeBoatId) {
      const profile = getProfile(activeBoatId);
      const tierNextLabel = profile.selectionMode === 'size'
        ? 'ถัดไป<span class="wizard-next-detail">: ค่าบังคับ</span>'
        : 'ถัดไป<span class="wizard-next-detail">: ค่าประกัน</span>';
      const routeNextLabel = profile.selectionMode === 'size'
        ? 'ถัดไป<span class="wizard-next-detail">: เลือกขนาดเรือ</span>'
        : 'ถัดไป<span class="wizard-next-detail">: เลือกจำนวนคน</span>';

      wizardEl.innerHTML = `
        <div class="wizard-step is-active" data-step="boat">
          <div class="form-card bb-form-card">
            ${stepHead(1, 'เลือกประเภทเรือ', 'เมื่อลูกค้าเลือกเมนูเรือแล้ว กดถัดไป')}
            <div class="bb-boat-pick-grid">
              ${BOATS.map((b) => `
                <label class="bb-boat-pick${b.id === activeBoatId ? ' is-selected' : ''}">
                  <input type="radio" name="boat" value="${b.id}"${b.id === activeBoatId ? ' checked' : ''}/>
                  <span class="bb-boat-pick-img"><img src="${b.image}" alt="${b.name}" loading="lazy"/></span>
                  <span class="bb-boat-pick-body">
                    <strong>${b.name}</strong>
                    <span class="bb-boat-pick-capacity">${b.capacity}</span>
                  </span>
                  <span class="bb-pick-check">${checkSvg}</span>
                </label>`).join('')}
            </div>
          </div>
          ${wizardNav('', 'route', 'ถัดไป<span class="wizard-next-detail">: เลือกเส้นทาง</span>')}
        </div>

        <div class="wizard-step" data-step="route">
          <div class="form-card bb-form-card">
            ${stepHead(2, 'เลือกเส้นทาง', 'ลูกค้าจะต้องเลือกเส้นทาง ซึ่งมี 6 เส้นทางให้เลือก')}
            <p class="bb-field-label">เลือกเส้นทาง <span class="req">*</span> <span class="field-hint">(เลือก 1 เส้นทาง)</span></p>
            <div class="bb-route-grid">
              ${routes.map((r, i) => `
                <label class="bb-route${i === 0 ? ' is-selected' : ''}">
                  <input type="radio" name="route" value="${r.id}"${i === 0 ? ' checked' : ''}/>
                  <span class="bb-route-img"><img src="${r.image}" alt="${r.name}" loading="lazy"/></span>
                  <span class="bb-route-body">
                    <span class="bb-route-name">${r.name}</span>
                    ${r.subtitle ? `<span class="bb-route-sub">${r.subtitle}</span>` : ''}
                  </span>
                  <span class="bb-pick-check">${checkSvg}</span>
                </label>`).join('')}
            </div>
          </div>
          ${wizardNav('boat', 'tier', routeNextLabel)}
        </div>

        <div class="wizard-step" data-step="tier">
          <div class="form-card bb-form-card">
            ${stepHead(3, profile.tierStepTitle, profile.tierStepDesc)}
            <div id="bb-tier-step">${tierStepBody(profile)}</div>
          </div>
          ${wizardNav('route', 'insurance', tierNextLabel)}
        </div>

        <div class="wizard-step" data-step="insurance">
          <div class="form-card bb-form-card">
            ${stepHead(4, 'ค่าประกันอุบัติเหตุ', `ค่าประกันอุบัติเหตุบังคับ ${profile.insurancePerPerson} บาทต่อคน`)}
            <div id="bb-insurance-step">${insuranceStepBody(profile)}</div>
          </div>
          ${wizardNav('tier', 'addons', 'ถัดไป<span class="wizard-next-detail">: บริการเสริม</span>')}
        </div>

        <div class="wizard-step" data-step="addons">
          <div class="form-card bb-form-card">
            ${stepHead(5, 'เลือกเมนูเพิ่มเติม', 'ลูกค้าจะต้องเลือกเมนูเพิ่มเติมที่มีให้ (สามารถเลือกเพิ่มได้มากกว่า 1 เมนู)')}
            <div class="bb-addon-grid">
              ${addons.map((a) => `
                <label class="bb-addon">
                  <input type="checkbox" name="addon" value="${a.id}"/>
                  <span class="bb-addon-icon">${ICONS?.[a.icon] || ICONS?.plus || '+'}</span>
                  <span class="bb-addon-body">
                    <strong>${a.label}</strong>
                    <span>${a.desc || ''}</span>
                  </span>
                  <span class="bb-addon-meta">
                    <span class="bb-addon-price">${a.priceLabel || fmt.format(a.price) + ' บาท'}</span>
                    <span class="bb-addon-check"><span class="bb-addon-check-txt">เพิ่ม</span>${checkSvg}</span>
                  </span>
                </label>`).join('')}
            </div>
          </div>
          ${wizardNav('insurance', 'summary', 'ถัดไป<span class="wizard-next-detail">: สรุปยอด</span>')}
        </div>

        <div class="wizard-step" data-step="summary">
          <div class="form-card bb-form-card">
            ${stepHead(6, 'สรุปรายการทั้งหมด', 'หลังจากเลือกครบแล้ว ระบบจะสรุปรายการให้ลูกค้าตรวจสอบ')}
            <div class="bb-inline-summary-wrap">
              <div class="bb-inline-summary-head">
                <span class="bb-cart-head-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
                </span>
                <strong>สรุปยอดรวม</strong>
              </div>
              <div class="bb-inline-summary bb-summary-card" id="bb-inline-summary" aria-live="polite"></div>
            </div>
            <p class="bb-footer-note" id="bb-footer-note"${profile.footerNote ? '' : ' hidden'}>${profile.footerNote || ''}</p>
          </div>
          ${wizardNav('addons', 'contact', 'ถัดไป<span class="wizard-next-detail">: กรอกข้อมูลจอง</span>')}
        </div>

        <div class="wizard-step" data-step="contact">
          <div class="form-card bb-form-card">
            ${stepHead(7, 'กรอกรายละเอียด &amp; จอง', 'กรอกข้อมูลแล้วกดจอง / ชำระเงินผ่าน LINE')}
            <div class="field-row two">
              <div class="field" data-bb-field="date">
                <label for="bb-date">วันที่เดินทาง <span class="req">*</span></label>
                <input class="input" type="date" id="bb-date" name="date" required/>
                <div class="field-error"></div>
              </div>
              <div class="field" data-bb-field="name">
                <label for="bb-name">ชื่อ-นามสกุล <span class="req">*</span></label>
                <input class="input" type="text" id="bb-name" name="name" autocomplete="name" required/>
                <div class="field-error"></div>
              </div>
            </div>
            <div class="field-row two">
              <div class="field" data-bb-field="phone">
                <label for="bb-phone">เบอร์โทร <span class="req">*</span></label>
                <input class="input" type="tel" id="bb-phone" name="phone" autocomplete="tel" inputmode="tel" required/>
                <div class="field-error"></div>
              </div>
              <div class="field">
                <label for="bb-note">หมายเหตุ <span class="field-hint">(ถ้ามี)</span></label>
                <input class="input" type="text" id="bb-note" name="note"/>
              </div>
            </div>
            <div class="bb-contact-total">
              <span>ยอดชำระสุทธิ</span>
              <strong id="bb-contact-total">฿0</strong>
            </div>
          </div>
          ${wizardNav('summary', '', '', `
            <button type="button" class="btn btn-line btn-lg wizard-next" id="bb-submit-line">
              <span data-icon="line"></span> จอง / ชำระเงินผ่าน LINE
            </button>`)}
        </div>`;

      const dateInput = form.querySelector('#bb-date');
      if (dateInput) dateInput.min = new Date().toISOString().split('T')[0];

      steps = [...form.querySelectorAll('.wizard-step')];
      bindWizardEvents();
      updateProgressLabels(profile);
      gotoStep(currentStep, { skipScroll: true });
    }

    function renderProgress() {
      progressEl.innerHTML = STEPS.map((s, i) => {
        const line = i < STEPS.length - 1
          ? `<li class="pip-line" data-line-after="${s.id}"></li>`
          : '';
        return `
          <li class="pip" data-pip="${s.id}">
            <span class="pip-num">${i + 1}</span>
            <span class="pip-text" data-pip-text="${s.id}">${s.label}</span>
          </li>${line}`;
      }).join('');
      pips = [...progressEl.querySelectorAll('.pip')];
      pipLines = [...progressEl.querySelectorAll('.pip-line')];
    }

    function updateProgressLabels(profile) {
      const tierPip = progressEl.querySelector('[data-pip-text="tier"]');
      if (tierPip) tierPip.textContent = profile.tierProgressLabel;
    }

    function gotoStep(targetKey, { skipScroll = false } = {}) {
      if (!STEPS.some((s) => s.id === targetKey)) return;
      currentStep = targetKey;
      steps.forEach((s) => s.classList.toggle('is-active', s.dataset.step === targetKey));
      const curIdx = STEPS.findIndex((s) => s.id === targetKey);
      pips.forEach((li) => {
        const idx = STEPS.findIndex((s) => s.id === li.dataset.pip);
        li.classList.toggle('is-active', idx === curIdx);
        li.classList.toggle('is-done', idx < curIdx);
      });
      pipLines.forEach((line) => {
        const afterIdx = STEPS.findIndex((s) => s.id === line.dataset.lineAfter);
        line.classList.toggle('is-done', afterIdx < curIdx);
      });
      if (!skipScroll) {
        const sec = document.querySelector('.bb-section');
        if (sec) {
          const headerH = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--header-h')) || 70;
          const y = sec.getBoundingClientRect().top + window.scrollY - headerH - 12;
          window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
        }
      }
    }

    function syncPickStyles() {
      form.querySelectorAll('.bb-boat-pick, .bb-route, .bb-tier-card, .bb-size-card, .bb-addon').forEach((el) => {
        el.classList.toggle('is-selected', el.querySelector('input')?.checked);
      });
    }

    function charterSummaryLabel(calc) {
      if (!calc.route || !calc.option) return '';
      if (calc.profile.charterSummaryPrefix) {
        return `${calc.profile.charterSummaryPrefix} (${calc.route.name} / ${calc.option.label} จุ ${calc.people} คน)`;
      }
      if (calc.profile.selectionMode === 'size') {
        return `ค่าเรือสปีดโบ๊ท (${calc.route.name} / ${calc.option.label} / ${calc.people} คน)`;
      }
      return `ค่าเรือ (${calc.route.name} / ${calc.option.label})`;
    }

    function summaryLines(calc) {
      const lines = [];
      if (calc.route && calc.option) {
        lines.push(`<div class="bb-summary-row"><span>${charterSummaryLabel(calc)}</span><strong>฿${fmt.format(calc.charter)}</strong></div>`);
      }
      if (calc.guideFee) {
        lines.push(`<div class="bb-summary-row"><span>ค่ามัคคุเทศก์</span><strong>฿${fmt.format(calc.guideFee)}</strong></div>`);
      }
      if (calc.safetyStaffTotal) {
        lines.push(`<div class="bb-summary-row"><span>สต๊าฟดูแลความปลอดภัย (${calc.people} คน ÷ ${calc.safetyStaffRatio} = ${calc.safetyStaffCount} คน)</span><strong>฿${fmt.format(calc.safetyStaffTotal)}</strong></div>`);
      }
      if (calc.insurance) {
        lines.push(`<div class="bb-summary-row"><span>ประกันอุบัติเหตุ (${calc.insuranceRate} × ${calc.people} คน)</span><strong>฿${fmt.format(calc.insurance)}</strong></div>`);
      }
      calc.selectedAddons.forEach((a) => {
        lines.push(`<div class="bb-summary-row"><span>${a.label}</span><strong>฿${fmt.format(a.price)}</strong></div>`);
      });
      return lines;
    }

    function render() {
      const state = getState();
      const calc = calculate(state);
      syncPickStyles();

      if (calc.boat) {
        document.getElementById('bb-page-title').textContent = `จอง${calc.boat.name}`;
      }

      const charterEl = document.getElementById('bb-charter-amount');
      if (charterEl) charterEl.textContent = calc.charter ? `฿${fmt.format(calc.charter)}` : '—';

      const insFormula = document.getElementById('bb-insurance-formula');
      const insAmount = document.getElementById('bb-insurance-amount');
      if (insFormula) {
        insFormula.textContent = calc.people
          ? `${calc.insuranceRate} บาท × ${calc.people} คน`
          : `${calc.insuranceRate} บาท × จำนวนคน`;
      }
      if (insAmount) {
        insAmount.innerHTML = calc.insurance
          ? `฿${fmt.format(calc.insurance)} <small>(${calc.people} คน)</small>`
          : '฿0';
      }

      const safetyFormula = document.getElementById('bb-safety-staff-formula');
      const safetyAmount = document.getElementById('bb-safety-staff-amount');
      if (safetyFormula) {
        safetyFormula.textContent = calc.people
          ? `${calc.people} คน ÷ ${calc.safetyStaffRatio} = ${calc.safetyStaffCount} คน`
          : 'คำนวณจากจำนวนคนบนเรือ';
      }
      if (safetyAmount) {
        safetyAmount.textContent = calc.safetyStaffTotal
          ? `฿${fmt.format(calc.safetyStaffTotal)}`
          : '฿0';
      }

      const footerNote = document.getElementById('bb-footer-note');
      if (footerNote) {
        footerNote.textContent = calc.profile.footerNote || '';
        footerNote.hidden = !calc.profile.footerNote;
      }

      const lines = summaryLines(calc);
      const summaryHtml = lines.length
        ? lines.join('') + `<div class="bb-summary-total"><span>ยอดชำระสุทธิ</span><strong>฿${fmt.format(calc.total)}</strong></div>`
        : '<p class="bb-cart-empty muted-sm">เลือกเส้นทางและตัวเลือกเพื่อดูสรุป</p>';

      const inline = document.getElementById('bb-inline-summary');
      if (inline) inline.innerHTML = summaryHtml;

      const cartLines = document.getElementById('bb-cart-lines');
      const totalEl = document.getElementById('bb-total');
      const contactTotal = document.getElementById('bb-contact-total');
      if (cartLines) {
        cartLines.innerHTML = lines.length
          ? lines.join('')
          : '<p class="bb-cart-empty muted-sm">ทำตามขั้นตอนเพื่อดูราคา</p>';
      }
      if (totalEl) totalEl.textContent = fmt.format(calc.total);
      if (contactTotal) contactTotal.textContent = `฿${fmt.format(calc.total)}`;

      return { state, calc };
    }

    function setError(field, msg) {
      const wrap = form.querySelector(`[data-bb-field="${field}"]`);
      if (!wrap) return;
      wrap.classList.toggle('has-error', !!msg);
      const err = wrap.querySelector('.field-error');
      if (err) err.textContent = msg || '';
    }

    function validateStep(stepKey) {
      const state = getState();
      let ok = true;
      if (stepKey === 'boat' && !state.boatId) {
        window.TT?.toast?.('กรุณาเลือกประเภทเรือ'); ok = false;
      } else if (stepKey === 'route' && !state.routeId) {
        window.TT?.toast?.('กรุณาเลือกเส้นทาง'); ok = false;
      } else if (stepKey === 'tier' && !state.tierId) {
        const p = getProfile(state.boatId);
        window.TT?.toast?.(p.selectionMode === 'size' ? 'กรุณาเลือกขนาดเรือ' : 'กรุณาเลือกจำนวนคน');
        ok = false;
      } else if (stepKey === 'contact') {
        if (!state.date) { setError('date', 'กรุณาเลือกวันที่'); ok = false; }
        else setError('date', '');
        if (!state.name) { setError('name', 'กรุณากรอกชื่อ'); ok = false; }
        else setError('name', '');
        const phoneClean = state.phone.replace(/\D/g, '');
        if (!state.phone || phoneClean.length < 9) {
          setError('phone', state.phone ? 'เบอร์ไม่ถูกต้อง' : 'กรุณากรอกเบอร์');
          ok = false;
        } else setError('phone', '');
      }
      if (!ok && stepKey !== 'contact') window.TT?.toast?.('กรุณาเลือกข้อมูลให้ครบ');
      return ok;
    }

    function buildMessage(state, calc) {
      const L = [
        'สวัสดีครับ/ค่ะ สนใจจองเรือเหมาลำ',
        '',
        '— เรือ —',
        calc.boat?.name || '',
        calc.route ? `เส้นทาง: ${calc.route.name}${calc.route.subtitle ? ' · ' + calc.route.subtitle : ''}` : '',
        calc.option ? `${calc.profile.selectionMode === 'size' ? 'ขนาดเรือ' : 'จำนวนคน'}: ${calc.option.label} (${calc.people} ท่าน)` : '',
        '',
        '— ข้อมูลผู้จอง —',
        `ชื่อ: ${state.name}`,
        `เบอร์: ${state.phone}`,
        `วันที่: ${formatThaiDate(state.date)}`,
        '',
        '— สรุปราคา —',
        calc.charter ? `${charterSummaryLabel(calc)}: ฿${fmt.format(calc.charter)}` : '',
        calc.guideFee ? `ค่ามัคคุเทศก์: ฿${fmt.format(calc.guideFee)}` : '',
        calc.safetyStaffTotal ? `สต๊าฟดูแลความปลอดภัย: ฿${fmt.format(calc.safetyStaffTotal)} (${calc.people}÷${calc.safetyStaffRatio}=${calc.safetyStaffCount} คน)` : '',
        calc.insurance ? `ประกันอุบัติเหตุ: ฿${fmt.format(calc.insurance)} (${calc.insuranceRate}×${calc.people})` : '',
      ];
      calc.selectedAddons.forEach((a) => L.push(`${a.label}: ฿${fmt.format(a.price)}`));
      L.push('', `>>> ยอดชำระสุทธิ: ฿${fmt.format(calc.total)} <<<`);
      if (state.note) L.push('', `หมายเหตุ: ${state.note}`);
      L.push('', 'รบกวนแอดมินติดต่อกลับเพื่อยืนยันการจองครับ/ค่ะ');
      return L.filter(Boolean).join('\n');
    }

    function bindWizardEvents() {
      form.querySelectorAll('.wizard-next[data-next]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          if (!validateStep(currentStep)) return;
          gotoStep(btn.dataset.next);
        });
      });
      form.querySelectorAll('.wizard-back').forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          gotoStep(btn.dataset.back);
        });
      });

      form.querySelectorAll('input[name="boat"]').forEach((radio) => {
        radio.addEventListener('change', () => {
          if (!radio.checked) return;
          const prevAddons = [...form.querySelectorAll('input[name="addon"]:checked')].map((el) => el.value);
          const saved = {
            date: form.querySelector('#bb-date')?.value,
            name: form.querySelector('#bb-name')?.value,
            phone: form.querySelector('#bb-phone')?.value,
            note: form.querySelector('#bb-note')?.value,
          };
          const step = currentStep;
          buildWizard(radio.value);
          if (saved.date) form.querySelector('#bb-date').value = saved.date;
          if (saved.name) form.querySelector('#bb-name').value = saved.name;
          if (saved.phone) form.querySelector('#bb-phone').value = saved.phone;
          if (saved.note) form.querySelector('#bb-note').value = saved.note;
          prevAddons.forEach((id) => {
            const cb = form.querySelector(`input[name="addon"][value="${id}"]`);
            if (cb) cb.checked = true;
          });
          gotoStep(step, { skipScroll: true });
          render();
          window.TT?.injectIcons?.();
          document.getElementById('bb-submit-line')?.addEventListener('click', onSubmitLine);
        });
      });

      form.addEventListener('change', (e) => {
        if (e.target.matches('input[name="route"], input[name="tier"], input[name="addon"]')) render();
      });
      form.addEventListener('input', render);

      document.getElementById('bb-submit-line')?.addEventListener('click', onSubmitLine);
    }

    function onSubmitLine() {
      const { state, calc } = render();
      if (!validateStep('contact')) return;
      const msg = buildMessage(state, calc);
      try { navigator.clipboard?.writeText(msg); } catch {}
      window.TT?.toast?.('คัดลอกข้อความแล้ว กำลังเปิด LINE…');
      window.open(lineUrl, '_blank', 'noopener');
    }

    renderProgress();
    buildWizard(defaultBoatId);
    render();
    window.TT?.injectIcons?.();
  });
})();
