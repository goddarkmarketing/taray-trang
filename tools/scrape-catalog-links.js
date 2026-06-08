/**
 * Scrape package card links from live catalog page.
 */
const https = require('https');
const fs = require('fs');
const path = require('path');

const CATALOG =
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

function extractCards(html) {
  const cards = [];
  const re =
    /elementor-widget-image-box[\s\S]*?<a\s+href="([^"]+)"[\s\S]*?<h3[^>]*>([\s\S]*?)<\/h3>[\s\S]*?<p[^>]*>([\s\S]*?)<\/p>[\s\S]*?รหัส\s*([A-Za-z0-9-]+)/gi;
  let m;
  while ((m = re.exec(html)) !== null) {
    cards.push({
      url: m[1],
      title: m[2].replace(/<[^>]+>/g, '').trim(),
      desc: m[3].replace(/<[^>]+>/g, '').trim(),
      code: m[4].trim(),
    });
  }
  if (cards.length) return cards;

  // fallback: split by image-box widgets
  const blocks = html.split('elementor-widget-image-box');
  for (const block of blocks.slice(1)) {
    const urlM = block.match(/href="(https:\/\/www\.xn--72c1af3ci9dk0j\.com\/[^"]+)"/);
    const codeM = block.match(/รหัส\s*([A-Za-z0-9-]+)/);
    const titleM = block.match(/elementor-image-box-title[^>]*>([\s\S]*?)<\//);
    const descM = block.match(/elementor-image-box-description[^>]*>([\s\S]*?)<\//);
    if (urlM && codeM) {
      cards.push({
        url: urlM[1],
        title: titleM ? titleM[1].replace(/<[^>]+>/g, '').trim() : '',
        desc: descM ? descM[1].replace(/<[^>]+>/g, '').trim() : '',
        code: codeM[1],
      });
    }
  }
  return cards;
}

async function main() {
  const html = await fetch(CATALOG);
  const cards = extractCards(html);
  const out = path.join(__dirname, '_catalog-cards.json');
  fs.writeFileSync(out, JSON.stringify(cards, null, 2), 'utf8');
  console.log('Found', cards.length, 'cards');
  const d = cards.filter((c) => /^D/i.test(c.code));
  const p1 = cards.filter((c) => /^P1/i.test(c.code));
  const p2 = cards.filter((c) => /^P2/i.test(c.code));
  console.log('D:', d.length, 'P1:', p1.length, 'P2:', p2.length);
  d.forEach((c) => console.log(c.code, '|', c.title.slice(0, 40), '|', c.url));
  console.log('--- P2 ---');
  p2.forEach((c) => console.log(c.code, '|', c.title.slice(0, 40), '|', c.url));
}

main().catch(console.error);
