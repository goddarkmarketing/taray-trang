const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const main = fs.readFileSync(path.join(root, 'assets/js/main.js'), 'utf8');
const block = main.match(/const ICONS = \{([\s\S]*?)\n  \};/);
if (!block) {
  console.error('ICONS block not found');
  process.exit(1);
}
const keys = new Set([...block[1].matchAll(/^\s+(\w+):/gm)].map((x) => x[1]));

const refs = new Set();
const files = [
  'assets/js/booking.js',
  'assets/js/boat-book.js',
  'assets/js/programs-page.js',
  'assets/js/social-embeds.js',
  'assets/js/main.js',
  'program.html',
  'about.html',
  'index.html',
  'booking.html',
];
const patterns = [
  /ICONS\.(\w+)/g,
  /ICONS\[['"](\w+)['"]\]/g,
  /data-icon="(\w+)"/g,
  /data-icon-name="(\w+)"/g,
];

for (const rel of files) {
  const file = path.join(root, rel);
  if (!fs.existsSync(file)) continue;
  const text = fs.readFileSync(file, 'utf8');
  for (const pattern of patterns) {
    let match;
    while ((match = pattern.exec(text))) refs.add(match[1]);
  }
}

const missing = [...refs].filter((k) => !keys.has(k)).sort();
console.log(`ICONS: ${keys.size} keys, ${refs.size} references`);
if (missing.length) {
  console.error('Missing keys:', missing.join(', '));
  process.exit(1);
}
console.log('OK — all referenced icon keys exist');
