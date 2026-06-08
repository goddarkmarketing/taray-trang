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
  ['p1-003', 'https://www.xn--72c1af3ci9dk0j.com/%E0%B9%80%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%A7%E0%B8%95%E0%B8%A3%E0%B8%B1%E0%B8%87%E0%B8%A3%E0%B8%AD%E0%B8%81%E0%B9%80%E0%B8%81%E0%B8%B2%E0%B8%B0%E0%B9%84%E0%B8%AB%E0%B8%87p1-003/'],
  ['p1-005', 'https://www.xn--72c1af3ci9dk0j.com/%e0%b9%80%e0%b8%97%e0%b8%b5%e0%b9%88%e0%b8%a2%e0%b8%a7%e0%b8%95%e0%b8%a3%e0%b8%b1%e0%b8%87%e0%b8%99%e0%b8%ad%e0%b8%99%e0%b9%83%e0%b8%99%e0%b9%80%e0%b8%a1%e0%b8%b7%e0%b8%ad%e0%b8%87p1-005-2/'],
];

(async () => {
  for (const [id, url] of pages) {
    const h = await fetch(url);
    fs.writeFileSync(`tools/_html-${id}.txt`, h.slice(0, 200000), 'utf8');
    const all = [...h.matchAll(/wp-content\/uploads\/[^\"'\s>]+\.(?:jpg|jpeg|png|webp)/gi)].map((m) => m[0]);
    const uniq = [...new Set(all)];
    console.log('===', id, 'unique', uniq.length);
    uniq.forEach((x, i) => console.log(i + 1, decodeURIComponent(x)));
  }
})();
