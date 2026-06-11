/**
 * Generate packages4d3n (8 items) from packages3d2n in site.json
 */
const fs = require('fs');
const path = require('path');

const sitePath = path.join(__dirname, '..', 'data', 'site.json');
const site = JSON.parse(fs.readFileSync(sitePath, 'utf8'));

function extraPrice(base) {
  if (base >= 3500) return 900;
  if (base >= 2500) return 750;
  if (base >= 1800) return 650;
  return 550;
}

function buildInclusionsByDay(pkg) {
  const sea = [
    'เรือนำเที่ยวทะเลตรัง',
    'อาหารเที่ยง',
    'ไกด์ดูแล',
    'อุปกรณ์ดำน้ำ เสื้อชูชีพ',
    'ค่าธรรมเนียมอุทยาน (คนไทย)',
    'ประกันอุบัติเหตุ 1,000,000 บาท',
  ];
  const hotel = (pkg.inclusions || []).find((x) => /ที่พัก|ห้องพัก|พัก/.test(x))
    || 'ห้องพัก 3 คืน';
  const day1 = [String(hotel).replace(/2\s*คืน/g, '3 คืน'), 'เช็กอินตามโปรแกรม'];
  const day2 = [...sea];
  const cityItems = (pkg.inclusions || []).filter((x) =>
    /ตุ๊กตุ๊ก|city tour|รถตู้|เมือง/i.test(x)
  );
  const day3 = cityItems.length
    ? cityItems
    : ['พักผ่อน / ทัวร์เมืองตรัง (ตามสะดวก)'];
  const day4 = ['เช็กเอาท์ · เดินทางกลับตามโปรแกรม'];
  return [
    { day: 1, label: 'วันที่ 1', items: day1 },
    { day: 2, label: 'วันที่ 2', items: day2 },
    { day: 3, label: 'วันที่ 3', items: day3 },
    { day: 4, label: 'วันที่ 4', items: day4 },
  ];
}

function extendStops(stops) {
  if (!stops || !stops.length) {
    return [
      'วัน 1: เช็กอินที่พัก',
      'วัน 2: ทะเลตรัง 4 เกาะ',
      'วัน 3: พักผ่อน / ทัวร์เมืองตรัง',
      'วัน 4: เช็กเอาท์และเดินทางกลับ',
    ];
  }
  const out = stops.map((s) => s.replace(/3 วัน 2 คืน/g, '4 วัน 3 คืน'));
  const last = out[out.length - 1] || '';
  if (/วัน\s*3.*เช็กเอาท์/i.test(last)) {
    out[out.length - 1] = last.replace(/วัน\s*3/i, 'วัน 3').replace(/เช็กเอาท์.*/, 'พักผ่อน / ทัวร์เมืองตรัง');
    out.push('วัน 4: เช็กเอาท์และเดินทางกลับ');
  } else if (!out.some((s) => /วัน\s*4/.test(s))) {
    out.push('วัน 4: เช็กเอาท์และเดินทางกลับ');
  }
  return out;
}

function extendItinerary(itinerary, hasCityTour) {
  const items = [...(itinerary || [])];
  const checkoutIdx = items.findIndex((it) =>
    /วันที่\s*3/i.test(it.title) && /เช็กเอาท์/i.test(it.title)
  );
  const cityOnD3 = items.some((it) => /วันที่\s*3/i.test(it.title) && /city tour/i.test(it.title));

  if (checkoutIdx >= 0) {
    const checkout = { ...items[checkoutIdx] };
    items.splice(checkoutIdx, 1);
    if (!cityOnD3 && !hasCityTour) {
      items.push({
        time: '09:00',
        title: 'วันที่ 3 · พักผ่อน',
        text: 'วันอิสระหรือเที่ยวเมืองตรังตามสะดวก',
      });
    }
    items.push({
      ...checkout,
      title: checkout.title.replace(/วันที่\s*3/i, 'วันที่ 4'),
      time: checkout.time === '10:00' ? '10:00' : '08:00',
    });
  } else if (!items.some((it) => /วันที่\s*4/i.test(it.title))) {
    items.push({
      time: '10:00',
      title: 'วันที่ 4 · เช็กเอาท์',
      text: 'เก็บสัมภาระ จบโปรแกรม',
    });
  }
  return items;
}

function extendTo4d3n(pkg) {
  const p3 = JSON.parse(JSON.stringify(pkg));
  p3.id = pkg.id.replace(/^p2-/, 'p3-');
  if (p3.packageCode) {
    p3.packageCode = String(p3.packageCode).replace(/^P2-/, 'P3-');
  }
  p3.duration = '4 วัน 3 คืน';
  p3.basePrice = pkg.basePrice + extraPrice(pkg.basePrice);
  const baseName = String(pkg.name).replace(/\s*4\s*วัน\s*3\s*คืน\s*$/, '').trim();
  p3.name = baseName.includes('4 วัน 3 คืน') ? baseName : `${baseName} 4 วัน 3 คืน`;
  p3.desc = String(pkg.desc || '')
    .replace(/3 วัน 2 คืน/g, '4 วัน 3 คืน')
    .replace(/พัก 2 คืน/g, 'พัก 3 คืน')
    .replace(/2 คืน/g, '3 คืน');
  if (p3.ribbon && /฿/.test(p3.ribbon)) {
    p3.ribbon = `เริ่มต้น ${p3.basePrice.toLocaleString('en-US')} ฿`;
  }
  if (p3.priceMeetingNote) {
    p3.priceMeetingNote = p3.priceMeetingNote.replace(
      /\d[\d,]* บาท/,
      `${p3.basePrice.toLocaleString('en-US')} บาท`
    );
  }
  p3.stops = extendStops(p3.stops);
  const hasCityTour = /city tour|ตุ๊กตุ๊ก/i.test(pkg.name + pkg.desc);
  p3.itinerary = extendItinerary(p3.itinerary, hasCityTour);
  if (p3.inclusions) {
    p3.inclusions = p3.inclusions.map((inc) =>
      String(inc).replace(/2\s*คืน/g, '3 คืน')
    );
  }
  p3.inclusionsByDay = buildInclusionsByDay(p3);
  return p3;
}

const source = site.packages3d2n || [];
if (source.length < 8) {
  console.warn('Expected 8 packages3d2n, found', source.length);
}

site.packages4d3n = source.slice(0, 8).map(extendTo4d3n);

fs.writeFileSync(sitePath, JSON.stringify(site, null, 4) + '\n', 'utf8');
console.log('Seeded', site.packages4d3n.length, 'packages4d3n:');
site.packages4d3n.forEach((p) => {
  console.log(' -', p.id, p.packageCode, p.name, p.basePrice);
});
