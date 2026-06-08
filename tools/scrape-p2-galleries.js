/**
 * Scrape 6 gallery images from live P2 (3D2N) detail pages.
 * Usage: node tools/scrape-p2-galleries.js
 */
const fs = require('fs');
const path = require('path');
const https = require('https');

const PAGES = [
  { id: 'p2-001', url: 'https://www.xn--72c1af3ci9dk0j.com/p2-001/' },
  { id: 'p2-002', url: 'https://www.xn--72c1af3ci9dk0j.com/p2-002/' },
  { id: 'p2-003', url: 'https://www.xn--72c1af3ci9dk0j.com/p2-003/' },
  { id: 'p2-004', url: 'https://www.xn--72c1af3ci9dk0j.com/p2-004/' },
  { id: 'p2-005', url: 'https://www.xn--72c1af3ci9dk0j.com/p2-005/' },
  { id: 'p2-006', url: 'https://www.xn--72c1af3ci9dk0j.com/p2-006/' },
  { id: 'p2-007', url: 'https://www.xn--72c1af3ci9dk0j.com/p2-007/' },
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

function fullUrl(imgPath) {
  return `https://www.xn--72c1af3ci9dk0j.com/${imgPath.replace(/-\d+x\d+(?=\.\w+$)/, '')}`;
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
    if (/P2-L00|P2-B00|โปรแกรม|P2-นอน/i.test(m[0]) && /2025\/08/.test(m[0])) continue;
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
      gallery.forEach((u, i) => console.log('   ', i + 1, decodeURIComponent(u.slice(-70))));
    } catch (e) {
      console.error(' FAIL', page.id, e.message);
      results[page.id] = { error: e.message, gallery: [] };
    }
  }

  const outPath = path.join(__dirname, '_p2-galleries.json');
  fs.writeFileSync(outPath, JSON.stringify(results, null, 2), 'utf8');
  console.log('Written', outPath);

  const sitePath = path.join(__dirname, '../data/site.json');
  const site = JSON.parse(fs.readFileSync(sitePath, 'utf8'));
  site.packages3d2n = site.packages3d2n.map((pkg) => {
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
  console.log('Updated site.json packages3d2n galleries');
}

main().catch(console.error);
