/* Talay Trang Admin — shared helpers */
(function () {
  const toastEl = document.getElementById('toast');

  window.ttToast = function (msg, isError) {
    if (!toastEl) return;
    toastEl.textContent = msg;
    toastEl.hidden = false;
    toastEl.classList.toggle('is-error', !!isError);
    clearTimeout(window._ttToastTimer);
    window._ttToastTimer = setTimeout(() => {
      toastEl.hidden = true;
    }, 3200);
  };

  window.ttSaveSection = async function (section, data) {
    const res = await fetch('api/save.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ section, data }),
    });
    const json = await res.json();
    if (!res.ok || !json.ok) {
      throw new Error(json.error || 'บันทึกไม่สำเร็จ');
    }
    return json;
  };

  window.ttCopyText = function (text) {
    navigator.clipboard.writeText(text).then(
      () => ttToast('คัดลอกแล้ว'),
      () => ttToast('คัดลอกไม่สำเร็จ', true)
    );
  };

  document.querySelectorAll('[data-copy]').forEach((btn) => {
    btn.addEventListener('click', () => ttCopyText(btn.dataset.copy));
  });

  document.querySelectorAll('[data-icon-select]').forEach((wrap) => {
    const trigger = wrap.querySelector('.icon-select-trigger');
    const menu = wrap.querySelector('.icon-select-menu');
    const hidden = wrap.querySelector('input[type="hidden"]');
    if (!trigger || !menu || !hidden) return;

    const close = () => {
      wrap.classList.remove('is-open');
      trigger.setAttribute('aria-expanded', 'false');
      menu.hidden = true;
    };

    const open = () => {
      document.querySelectorAll('[data-icon-select].is-open').forEach((other) => {
        if (other !== wrap) {
          other.classList.remove('is-open');
          const otherMenu = other.querySelector('.icon-select-menu');
          const otherTrigger = other.querySelector('.icon-select-trigger');
          if (otherMenu) otherMenu.hidden = true;
          if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
        }
      });
      wrap.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');
      menu.hidden = false;
    };

    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      wrap.classList.contains('is-open') ? close() : open();
    });

    menu.querySelectorAll('.icon-select-option').forEach((opt) => {
      opt.addEventListener('click', () => {
        const value = opt.dataset.value || '';
        hidden.value = value;
        trigger.querySelector('.icon-select-item-icon').innerHTML = opt.querySelector('.icon-select-item-icon').innerHTML;
        trigger.querySelector('.icon-select-item-label').textContent = opt.querySelector('.icon-select-item-label').textContent;
        menu.querySelectorAll('.icon-select-option').forEach((item) => {
          const selected = item === opt;
          item.classList.toggle('is-selected', selected);
          item.setAttribute('aria-selected', selected ? 'true' : 'false');
        });
        close();
      });
    });

    document.addEventListener('click', (e) => {
      if (!wrap.contains(e.target)) close();
    });
  });

  window.ttUploadImageFile = async function (file) {
    const fd = new FormData();
    fd.append('file', file);
    const res = await fetch('api/upload.php', { method: 'POST', body: fd });
    const json = await res.json();
    if (!res.ok || !json.ok) {
      throw new Error(json.error || 'อัปโหลดไม่สำเร็จ');
    }
    return json.url;
  };

  window.ttBindImageUrlRow = function (row) {
    const input = row.querySelector('.image-url-input');
    const btn = row.querySelector('.image-upload-btn');
    const fileEl = row.querySelector('.image-upload-file');
    if (!input || !btn || !fileEl || row.dataset.uploadBound === '1') return;
    row.dataset.uploadBound = '1';

    btn.addEventListener('click', () => fileEl.click());

    fileEl.addEventListener('change', async () => {
      const file = fileEl.files && fileEl.files[0];
      fileEl.value = '';
      if (!file) return;

      btn.classList.add('is-loading');
      btn.textContent = 'กำลังอัปโหลด…';
      try {
        const url = await ttUploadImageFile(file);
        const append = input.dataset.imageAppend === '1';
        if (input.tagName === 'TEXTAREA') {
          const cur = input.value.trim();
          input.value = append && cur ? cur + '\n' + url : url;
        } else {
          input.value = url;
        }
        input.dispatchEvent(new Event('input', { bubbles: true }));
        ttToast('อัปโหลดสำเร็จ — ใส่ path ในช่องแล้ว');
      } catch (err) {
        ttToast(err.message || 'อัปโหลดไม่สำเร็จ', true);
      } finally {
        btn.classList.remove('is-loading');
        btn.textContent = 'อัปโหลด';
      }
    });
  };

  window.ttBuildImageUrlField = function (opts) {
    const field = document.createElement('div');
    field.className = 'field';
    if (opts.gridColumn) field.style.gridColumn = opts.gridColumn;

    const labelEl = document.createElement('label');
    labelEl.htmlFor = opts.id;
    labelEl.textContent = opts.label;
    labelEl.id = opts.labelId || '';

    const row = document.createElement('div');
    row.className = 'image-url-row';

    let input;
    if (opts.multiline) {
      input = document.createElement('textarea');
      input.rows = opts.rows || 8;
      if (opts.append) input.dataset.imageAppend = '1';
    } else {
      input = document.createElement('input');
      input.type = 'text';
      if (opts.required) input.required = true;
    }
    input.name = opts.name;
    input.id = opts.id;
    input.className = 'image-url-input';
    input.placeholder = opts.placeholder || 'https://... หรือ assets/uploads/photo.jpg';
    input.value = opts.value || '';

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn btn-ghost image-upload-btn';
    btn.textContent = 'อัปโหลด';

    const fileEl = document.createElement('input');
    fileEl.type = 'file';
    fileEl.className = 'image-upload-file';
    fileEl.accept = 'image/*';
    fileEl.hidden = true;

    row.append(input, btn, fileEl);
    field.append(labelEl, row);
    ttBindImageUrlRow(row);
    return field;
  };

  document.querySelectorAll('.image-url-row').forEach((row) => ttBindImageUrlRow(row));

  /* ---------- Page help: live site preview in popover ---------- */
  const PREVIEW_W = 1280;

  const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

  const nextFrame = () => new Promise((r) => requestAnimationFrame(() => requestAnimationFrame(r)));

  function revealHiddenWizardStep(el) {
    const step = el.closest('.wizard-step');
    if (!step || step.classList.contains('is-active')) return;
    const form = step.closest('form');
    (form ? form.querySelectorAll('.wizard-step') : [step]).forEach((s) => {
      s.classList.toggle('is-active', s === step);
    });
  }

  function isMeasurable(el) {
    if (!el) return false;
    const rect = el.getBoundingClientRect();
    return rect.height > 0 && rect.width > 0;
  }

  async function waitForTarget(doc, selector, waitChildren) {
    const deadline = Date.now() + 8000;
    let previewSignal = false;
    doc.addEventListener('tt:preview-ready', () => { previewSignal = true; }, { once: true });

    while (Date.now() < deadline) {
      const el = doc.querySelector(selector);
      if (!el) {
        await sleep(80);
        continue;
      }
      if (waitChildren && el.children.length === 0) {
        await sleep(80);
        continue;
      }
      if (isMeasurable(el)) {
        return el;
      }
      revealHiddenWizardStep(el);
      if (isMeasurable(el)) {
        return el;
      }
      if (previewSignal) {
        await nextFrame();
        if (isMeasurable(el)) return el;
      }
      await sleep(80);
    }

    const el = doc.querySelector(selector);
    if (!el) return null;
    if (waitChildren && el.children.length === 0) return null;
    revealHiddenWizardStep(el);
    return isMeasurable(el) ? el : null;
  }

  function resolvePreviewTarget(el, scope) {
    if (!el) return null;
    if (scope === 'section') {
      return el.closest('section') || el;
    }
    return el;
  }

  function measureDocOffset(el, win) {
    const rect = el.getBoundingClientRect();
    const scrollY = win.scrollY || win.document.documentElement.scrollTop || 0;
    return {
      top: rect.top + scrollY,
      height: rect.height,
    };
  }

  async function waitForLayoutStable(doc, win, targetEl) {
    if (doc.fonts && doc.fonts.ready) {
      try {
        await Promise.race([doc.fonts.ready, sleep(600)]);
      } catch (_) { /* ignore */ }
    }
    const scope = targetEl || doc.documentElement;
    const pending = Array.from(scope.querySelectorAll('img')).filter((img) => !img.complete);
    if (pending.length) {
      await Promise.race([
        Promise.all(pending.map((img) => new Promise((resolve) => {
          img.addEventListener('load', resolve, { once: true });
          img.addEventListener('error', resolve, { once: true });
        }))),
        sleep(900),
      ]);
    }
    win.scrollTo(0, 0);
    await sleep(60);
    await nextFrame();
  }

  async function mountLivePreview(fig) {
    if (fig.dataset.previewReady === '1' || fig.dataset.previewLoading === '1') return;

    const live = fig.querySelector('.admin-page-help-live');
    const iframe = fig.querySelector('.admin-page-help-iframe');
    const stage = fig.querySelector('.admin-page-help-live-stage');
    const clip = fig.querySelector('.admin-page-help-live-clip');
    const loading = fig.querySelector('.admin-page-help-live-loading');
    if (!live || !iframe || !stage || !clip) return;

    const page = fig.dataset.page;
    const selector = fig.dataset.selector;
    if (!page || !selector) return;

    const maxH = parseInt(fig.dataset.maxHeight || '200', 10);
    const waitChildren = fig.dataset.waitChildren === '1';
    const scope = fig.dataset.scope || '';

    fig.dataset.previewLoading = '1';

    iframe.style.width = PREVIEW_W + 'px';
    iframe.style.maxWidth = 'none';
    iframe.style.visibility = 'hidden';
    stage.style.width = PREVIEW_W + 'px';
    stage.style.marginTop = '0';
    stage.style.transform = 'none';

    const pageUrl = page + (page.includes('?') ? '&' : '?') + 'tt_preview=1';
    iframe.src = pageUrl;

    await new Promise((resolve) => {
      iframe.addEventListener('load', resolve, { once: true });
    });

    const doc = iframe.contentDocument;
    const win = iframe.contentWindow;
    if (!doc || !win) {
      if (loading) loading.textContent = 'โหลดตัวอย่างไม่สำเร็จ';
      iframe.style.visibility = '';
      delete fig.dataset.previewLoading;
      return;
    }

    const previewStyle = doc.createElement('style');
    previewStyle.textContent = `
      *, *::before, *::after {
        animation-duration: 0.001ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.001ms !important;
      }
      .nav-drawer, .sticky-cta { display: none !important; }
      body.has-sticky { padding-bottom: 0 !important; }
    `;
    doc.head.appendChild(previewStyle);

    const found = await waitForTarget(doc, selector, waitChildren);
    if (!found) {
      if (loading) loading.textContent = 'ไม่พบส่วนนี้บนหน้าเว็บ';
      iframe.style.visibility = '';
      delete fig.dataset.previewLoading;
      return;
    }

    if (loading) loading.textContent = 'กำลังจัดตำแหน่ง…';

    const el = resolvePreviewTarget(found, scope);
    await waitForLayoutStable(doc, win, el);
    if (!el) {
      if (loading) loading.textContent = 'ไม่พบส่วนนี้บนหน้าเว็บ';
      iframe.style.visibility = '';
      delete fig.dataset.previewLoading;
      return;
    }

    el.style.outline = '3px solid rgba(25, 118, 210, .55)';
    el.style.outlineOffset = '2px';

    win.scrollTo(0, 0);
    await nextFrame();

    const bounds = measureDocOffset(el, win);
    const pad = 8;
    const top = Math.max(0, bounds.top - pad);
    const elHeight = bounds.height + pad * 2;
    const docHeight = Math.max(
      doc.documentElement.scrollHeight,
      doc.body ? doc.body.scrollHeight : 0,
      top + elHeight + 24
    );

    iframe.style.height = docHeight + 'px';
    stage.style.height = docHeight + 'px';

    await nextFrame();

    const clipWidth = clip.clientWidth || live.clientWidth || 392;
    const scale = clipWidth / PREVIEW_W;
    let viewH = Math.ceil(elHeight * scale);
    if (maxH > 0) viewH = Math.min(viewH, maxH);

    live.style.height = viewH + 'px';
    stage.style.transform = 'scale(' + scale + ')';
    stage.style.marginTop = (-top * scale) + 'px';

    clip.classList.add('is-ready');
    iframe.style.visibility = '';
    if (loading) loading.hidden = true;
    fig.dataset.previewReady = '1';
    delete fig.dataset.previewLoading;
  }

  function loadPageHelpPreviews(help) {
    help.querySelectorAll('.admin-page-help-live-wrap').forEach((fig) => {
      mountLivePreview(fig);
    });
  }

  document.querySelectorAll('.admin-page-help').forEach((help) => {
    help.addEventListener('mouseenter', () => loadPageHelpPreviews(help));
    help.addEventListener('focusin', () => loadPageHelpPreviews(help));
  });

  function updateGalleryPreview(input) {
    const slot = input.closest('[data-gallery-slot]');
    if (!slot) return;
    const preview = slot.querySelector('[data-gallery-preview]');
    if (!preview) return;
    const url = (input.value || '').trim();
    if (url) {
      preview.innerHTML = '<img src="' + url.replace(/"/g, '&quot;') + '" alt="" loading="lazy"/>';
    } else {
      preview.innerHTML = '<span class="gallery-slot-empty">ยังไม่มีรูป</span>';
    }
  }

  document.querySelectorAll('.gallery-slot-input').forEach((input) => {
    input.addEventListener('input', () => updateGalleryPreview(input));
    input.addEventListener('change', () => updateGalleryPreview(input));
  });

  document.querySelectorAll('[data-gallery-slot] .image-url-row').forEach((row) => {
    const input = row.querySelector('.gallery-slot-input');
    if (!input || row.dataset.galleryUploadHooked === '1') return;
    row.dataset.galleryUploadHooked = '1';
    ttBindImageUrlRow(row);
    row.querySelector('.image-upload-file')?.addEventListener('change', () => {
      setTimeout(() => updateGalleryPreview(input), 50);
    });
  });

  function updateCardImagePreview(input) {
    const field = input.closest('[data-card-image-field]');
    if (!field) return;
    const preview = field.querySelector('[data-card-image-preview]');
    if (!preview) return;
    const url = (input.value || '').trim();
    if (url) {
      preview.innerHTML = '<img src="' + url.replace(/"/g, '&quot;') + '" alt="" loading="lazy"/>';
    } else {
      preview.innerHTML = '<span class="card-image-empty">ยังไม่มีภาพปกการ์ด</span>';
    }
  }

  document.querySelectorAll('.card-image-input').forEach((input) => {
    input.addEventListener('input', () => updateCardImagePreview(input));
    input.addEventListener('change', () => updateCardImagePreview(input));
  });

  document.querySelectorAll('[data-card-image-field] .image-url-row').forEach((row) => {
    const input = row.querySelector('.card-image-input');
    if (!input || row.dataset.cardUploadHooked === '1') return;
    row.dataset.cardUploadHooked = '1';
    ttBindImageUrlRow(row);
    row.querySelector('.image-upload-file')?.addEventListener('change', () => {
      setTimeout(() => updateCardImagePreview(input), 50);
    });
  });
})();
