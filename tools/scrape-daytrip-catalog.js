/**
 * Scrape daytrip (D-*) packages from live catalog + detail pages.
 * Usage: node tools/scrape-daytrip-catalog.js
 */
const fs = require('fs');
const path = require('path');
const https = require('https');

const LIST_URL =
  'https://www.xn--72c1af3ci9dk0j.com/%e0%b9%81%e0%b8%9e%e0%b9%87%e0%b8%84%e0%b9%80%e0%b8%81%e0%b8%88%e0%b8%97%e0%b8%b1%e0%b8%a7%e0%b8%a3%e0%b9%8c/';

const EXISTING = {
  'D-001': 'dl001',
  'D-002': 'dt001',
  'D-003': 'ds001',
  'D-004': 'ds002',
};

const ID_MAP = {
  'D-005': 'd005',
  'D-07': 'd007',
  'D-008': 'd008',
  'D-009': 'd009',
  'D-010': 'd010',
  'D-011': 'd011',
  'D-012': 'd012',
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
  const galleryMatch = html.match(
    /elementor-widget-image-gallery[\s\S]*?<div id=['"]gallery-1['"][\s\S]*?<\/div>\s*<\/div>\s*<\/div>/i
  );
  const block = galleryMatch ? galleryMatch[0] : html;
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

function parseDetail(html, card) {
  const gallery = extractGallery(html);
  const ogImage = html.match(/property="og:image" content="([^"]+)"/);
  const image = gallery[0] || ogImage?.[1] || card.image;

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

  const season = html.match(/แพ็คเกจนี้เริ่มใช้([^<]+)/);
  const childMatch = html.match(/เด็ก[^0-9]*([\d,]+)\s*บาท/);
  const adultMatch = html.match(/ผู้ใหญ่[^0-9]*([\d,]+)\s*บาท/);

  const steps = [];
  const stepRe =
    /elementor-icon-box-title[^>]*>([^<]+)<[\s\S]*?elementor-icon-box-description[^>]*>([\s\S]*?)<\//g;
  let sm;
  while ((sm = stepRe.exec(html)) !== null) {
    const title = stripTags(sm[1]);
    const text = stripTags(sm[2]);
    if (title && text) steps.push({ time: '', title, text });
  }

  return {
    gallery: gallery.length >= 3 ? gallery : [image, image, image, image, image, image],
    image,
    inclusions: incl.length ? incl : undefined,
    priceNotes: excl.length ? excl.map((x) => `ไม่รวม: ${x}`) : undefined,
    season: season ? `เริ่มใช้${stripTags(season[1])}` : undefined,
    childPrice: childMatch ? parseInt(childMatch[1].replace(/,/g, ''), 10) : undefined,
    adultFromDetail: adultMatch ? parseInt(adultMatch[1].replace(/,/g, ''), 10) : undefined,
    itinerary: steps.length >= 3 ? steps.slice(0, 8).map((s, i) => ({
      ...s,
      time: `${String(7 + i * 2).padStart(2, '0')}:00`,
    })) : undefined,
  };
}

function boatFromDesc(desc, title) {
  const t = `${title} ${desc}`;
  if (/speedboat|สปีด/i.test(t)) return ['speedboat'];
  if (/เรือทัวร์|เรือใหญ่|big/i.test(t)) return ['bigboat'];
  if (/เรือหางยาว|longtail/i.test(t)) return ['longtail'];
  if (/ตุ๊ก|tuktuk|รถ/i.test(t)) return [];
  return ['speedboat'];
}

function buildPackage(card, detail) {
  const id = ID_MAP[card.code] || card.code.toLowerCase().replace(/-/g, '');
  const basePrice = detail.adultFromDetail || parsePrice(card.ribbon) || 0;
  const childPrice = detail.childPrice || (basePrice ? Math.round(basePrice * 0.85) : 0);
  const ribbon = parsePrice(card.ribbon)
    ? `${parsePrice(card.ribbon).toLocaleString('en-US')} ฿`
    : card.ribbon;

  return {
    id,
    packageCode: card.code,
    name: card.title.replace(/\s+/g, ' ').trim(),
    route: card.title.replace(/\s+/g, ' ').trim(),
    stops: card.title.split(/\s+/).filter(Boolean).slice(0, 4),
    duration: 'ประมาณ 8 ชั่วโมง',
    basePrice,
    childPrice,
    boats: boatFromDesc(card.desc, card.title),
    desc: card.desc,
    ribbon,
    rating: 4.9,
    reviewCount: '',
    oldPrice: 0,
    stars: 5,
    galleryCaption: card.title.replace(/\s+/g, ' ').trim(),
    season: detail.season || '1 ต.ค. – 31 พ.ค. · ออกเดินทางทุกวัน',
    priceMeetingNote: 'เจอกันที่ท่าเรือปากเมง · รับในเมือง +200 บาท/ท่าน',
    pickupSurcharge: 200,
    itinerary: detail.itinerary,
    inclusions: detail.inclusions || [
      'เรือนำเที่ยวทะเลตรัง',
      'อาหารเที่ยง',
      'สตาฟดูแล / ไกด์',
      'อุปกรณ์ดำน้ำ หน้ากาก เสื้อชูชีพ',
      'ค่าธรรมเนียมอุทยาน (คนไทย)',
      'น้ำดื่ม · น้ำแข็ง',
      'ค่าประกันอุบัติเหตุ 1,000,000 บาท',
    ],
    priceNotes: detail.priceNotes || [
      'เด็กเล็ก 1–2.99 ปี ฟรี',
      'ไม่รวม: ค่าธรรมเนียมชาวต่างชาติ 200 บาท',
    ],
    warning:
      'โปรแกรมอาจเปลี่ยนแปลงตามสภาพอากาศ ระดับน้ำขึ้นลง และคลื่นลม แจ้งเลื่อนวันเที่ยวล่วงหน้า 7 วัน ไม่คืนเงินทุกกรณี',
    image: detail.image,
    gallery: detail.gallery,
    licenseNo: '43/00540',
    liveUrl: card.url,
  };
}

async function main() {
  const html = await fetch(LIST_URL);
  const cards = parseCards(html);
  console.log('Catalog D cards:', cards.map((c) => c.code).join(', '));

  const targetCodes = ['D-005', 'D-07', 'D-008', 'D-009'];
  const toScrape = cards.filter((c) => targetCodes.includes(c.code));
  const scraped = [];

  for (const card of toScrape) {
    await new Promise((r) => setTimeout(r, 400));
    try {
      const detailHtml = await fetch(card.url);
      const detail = parseDetail(detailHtml, card);
      scraped.push(buildPackage(card, detail));
      console.log('OK', card.code, card.url);
    } catch (e) {
      console.warn('FAIL', card.code, e.message);
      scraped.push(buildPackage(card, { gallery: [card.image], image: card.image }));
    }
  }

  const out = path.join(__dirname, '_daytrip-new.json');
  fs.writeFileSync(out, JSON.stringify(scraped, null, 2), 'utf8');
  console.log('Wrote', out, scraped.length, 'packages');
}

main().catch(console.error);
