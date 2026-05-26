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
    return platformOf(video) === 'facebook'
      ? isFacebookVideoUrl(url)
      : isTikTokVideoUrl(url);
  }

  function tikTokEmbedHtml(url) {
    const cite = url.split('?')[0];
    return `<blockquote class="tiktok-embed" cite="${esc(cite)}" style="max-width:605px;min-width:325px;margin:0 auto;"><section></section></blockquote>`;
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

  function facebookPagePluginHtml(pageUrl) {
    const url = pageUrl.split('?')[0];
    return `<div class="fb-page-wrap">
      <div class="fb-page"
        data-href="${esc(url)}"
        data-tabs="timeline"
        data-width="500"
        data-height="720"
        data-small-header="false"
        data-adapt-container-width="true"
        data-hide-cover="false"
        data-show-facepile="true">
        <blockquote cite="${esc(url)}" class="fb-xfbml-parse-ignore">
          <a href="${esc(url)}" target="_blank" rel="noopener">ดูเพจ Facebook</a>
        </blockquote>
      </div>
    </div>`;
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

  let fbSdkLoading = false;

  function loadFacebookSdk() {
    if (window.FB || fbSdkLoading) return Promise.resolve();
    fbSdkLoading = true;
    return new Promise((resolve) => {
      window.fbAsyncInit = function () {
        resolve();
      };
      const id = 'facebook-jssdk';
      if (document.getElementById(id)) {
        resolve();
        return;
      }
      const js = document.createElement('script');
      js.id = id;
      js.async = true;
      js.defer = true;
      js.crossOrigin = 'anonymous';
      js.src = 'https://connect.facebook.net/th_TH/sdk.js#xfbml=1&version=v21.0';
      js.onload = () => resolve();
      document.body.appendChild(js);
      if (!document.getElementById('fb-root')) {
        const root = document.createElement('div');
        root.id = 'fb-root';
        document.body.prepend(root);
      }
    });
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

    host.innerHTML = facebookPagePluginHtml(pageUrl);
    loadFacebookSdk().then(() => {
      if (window.FB && window.FB.XFBML) {
        window.FB.XFBML.parse(host);
      }
    }).catch(() => {});

    host.insertAdjacentHTML('beforeend', facebookFallbackCard(fbUrl, pageUrl));
  }

  function mountSocialFeeds(opts) {
    const { videos, site, icons } = opts;
    const ic = icons || TT.ICONS || {};

    const tiktokHost = document.getElementById('tiktok-feed');
    const fbVideosHost = document.getElementById('facebook-videos');
    const fbFeedHost = document.getElementById('facebook-feed');

    const tiktokEmbeds = renderEmbedGrid(videos, 'tiktok');
    const tiktokAll = renderFeedGrid(videos, 'tiktok', ic);
    if (tiktokHost) {
      if (tiktokEmbeds) {
        tiktokHost.innerHTML = `<div class="embed-grid embed-grid--tiktok">${tiktokEmbeds}</div>`;
      } else if (tiktokAll) {
        tiktokHost.innerHTML = `<p class="social-feed-hint">วาง<strong>ลิงก์คลิป TikTok</strong> (ไม่ใช่ลิงก์โปรไฟล์) ใน admin → วิดีโอ เพื่อฝังเล่นบนเว็บได้</p><div class="video-grid">${tiktokAll}</div>`;
      } else {
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

    if (tiktokEmbeds && !document.getElementById('tiktok-embed-js')) {
      const s = document.createElement('script');
      s.id = 'tiktok-embed-js';
      s.async = true;
      s.src = 'https://www.tiktok.com/embed.js';
      document.body.appendChild(s);
    }
  }

  Object.assign(TT, {
    platformOf,
    isTikTokVideoUrl,
    isFacebookVideoUrl,
    canEmbedVideo: canEmbed,
    renderVideoItem,
    renderVideoThumb: videoThumbHtml,
    mountSocialFeeds,
    mountFacebookPageFeed,
  });
})();
