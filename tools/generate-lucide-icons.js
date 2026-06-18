/**
 * Generate Lucide SVG strings for window.TT.ICONS + admin frontend-icons.php
 * Run: node tools/generate-lucide-icons.js
 */
const fs = require('fs');
const path = require('path');
const lucide = require('lucide');

/** @type {Record<string, string>} site key -> Lucide export name */
const LUCIDE_MAP = {
  phone: 'Phone',
  menu: 'Menu',
  close: 'X',
  arrow: 'ArrowRight',
  play: 'Play',
  star: 'Star',
  map: 'MapPin',
  clock: 'Clock',
  sun: 'Sun',
  moon: 'Moon',
  calendar: 'Calendar',
  shield: 'ShieldCheck',
  starOutline: 'Star',
  compass: 'Compass',
  chat: 'MessageCircle',
  users: 'Users',
  user: 'User',
  route: 'Route',
  info: 'Info',
  alert: 'TriangleAlert',
  calculator: 'Calculator',
  anchor: 'Anchor',
  bigBoat: 'Ship',
  longtail: 'Sailboat',
  speedboat: 'Ship',
  bed: 'Bed',
  ticket: 'Ticket',
  car: 'Car',
  briefcase: 'Briefcase',
  scuba: 'Waves',
  cutlery: 'Utensils',
  edit: 'Pencil',
  camera: 'Camera',
  carSelf: 'Car',
  carPick: 'Bus',
  chatBubble: 'MessageCircleQuestion',
  check: 'Check',
  copy: 'Copy',
  minus: 'Minus',
  plus: 'Plus',
  arrowLeft: 'ArrowLeft',
};

/** Brand icons — not in Lucide; keep official-style paths */
const BRAND_ICONS = {
  line: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.36 10.62c0-3.78-3.79-6.85-8.45-6.85S2.46 6.84 2.46 10.62c0 3.39 3 6.23 7.06 6.77.28.06.65.18.74.42.08.21.05.55.03.77l-.12.72c-.04.21-.17.83.73.45.9-.38 4.84-2.85 6.61-4.88 1.22-1.34 1.85-2.7 1.85-4.25Zm-11.4 2.02H6.28c-.24 0-.43-.2-.43-.43V9.05c0-.24.2-.43.43-.43.24 0 .43.19.43.43v2.74h1.25c.24 0 .43.19.43.43 0 .23-.19.42-.43.42Zm1.69-.43c0 .23-.19.43-.43.43-.24 0-.43-.2-.43-.43V9.05c0-.24.19-.43.43-.43.24 0 .43.19.43.43v3.16Zm3.79 0c0 .18-.12.35-.3.41a.44.44 0 0 1-.13.02c-.14 0-.27-.07-.35-.18l-1.72-2.35v2.1c0 .23-.19.43-.43.43-.24 0-.43-.2-.43-.43V9.05c0-.18.12-.35.3-.41.05-.01.09-.02.13-.02.14 0 .27.07.35.17l1.72 2.35v-2.1c0-.24.19-.43.43-.43.24 0 .43.19.43.43v3.16Zm3.05-1.6h-1.25v.74h1.25c.23 0 .43.2.43.43 0 .23-.2.43-.43.43h-1.69c-.23 0-.42-.2-.42-.43V9.05c0-.23.19-.43.43-.43h1.69c.23 0 .43.19.43.43 0 .23-.2.43-.43.43h-1.25v.74h1.25c.23 0 .43.19.43.43 0 .23-.2.42-.44.42Z"/></svg>',
  fb: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12Z"/></svg>',
  tt: '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.6 6.32a4.83 4.83 0 0 1-3.77-2.16h-2.99v13.05a2.83 2.83 0 1 1-2.83-2.83c.21 0 .42.03.62.07v-3.05a5.94 5.94 0 1 0 5.21 5.9V9.43a7.96 7.96 0 0 0 4.45 1.37V7.81a4.86 4.86 0 0 1-.69-1.49Z"/></svg>',
};

/** @type {Record<string, Record<string, string>>} */
const ICON_ATTRS = {
  play: { fill: 'currentColor', stroke: 'none' },
  star: { fill: 'currentColor', stroke: 'none' },
  starOutline: { fill: 'none', stroke: 'currentColor' },
  check: { 'stroke-width': '2.5' },
};

function renderNode(node) {
  const [tag, attrs, children] = node;
  const attrStr = Object.entries(attrs || {})
    .map(([k, v]) => `${k}="${String(v).replace(/"/g, '&quot;')}"`)
    .join(' ');
  if (!children?.length) {
    return `<${tag} ${attrStr}/>`;
  }
  return `<${tag} ${attrStr}>${children.map(renderNode).join('')}</${tag}>`;
}

function lucideToSvg(iconNode, customAttrs = {}) {
  const attrs = {
    viewBox: '0 0 24 24',
    fill: 'none',
    stroke: 'currentColor',
    'stroke-width': 2,
    'stroke-linecap': 'round',
    'stroke-linejoin': 'round',
    'aria-hidden': 'true',
    ...customAttrs,
  };
  return renderNode(['svg', attrs, iconNode]);
}

