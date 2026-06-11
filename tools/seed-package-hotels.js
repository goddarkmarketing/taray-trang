/**
 * Add hotel options to all overnight packages (2D1N / 3D2N / 4D3N).
 * Run: node tools/seed-package-hotels.js && node tools/regenerate-fallback.js
 */
const fs = require('fs');
const path = require('path');

const sitePath = path.join(__dirname, '..', 'data', 'site.json');
const site = JSON.parse(fs.readFileSync(sitePath, 'utf8'));

const COLLECTIONS = ['packages2d1n', 'packages3d2n', 'packages4d3n'];
const CITY_BASE = 975;

const p1_001 = (site.packages2d1n || []).find((p) => p.id === 'p1-001');
const CITY_HOTELS_TEMPLATE = JSON.parse(JSON.stringify(p1_001?.hotels || []));

const KOH_NGAI_HOTELS = [
  { name: 'เกาะไหง แคมปิ้ง', rooms: [{ name: 'ห้องมาตรฐาน', book: true }] },
  { name: 'เกาะไหง รีสอร์ท', rooms: [{ name: 'ห้องมาตรฐาน', book: true }] },
  { name: 'เกาะไหง แฟนตาซี รีสอร์ท', rooms: [{ name: 'ห้องมาตรฐาน', book: true }] },
  { name: 'เกาะไหง ทับวารินทร์ รีสอร์ท', rooms: [{ name: 'ห้องมาตรฐาน', book: true }] },
];

const LIBONG_HOTELS = [
  { name: 'โฮมสเตย์เกาะลิบง', rooms: [{ name: 'ห้องมาตรฐาน', book: true }] },
];

function clone(obj) {
  return JSON.parse(JSON.stringify(obj));
}

function scaleCityHotels(hotels, basePrice) {
  const factor = Math.max(1, Number(basePrice || CITY_BASE) / CITY_BASE);
  return hotels.map((hotel) => ({
    name: hotel.name,
    rooms: (hotel.rooms || []).map((room) => {
      if (room.book) return { name: room.name, book: true };
      return { name: room.name, price: Math.round((room.price || 0) * factor) };
    }),
  }));
}

function hotelType(pkg) {
  const text = `${pkg.name || ''} ${pkg.route || ''}`;
  if (/เกาะไหง/.test(text)) return 'ngai';
  if (/เกาะลิบง|ลิบง/.test(text)) return 'libong';
  return 'city';
}

function hotelsFor(pkg) {
  const type = hotelType(pkg);
  if (type === 'ngai') return clone(KOH_NGAI_HOTELS);
  if (type === 'libong') return clone(LIBONG_HOTELS);
  return scaleCityHotels(CITY_HOTELS_TEMPLATE, pkg.basePrice);
}

let added = 0;
let skipped = 0;

COLLECTIONS.forEach((key) => {
  (site[key] || []).forEach((pkg) => {
    if (pkg.hotels && pkg.hotels.length) {
      skipped += 1;
      return;
    }
    pkg.hotels = hotelsFor(pkg);
    added += 1;
    console.log(`+ ${pkg.id} (${key}) → ${pkg.hotels.length} ที่พัก`);
  });
});

site.meta = site.meta || {};
site.meta.updated = new Date().toISOString();

fs.writeFileSync(sitePath, JSON.stringify(site, null, 4) + '\n', 'utf8');
console.log(`\nDone: ${added} updated, ${skipped} already had hotels`);
