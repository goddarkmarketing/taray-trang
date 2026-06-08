/**
 * Merge 8 daytrip programs + add P2-008 to site.json
 */
const fs = require('fs');
const path = require('path');
const https = require('https');

const sitePath = path.join(__dirname, '../data/site.json');
const catalogPath = path.join(__dirname, '_catalog.html');

const URL_OVERRIDE = {
  'D-005': 'https://www.xn--72c1af3ci9dk0j.com/%e0%b9%80%e0%b8%97%e0%b8%b5%e0%b9%88%e0%b8%a2%e0%b8%a7%e0%b9%80%e0%b8%81%e0%b8%b2%e0%b8%b0%e0%b9%80%e0%b8%ab%e0%b8%a5%e0%b8%b2%e0%b9%80%e0%b8%ab%e0%b8%a5%e0%b8%b5%e0%b8%a2%e0%b8%87/',
  'D-008': 'https://www.xn--72c1af3ci9dk0j.com/%e0%b8%ab%e0%b8%a1%e0%b8%b9%e0%b9%88%e0%b8%9a%e0%b9%89%e0%b8%b2%e0%b8%99%e0%b8%9b%e0%b8%a5%e0%b8%b2%e0%b8%94%e0%b8%b2%e0%b8%a7/',
  'D-009': 'https://www.xn--72c1af3ci9dk0j.com/%e0%b9%80%e0%b8%97%e0%b8%b5%e0%b9%88%e0%b8%a2%e0%b8%a7%e0%b9%80%e0%b8%81%e0%b8%b2%e0%b8%b0%e0%b9%84%e0%b8%ab%e0%b8%87%e0%b9%80%e0%b8%81%e0%b8%b2%e0%b8%b0%e0%b8%a2%e0%b8%b2/',
};

const CARD_IMAGE = {
  dl001: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday2.jpg',
  dt001: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2022/11/264629958_10159283950075219_1058486100921441363_n.jpeg',
  ds001: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2024/10/1.jpg',
  ds002: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2024/10/2.jpg',
  d005: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2025/08/Untitled-design.png',
  d007: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์25.jpg',
  d008: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/ถ้ำมรกต-1.jpg',
  d009: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2024/10/Untitled-design-2-1.png',
};

const FALLBACK_GALLERY = {
  d005: [
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์1.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์25.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์20.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์16.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์5.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2025/08/Untitled-design.png',
  ],
  d007: [
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์16.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์25.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์20.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะเหลาเหลียงวันเดย์1.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวตรังไปเช้าเย็นกลับ1.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday3.jpg',
  ],
  d008: [
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/ถ้ำมรกต-1.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday3.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday2.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวตรังไปเช้าเย็นกลับ29.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวตรังไปเช้าเย็นกลับ27.jpg',
    'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวตรังไปเช้าเย็นกลับ12.jpg',
  ],
};

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

function stripTags(html) {
  return html.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
}

