/**
 * Regenerate assets/js/data-fallback.js from data/site.json (UTF-8).
 * Run: node tools/regenerate-fallback.js
 */
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const sitePath = path.join(root, 'data/site.json');
const fallbackPath = path.join(root, 'assets/js/data-fallback.js');

const data = JSON.parse(fs.readFileSync(sitePath, 'utf8'));
const js =
  '/* Auto-generated — อัปเดตจากระบบหลังบ้าน */\n' +
  'window.__TT_FALLBACK = ' +
  JSON.stringify(data) +
  ';\n';

fs.writeFileSync(fallbackPath, js, 'utf8');
console.log('Written', path.relative(root, fallbackPath));
