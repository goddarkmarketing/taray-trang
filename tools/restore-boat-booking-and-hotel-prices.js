/**
 * Restore boatBooking config + Koh Ngai hotel room prices.
 * Run: node tools/restore-boat-booking-and-hotel-prices.js && node tools/regenerate-fallback.js
 */
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const sitePath = path.join(__dirname, '..', 'data', 'site.json');
const site = JSON.parse(fs.readFileSync(sitePath, 'utf8'));

const boatBookingRaw = execSync('git show 6f0f9ca:data/site.json', { encoding: 'utf8', maxBuffer: 20 * 1024 * 1024 });
const boatBooking = JSON.parse(boatBookingRaw).boatBooking;
if (!boatBooking) throw new Error('boatBooking not found in git commit 6f0f9ca');
site.boatBooking = boatBooking;

const KOH_NGAI_BASE = {
  'เกาะไหง แคมปิ้ง': 1500,
  'เกาะไหง รีสอร์ท': 1700,
  'เกาะไหง แฟนตาซี รีสอร์ท': 1900,
  'เกาะไหง ทับวารินทร์ รีสอร์ท': 2100,
};

const LIBONG_BASE = {
  'โฮมสเตย์เกาะลิบง': 1800,
};

function priceForHotel(hotelName, pkgBase) {
  const ngaiBase = KOH_NGAI_BASE[hotelName];
  if (ngaiBase != null) {
    const factor = Math.max(1, Number(pkgBase || 1500) / 1500);
    return Math.round(ngaiBase * factor);
  }
  const libongBase = LIBONG_BASE[hotelName];
  if (libongBase != null) {
    const factor = Math.max(1, Number(pkgBase || 1800) / 1800);
    return Math.round(libongBase * factor);
  }
  return null;
}

let hotelUpdates = 0;
['packages2d1n', 'packages3d2n', 'packages4d3n'].forEach((key) => {
  (site[key] || []).forEach((pkg) => {
    (pkg.hotels || []).forEach((hotel) => {
      const price = priceForHotel(hotel.name, pkg.basePrice);
      if (price == null) return;
      (hotel.rooms || []).forEach((room) => {
        if (room.book && (room.price == null || room.price <= 0)) {
          room.price = price;
          delete room.book;
          hotelUpdates += 1;
        }
      });
    });
  });
});

site.meta = site.meta || {};
site.meta.updated = new Date().toISOString();

fs.writeFileSync(sitePath, JSON.stringify(site, null, 4) + '\n', 'utf8');
console.log('Restored boatBooking');
console.log(`Updated ${hotelUpdates} hotel room prices`);
