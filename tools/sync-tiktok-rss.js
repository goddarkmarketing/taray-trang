#!/usr/bin/env node
/**
 * ดึง RSS จาก tiktok-rss-flat → อัปเดต data/site.json (videos TikTok)
 * https://github.com/conoro/tiktok-rss-flat
 *
 * ใช้ใน GitHub Actions หรือรัน local: node tools/sync-tiktok-rss.js
 */
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const sitePath = path.join(root, 'data/site.json');
const fallbackPath = path.join(root, 'assets/js/data-fallback.js');
const configPath = path.join(root, 'data/tiktok-sync.json');

function loadConfig() {
  let cfg = { rssUrl: '', username: 'talaytrang', maxVideos: 12 };
  if (fs.existsSync(configPath)) {
    try {
      cfg = { ...cfg, ...JSON.parse(fs.readFileSync(configPath, 'utf8')) };
    } catch (e) {
      console.warn('[sync-tiktok] Invalid tiktok-sync.json', e.message);
    }
  }
  if (process.env.TIKTOK_RSS_URL?.trim()) {
    cfg.rssUrl = process.env.TIKTOK_RSS_URL.trim();
  }
  return cfg;
}

function decodeHtml(s) {
  return String(s ?? '')
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'")
    .replace(/<!\[CDATA\[([\s\S]*?)\]\]>/g, '$1')
    .trim();
}

function extractTag(block, tag) {
  const cdata = new RegExp(`<${tag}><!\\[CDATA\\[([\\s\\S]*?)\\]\\]></${tag}>`, 'i').exec(block);
  if (cdata) return decodeHtml(cdata[1]);
  const plain = new RegExp(`<${tag}>([\\s\\S]*?)</${tag}>`, 'i').exec(block);
  return plain ? decodeHtml(plain[1]) : '';
}

function normalizeTikTokUrl(url) {
  let u = url.trim();
  if (!u) return '';
  if (u.startsWith('//')) u = 'https:' + u;
  if (u.startsWith('http://')) u = 'https://' + u.slice(7);
  if (u.startsWith('https://tiktok.com/')) {
    u = 'https://www.tiktok.com/' + u.slice('https://tiktok.com/'.length);
  }
  return u.split('?')[0];
}

function videoIdFromUrl(url) {
  const m = url.match(/\/video\/(\d+)/);
  return m ? m[1] : '';
}

function pubDateFromBlock(block) {
  const raw = extractTag(block, 'pubDate');
  const t = raw ? Date.parse(raw) : NaN;
  return Number.isFinite(t) ? t : 0;
}

function parseRssItems(xml) {
  const items = [];
  const re = /<item>([\s\S]*?)<\/item>/gi;
  let m;
  while ((m = re.exec(xml)) !== null) {
    const block = m[1];
    const title = extractTag(block, 'title');
    const link = normalizeTikTokUrl(extractTag(block, 'link'));
    const desc = extractTag(block, 'description');
    const thumbMatch = desc.match(/src="([^"]+)"/i);
    const thumb = thumbMatch ? thumbMatch[1] : '';
    if (!link || !title) continue;
    items.push({ title, link, thumb, pubDate: pubDateFromBlock(block) });
  }
  // RSS จาก tiktok-rss-flat เรียงเก่า→ใหม่ — เอาคลิปล่าสุดก่อน
  items.sort((a, b) => b.pubDate - a.pubDate);
  return items;
}

function writeFallback(data) {
  const js =
    '/* Auto-generated — อัปเดตจากระบบหลังบ้าน / TikTok RSS sync */\n' +
    'window.__TT_FALLBACK = ' +
    JSON.stringify(data) +
    ';\n';
  fs.writeFileSync(fallbackPath, js, 'utf8');
}

async function main() {
  const cfg = loadConfig();
  const rssUrl = cfg.rssUrl?.trim();

  if (!rssUrl) {
    console.log('[sync-tiktok] Skip: ไม่มี rssUrl — ตั้งใน data/tiktok-sync.json หรือ TIKTOK_RSS_URL');
    process.exit(0);
  }

  console.log('[sync-tiktok] Fetching', rssUrl);

  let xml;
  try {
    const res = await fetch(rssUrl, {
      headers: { 'User-Agent': 'TalayTrang-TikTokSync/1.0' },
    });
    if (!res.ok) {
      console.warn('[sync-tiktok] RSS HTTP', res.status, '— ตรวจสอบว่า fork tiktok-rss-flat รัน Actions แล้ว');
      process.exit(0);
    }
    xml = await res.text();
  } catch (e) {
    console.warn('[sync-tiktok] Fetch failed:', e.message);
    process.exit(0);
  }

  const rssItems = parseRssItems(xml);
  if (!rssItems.length) {
    console.warn('[sync-tiktok] RSS ว่าง — รอ tiktok-rss-flat สร้าง feed หรือตรวจ MS_TOKEN');
    process.exit(0);
  }

  const max = Math.max(1, Math.min(24, Number(cfg.maxVideos) || 12));
  const picked = rssItems.slice(0, max);

  const data = JSON.parse(fs.readFileSync(sitePath, 'utf8'));
  const keepFacebook = (data.videos || []).filter((v) => v.platform === 'facebook');

  const tiktokVideos = picked.map((item, i) => {
    const vid = videoIdFromUrl(item.link) || String(i + 1);
    return {
      id: vid,
      platform: 'tiktok',
      thumb: item.thumb || data.images?.heroVideos || '',
      title: item.title,
      views: '',
      url: item.link,
    };
  });

  data.videos = [...tiktokVideos, ...keepFacebook];
  data.meta = {
    ...(data.meta || {}),
    version: 1,
    updated: new Date().toISOString(),
    tiktokRssSynced: rssUrl,
  };

  fs.writeFileSync(sitePath, JSON.stringify(data, null, 4) + '\n', 'utf8');
  writeFallback(data);

  console.log('[sync-tiktok] Updated', tiktokVideos.length, 'TikTok video(s), kept', keepFacebook.length, 'Facebook');
}

main().catch((e) => {
  console.error('[sync-tiktok] Error:', e);
  process.exit(1);
});
