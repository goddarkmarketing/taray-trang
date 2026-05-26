const fs = require('fs');
const path = require('path');

const PX = (id, w = 1200) =>
  `https://images.pexels.com/photos/${id}/pexels-photo-${id}.jpeg?auto=compress&cs=tinysrgb&w=${w}`;

const root = path.join(__dirname, '..');
let code = fs.readFileSync(path.join(root, 'assets/js/data.js'), 'utf8');
code = code.replace(/^const PX[\s\S]*?^const IMAGES/m, 'const IMAGES');
code = code.replace(/\nwindow\.TT[\s\S]*$/m, '');
const fn = new Function(
  'PX',
  code + '; return { SITE, IMAGES, NAV_ITEMS, BOATS, OPTIONS, PROGRAMS, REVIEWS, VIDEOS, WHY_US, STEPS, SERVICES, HERO_SLIDES, ARTICLES };'
);
const d = fn(PX);

const out = {
  meta: { version: 1, updated: new Date().toISOString() },
  images: d.IMAGES,
  site: d.SITE,
  navItems: d.NAV_ITEMS,
  boats: d.BOATS,
  options: d.OPTIONS,
  programs: d.PROGRAMS,
  reviews: d.REVIEWS,
  videos: d.VIDEOS,
  whyUs: d.WHY_US,
  steps: d.STEPS,
  services: d.SERVICES,
  heroSlides: d.HERO_SLIDES,
  articles: d.ARTICLES,
};

const dir = path.join(root, 'data');
fs.mkdirSync(dir, { recursive: true });
fs.writeFileSync(path.join(dir, 'site.json'), JSON.stringify(out, null, 2), 'utf8');
console.log('Written data/site.json', (fs.statSync(path.join(dir, 'site.json')).size / 1024).toFixed(1) + ' KB');

const fb = `/* Auto-generated fallback — อัปเดตเมื่อบันทึกจาก admin */\nwindow.__TT_FALLBACK = ${JSON.stringify(out)};\n`;
fs.writeFileSync(path.join(root, 'assets/js/data-fallback.js'), fb, 'utf8');
console.log('Written assets/js/data-fallback.js');
