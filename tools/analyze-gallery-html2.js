const fs = require('fs');
const https = require('https');

function fetch(url) {
  return new Promise((resolve, reject) => {
    https
      .get(url, { headers: { 'User-Agent': 'Mozilla/5.0' } }, (res) => {
        const chunks = [];
        res.on('data', (c) => chunks.push(c));
        res.on('end', () => resolve(Buffer.concat(chunks).toString('utf8')));
      })
      .on('error', reject);
  });
}

const pages = [
  ['p1-002', 'https://www.xn--72c1af3ci9dk0j.com/%E0%B9%80%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%95%E0%B8%A3%E0%B8%B1%E0%B8%87%E0%B8%99%E0%B8%AD%E0%b8%99%E0%B9%80%E0%B8%81%E0%B8%B2%E0%B8%B0%E0%B9%84%E0%B8%AB%E0%B8%87p1-002/'],
  ['p1-004', 'https://www.xn--72c1af3ci9dk0j.com/%E0%B9%80%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%95%E0%B8%A3%E0%B8%B1%E0%B8%87%E0%B8%9E%E0%B8%B1%E0%B8%81%E0%B9%80%E0%B8%A1%E0%B8%B7%E0%B8%AD%E0%B8%87%E0%B8%A3%E0%B8%AD%E0%B8%81p1-004/'],
  ['p1-006', 'https://www.xn--72c1af3ci9dk0j.com/p1-006/'],
];

(async () => {
  for (const [id, url] of pages) {
    const h = await fetch(url);
    const all = [...h.matchAll(/wp-content\/uploads\/[^\"'\s>]+\.(?:jpg|jpeg|png|webp)/gi)].map((m) => m[0]);
    const full = all.filter((x) => !/-\d+x\d+\./.test(x) && !/cropped|icon/i.test(x));
    const uniq = [...new Set(full)];
    console.log('===', id, uniq.length);
    uniq.forEach((x, i) => console.log(i + 1, decodeURIComponent(x)));
  }
})();
