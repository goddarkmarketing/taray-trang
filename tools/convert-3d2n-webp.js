const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const dir = path.join(__dirname, '../assets/images/แพ็คเกจ 3 วัน 2 คืน');
const outDir = path.join(__dirname, '_3d2n-png');
fs.mkdirSync(outDir, { recursive: true });

(async () => {
  const files = fs.readdirSync(dir).filter((f) => f.endsWith('.webp'));
  for (const f of files) {
    const base = f.replace(/\.webp$/i, '');
    const out = path.join(outDir, base + '.png');
    await sharp(path.join(dir, f)).png().toFile(out);
    console.log('converted', f, '->', out);
  }
})();
