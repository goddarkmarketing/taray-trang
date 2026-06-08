/**
 * Sync daytrip programs from brochures + live gallery images.
 * Usage: node tools/sync-daytrip-from-images.js
 */
const fs = require('fs');
const path = require('path');
const https = require('https');

const sitePath = path.join(__dirname, '../data/site.json');
const IMG = `assets/images/${encodeURIComponent('ไปเช้าเย็นกลับ')}/`;

const CAPTION_4 = 'ถ้ำมรกต เกาะกระดาน เกาะแหวน เกาะเชือก';
const CAPTION_ROK = 'เกาะรอก เกาะม้า';
const WARNING =
  'โปรแกรมอาจเปลี่ยนแปลงตามสภาพอากาศ ระดับน้ำขึ้นลง และคลื่นลม แจ้งเลื่อนวันเที่ยวล่วงหน้า 7 วัน ไม่คืนเงินทุกกรณี';
const WARNING_PREG =
  'ไม่เหมาะสำหรับสตรีมีครรภ์ หากมีโรคประจำตัวโปรดแจ้งไกค์ประจำเรือ · โปรแกรมอาจเปลี่ยนแปลงตามสภาพอากาศและคลื่นลม';

const LIVE = {
  dl001: 'https://www.xn--72c1af3ci9dk0j.com/%e0%b9%80%e0%b8%99%e0%b8%b7%e0%b9%89%e0%b8%ad%e0%b8%b7%e0%b8%ad%e0%b8%ab%e0%b8%b2%e0%b8%87%e0%b8%a2%e0%b8%b2%e0%b8%b74%e0%b9%80%e0%b8%81%e0%b8%b2%e0%b8%b0/',
  dt001: 'https://www.xn--72c1af3ci9dk0j.com/%E0%B9%80%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%A7%E0%B8%B1%E0%B8%99%E0%B9%80%E0%B8%94%E0%B8%B5%E0%B8%A2%E0%B8%A74%E0%B9%80%E0%B8%81%E0%B8%B2%E0%B8%B0/',
  ds001: 'https://www.xn--72c1af3ci9dk0j.com/d-007/',
  ds002: 'https://www.xn--72c1af3ci9dk0j.com/%e0%b9%80%e0%b8%97%e0%b8%b5%e0%b9%88%e0%b8%a2%e0%b8%a7%e0%b9%80%e0%b8%81%e0%b8%b2%e0%b8%b0%e0%b8%a3%e0%b8%ad%e0%b8%81/',
};

function img(file) {
  return IMG + encodeURIComponent(file);
}

function galleryFrom(file, extra = []) {
  const urls = [file, ...extra].map(img);
  while (urls.length < 6) urls.push(urls[urls.length - 1]);
  return urls.slice(0, 6);
}

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

const FALLBACK_4ISLAND = [
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday3.jpg',
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday2.jpg',
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวตรังไปเช้าเย็นกลับ29.jpg',
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวตรังไปเช้าเย็นกลับ27.jpg',
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวตรังไปเช้าเย็นกลับ12.jpg',
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวตรังไปเช้าเย็นกลับ15.jpg',
];

const FALLBACK_ROK = [
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Kohrok.jpg',
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะรอกวันเดียว6.jpg',
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะรอกวันเดียว5.jpg',
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะรอกวันเดียว4.jpg',
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะรอกวันเดียว3.jpg',
  'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะรอกวันเดียว2.jpg',
];

const CARD_IMAGE = {
  dl001: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday2.jpg',
  dt001: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2022/11/264629958_10159283950075219_1058486100921441363_n.jpeg',
  ds001: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2024/10/1.jpg',
  ds002: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2024/10/2.jpg',
};

function isBadGalleryUrl(url) {
  return /cropped|iconfish|icon|logo|favicon|line_oa_chat|\.png$/i.test(url);
}

function cleanGallery(urls) {
  const out = [];
  const seen = new Set();
  for (const u of urls) {
    if (isBadGalleryUrl(u) || seen.has(u)) continue;
    seen.add(u);
    out.push(u);
  }
  return out.slice(0, 6);
}

