/**
 * Sync packages2d1n in data/site.json from scraped live-site data (P1 series).
 * Usage: node tools/apply-p1-to-site.js
 */
const fs = require('fs');
const path = require('path');

const sitePath = path.join(__dirname, '../data/site.json');
const scrapedPath = path.join(__dirname, '_p1-scraped.json');

const FOUR_ISLANDS = 'ถ้ำมรกต → เกาะกระดาน → เกาะแหวน → เกาะเชือก';
const GALLERY_CAPTION = 'ถ้ำมรกต เกาะกระดาน เกาะแหวน เกาะเชือก';

const DEFAULT_EXCLUSIONS = [
  'อาหารเย็น',
  'รถส่งกลับสนามบิน บขส',
  'ค่าธรรมเนียมสำหรับชาวต่างชาติ 200 บาท',
  'ภาษีมูลค่าเพิ่ม 7% หรือ ภาษีหัก ณ ที่จ่าย 3% (กรณีต้องการเอกสารรับเงิน)',
];

const LONGTAIL_OVERNIGHT_INCL = [
  'ค่าที่พัก 1 คืน',
  'อาหารเที่ยง',
  'ค่าเรือหางยาวนำเที่ยวทะเลตรัง',
  'อุปกรณ์ดำน้ำ หน้ากาก เสื้อชูชีพ',
  'ค่าธรรมเนียมอุทยานคนไทย',
  'ค่ามัคคุเทศก์นำเที่ยวตลอดโปรแกรม',
  'ค่าประกันอุบัติเหตุ ทุนประกัน 1,000,000 บาท',
];

const BIGBOAT_OVERNIGHT_INCL = [
  'ค่าที่พัก 1 คืน',
  'อาหารเที่ยง (บุฟเฟ่ต์บนเรือ)',
  'ค่าเรือทัวร์นำเที่ยวทะเลตรัง',
  'อุปกรณ์ดำน้ำ หน้ากาก เสื้อชูชีพ',
  'ค่าธรรมเนียมอุทยานคนไทย',
  'อาหารว่าง ผลไม้ น้ำดื่ม น้ำแข็ง',
  'ค่ามัคคุเทศก์นำเที่ยวตลอดโปรแกรม',
  'ค่าประกันอุบัติเหตุ ทุนประกัน 1,000,000 บาท',
];

function pkg(cfg) {
  const gallery =
    cfg.gallery && cfg.gallery.length
      ? cfg.gallery.slice(0, 6)
      : [cfg.image, cfg.image, cfg.image, cfg.image, cfg.image, cfg.image];
  return {
    id: cfg.id,
    name: cfg.name,
    image: cfg.image,
    route: cfg.route || FOUR_ISLANDS,
    stops: cfg.stops || [
      'วัน 1: ทะเลตรัง 4 เกาะ + เช็กอินที่พัก',
      'วัน 2: เที่ยวต่อตามโปรแกรม + กลับท่าเรือ',
    ],
    duration: '2 วัน 1 คืน',
    basePrice: cfg.basePrice,
    boats: cfg.boats,
    desc: cfg.desc,
    ...(cfg.ribbon ? { ribbon: cfg.ribbon } : {}),
    rating: cfg.rating ?? 4.8,
    reviewCount: cfg.reviewCount ?? '',
    oldPrice: cfg.oldPrice ?? 0,
    stars: cfg.stars ?? 5,
    packageCode: cfg.packageCode,
    gallery,
    galleryCaption: cfg.galleryCaption || GALLERY_CAPTION,
    licenseNo: '43/00540',
    season: cfg.season || 'ตั้งแต่วันที่ 1 ตุลาคม – 31 พฤษภาคม ทุกปี ออกเดินทางทุกวัน',
    inclusions: cfg.inclusions,
    priceMeetingNote: cfg.priceMeetingNote || 'ราคานี้เจอกันที่ท่าเรือปากเมง',
    priceNotes: [
      ...(cfg.exclusions || DEFAULT_EXCLUSIONS).map((t) => `ไม่รวม: ${t}`),
      ...(cfg.priceNotesExtra || []),
    ],
  };
}

