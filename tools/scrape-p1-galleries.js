/**
 * Scrape 6 gallery images from live P1 detail pages.
 * Usage: node tools/scrape-p1-galleries.js
 */
const fs = require('fs');
const path = require('path');
const https = require('https');

const PAGES = [
  { id: 'p1-001', url: 'https://www.xn--72c1af3ci9dk0j.com/%e0%b9%80%e0%b8%97%e0%b8%b5%e0%b9%88%e0%b8%a2%e0%b8%a7%e0%b8%95%e0%b8%a3%e0%b8%b1%e0%b8%87%e0%b8%99%e0%b8%ad%e0%b8%99%e0%b9%83%e0%b8%99%e0%b9%80%e0%b8%a1%e0%b8%b7%e0%b8%ad%e0%b8%87p1001/' },
  { id: 'p1-002', url: 'https://www.xn--72c1af3ci9dk0j.com/%E0%B9%80%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%95%E0%B8%A3%E0%B8%B1%E0%B8%87%E0%B8%99%E0%B8%AD%E0%b8%99%E0%B9%80%E0%B8%81%E0%B8%B2%E0%B8%B0%E0%B9%84%E0%B8%AB%E0%B8%87p1-002/' },
  { id: 'p1-003', url: 'https://www.xn--72c1af3ci9dk0j.com/%E0%B9%80%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%95%E0%B8%A3%E0%B8%B1%E0%B8%87%E0%B8%A3%E0%B8%AD%E0%B8%81%E0%B9%80%E0%B8%81%E0%B8%B2%E0%B8%B0%E0%B9%84%E0%B8%AB%E0%B8%87p1-003/' },
  { id: 'p1-004', url: 'https://www.xn--72c1af3ci9dk0j.com/%E0%B9%80%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%95%E0%B8%A3%E0%B8%B1%E0%B8%87%E0%B8%9E%E0%B8%B1%E0%B8%81%E0%B9%80%E0%B8%A1%E0%B8%B7%E0%B8%AD%E0%B8%87%E0%B8%A3%E0%B8%AD%E0%B8%81p1-004/' },
  { id: 'p1-005', url: 'https://www.xn--72c1af3ci9dk0j.com/%e0%b9%80%e0%b8%97%e0%b8%b5%e0%b9%88%e0%b8%a2%e0%b8%a7%e0%b8%95%e0%b8%a3%e0%b8%b1%e0%b8%87%e0%b8%99%e0%b8%ad%e0%b8%99%e0%b9%83%e0%b8%99%e0%b9%80%e0%b8%a1%e0%b8%b7%e0%b8%ad%e0%b8%87p1-005-2/' },
  { id: 'p1-006', url: 'https://www.xn--72c1af3ci9dk0j.com/p1-006/' },
];

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

function fullUrl(path) {
  return `https://www.xn--72c1af3ci9dk0j.com/${path.replace(/-\d+x\d+(?=\.\w+$)/, '')}`;
}

function extractGallery(html) {
  const imgs = [];
  const seen = new Set();

  const galleryMatch = html.match(
    /elementor-widget-image-gallery[\s\S]*?<div id=['"]gallery-1['"][\s\S]*?<\/div>\s*<\/div>\s*<\/div>/i
  );
  const block = galleryMatch ? galleryMatch[0] : html;

  const hrefRe =
    /href=['"](https:\/\/www\.xn--72c1af3ci9dk0j\.com\/wp-content\/uploads\/[^'"]+\.(?:jpg|jpeg|png|webp))['"]/gi;
  let m;
  while ((m = hrefRe.exec(block)) !== null) {
    const url = m[1].replace(/-\d+x\d+(?=\.\w+$)/, '');
    if (!seen.has(url)) {
      seen.add(url);
      imgs.push(url);
    }
  }

  if (imgs.length >= 6) return imgs.slice(0, 6);

  const pathRe = /wp-content\/uploads\/[^\"'\s>]+\.(?:jpg|jpeg|png|webp)/gi;
  while ((m = pathRe.exec(block)) !== null) {
    if (/-\d+x\d+\./.test(m[0]) || /cropped|icon/i.test(m[0])) continue;
    const url = fullUrl(m[0]);
    if (!seen.has(url)) {
      seen.add(url);
      imgs.push(url);
    }
  }

  return imgs.slice(0, 6);
}

async function main() {
  const results = {};
  for (const page of PAGES) {
    console.log('Fetching', page.id, page.url);
    await new Promise((r) => setTimeout(r, 500));
    try {
      const html = await fetch(page.url);
      const gallery = extractGallery(html);
      results[page.id] = { count: gallery.length, gallery };
      console.log(' ', page.id, gallery.length, 'images');
      gallery.forEach((u, i) => console.log('   ', i + 1, u.slice(-60)));
    } catch (e) {
      console.error(' FAIL', page.id, e.message);
      results[page.id] = { error: e.message, gallery: [] };
    }
  }

  const outPath = path.join(__dirname, '_p1-galleries.json');
  fs.writeFileSync(outPath, JSON.stringify(results, null, 2), 'utf8');
  console.log('Written', outPath);

  // Patch site.json
  const sitePath = path.join(__dirname, '../data/site.json');
  const site = JSON.parse(fs.readFileSync(sitePath, 'utf8'));
  site.packages2d1n = site.packages2d1n.map((pkg) => {
    const g = results[pkg.id]?.gallery;
    if (!g || g.length < 3) return pkg;
    return {
      ...pkg,
      image: g[0],
      gallery: g,
    };
  });
  site.meta.updated = new Date().toISOString().replace(/\.\d{3}Z$/, '+07:00');
  fs.writeFileSync(sitePath, JSON.stringify(site, null, 4) + '\n', 'utf8');
  console.log('Updated site.json galleries');
}

main().catch(console.error);