function parseCards(html) {
  const cards = [];
  const re =
    /<a class="elementor-cta" href="([^"]+)"[\s\S]*?data-back="([^"]+)"[\s\S]*?<h[26] class="elementor-cta__title[^"]*">\s*([\s\S]*?)<\/h[26]>[\s\S]*?<div class="elementor-cta__description[^"]*">\s*([\s\S]*?)<\/div>[\s\S]*?รหัส\s*(D-[A-Za-z0-9]+)[\s\S]*?<div class="elementor-ribbon-inner">([^<]+)<\/div>/g;
  let m;
  while ((m = re.exec(html)) !== null) {
    cards.push({
      url: m[1].trim(),
      image: m[2],
      title: stripTags(m[3]),
      desc: stripTags(m[4]),
      code: m[5].trim(),
      ribbon: stripTags(m[6]),
    });
  }
  const seen = new Set();
  return cards.filter((c) => {
    if (seen.has(c.code)) return false;
    seen.add(c.code);
    return true;
  });
}

function isBadGalleryUrl(url) {
  return /cropped|iconfish|icon|logo|favicon|line_oa_chat|\.png$/i.test(url);
}

function extractGallery(html) {
  const imgs = [];
  const seen = new Set();
  const block =
    html.match(
      /elementor-widget-image-gallery[\s\S]*?<div id=['"]gallery-1['"][\s\S]*?<\/div>\s*<\/div>\s*<\/div>/i
    )?.[0] || html;
  const hrefRe =
    /href=['"](https:\/\/www\.xn--72c1af3ci9dk0j\.com\/wp-content\/uploads\/[^'"]+\.(?:jpg|jpeg|png|webp))['"]/gi;
  let m;
  while ((m = hrefRe.exec(block)) !== null) {
    const url = m[1].replace(/-\d+x\d+(?=\.\w+$)/, '');
    if (isBadGalleryUrl(url) || seen.has(url)) continue;
    seen.add(url);
    imgs.push(url);
  }
  return imgs.slice(0, 6);
}

function parsePrice(text) {
  const m = String(text).match(/([\d,]+)\s*บาท/);
  if (m) return parseInt(m[1].replace(/,/g, ''), 10);
  const m2 = String(text).match(/([\d,]+)\s*฿/);
  if (m2) return parseInt(m2[1].replace(/,/g, ''), 10);
  return 0;
}

function parseDetail(html) {
  const gallery = extractGallery(html);
  const incl = [];
  const excl = [];
  const sections = html.split('ราคานี้รวม');
  if (sections[1]) {
    const part = sections[1].split('ราคานี้ไม่รวม');
    const listRe = /elementor-icon-list-text">([^<]+)</g;
    let lm;
    while ((lm = listRe.exec(part[0] || ''))) incl.push(lm[1].trim());
    listRe.lastIndex = 0;
    while ((lm = listRe.exec(part[1] || ''))) excl.push(lm[1].trim());
  }
  const childMatch = html.match(/เด็ก[^0-9]*([\d,]+)\s*บาท/);
  const adultMatch = html.match(/ผู้ใหญ่[^0-9]*([\d,]+)\s*บาท/);
  return { gallery, inclusions: incl, priceNotes: excl, childPrice: childMatch ? parseInt(childMatch[1].replace(/,/g, ''), 10) : undefined, basePrice: adultMatch ? parseInt(adultMatch[1].replace(/,/g, ''), 10) : undefined };
}

function buildFromCard(card, detail, id) {
  const basePrice = detail.basePrice || parsePrice(card.ribbon) || 0;
  const childPrice = detail.childPrice || (basePrice ? Math.round(basePrice * 0.85) : 0);
  const gallery =
    detail.gallery.length >= 3
      ? detail.gallery
      : FALLBACK_GALLERY[id] || [card.image, card.image, card.image, card.image, card.image, card.image];
  return {
    id,
    packageCode: card.code,
    name: card.title.replace(/\s+/g, ' ').trim(),
    route: card.title.replace(/\s+/g, ' ').trim(),
    stops: card.desc.split(/[·,]/).map((s) => s.trim()).filter(Boolean).slice(0, 4),
    duration: 'ประมาณ 8 ชั่วโมง',
    basePrice,
    childPrice,
    boats: ['speedboat'],
    desc: card.desc,
    ribbon: basePrice ? `${basePrice.toLocaleString('en-US')} ฿` : card.ribbon,
    rating: 4.9,
    reviewCount: '',
    oldPrice: 0,
    stars: 5,
    galleryCaption: card.title.replace(/\s+/g, ' ').trim(),
    season: '1 ต.ค. – 31 พ.ค. · ออกเดินทางทุกวัน',
    priceMeetingNote: 'เจอกันที่ท่าเรือปากเมง · รับในเมือง +200 บาท/ท่าน',
    pickupSurcharge: 200,
    inclusions: detail.inclusions.length
      ? detail.inclusions
      : [
          'เรือนำเที่ยวทะเลตรัง',
          'อาหารเที่ยง',
          'สตาฟดูแล / ไกด์',
          'อุปกรณ์ดำน้ำ หน้ากาก เสื้อชูชีพ',
          'ค่าธรรมเนียมอุทยาน (คนไทย)',
          'น้ำดื่ม · น้ำแข็ง',
          'ค่าประกันอุบัติเหตุ 1,000,000 บาท',
        ],
    priceNotes: detail.priceNotes.length
      ? detail.priceNotes.map((x) => (x.startsWith('ไม่รวม') ? x : `ไม่รวม: ${x}`))
      : ['เด็กเล็ก 1–2.99 ปี ฟรี', 'ไม่รวม: ค่าธรรมเนียมชาวต่างชาติ 200 บาท'],
    warning:
      'โปรแกรมอาจเปลี่ยนแปลงตามสภาพอากาศ ระดับน้ำขึ้นลง และคลื่นลม แจ้งเลื่อนวันเที่ยวล่วงหน้า 7 วัน ไม่คืนเงินทุกกรณี',
    image: CARD_IMAGE[id] || gallery[0] || card.image,
    gallery,
    licenseNo: '43/00540',
  };
}

const ID_BY_CODE = {
  'D-001': 'dl001',
  'D-002': 'dt001',
  'D-003': 'ds001',
  'D-004': 'ds002',
  'D-005': 'd005',
  'D-07': 'd007',
  'D-008': 'd008',
  'D-009': 'd009',
};

async function scrapeCard(card) {
  const url = URL_OVERRIDE[card.code] || card.url;
  try {
    const html = await fetch(url);
    const detail = parseDetail(html);
    return buildFromCard(card, detail, ID_BY_CODE[card.code]);
  } catch (e) {
    console.warn('scrape fail', card.code, e.message);
    return buildFromCard(card, { gallery: [], inclusions: [], priceNotes: [] }, ID_BY_CODE[card.code]);
  }
}

function buildP2_008() {
  const p1 = JSON.parse(fs.readFileSync(sitePath, 'utf8')).packages2d1n.find((p) => p.id === 'p1-008');
  return {
    id: 'p2-008',
    packageCode: 'P2-008',
    name: 'ทะเลตรัง + เกาะรอก นอนในเมืองตรัง',
    image: 'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะรอกวันเดียว5.jpg',
    gallery: [
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะรอกวันเดียว6.jpg',
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวเกาะรอกวันเดียว5.jpg',
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday3.jpg',
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/Oneday2.jpg',
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวตรังไปเช้าเย็นกลับ18.jpg',
      'https://www.xn--72c1af3ci9dk0j.com/wp-content/uploads/2019/06/เที่ยวตรังไปเช้าเย็นกลับ29.jpg',
    ],
    route: 'ทะเลตรัง 4 เกาะ → เกาะรอก + เกาะม้า (พักเมืองตรัง 2 คืน)',
    stops: [
      'วัน 1: เช็กอิน · ทะเลตรัง 4 เกาะ (เรือทัวร์)',
      'วัน 2: เที่ยวเกาะรอกเต็มวัน (สปีดโบต)',
      'วัน 3: เช็กเอาท์และเดินทางกลับ',
    ],
    duration: '3 วัน 2 คืน',
    basePrice: 3500,
    childPrice: 2800,
    boats: ['bigboat', 'speedboat'],
    desc: 'เที่ยวทะเลตรังด้วยเรือทัวร์ พักเมืองตรัง 2 คืน เที่ยวเกาะรอกและเกาะม้าเต็มวันกับสปีดโบต',
    ribbon: 'เริ่มต้น 3,500 ฿',
    rating: 4.8,
    reviewCount: '',
    oldPrice: 0,
    stars: 5,
    galleryCaption: 'ทะเลตรัง · เกาะรอก · เกาะม้า',
    licenseNo: '43/00540',
    season: 'ตั้งแต่วันที่ 1 ตุลาคม – 31 พฤษภาคม ทุกปี ออกเดินทางทุกวัน',
    priceMeetingNote: 'เจอกันที่ท่าเรือปากเมง · รับในเมือง +200 บาท/ท่าน',
    pickupSurcharge: 200,
    itinerary: [
      { time: '12:00', title: 'วันที่ 1 · เช็กอิน', text: 'เช็กอินที่พักในเมืองตรัง' },
      { time: '09:00', title: 'วันที่ 1 · ท่าเรือปากเมง', text: 'พร้อมกันที่ท่าเรือปากเมง' },
      { time: '09:30', title: 'วันที่ 1 · ถ้ำมรกต', text: 'ลงเรือเดินทางสู่เกาะมุก สัมผัสความงามของถ้ำมรกต' },
      { time: '11:30', title: 'วันที่ 1 · เกาะกระดาน', text: 'เล่นน้ำ ถ่ายภาพ รับประทานอาหารเที่ยงบุฟเฟ่ต์บนเรือ' },
      { time: '15:30', title: 'วันที่ 1 · เกาะแหวน · เกาะเชือก', text: 'ดำน้ำชมปะการังและฝูงปลา' },
      { time: '09:00', title: 'วันที่ 2 · เกาะรอก', text: 'ออกเรือสู่เกาะรอก หาดทรายขาว น้ำทะเลใส' },
      { time: '15:00', title: 'วันที่ 2 · เกาะม้า', text: 'ดำน้ำชมปะการังที่เกาะม้า' },
      { time: '10:00', title: 'วันที่ 3 · เช็กเอาท์', text: 'เก็บสัมภาระ จบโปรแกรม' },
    ],
    inclusions: p1?.inclusions || [
      'ค่าที่พัก 2 คืน ในเมืองตรัง',
      'เรือทัวร์ทะเลตรัง 4 เกาะ',
      'เรือสปีดโบตเที่ยวเกาะรอก',
      'อาหารเที่ยง',
      'อุปกรณ์ดำน้ำ · เสื้อชูชีพ',
      'ค่าธรรมเนียมอุทยาน · ประกัน 1,000,000 บาท',
    ],
    priceNotes: [
      'ไม่รวม: อาหารเช้า · อาหารเย็น',
      'ไม่รวม: ค่าธรรมเนียมชาวต่างชาติ 200 บาท',
    ],
    warning:
      'โปรแกรมอาจเปลี่ยนแปลงตามสภาพอากาศและคลื่นลม · ไม่เหมาะสำหรับสตรีมีครรภ์',
  };
}

async function main() {
  const site = JSON.parse(fs.readFileSync(sitePath, 'utf8'));
  const catalogHtml = fs.readFileSync(catalogPath, 'utf8');
  const cards = parseCards(catalogHtml);
  const order = ['D-005', 'D-07', 'D-008', 'D-009'];
  const programs = [...(site.programs || [])];
  const existingIds = new Set(programs.map((p) => p.id));

  for (const code of order) {
    const card = cards.find((c) => c.code === code);
    if (!card) {
      console.warn('missing card', code);
      continue;
    }
    const id = ID_BY_CODE[code];
    if (existingIds.has(id)) {
      console.log('skip existing', code, id);
      continue;
    }
    await new Promise((r) => setTimeout(r, 350));
    const pkg = await scrapeCard(card);
    if (code === 'D-07' && !pkg.basePrice) {
      pkg.basePrice = 2800;
      pkg.childPrice = 2380;
      pkg.ribbon = '2,800 ฿';
    }
    programs.push(pkg);
    console.log('OK', code, pkg.id, pkg.basePrice);
  }

  // Ensure unique card covers for first 4
  for (const p of programs) {
    if (CARD_IMAGE[p.id]) p.image = CARD_IMAGE[p.id];
  }

  site.programs = programs.sort(
    (a, b) =>
      ['dl001', 'dt001', 'ds001', 'ds002', 'd005', 'd007', 'd008', 'd009'].indexOf(a.id) -
      ['dl001', 'dt001', 'ds001', 'ds002', 'd005', 'd007', 'd008', 'd009'].indexOf(b.id)
  );
  if (!site.packages3d2n.some((p) => p.id === 'p2-008')) {
    site.packages3d2n = [...(site.packages3d2n || []), buildP2_008()];
  }

  if (site.homeSections?.boats) {
    site.homeSections.boats.lead =
      'ไปเช้าเย็นกลับ ทะเลตรัง 4 เกาะ เกาะเหลาเหลียง เกาะมุก เกาะไหง และเกาะรอก — เริ่มต้น 600 บาท ครบเรือ ไกด์ อาหาร และประกัน';
  }

  site.meta.updated = new Date().toISOString().replace(/\.\d{3}Z$/, '+07:00');
  fs.writeFileSync(sitePath, JSON.stringify(site, null, 4) + '\n', 'utf8');
  console.log('programs', programs.length, 'packages3d2n', site.packages3d2n.length);
}

main().catch(console.error);
