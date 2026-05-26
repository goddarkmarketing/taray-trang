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
})();
