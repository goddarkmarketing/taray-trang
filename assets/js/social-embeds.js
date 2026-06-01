/* =========================================================
   Talay Trang — Social embeds (TikTok + Facebook)
   ไม่ใช้ API — ใช้ embed อย่างเป็นทางการจากแพลตฟอร์ม
   ========================================================= */
(function () {
  const TT = window.TT || {};
  window.TT = TT;

  function esc(s) {
    return String(s ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function platformOf(video) {
    return video?.platform === 'facebook' ? 'facebook' : 'tiktok';
  }

  function tikTokVideoId(url) {
    const m = (url || '').match(/\/video\/(\d+)/);
    return m ? m[1] : '';
  }

  function isTikTokVideoUrl(url) {
    if (!url) return false;
    return /tiktok\.com\/@[^/]+\/video\/\d+/i.test(url)
      || /tiktok\.com\/t\/\w+/i.test(url)
      || /vm\.tiktok\.com/i.test(url);
  }

  function isFacebookVideoUrl(url) {
    if (!url) return false;
    return /facebook\.com\/.*\/videos\//i.test(url)
      || /facebook\.com\/reel\//i.test(url)
      || /facebook\.com\/watch\/?\?v=/i.test(url)
      || /fb\.watch\//i.test(url)
      || /facebook\.com\/share\/v\//i.test(url);
  }

  function canEmbed(video) {
    const url = video?.url || '';
    if (platformOf(video) === 'facebook') {
      return isFacebookVideoUrl(url);
    }
    return isTikTokVideoUrl(url);
  }

  function tikTokEmbedHtml(url) {
    const videoId = tikTokVideoId(url);
    if (!videoId) return '';
    return `<div class="tiktok-iframe-wrap">
      <iframe
        src="https://www.tiktok.com/embed/v2/${esc(videoId)}"
        width="325"
        height="738"
        style="border:0;max-width:100%;"
        allow="fullscreen; encrypted-media; picture-in-picture"
        allowfullscreen
        loading="lazy"
        referrerpolicy="strict-origin-when-cross-origin"
        title="TikTok video"></iframe>
    </div>`;
  }

  function facebookVideoHtml(url, width) {
    const w = width || 500;
    const href = encodeURIComponent(url.split('?')[0]);
    return `<div class="fb-video-wrap"><iframe src="https://www.facebook.com/plugins/video.php?height=476&href=${href}&show_text=false&width=${w}" width="${w}" height="476" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowfullscreen title="Facebook video"></iframe></div>`;
  }

  function videoThumbHtml(video, i, icons) {
    const ic = icons || TT.ICONS || {};
    const delay = typeof i === 'number' ? ` style="--rd:${i * 0.04}s"` : '';
    const platform = platformOf(video);
    const badge = platform === 'facebook' ? ic.fb : ic.tt;
    return `<a class="video-thumb" href="${esc(video.url)}" target="_blank" rel="noopener"${delay} data-reveal>
      <img src="${esc(video.thumb)}" alt="${esc(video.title)}" loading="lazy"/>
      <div class="play"><span>${badge || ic.play || ''}</span></div>
      <div class="meta"><strong>${esc(video.title)}</strong><span class="views">${badge || ''} ${esc(video.views || '')}</span></div>
    </a>`;
  }

  function embedCardHtml(video) {
    const url = video.url || '';
    const inner = platformOf(video) === 'facebook'
      ? facebookVideoHtml(url)
      : tikTokEmbedHtml(url);
    return `<article class="embed-card embed-card--${platformOf(video)}" data-reveal>
      <div class="embed-card-body">${inner}</div>
      ${video.title ? `<p class="embed-card-title">${esc(video.title)}</p>` : ''}
    </article>`;
  }

  function lazyEmbedCardHtml(video) {
    const platform = platformOf(video);
    const thumb = video.thumb
      ? `<img src="${esc(video.thumb)}" alt="${esc(video.title)}" loading="lazy"/>`
      : '';
    return `<article class="embed-card embed-card--${platform} embed-card--lazy" hidden data-feed-item="hidden" data-lazy-url="${esc(video.url || '')}" data-lazy-platform="${platform}" data-reveal>
      <div class="embed-card-body embed-card-body--placeholder">${thumb}</div>
      ${video.title ? `<p class="embed-card-title">${esc(video.title)}</p>` : ''}
    </article>`;
  }

  function activateLazyEmbedCard(article) {
    const url = article.dataset.lazyUrl || '';
    const platform = article.dataset.lazyPlatform || 'tiktok';
    if (!url) return;
    const inner = platform === 'facebook' ? facebookVideoHtml(url) : tikTokEmbedHtml(url);
    const body = article.querySelector('.embed-card-body');
    if (body) body.outerHTML = `<div class="embed-card-body">${inner}</div>`;
    article.classList.remove('embed-card--lazy');
    article.removeAttribute('hidden');
    article.dataset.feedItem = 'shown';
  }

  function markFeedItemHidden(html) {
    if (/^<a[\s>]/i.test(html)) {
      return html.replace(/^<a/i, '<a hidden data-feed-item="hidden"');
    }
    return html.replace('<article', '<article hidden data-feed-item="hidden"');
  }

  function markFeedItemShown(html) {
    if (/^<a[\s>]/i.test(html)) {
      return html.replace(/^<a/i, '<a data-feed-item="shown"');
    }
    return html.replace('<article', '<article data-feed-item="shown"');
  }

  const FEED_PAGE_SIZE = 6;

  function mountPaginatedFeed(host, videos, platform, icons, pageSize) {
    const size = pageSize || FEED_PAGE_SIZE;
    const list = filterVideos(videos, platform);
    if (!list.length) return false;

    const visible = list.slice(0, size);
    const hidden = list.slice(size);
    const gridClass = platform === 'tiktok' ? 'embed-grid embed-grid--tiktok' : 'embed-grid embed-grid--facebook';

    const visibleHtml = visible.map((v, i) => markFeedItemShown(renderVideoItem(v, i, icons))).join('');
    const hiddenHtml = hidden.map((v, i) => {
      if (canEmbed(v)) return lazyEmbedCardHtml(v);
      return markFeedItemHidden(renderVideoItem(v, size + i, icons));
    }).join('');

    const moreBtn = hidden.length
      ? `<div class="feed-more-wrap text-center mt-4" data-reveal>
          <button type="button" class="btn btn-outline btn-lg feed-more-btn" data-feed-step="${size}" aria-expanded="false">
            ดูเพิ่มเติม (${hidden.length} คลิป)
          </button>
        </div>`
      : '';

    host.innerHTML = `<div class="${gridClass}">${visibleHtml}${hiddenHtml}</div>${moreBtn}`;

    const btn = host.querySelector('.feed-more-btn');
    const grid = host.querySelector('.embed-grid');
    if (btn && grid) {
      btn.addEventListener('click', () => {
        const step = parseInt(btn.dataset.feedStep, 10) || size;
        const batch = [...grid.querySelectorAll('[data-feed-item="hidden"]')].slice(0, step);
        batch.forEach((article) => {
          if (article.classList.contains('embed-card--lazy')) {
            activateLazyEmbedCard(article);
          } else {
            article.removeAttribute('hidden');
            article.dataset.feedItem = 'shown';
          }
        });
        btn.setAttribute('aria-expanded', 'true');
        const remaining = grid.querySelectorAll('[data-feed-item="hidden"]').length;
        if (remaining <= 0) {
          btn.closest('.feed-more-wrap')?.remove();
        } else {
          btn.textContent = `ดูเพิ่มเติม (${remaining} คลิป)`;
        }
        if (typeof TT.bindReveal === 'function') TT.bindReveal();
      });
    }
    return true;
  }

  function renderVideoItem(video, i, icons) {
    return canEmbed(video) ? embedCardHtml(video) : videoThumbHtml(video, i, icons);
  }

  function filterVideos(videos, platform) {
    return (videos || []).filter(v => platformOf(v) === platform);
  }

  function renderFeedGrid(videos, platform, icons) {
    const list = filterVideos(videos, platform);
    if (!list.length) return '';
    return list.map((v, i) => renderVideoItem(v, i, icons)).join('');
  }

  function renderEmbedGrid(videos, platform) {
    const list = filterVideos(videos, platform).filter(canEmbed);
    if (!list.length) return '';
    return list.map(v => embedCardHtml(v)).join('');
  }

  function resolveFacebookFeedUrl(site) {
    const page = (site?.facebookPageUrl || '').trim();
    if (page && !isFacebookShareUrl(page)) return page.split('?')[0];
    const link = (site?.facebookUrl || '').trim();
    if (link && !isFacebookShareUrl(link)) return link.split('?')[0];
    return '';
  }

  function isFacebookShareUrl(url) {
    return /facebook\.com\/share\//i.test(url);
  }

  function facebookOpenUrl(site, pageUrl) {
    const fbUrl = (site?.facebookUrl || '').trim();
    return pageUrl || fbUrl || '#';
  }

  function facebookPageLabel(pageUrl) {
    return pageUrl
      ? pageUrl.replace(/^https?:\/\/(www\.)?facebook\.com\/?/i, '').replace(/\/$/, '')
      : 'Facebook';
  }

  function fbPluginWidth(hostEl) {
    const container = hostEl?.closest('.container');
    const base = container ? container.clientWidth : (hostEl?.clientWidth || 0);
    const w = Math.max(280, Math.floor(base - 48));
    return Math.min(500, w);
  }

  function facebookPageIframeHtml(pageUrl, height, width) {
    const url = pageUrl.split('?')[0];
    const href = encodeURIComponent(url);
    const h = height || 720;
    const w = width || 500;
    const src = `https://www.facebook.com/plugins/page.php?href=${href}&tabs=timeline&width=${w}&height=${h}&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&locale=th_TH`;
    return `<iframe
        class="fb-page-iframe"
        src="${src}"
        width="${w}"
        height="${h}"
        style="border:none;overflow:hidden;width:100%;min-height:${h}px;"
        scrolling="yes"
        frameborder="0"
        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
        allowfullscreen
        title="Facebook Page"></iframe>`;
  }

  function injectFacebookPageIframe(host, pageUrl) {
    const wrap = host.querySelector('#fb-page-iframe-wrap');
    if (!wrap || !pageUrl) return;
    const w = fbPluginWidth(host);
    wrap.innerHTML = facebookPageIframeHtml(pageUrl, 720, w);
  }

  function facebookPageAsideHtml(pageUrl, site) {
    const openUrl = facebookOpenUrl(site, pageUrl);
    const label = facebookPageLabel(pageUrl);
    const brand = site?.brandTh || site?.brand || 'Talay Trang';
    const phone = site?.phoneDisplay || site?.phone || '';
    const lineUrl = site?.lineUrl || '#';
    const lineId = site?.lineId || '@talaytrang';
    const hours = site?.hours || '';
    const address = site?.address || 'จังหวัดตรัง';

    return `<aside class="fb-page-aside" data-reveal>
      <div class="fb-page-aside-glow" aria-hidden="true"></div>
      <div class="fb-page-aside-inner">
        <div class="fb-page-aside-head">
          <span class="fb-page-aside-badge">Facebook Page</span>
          <h3 class="fb-page-aside-title">@${esc(label)}</h3>
          <p class="fb-page-aside-lead">${esc(brand)} — ติดตามเพจเพื่อดูโปรโมชั่น คลิปทริปจริง และอัปเดตสภาพอากาศก่อนออกเรือ</p>
        </div>

        <div class="fb-aside-features">
          <article class="fb-aside-feature">
            <span class="fb-aside-feature-icon" aria-hidden="true">🌊</span>
            <div>
              <strong>โปรเที่ยวทะเลตรัง</strong>
              <p>แพ็กเกจ ไปเช้า–เย็นกลับ / 2 วัน 1 คืน / เหมาลำเรือ — อัปเดตสม่ำเสมอ</p>
            </div>
          </article>
          <article class="fb-aside-feature">
            <span class="fb-aside-feature-icon" aria-hidden="true">📸</span>
            <div>
              <strong>คลิปจากทริปจริง</strong>
              <p>ดูบรรยากาศ น้ำทะเล และรีวิวจากลูกค้าจริงก่อนตัดสินใจจอง</p>
            </div>
          </article>
          <article class="fb-aside-feature">
            <span class="fb-aside-feature-icon" aria-hidden="true">🌧️</span>
            <div>
              <strong>อัปเดตสภาพอากาศ</strong>
              <p>แจ้งฝน คลื่น และความพร้อมออกเรือ — ช่วยวางแผนทริปได้แม่นยำ</p>
            </div>
          </article>
        </div>

        <div class="fb-aside-info">
          ${phone ? `<div class="fb-aside-info-row"><span>โทร</span><a href="tel:${esc(String(phone).replace(/\D/g, ''))}">${esc(phone)}</a></div>` : ''}
          <div class="fb-aside-info-row"><span>LINE</span><a href="${esc(lineUrl)}" target="_blank" rel="noopener">${esc(lineId)}</a></div>
          ${hours ? `<div class="fb-aside-info-row"><span>เวลา</span><span>${esc(hours)}</span></div>` : ''}
          <div class="fb-aside-info-row"><span>พื้นที่</span><span>${esc(address)}</span></div>
        </div>

        <div class="fb-page-aside-cta">
          <a class="btn btn-primary btn-lg fb-aside-btn-fb" href="${esc(openUrl)}" target="_blank" rel="noopener">
            <span data-icon="fb"></span>เปิดเพจ Facebook
          </a>
          <a class="btn btn-outline btn-lg" href="${esc(lineUrl)}" target="_blank" rel="noopener">จองผ่าน LINE</a>
        </div>
      </div>
    </aside>`;
  }

  function facebookPageEmbedHtml(pageUrl, site) {
    return `<div class="fb-page-embed" data-reveal>
      <div class="fb-page-layout">
        <div class="fb-page-iframe-wrap" id="fb-page-iframe-wrap">
          <p class="fb-page-loading muted">กำลังโหลด Facebook Feed…</p>
        </div>
        ${facebookPageAsideHtml(pageUrl, site || {})}
      </div>
    </div>`;
  }

  function bindFacebookPageEmbed(host, pageUrl) {
    if (!pageUrl) return;
    requestAnimationFrame(() => {
      injectFacebookPageIframe(host, pageUrl);
    });
  }

  function facebookFallbackCard(fbUrl, pageUrl) {
    const openUrl = fbUrl || pageUrl || '#';
    const pageLabel = pageUrl ? pageUrl.replace(/^https?:\/\/(www\.)?facebook\.com\/?/i, '') : 'Facebook';
    return `<div class="social-feed-empty social-feed-fb-card" data-reveal>
      <p><strong>เพจ Facebook — ${esc(pageLabel)}</strong></p>
      <p class="muted">Feed อาจไม่แสดงในบางเบราว์เซอร์ (Ad blocker / คุกกี้) — กดปุ่มด้านล่างเพื่อเปิดเพจโดยตรง</p>
      <a class="btn btn-primary" href="${esc(openUrl)}" target="_blank" rel="noopener">เปิดเพจ Facebook</a>
    </div>`;
  }

  function mountFacebookPageFeed(containerId, site) {
    const host = document.getElementById(containerId);
    if (!host) return;
    const fbUrl = (site?.facebookUrl || '').trim();
    const pageUrl = resolveFacebookFeedUrl(site);

    if (!pageUrl) {
      if (fbUrl) {
        host.innerHTML = facebookFallbackCard(fbUrl, '');
      } else {
        host.innerHTML = `<div class="social-feed-empty"><p>ยังไม่ได้ตั้งลิงก์ Facebook</p><p class="muted">ตั้งค่าใน admin → ข้อมูลเว็บ → <strong>Facebook URL</strong></p></div>`;
      }
      return;
    }

    host.innerHTML = facebookPageEmbedHtml(pageUrl, site || {});
    bindFacebookPageEmbed(host, pageUrl);
  }

  function mountSocialFeeds(opts) {
    const { videos, site, icons } = opts;
    const ic = icons || TT.ICONS || {};

    const tiktokHost = document.getElementById('tiktok-feed');
    const fbVideosHost = document.getElementById('facebook-videos');
    const fbFeedHost = document.getElementById('facebook-feed');

    if (tiktokHost) {
      const mounted = mountPaginatedFeed(tiktokHost, videos, 'tiktok', ic, FEED_PAGE_SIZE);
      if (!mounted) {
        tiktokHost.innerHTML = `<div class="social-feed-empty"><p>ยังไม่มีคลิป TikTok</p><p class="muted">เพิ่มใน admin → วิดีโอ แล้ววางลิงก์คลิปจาก TikTok (Share → Copy link)</p></div>`;
      }
    }

    const fbEmbeds = renderEmbedGrid(videos, 'facebook');
    const fbAll = renderFeedGrid(videos, 'facebook', ic);
    if (fbVideosHost) {
      const clipsWrap = document.getElementById('facebook-clips-wrap');
      if (fbEmbeds) {
        fbVideosHost.innerHTML = `<div class="embed-grid embed-grid--facebook">${fbEmbeds}</div>`;
        if (clipsWrap) clipsWrap.hidden = false;
      } else if (fbAll) {
        fbVideosHost.innerHTML = `<p class="social-feed-hint">วาง<strong>ลิงก์คลิป Facebook</strong> ใน admin → วิดีโอ (เลือกแพลตฟอร์ม Facebook)</p><div class="video-grid">${fbAll}</div>`;
        if (clipsWrap) clipsWrap.hidden = false;
      } else {
        fbVideosHost.innerHTML = '';
        if (clipsWrap) clipsWrap.hidden = true;
      }
    }

    if (fbFeedHost) mountFacebookPageFeed('facebook-feed', site || TT.SITE || {});
  }

  Object.assign(TT, {
    platformOf,
    isTikTokVideoUrl,
    tikTokVideoId,
    isFacebookVideoUrl,
    canEmbedVideo: canEmbed,
    renderVideoItem,
    renderVideoThumb: videoThumbHtml,
    mountSocialFeeds,
    mountFacebookPageFeed,
  });
})();
