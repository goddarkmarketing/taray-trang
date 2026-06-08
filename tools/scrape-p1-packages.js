/**
 * Scrape P1 (2D1N) package cards + detail pages from live site.
 * Usage: node tools/scrape-p1-packages.js
 */
const fs = require('fs');
const path = require('path');
const https = require('https');

const LIST_URL =
  'https://www.xn--72c1af3ci9dk0j.com/%e0%b9%81%e0%b8%9e%e0%b9%87%e0%b8%84%e0%b9%80%e0%b8%81%e0%b8%88%e0%b8%97%e0%b8%b1%e0%b8%a7%e0%b8%a3%e0%b9%8c/';

function fetch(url) {
  return new Promise((resolve, reject) => {
    https
      .get(url, { headers: { 'User-Agent': 'Mozilla/5.0' } }, (res) => {
        if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
          return fetch(res.headers.location).then(resolve, reject);
        }
        const chunks = [];
        res.on('data', (c) => chunks.push(c));
        res.on('end', () => resolve(Buffer.concat(chunks).toString('utf8')));
      })
      .on('error', reject);
  });
}

function stripTags(html) {
  return html.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
}

function parseCards(html) {
  const cards = [];
  const re =
    /<a class="elementor-cta" href="([^"]+)"[\s\S]*?<h2 class="elementor-cta__title[^"]*">\s*([\s\S]*?)<\/h2>[\s\S]*?<div class="elementor-cta__description[^"]*">\s*([\s\S]*?)<\/div>[\s\S]*?รหัส (P1-\d+)[\s\S]*?<div class="elementor-ribbon-inner">([^<]+)<\/div>/g;
  let m;
  while ((m = re.exec(html)) !== null) {
    const title = stripTags(m[2]);
    cards.push({
      url: m[1],
      title,
      desc: stripTags(m[3]),
      code: m[4],
      ribbon: stripTags(m[5]),
      image: (html.match(new RegExp(m[1].replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '[\\s\\S]*?data-back="([^"]+)"')) || [])[1] || '',
    });
  }
  // dedupe by code (site has duplicate links)
  const seen = new Set();
  return cards.filter((c) => {
    if (seen.has(c.code)) return false;
    seen.add(c.code);
    return true;
  });
}

function parseDetail(html) {
  const h2 = html.match(/<h2 class="elementor-heading-title[^"]*">([^<]+)<\/h2>/);
  const subtitle = html.match(/<strong>([^<]*เที่ยวทะเล[^<]*)<\/strong>/);
  const code = html.match(/รหัส (P1[^<]+)/);
  const season = html.match(/แพ็คเกจนี้เริ่มใช้([^<]+)/);
  const ogImage = html.match(/property="og:image" content="([^"]+)"/);

  const incl = [];
  const excl = [];
  let mode = null;
  const listRe = /elementor-icon-list-text">([^<]+)</g;
  const sections = html.split('ราคานี้รวม');
  if (sections[1]) {
    const part = sections[1].split('ราคานี้ไม่รวม');
    const inclHtml = part[0] || '';
    const exclHtml = part[1] || '';
    let lm;
    while ((lm = listRe.exec(inclHtml))) incl.push(lm[1].trim());
    listRe.lastIndex = 0;
    while ((lm = listRe.exec(exclHtml))) excl.push(lm[1].trim());
  }

  const gallery = [];
  const imgRe = /href='(https:\/\/www\.xn--72c1af3ci9dk0j\.com\/wp-content\/uploads\/[^']+\.(?:jpg|jpeg|png|webp))'/gi;
  let im;
  while ((im = imgRe.exec(html)) && gallery.length < 6) {
    if (!gallery.includes(im[1])) gallery.push(im[1]);
  }

  return {
    pageTitle: h2 ? stripTags(h2[1]) : '',
    subtitle: subtitle ? stripTags(subtitle[1]) : '',
    detailCode: code ? code[1].trim() : '',
    season: season ? season[1].trim() : '',
    ogImage: ogImage ? ogImage[1] : '',
    inclusions: incl,
    exclusions: excl,
    gallery: gallery.slice(0, 6),
  };
}

function parsePrice(ribbon) {
  const n = ribbon.match(/(\d[\d,]*)/);
  return n ? parseInt(n[1].replace(/,/g, ''), 10) : 0;
}

async function main() {
  console.log('Fetching listing...');
  const listHtml = await fetch(LIST_URL);
  const cards = parseCards(listHtml);
  console.log('Found', cards.length, 'packages');

  const out = [];
  for (const card of cards.slice(0, 8)) {
    console.log('Detail:', card.code, card.url);
    await new Promise((r) => setTimeout(r, 400));
    try {
      const html = await fetch(card.url);
      const detail = parseDetail(html);
      out.push({ ...card, ...detail, basePrice: parsePrice(card.ribbon) });
    } catch (e) {
      console.error('Failed', card.code, e.message);
      out.push({ ...card, error: e.message, basePrice: parsePrice(card.ribbon) });
    }
  }

  const outPath = path.join(__dirname, '_p1-scraped.json');
  fs.writeFileSync(outPath, JSON.stringify(out, null, 2), 'utf8');
  console.log('Written', outPath);
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