function buildIcons() {
  /** @type {Record<string, string>} */
  const icons = { ...BRAND_ICONS };
  const missing = [];

  for (const [key, lucideName] of Object.entries(LUCIDE_MAP)) {
    const iconNode = lucide[lucideName];
    if (!iconNode) {
      missing.push(`${key} -> ${lucideName}`);
      continue;
    }
    icons[key] = lucideToSvg(iconNode, ICON_ATTRS[key] || {});
  }

  if (missing.length) {
    console.error('Missing Lucide icons:\n', missing.join('\n'));
    process.exit(1);
  }

  return icons;
}

function jsObject(icons) {
  const lines = Object.entries(icons).map(([key, svg]) => {
    const escaped = svg.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    return `    ${key}: '${escaped}'`;
  });
  return `/* Auto-generated from Lucide — node tools/generate-lucide-icons.js */\n  const ICONS = {\n${lines.join(',\n')},\n  };`;
}

function phpArray(icons) {
  const lines = Object.entries(icons).map(([key, svg]) => {
    const escaped = svg.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    return `        '${key}' => '${escaped}'`;
  });
  return lines.join(",\n");
}

const icons = buildIcons();
const root = path.join(__dirname, '..');
const jsBlock = jsObject(icons);

// Patch main.js ICONS block
const mainPath = path.join(root, 'assets/js/main.js');
let main = fs.readFileSync(mainPath, 'utf8');
const start = main.indexOf('  const ICONS = {');
const end = main.indexOf('\n  };', start);
if (start < 0 || end < 0) {
  console.error('Could not find ICONS block in main.js');
  process.exit(1);
}
main = main.slice(0, start) + jsBlock + main.slice(end + '\n  };'.length);
fs.writeFileSync(mainPath, main);

// Patch frontend-icons.php
const phpPath = path.join(root, 'admin/includes/frontend-icons.php');
let php = fs.readFileSync(phpPath, 'utf8');
const phpStart = php.indexOf('    return [');
const phpEnd = php.indexOf('    ];', phpStart);
if (phpStart < 0 || phpEnd < 0) {
  console.error('Could not find icons array in frontend-icons.php');
  process.exit(1);
}
php = php.slice(0, phpStart) + `    return [\n${phpArray(icons)},\n    ` + php.slice(phpEnd);
fs.writeFileSync(phpPath, php);

// Admin sidebar icons — map to Lucide
const ADMIN_MAP = {
  dashboard: 'LayoutDashboard',
  site: 'Globe',
  nav: 'Menu',
  hero: 'Image',
  services: 'Layers',
  boats: 'Anchor',
  'boat-booking': 'Ship',
  programs: 'Route',
  options: 'Plus',
  reviews: 'Star',
  videos: 'Video',
  why: 'BadgeCheck',
  steps: 'ListOrdered',
  articles: 'FileText',
  about: 'Info',
  'section-headings': 'Heading',
  deals: 'LayoutGrid',
  seo: 'Search',
  images: 'Images',
  media: 'Upload',
  backup: 'DatabaseBackup',
  password: 'Lock',
  external: 'ExternalLink',
  logout: 'LogOut',
};

function buildAdminPaths() {
  const out = {};
  for (const [id, name] of Object.entries(ADMIN_MAP)) {
    const node = lucide[name];
    if (!node) {
      console.error(`Missing admin icon: ${id} -> ${name}`);
      process.exit(1);
    }
    const inner = node.map(renderNode).join('');
    out[id] = inner;
  }
  return out;
}

const adminPaths = buildAdminPaths();
const adminPhpPath = path.join(root, 'admin/includes/icons.php');
let adminPhp = fs.readFileSync(adminPhpPath, 'utf8');
const pathsStart = adminPhp.indexOf('    $paths = [');
const pathsEnd = adminPhp.indexOf('    ];', pathsStart);
const pathLines = Object.entries(adminPaths)
  .map(([id, inner]) => `        '${id}' => '${inner.replace(/'/g, "\\'")}'`)
  .join(',\n');
adminPhp = adminPhp.slice(0, pathsStart) + `    $paths = [\n${pathLines},\n    ` + adminPhp.slice(pathsEnd);
fs.writeFileSync(adminPhpPath, adminPhp);

console.log(`Updated ${Object.keys(icons).length} frontend icons + ${Object.keys(adminPaths).length} admin icons from Lucide`);

const site = JSON.parse(fs.readFileSync(path.join(root, 'data/site.json'), 'utf8'));
const used = new Set();
const walk = (v) => {
  if (!v || typeof v !== 'object') return;
  if (Array.isArray(v)) return v.forEach(walk);
  if (typeof v.icon === 'string') used.add(v.icon);
  Object.values(v).forEach(walk);
};
walk(site);
const missingUsed = [...used].filter((k) => !icons[k]);
if (missingUsed.length) {
  console.error('Icons referenced in site.json but missing from ICONS:', missingUsed.join(', '));
  process.exit(1);
}
console.log(`Verified ${used.size} icon keys in site.json`);