const scraped = JSON.parse(fs.readFileSync(scrapedPath, 'utf8'));
const byCode = Object.fromEntries(scraped.map((s) => [s.code, s]));

const P1_001_IMAGE =
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/%E0%B8%96%E0%B9%89%E0%B8%B3%E0%B8%A1%E0%B8%A3%E0%B8%81%E0%B8%95%E0%B8%88%E0%B8%B1%E0%B8%87%E0%B8%AB%E0%B8%A7%E0%B8%B1%E0%B8%94%E0%B8%95%E0%B8%A3%E0%B8%B1%E0%B8%87.jpg';

const packages2d1n = [
  pkg({
    id: 'p1-001',
    packageCode: 'P1-001',
    name: 'ทะเลตรัง 4 เกาะ นอนหาดปากเมง',
    desc: 'เที่ยวทะเลตรัง 4 เกาะ นอนรีสอร์ทบริเวณหาดปากเมง ท่องทะเลด้วยเรือหางยาว เที่ยวจุใจ',
    basePrice: 975,
    boats: ['longtail'],
    image: P1_001_IMAGE,
    ribbon: 'เริ่มต้น 975 ฿',
    inclusions: LONGTAIL_OVERNIGHT_INCL,
    gallery: [
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2025/08/P1-L001.jpg',
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday3.jpg',
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday2.jpg',
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/%E0%B9%80%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%95%E0%B8%A3%E0%B8%B1%E0%B8%87%E0%B9%84%E0%B8%9B%E0%B9%80%E0%B8%8A%E0%B9%89%E0%B8%B2%E0%B9%80%E0%B8%A2%E0%B9%87%E0%B8%99%E0%B8%81%E0%B8%A5%E0%B8%B1%E0%B8%9A29.jpg',
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/%E0%B9%80%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%95%E0%B8%A3%E0%B8%B1%E0%B8%87%E0%B9%84%E0%B8%9B%E0%B9%80%E0%B8%8A%E0%B9%89%E0%B8%B2%E0%B9%80%E0%B8%A2%E0%B9%87%E0%B8%99%E0%B8%81%E0%B8%A5%E0%B8%B1%E0%B8%9A27.jpg',
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/%E0%B9%80%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%95%E0%B8%A3%E0%B8%B1%E0%B8%87%E0%B9%84%E0%B8%9B%E0%B9%80%E0%B8%8A%E0%B9%89%E0%B8%B2%E0%B9%80%E0%B8%A2%E0%B9%87%E0%B8%99%E0%B8%81%E0%B8%A5%E0%B8%B1%E0%B8%9A12.jpg',
    ],
    stops: [
      'วัน 1: ทะเลตรัง 4 เกาะ + เช็กอินรีสอร์ทหาดปากเมง',
      'วัน 2: พักผ่อนริมทะเล + กลับท่าเรือปากเมง',
    ],
    priceMeetingNote: 'ราคาเริ่มต้น 975 บาท/ท่าน — เลือกโรงแรมได้ตามงบ',
  }),
  pkg({
    id: 'p1-002',
    packageCode: 'P1-002',
    name: byCode['P1-002'].title.replace(/\s+/g, ' ').trim(),
    desc: byCode['P1-002'].desc,
    basePrice: 1500,
    boats: ['longtail'],
    image: byCode['P1-002'].image,
    ribbon: 'เริ่มต้น 1,500 ฿',
    inclusions: byCode['P1-002'].inclusions,
    exclusions: byCode['P1-002'].exclusions,
    gallery: byCode['P1-002'].gallery,
    season: byCode['P1-002'].season
      ? `ตั้งแต่${byCode['P1-002'].season.replace(/^ตั้งแต่/, '')}`
      : undefined,
    route: 'ถ้ำมรกต → เกาะกระดาน → เกาะแหวน → เกาะเชือก (พักเกาะไหง)',
    stops: [
      'วัน 1: ทะเลตรัง 4 เกาะ + พักบนเกาะไหง',
      'วัน 2: เที่ยวชายหาดเกาะไหง + กลับท่าเรือ',
    ],
    priceMeetingNote: 'ราคาเริ่มต้น 1,500 บาท/ท่าน — รวมที่พักบนเกาะไหง',
  }),
  pkg({
    id: 'p1-003',
    packageCode: 'P1-003',
    name: byCode['P1-003'].title.replace(/\s+/g, ' ').trim(),
    desc: byCode['P1-003'].desc,
    basePrice: 975,
    boats: ['longtail'],
    image: byCode['P1-003'].image,
    ribbon: 'เริ่มต้น 975 ฿',
    inclusions: byCode['P1-003'].inclusions,
    exclusions: byCode['P1-003'].exclusions,
    gallery: byCode['P1-003'].gallery,
    season: byCode['P1-003'].season
      ? `ตั้งแต่${byCode['P1-003'].season.replace(/^ตั้งแต่/, '')}`
      : undefined,
    stops: [
      'วัน 1: ทะเลตรัง 4 เกาะ + เช็กอินโรงแรมในเมืองตรัง',
      'วัน 2: พักผ่อนในเมือง + กลับท่าเรือ',
    ],
    priceMeetingNote: 'ราคาเริ่มต้น 975 บาท/ท่าน — รวมรถรับในเมือง (ตามเงื่อนไข)',
  }),
  pkg({
    id: 'p1-004',
    packageCode: 'P1-004',
    name: 'ทะเลตรัง 4 เกาะ นอนหาดปากเมง',
    desc: byCode['P1-004'].desc,
    basePrice: 1275,
    boats: ['bigboat'],
    image: byCode['P1-004'].image,
    ribbon: 'เริ่มต้น 1,275 ฿',
    inclusions: byCode['P1-004'].inclusions,
    exclusions: byCode['P1-004'].exclusions,
    gallery: byCode['P1-004'].gallery,
    season: byCode['P1-004'].season
      ? `ตั้งแต่${byCode['P1-004'].season.replace(/^ตั้งแต่/, '')}`
      : undefined,
    stops: [
      'วัน 1: ทะเลตรัง 4 เกาะ (เรือใหญ่) + พักหาดปากเมง',
      'วัน 2: พักรีสอร์ทปากเมง + กลับท่าเรือ',
    ],
    priceMeetingNote: 'ราคาเริ่มต้น 1,275 บาท/ท่าน — เดินทางโดยเรือทัวร์',
  }),
  pkg({
    id: 'p1-005',
    packageCode: 'P1-005',
    name: byCode['P1-005'].title.replace(/\s+/g, ' ').trim(),
    desc: byCode['P1-005'].desc,
    basePrice: 2800,
    boats: ['bigboat'],
    image: byCode['P1-005'].image,
    ribbon: 'เริ่มต้น 2,800 ฿',
    inclusions: byCode['P1-005'].inclusions,
    exclusions: byCode['P1-005'].exclusions,
    gallery: byCode['P1-005'].gallery,
    season: 'ตั้งแต่วันที่ 16 ตุลาคม – 31 พฤษภาคม ทุกปี',
    route: 'ถ้ำมรกต → เกาะกระดาน → เกาะแหวน → เกาะเชือก (พักเกาะไหง)',
    stops: [
      'วัน 1: ทะเลตรัง 4 เกาะ (เรือใหญ่) + พักเกาะไหง',
      'วัน 2: เที่ยวเกาะไหง + กลับท่าเรือ',
    ],
    priceMeetingNote: 'ราคาเริ่มต้น 2,800 บาท/ท่าน — รวมที่พักบนเกาะไหง',
  }),
  pkg({
    id: 'p1-006',
    packageCode: 'P1-006',
    name: byCode['P1-006'].title.replace(/\s+/g, ' ').trim(),
    desc: byCode['P1-006'].desc,
    basePrice: 1475,
    boats: ['bigboat'],
    image: byCode['P1-006'].image,
    ribbon: 'เริ่มต้น 1,475 ฿',
    inclusions: byCode['P1-006'].inclusions,
    exclusions: byCode['P1-006'].exclusions,
    gallery: byCode['P1-006'].gallery,
    season: byCode['P1-006'].season
      ? `ตั้งแต่${byCode['P1-006'].season.replace(/^ตั้งแต่/, '')}`
      : undefined,
    stops: [
      'วัน 1: ทะเลตรัง 4 เกาะ (เรือใหญ่) + เช็กอินโรงแรมในเมืองตรัง',
      'วัน 2: พักผ่อนในเมือง + กลับท่าเรือ',
    ],
    priceMeetingNote: 'ราคาเริ่มต้น 1,475 บาท/ท่าน — รวมรถรับในเมือง (ตามเงื่อนไข)',
  }),
  pkg({
    id: 'p1-007',
    packageCode: 'P1-007',
    name: 'เที่ยวเกาะเหลาเหลียง นอนเกาะลิบง',
    desc: 'เที่ยวทะเลตรัง เต็มวันด้วยเรือหางยาว พักโฮมสเตย์บนเกาะลิบง เที่ยวลิบงเต็มวัน',
    basePrice: 1500,
    boats: ['longtail'],
    image:
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์25.jpg',
    ribbon: 'ราคาประหยัด',
    route: 'เกาะเหลาเหลียง → เกาะลิบง (พักค้างคืน)',
    stops: [
      'วัน 1: เที่ยวเกาะเหลาเหลียง + พักโฮมสเตย์เกาะลิบง',
      'วัน 2: เที่ยวเกาะลิบงเต็มวัน + กลับท่าเรือ',
    ],
    galleryCaption: 'เกาะเหลาเหลียง เกาะลิบง',
    inclusions: [
      ...LONGTAIL_OVERNIGHT_INCL,
      'อาหารเช้า ณ ที่พัก (ตามโรงแรม/โฮมสเตย์)',
    ],
    priceMeetingNote: 'ราคาประหยัด — สอบถามราคาตามช่วงและที่พักผ่าน LINE @talaytrang',
    priceNotesExtra: ['ราคาขึ้นอยู่กับที่พักและช่วงเวลา — ติดต่อสอบถามก่อนจอง'],
  }),
  pkg({
    id: 'p1-008',
    packageCode: 'P1-008',
    name: 'ทะเลตรัง + เกาะรอก นอนในเมืองตรัง',
    desc: 'เที่ยวทะเลตรังด้วยเรือทัวร์ เต็มอิ่มจุใจ พักเมืองตรัง เที่ยวเกาะรอกเต็มวันกับสปีดโบต',
    basePrice: 2500,
    boats: ['bigboat', 'speedboat'],
    image:
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะรอกวันเดียว6.jpg',
    ribbon: 'ราคาประหยัด',
    route: 'ทะเลตรัง 4 เกาะ → เกาะรอก + เกาะม้า (พักเมืองตรัง)',
    stops: [
      'วัน 1: ทะเลตรัง 4 เกาะ (เรือทัวร์) + พักในเมืองตรัง',
      'วัน 2: เที่ยวเกาะรอกเต็มวัน (สปีดโบต) + กลับท่าเรือ',
    ],
    galleryCaption: 'ทะเลตรัง เกาะรอก เกาะม้า',
    inclusions: [
      'ค่าที่พัก 1 คืน ในเมืองตรัง',
      'อาหารเที่ยง (บุฟเฟ่ต์บนเรือทัวร์)',
      'ค่าเรือทัวร์นำเที่ยวทะเลตรัง',
      'ค่าเรือสปีดโบตเที่ยวเกาะรอก',
      'อุปกรณ์ดำน้ำ หน้ากาก เสื้อชูชีพ',
      'ค่าธรรมเนียมอุทยานคนไทย',
      'ค่ามัคคุเทศก์นำเที่ยวตลอดโปรแกรม',
      'ค่าประกันอุบัติเหตุ ทุนประกัน 1,000,000 บาท',
    ],
    priceMeetingNote: 'ราคาประหยัด — สอบถามราคาตามช่วงและที่พักผ่าน LINE @talaytrang',
    priceNotesExtra: ['ราคาขึ้นอยู่กับที่พักและช่วงเวลา — ติดต่อสอบถามก่อนจอง'],
  }),
];

const site = JSON.parse(fs.readFileSync(sitePath, 'utf8'));
site.packages2d1n = packages2d1n;
site.meta.updated = new Date().toISOString().replace(/\.\d{3}Z$/, '+07:00');
fs.writeFileSync(sitePath, JSON.stringify(site, null, 4) + '\n', 'utf8');
console.log('Updated packages2d1n:', packages2d1n.length, 'packages');