function fullUrl(p) {
  return `https://www.xn--72c1af3ci9dk0j.com/${p.replace(/-\d+x\d+(?=\.\w+$)/, '')}`;
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
  if (imgs.length >= 6) return cleanGallery(imgs);
  const pathRe = /wp-content\/uploads\/[^\"'\s>]+\.(?:jpg|jpeg|png|webp)/gi;
  while ((m = pathRe.exec(block)) !== null) {
    if (/-\d+x\d+\./.test(m[0]) || /cropped|icon/i.test(m[0])) continue;
    const url = fullUrl(m[0]);
    if (!seen.has(url)) {
      seen.add(url);
      imgs.push(url);
    }
  }
  return cleanGallery(imgs);
}

function fourIslandsItinerary(opts) {
  const { pickupCity, lunchOn, meetOnly } = opts;
  const steps = [];
  if (pickupCity) {
    steps.push({
      time: '07:30',
      title: 'รับในเมืองตรัง',
      text: 'รับลูกค้าในเมืองตรัง เดินทางสู่ท่าเรือปากเมง',
    });
  } else if (!meetOnly) {
    steps.push({
      time: '07:30',
      title: 'ท่าเรือปากเมง',
      text: 'พร้อมกันที่ท่าเรือปากเมง',
    });
  } else {
    steps.push({
      time: '07:30',
      title: 'ท่าเรือปากเมง',
      text: 'ลูกค้าเจอกันที่ท่าเรือปากเมง (ราคานี้เจอที่ท่าเรือ)',
    });
  }
  steps.push(
    {
      time: '09:30',
      title: 'ถ้ำมรกต',
      text: 'ลงเรือเดินทางสู่เกาะมุก สัมผัสความงามของถ้ำมรกต Unseen in Thailand',
    },
    {
      time: '11:30',
      title: 'เกาะกระดาน',
      text:
        lunchOn === 'boat'
          ? 'เล่นน้ำ ถ่ายภาพ ชมบรรยากาศบนเกาะกระดาน พร้อมรับประทานอาหารเที่ยงบุฟเฟ่ต์บนเรือ'
          : lunchOn === 'box'
            ? 'เล่นน้ำ ถ่ายภาพ ชมบรรยากาศบนเกาะกระดาน พร้อมรับประทานอาหารเที่ยง (อาหารกล่อง)'
            : 'เล่นน้ำ ถ่ายภาพ ชมบรรยากาศบนเกาะกระดาน พร้อมรับประทานอาหารเที่ยงบนเกาะ',
    },
    {
      time: '13:30',
      title: 'อ่าวไผ่',
      text: 'ชมแนวปะการังที่สมบูรณ์ จุดดำน้ำสำคัญ (จุดที่สองของเกาะกระดาน)',
    },
    {
      time: '15:30',
      title: 'เกาะแหวน · เกาะเชือก',
      text: 'ดำน้ำชมปะการังและฝูงปลานานาชนิด',
    },
    {
      time: '16:30',
      title: 'กลับท่าเรือ',
      text: 'เดินทางกลับท่าเรือปากเมง อาบน้ำเปลี่ยนเสื้อผ้า จบโปรแกรม',
    }
  );
  return steps;
}

function rokItinerary() {
  return [
    { time: '07:30', title: 'รับในเมืองตรัง', text: 'รับลูกค้าในเมืองตรัง เดินทางสู่ท่าเรือปากเมง' },
    {
      time: '09:00',
      title: 'เกาะรอก',
      text: 'ออกเรือสู่เกาะรอก หาดทรายขาว น้ำทะเลใส เห็นปะการังใต้น้ำ',
    },
    {
      time: '10:30',
      title: 'เกาะรอกนอก',
      text: 'แวะเกาะรอกนอก ถ่ายภาพและพักผ่อนบนหาดทราย',
    },
    {
      time: '12:00',
      title: 'เกาะรอกใน',
      text: 'รับประทานอาหารเที่ยงที่เกาะรอกใน เล่นน้ำและเดินชมหาด',
    },
    {
      time: '13:30',
      title: 'ดำน้ำเกาะรอกใน',
      text: 'ดำน้ำชมปะการังอ่อนและฝูงปลา',
    },
    {
      time: '15:00',
      title: 'เกาะม้า',
      text: 'เดินทางสู่เกาะม้า ดำน้ำชมปะการังอ่อน ปะการังแข็ง และปลานานาชนิด',
    },
    {
      time: '16:30',
      title: 'กลับท่าเรือ',
      text: 'เดินทางกลับท่าเรือปากเมง อาบน้ำเปลี่ยนเสื้อผ้า จบโปรแกรม',
    },
  ];
}

const basePrograms = [
  {
    id: 'dl001',
    packageCode: 'DL001',
    name: 'ไปเช้าเย็นกลับ 4 เกาะ (เรือหางยาว)',
    brochure: '650-538x1024.webp',
    route: CAPTION_4,
    stops: ['ถ้ำมรกต', 'เกาะกระดาน', 'เกาะแหวน', 'เกาะเชือก'],
    duration: 'ประมาณ 8 ชั่วโมง',
    basePrice: 650,
    childPrice: 550,
    boats: ['longtail'],
    desc: 'ทะเลตรัง 4 เกาะด้วยเรือหางยาว อาหารกล่อง ครบถ้ำมรกตและดำน้ำ 2 จุดที่เกาะกระดาน',
    ribbon: '650 ฿',
    rating: 4.9,
    reviewCount: '',
    oldPrice: 0,
    stars: 5,
    galleryCaption: CAPTION_4,
    season: '1 ต.ค. – 31 พ.ค. · ออกเดินทางทุกวัน ตั้งแต่ 6 ท่านขึ้นไป',
    priceMeetingNote: 'เจอกันที่ท่าเรือปากเมง · ผู้ใหญ่ 650 บาท · เด็ก 550 บาท',
    itinerary: fourIslandsItinerary({ meetOnly: true, lunchOn: 'box' }),
    inclusions: [
      'เรือหางยาวนำเที่ยวทะเลตรัง',
      'อาหารเที่ยง (อาหารกล่อง)',
      'สตาฟดูแล / ไกด์',
      'อุปกรณ์ดำน้ำ หน้ากาก เสื้อชูชีพ',
      'ค่าธรรมเนียมอุทยาน (คนไทย)',
      'น้ำดื่ม · น้ำแข็ง',
      'ค่าประกันอุบัติเหตุ 1,000,000 บาท',
    ],
    priceNotes: [
      'เด็กเล็ก 1–2.99 ปี ฟรี',
      'เด็ก 3–11.99 ปี 550 บาท · ผู้ใหญ่ 12 ปีขึ้นไป 650 บาท',
      'ไม่รวม: รถรับจากเมือง/สนามบิน',
      'ไม่รวม: ค่าธรรมเนียมชาวต่างชาติ 200 บาท',
    ],
    warning: WARNING,
    liveUrl: LIVE.dl001,
  },
  {
    id: 'dt001',
    packageCode: 'DT001',
    name: 'ไปเช้าเย็นกลับ 4 เกาะ (เรือทัวร์)',
    brochure: '950-538x1024.webp',
    route: CAPTION_4,
    stops: ['ถ้ำมรกต', 'เกาะกระดาน', 'เกาะแหวน', 'เกาะเชือก'],
    duration: 'ประมาณ 8 ชั่วโมง',
    basePrice: 950,
    childPrice: 700,
    boats: ['bigboat'],
    desc: 'ทะเลตรัง 4 เกาะด้วยเรือทัวร์ อาหารบุฟเฟ่ต์บนเรือ กาแฟและของว่าง รับในเมืองได้',
    ribbon: '950 ฿',
    rating: 4.9,
    reviewCount: '',
    oldPrice: 0,
    stars: 5,
    galleryCaption: CAPTION_4,
    season: '1 ต.ค. – 31 พ.ค. · ออกเดินทางทุกวัน ตั้งแต่ 2 ท่านขึ้นไป',
    priceMeetingNote: 'เจอกันที่ท่าเรือปากเมง · รับในเมือง +200 บาท/ท่าน',
    pickupSurcharge: 200,
    itinerary: fourIslandsItinerary({ pickupCity: true, lunchOn: 'boat' }),
    inclusions: [
      'เรือทัวร์นำเที่ยวทะเลตรัง',
      'อาหารเที่ยงบุฟเฟ่ต์บนเรือ',
      'สตาฟดูแล / ไกด์',
      'อุปกรณ์ดำน้ำ หน้ากาก เสื้อชูชีพ',
      'ค่าธรรมเนียมอุทยาน (คนไทย)',
      'กาแฟ · ของว่าง',
      'ค่าประกันอุบัติเหตุ 1,000,000 บาท',
    ],
    priceNotes: [
      'เด็กเล็ก 1–2.99 ปี ฟรี',
      'เด็ก 3–11.99 ปี 700 บาท · ผู้ใหญ่ 950 บาท',
      'รับในเมืองตรัง +200 บาท/ท่าน',
      'ไม่รวม: ค่าธรรมเนียมชาวต่างชาติ 200 บาท',
    ],
    warning: WARNING,
    liveUrl: LIVE.dt001,
  },
  {
    id: 'ds001',
    packageCode: 'DS001',
    name: 'ไปเช้าเย็นกลับ 4 เกาะ (สปีดโบ트)',
    brochure: '1350-538x1024.webp',
    route: CAPTION_4,
    stops: ['ถ้ำมรกต', 'เกาะกระดาน', 'เกาะแหวน', 'เกาะเชือก'],
    duration: 'ประมาณ 8 ชั่วโมง',
    basePrice: 1350,
    childPrice: 1050,
    boats: ['speedboat'],
    desc: 'ทะเลตรัง 4 เกาะด้วยสปีดโบ트 เร็ว สะดวก อาหารบุฟเฟ่ต์บนเกาะกระดาน',
    ribbon: '1,350 ฿',
    rating: 4.9,
    reviewCount: '',
    oldPrice: 0,
    stars: 5,
    galleryCaption: CAPTION_4,
    season: '1 ต.ค. – 31 พ.ค. · ออกเดินทางทุกวัน ตั้งแต่ 10 ท่านขึ้นไป',
    priceMeetingNote: 'เจอกันที่ท่าเรือปากเมง · รับในเมือง +200 บาท/ท่าน',
    pickupSurcharge: 200,
    itinerary: fourIslandsItinerary({ pickupCity: true, lunchOn: 'island' }),
    inclusions: [
      'สปีดโบตนำเที่ยวทะเลตรัง',
      'อาหารเที่ยงบุฟเฟ่ต์',
      'สตาฟดูแล / ไกด์',
      'อุปกรณ์ดำน้ำ หน้ากาก เสื้อชูชีพ',
      'ค่าธรรมเนียมอุทยาน (คนไทย)',
      'น้ำดื่ม · น้ำแข็ง',
      'ค่าประกันอุบัติเหตุ 1,000,000 บาท',
    ],
    priceNotes: [
      'เด็กเล็ก 1–2.99 ปี ฟรี',
      'เด็ก 3–11.99 ปี 1,050 บาท · ผู้ใหญ่ 1,350 บาท',
      'รับในเมืองตรัง +200 บาท/ท่าน',
      'ไม่รวม: ค่าธรรมเนียมชาวต่างชาติ 200 บาท',
    ],
    warning: WARNING,
    liveUrl: LIVE.ds001,
  },
  {
    id: 'ds002',
    packageCode: 'DS002',
    name: 'ไปเช้าเย็นกลับ เกาะรอก · เกาะม้า',
    brochure: 'เกาะรอก-1650-1-538x1024.webp',
    route: 'เกาะรอกใน → เกาะรอกนอก → เกาะม้า',
    stops: ['เกาะรอก', 'เกาะรอกนอก', 'เกาะรอกใน', 'เกาะม้า'],
    duration: 'ประมาณ 8 ชั่วโมง',
    basePrice: 1650,
    childPrice: 1350,
    boats: ['speedboat'],
    desc: 'ราชินีแห่งท้องทะเลอันดามัน น้ำใส หาดทรายขาว ดำน้ำเกาะรอกและเกาะม้า',
    ribbon: '1,650 ฿',
    rating: 4.9,
    reviewCount: '',
    oldPrice: 0,
    stars: 5,
    galleryCaption: CAPTION_ROK,
    season: '1 ต.ค. – 31 พ.ค. · ออกเดินทางทุกวัน ตั้งแต่ 10 ท่านขึ้นไป',
    priceMeetingNote: 'เจอกันที่ท่าเรือปากเมง · รับในเมือง +200 บาท/ท่าน',
    pickupSurcharge: 200,
    itinerary: rokItinerary(),
    inclusions: [
      'สปีดโบตนำเที่ยว',
      'อาหารเที่ยง',
      'สตาฟดูแล / ไกด์',
      'อุปกรณ์ดำน้ำ หน้ากาก เสื้อชูชีพ',
      'ค่าธรรมเนียมอุทยาน (คนไทย)',
      'น้ำดื่ม · น้ำแข็ง',
      'ค่าประกันอุบัติเหตุ 1,000,000 บาท',
    ],
    priceNotes: [
      'เด็กเล็ก 1–2.99 ปี ฟรี',
      'เด็ก 3–11.99 ปี 1,350 บาท · ผู้ใหญ่ 1,650 บาท',
      'รับในเมืองตรัง +200 บาท/ท่าน',
      'ไม่รวม: ค่าธรรมเนียมชาวต่างชาติ 200 บาท',
    ],
    warning: WARNING_PREG,
    liveUrl: LIVE.ds002,
  },
];

async function main() {
  const programs = [];
  for (const pkg of basePrograms) {
    await new Promise((r) => setTimeout(r, 400));
    let gallery = galleryFrom(pkg.brochure);
    let image = img(pkg.brochure);
    try {
      const html = await fetch(pkg.liveUrl);
      let scraped = cleanGallery(extractGallery(html));
      if (scraped.length < 3) {
        scraped = pkg.id === 'ds002' ? FALLBACK_ROK : FALLBACK_4ISLAND;
      }
      if (scraped.length >= 3) {
        gallery = scraped;
        console.log(pkg.id, 'gallery', scraped.length, 'from live');
      } else {
        console.log(pkg.id, 'using fallback gallery');
        gallery = pkg.id === 'ds002' ? FALLBACK_ROK : FALLBACK_4ISLAND;
      }
      image = CARD_IMAGE[pkg.id] || gallery[0];
    } catch (e) {
      console.warn(pkg.id, 'scrape failed:', e.message);
      gallery = pkg.id === 'ds002' ? FALLBACK_ROK : FALLBACK_4ISLAND;
      image = CARD_IMAGE[pkg.id] || gallery[0];
    }

    const { liveUrl, brochure, ...rest } = pkg;
    programs.push({
      ...rest,
      image,
      gallery,
      highlights: [img(pkg.brochure)],
      licenseNo: '43/00540',
    });
  }

  const site = JSON.parse(fs.readFileSync(sitePath, 'utf8'));
  site.programs = programs;
  if (site.homeSections?.boats) {
    site.homeSections.boats.lead =
      'ไปเช้าเย็นกลับ ถ้ำมรกต เกาะกระดาน เกาะแหวน เกาะเชือก และเกาะรอก — เริ่มต้น 650 บาท ครบเรือ ไกด์ อาหาร และประกัน';
  }
  site.meta.updated = new Date().toISOString().replace(/\.\d{3}Z$/, '+07:00');
  fs.writeFileSync(sitePath, JSON.stringify(site, null, 4) + '\n', 'utf8');
  console.log('Synced', programs.length, 'daytrip programs');
}

main().catch(console.error);
