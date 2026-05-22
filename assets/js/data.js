/* =========================================================
   Talay Trang — Mock Data
   ไฟล์เดียวสำหรับแก้เนื้อหา รูปภาพ ราคา และโปรแกรมทั้งหมด
   ========================================================= */

/* ---------- IMAGES Registry ----------
   รูปทั้งหมดเก็บที่นี่จุดเดียว ใช้ Pexels (ฟรี โหลดตรงได้)
   ถ้าต้องการเปลี่ยนรูป → แทนค่า ID Pexels ตรงนี้พอ
*/
const PX = (id, w = 1200) => `https://images.pexels.com/photos/${id}/pexels-photo-${id}.jpeg?auto=compress&cs=tinysrgb&w=${w}`;

const IMAGES = {
  // Heroes — ใช้ width 1600 เพื่อความคมบนจอใหญ่
  heroHome:     PX(14573822, 1920), // Phi Phi aerial drone
  heroBoats:    PX(1591376,  1920), // longtail boats Thailand
  heroPrograms: PX(1647064,  1920), // aerial water + island
  heroBooking:  PX(31200075, 1920), // Krabi Tub Island
  heroReviews:  PX(9164423,  1920), // Phi Phi warm
  heroVideos:   PX(28581876, 1920), // Phi Phi adventure
  heroAbout:    PX(13419316, 1920), // Phi Phi with longtails
  heroContact:  PX(457881,   1920), // clear blue shore

  // Boat showcase cards (4:3)
  boatLongtail:  PX(237741,   1200), // คนกับเรือหางยาวกระบี่
  boatSpeedboat: PX(90427,    1200), // Yellow speed boat Krabi
  boatLarge:     PX(13680386, 1200), // Aerial white yacht

  // Programs cards (16:10)
  programFourIslands: PX(14573822, 1200),
  programKradan:      PX(1450363,  1200),
  programDiving:      PX(15763636, 1200), // coral reef underwater
  programEmerald:     PX(1647064,  1200),
  programPrivate:     PX(17942107, 1200), // yacht turquoise sea

  // Videos thumbs (9:16) — re-use ทั่วไป
  videoThumbs: [
    PX(14573822, 800),
    PX(15763636, 800),
    PX(90427,    800),
    PX(13419316, 800),
    PX(1647064,  800),
    PX(1450363,  800),
    PX(6698714,  800),
    PX(28581876, 800),
  ],

  // About story stack
  about1: PX(1591376,  1000),
  about2: PX(237741,   1000),
  about3: PX(31029704, 1000),

  // Service tiles (แพ็คเกจทัวร์ตรัง 11 ใบ) — ใช้ขนาด 900 พอเหมาะกับการ์ด
  svcBigBoat:    PX(13680386, 900),  // เช่าเรือทัวร์ขนาดใหญ่
  svcLongtail:   PX(1591376,  900),  // เช่าเรือหางยาว
  svcSpeedboat:  PX(90427,    900),  // เช่าเรือ Speedboat
  svcHotel:      PX(5740342,  900),  // จองห้องพัก
  svcFerry:      PX(14573822, 900),  // ตั๋วเรือเข้าเกาะ
  svcCar:        PX(15804640, 900),  // เช่ารถ
  svcSeminar:    PX(3184292,  900),  // ประชุมสัมมนา
  svcScuba:      PX(6173672,  900),  // ดำน้ำลึก
  svcFood:       PX(769289,   900),  // จัดทำอาหาร
  svcPlan:       PX(7235811,  900),  // วางแผนด้วยตัวเอง
  svcFilm:       PX(17051407, 900),  // สถานที่ถ่ายหนัง

  // Local user-supplied images (ยังเก็บไว้ใช้ในจุด trust/พื้นหลังเสริม)
  local: Array.from({length: 11}, (_, i) => `assets/images/img-${String(i+1).padStart(2,'0')}.webp`),
};

/* ---------- SITE info ---------- */
const SITE = {
  brand: 'Talay Trang',
  brandTh: 'เช่าเรือตรัง',
  tagline: 'เลือกเรือได้ตามสไตล์คุณ',
  phone: '081-234-5678',          // ⚠ แก้เป็นเบอร์จริงของเจ้าของ
  phoneDisplay: '081-234-5678',
  lineId: '@talaytrang',
  lineUrl: 'https://line.me/R/ti/p/%40talaytrang',
  facebookUrl: 'https://www.facebook.com/share/185Q3dvN1q/?mibextid=wwXIfr',
  tiktokUrl: 'https://www.tiktok.com/@talaytrang',
  address: 'อำเภอกันตัง จังหวัดตรัง',
  addressFull: 'ออฟฟิศ เช่าเรือตรัง (Talay Trang) อ.กันตัง จ.ตรัง 92110',
  hours: 'เปิดบริการทุกวัน 07:00 – 21:00 น.',
  mapEmbed: 'https://www.google.com/maps?q=Kantang+pier+Trang&output=embed',
};

const NAV_ITEMS = [
  { href: 'index.html',    label: 'หน้าแรก' },
  { href: 'boats.html',    label: 'ประเภทเรือ' },
  { href: 'programs.html', label: 'โปรแกรม' },
  { href: 'booking.html',  label: 'จองเรือ' },
  { href: 'reviews.html',  label: 'รีวิว' },
  { href: 'videos.html',   label: 'วิดีโอ' },
  { href: 'about.html',    label: 'เกี่ยวกับเรา' },
  { href: 'contact.html',  label: 'ติดต่อ' },
];

/* ---------- ประเภทเรือ ---------- */
const BOATS = [
  {
    id: 'longtail',
    name: 'เรือหางยาว',
    tag: 'Local Vibes',
    image: IMAGES.boatLongtail,
    capacity: '6–10 ท่าน',
    capacityMax: 10,
    basePrice: 3500,
    short: 'บรรยากาศท้องถิ่นแท้ ๆ ของชาวเลตรัง ถ่ายรูปสวย ค่าใช้จ่ายเป็นมิตร',
    description: 'เรือหางยาวคือเอกลักษณ์ของทะเลตรัง เหมาะกับกลุ่มเล็ก คู่รัก หรือครอบครัวที่อยากสัมผัสบรรยากาศแบบโลคอลใกล้ชิดธรรมชาติ มุมถ่ายรูปสวยทุกองศา ราคาสบายกระเป๋า',
    suitable: 'คู่รัก ครอบครัวเล็ก สายถ่ายรูป ทริปประหยัด',
    highlights: [
      'จุได้ 6–10 ท่านต่อลำ สบาย ๆ',
      'เข้าได้ถึงเกาะใกล้และจุดถ่ายรูปลับ',
      'เรือเปิดโล่ง รับลมทะเล สดชื่นตลอดทริป',
      'กัปตันชาวตรังโดยกำเนิด รู้ทะเลเป็นอย่างดี',
    ],
  },
  {
    id: 'speedboat',
    name: 'Speed Boat',
    tag: 'Fast & Comfort',
    image: IMAGES.boatSpeedboat,
    capacity: '10–25 ท่าน',
    capacityMax: 25,
    basePrice: 8000,
    short: 'เร็ว นั่งสบาย ไปได้หลายเกาะในวันเดียว เหมาะกับคนที่อยากเที่ยวให้คุ้มเวลา',
    description: 'Speed Boat สำหรับคนที่อยากเก็บทุกเกาะให้ครบในวันเดียว เครื่องแรง ตัดคลื่นเร็ว เบาะนั่งสบาย มีหลังคากันแดดและฝน เหมาะกับทริปเพื่อน ครอบครัว หรือบริษัทขนาดกลาง',
    suitable: 'กรุ๊ปกลาง 10–25 ท่าน สายเที่ยวเก็บจุด ไม่อยากเสียเวลา',
    highlights: [
      'รองรับ 10–25 ท่านต่อลำ',
      'ความเร็วสูง เก็บได้ 4–7 เกาะใน 1 วัน',
      'มีหลังคา + เสื้อชูชีพครบทุกที่นั่ง',
      'มีถังเก็บความเย็นบนเรือ น้ำดื่มไม่อั้น',
    ],
  },
  {
    id: 'bigboat',
    name: 'เรือใหญ่',
    tag: 'Premium Group',
    image: IMAGES.boatLarge,
    capacity: '25–60 ท่าน',
    capacityMax: 60,
    basePrice: 12000,
    short: 'พื้นที่กว้างขวาง เหมาะกับกรุ๊ปใหญ่ สัมมนา บริษัท หรือทริปครอบครัวขยายใหญ่',
    description: 'เรือใหญ่ของเรามีพื้นที่กว้าง มีโต๊ะนั่งทานข้าวบนเรือ จัดทริปกินเลี้ยงกลางทะเลได้สบาย เหมาะกับงานสัมมนา บริษัท วันเกิด งานแต่ง หรือทริปครอบครัวใหญ่ที่ต้องการความสะดวก',
    suitable: 'บริษัท สัมมนา ทริปครอบครัวใหญ่ 25 ท่านขึ้นไป งานเหมาลำพิเศษ',
    highlights: [
      'รองรับสูงสุด 60 ท่าน',
      'มีห้องน้ำบนเรือ',
      'พื้นที่นั่งทานอาหารบนเรือสบาย ๆ',
      'จัดกิจกรรมกรุ๊ปได้ ถ่ายภาพหมู่สวย',
    ],
  },
];

/* ---------- Option เพิ่มเติม ---------- */
const OPTIONS = [
  { id: 'lunch',   icon: 'cutlery', label: 'อาหารเที่ยง',     desc: 'เซ็ตอาหารทะเลสด',         price: 250,  unit: 'per_person', priceLabel: '+250 บาท/คน' },
  { id: 'guide',   icon: 'users',   label: 'ไกด์นำเที่ยว',     desc: 'ไกด์ท้องถิ่นพูดไทย/อังกฤษ', price: 1500, unit: 'flat',       priceLabel: '+1,500 บาท/ทริป' },
  { id: 'photo',   icon: 'camera',  label: 'ช่างภาพมืออาชีพ', desc: 'ส่งภาพภายใน 3 วัน',       price: 2500, unit: 'flat',       priceLabel: '+2,500 บาท/ทริป' },
  { id: 'snorkel', icon: 'scuba',   label: 'อุปกรณ์ดำน้ำ',     desc: 'หน้ากาก + ท่อหายใจ',      price: 150,  unit: 'per_person', priceLabel: '+150 บาท/คน' },
  { id: 'shuttle', icon: 'carPick', label: 'รถรับส่ง',        desc: 'รับส่งจากที่พักในตัวเมือง',  price: 1000, unit: 'flat',       priceLabel: '+1,000 บาท/ทริป' },
];

/* ---------- โปรแกรมทัวร์ ---------- */
const PROGRAMS = [
  {
    id: 'four-islands',
    name: 'โปรแกรม 4 เกาะ ทะเลตรัง',
    image: IMAGES.programFourIslands,
    route: 'เกาะมุก → ถ้ำมรกต → เกาะกระดาน → เกาะเชือก',
    stops: ['เกาะมุก', 'ถ้ำมรกต', 'เกาะกระดาน', 'เกาะเชือก'],
    duration: 'ประมาณ 8 ชั่วโมง',
    basePrice: 3500,
    boats: ['longtail', 'speedboat', 'bigboat'],
    desc: 'โปรแกรมยอดฮิตของทะเลตรัง ครบทุกไฮไลต์ในวันเดียว เริ่มจากถ้ำมรกตที่ต้องว่ายผ่านถ้ำเข้าไปสู่หาดในซ่อนตัว ตามด้วยปะการังหลากสีที่เกาะเชือก จบที่หาดทรายขาวเกาะกระดาน',
    ribbon: 'ยอดนิยม',
  },
  {
    id: 'kradan',
    name: 'โปรแกรมเกาะกระดาน',
    image: IMAGES.programKradan,
    route: 'เกาะกระดาน → จุดดำน้ำ → ชมพระอาทิตย์',
    stops: ['หาดเกาะกระดาน', 'จุดดำน้ำตื้น', 'จุดถ่ายภาพ'],
    duration: 'ประมาณ 6 ชั่วโมง',
    basePrice: 4500,
    boats: ['longtail', 'speedboat'],
    desc: 'พักผ่อนแบบสบาย ๆ บนหาดทรายขาวละเอียดของเกาะกระดาน หาดที่ได้ชื่อว่าน้ำใสที่สุดในตรัง เหมาะกับคู่รักและครอบครัวที่อยากเล่นน้ำชิล ๆ ไม่เร่งรีบ',
  },
  {
    id: 'diving',
    name: 'โปรแกรมดำน้ำดูปะการัง',
    image: IMAGES.programDiving,
    route: 'เกาะเชือก → เกาะม้า → เกาะแหวน',
    stops: ['ดำน้ำดูปะการัง 3 จุด', 'ชมฝูงปลาเสือ', 'จุดถ่ายใต้น้ำ'],
    duration: 'ประมาณ 7 ชั่วโมง',
    basePrice: 5500,
    boats: ['speedboat', 'bigboat'],
    desc: 'สายผจญภัยและสายดำน้ำต้องห้ามพลาด พาดำปะการังหลากสี ฝูงปลานานาชนิด พร้อมไกด์ดำน้ำมืออาชีพดูแลความปลอดภัยตลอดทริป',
  },
  {
    id: 'emerald-cave',
    name: 'โปรแกรมเกาะมุก / ถ้ำมรกต',
    image: IMAGES.programEmerald,
    route: 'เกาะมุก → ถ้ำมรกต → จุดดำน้ำ',
    stops: ['ว่ายเข้าถ้ำมรกต', 'หาดในถ้ำ', 'ดำน้ำชมปะการัง'],
    duration: 'ประมาณ 5 ชั่วโมง',
    basePrice: 3200,
    boats: ['longtail', 'speedboat'],
    desc: 'มหัศจรรย์ของทะเลตรัง ว่ายผ่านถ้ำมืดยาว 80 เมตรเข้าสู่หาดทรายในถ้ำที่ซ่อนตัว แสงจากปากถ้ำสะท้อนน้ำเป็นสีมรกตงดงาม ห้ามพลาดเด็ดขาด',
    ribbon: 'ไฮไลต์',
  },
  {
    id: 'private-charter',
    name: 'โปรแกรมเหมาลำส่วนตัว',
    image: IMAGES.programPrivate,
    route: 'ออกแบบเส้นทางเองได้ตามใจคุณ',
    stops: ['ปรับเส้นทางได้', 'เลือกจุดแวะเอง', 'เวลายืดหยุ่น'],
    duration: 'เต็มวัน (8–10 ชั่วโมง)',
    basePrice: 8000,
    boats: ['longtail', 'speedboat', 'bigboat'],
    desc: 'เหมาลำส่วนตัวสำหรับครอบครัว เพื่อนสนิท หรืองานพิเศษ คุณกำหนดเส้นทาง เลือกจุดแวะเอง อยากแวะนานเท่าไหร่ก็ได้ ทีมงานพร้อมเสิร์ฟตามใจคุณตลอดทริป',
    ribbon: 'Custom',
  },
];

/* ---------- รีวิวลูกค้า ---------- */
const REVIEWS = [
  {
    name: 'คุณพิม ขจร', initial: 'พ', trip: 'โปรแกรม 4 เกาะ', from: 'กรุงเทพฯ', rating: 5,
    text: 'ทริปดีมากค่ะ พี่กัปตันใจดี รู้จุดถ่ายรูปสวย ๆ เยอะมาก แนะนำมาเที่ยวตรังต้องลองเช่าเรือกับ Talay Trang เลย ราคาตามที่ตกลงไม่มีบวกเพิ่ม ประทับใจสุด ๆ ค่ะ',
  },
  {
    name: 'คุณก็อต ใจดี', initial: 'ก', trip: 'เหมา Speed Boat 12 ท่าน', from: 'นนทบุรี', rating: 5,
    text: 'จองง่ายมากครับ ทักไลน์ตอบไว ไปถึงท่าเรือมีคนคอยรับ เรือสะอาด ปลอดภัย ลูก ๆ สนุกมาก ดำน้ำดูปะการังที่เกาะเชือกสวยจริง ขอจองอีกแน่นอนปีหน้า',
  },
  {
    name: 'คุณเจน + เพื่อน 8 คน', initial: 'จ', trip: 'โปรแกรมเกาะกระดาน', from: 'เชียงใหม่', rating: 5,
    text: 'หาดที่เกาะกระดานสวยมาก น้ำใสเหมือนในโบรชัวร์ พี่เจ้าของแนะนำเมนูอาหารทะเลอร่อยมาก ขอบคุณที่ดูแลกลุ่มเราดีตลอดทั้งวันค่ะ',
  },
  {
    name: 'คุณนัท ฮันนีมูน', initial: 'น', trip: 'เหมาเรือหางยาวส่วนตัว', from: 'ภูเก็ต', rating: 5,
    text: 'เลือกเรือหางยาวเพราะอยากได้บรรยากาศโลคอล ภาพออกมาสวยมากกก ขอบคุณช่างภาพที่ทาง Talay Trang แนะนำให้ด้วยค่ะ เก็บภาพคู่ที่ถ้ำมรกตได้สวยเกินคาด',
  },
  {
    name: 'คุณบอย ทริปบริษัท', initial: 'บ', trip: 'เหมาเรือใหญ่ 45 ท่าน', from: 'สมุทรปราการ', rating: 5,
    text: 'จัดทริปสัมมนาบริษัท 45 คน เรือใหญ่นั่งสบาย มีพื้นที่กินเลี้ยงบนเรือ พี่กัปตันคุยสนุก ทีมงานบริการดีเยี่ยม จะใช้บริการอีกแน่นอนปีหน้าครับ',
  },
  {
    name: 'คุณอาย สายดำน้ำ', initial: 'อ', trip: 'โปรแกรมดำน้ำดูปะการัง', from: 'สงขลา', rating: 5,
    text: 'มาตรังปีนึงเที่ยวเดียว ทุกครั้งเลือก Talay Trang เพราะรู้ใจ รู้ว่าควรไปจุดไหนช่วงไหน ปะการังที่เกาะเชือกยังสวยอยู่ ขอชื่นชมเรื่องความปลอดภัยมาก ๆ ค่ะ',
  },
  {
    name: 'คุณแพร ครอบครัวใหญ่', initial: 'พ', trip: 'เหมา Speed Boat 22 ท่าน', from: 'นครปฐม', rating: 5,
    text: 'พามากันสามรุ่น ตั้งแต่ปู่ย่า พ่อแม่ ถึงหลาน ๆ ทีมงานดูแลผู้สูงอายุดีมาก มีเสื้อชูชีพให้ทุกคน อาหารเที่ยงบนเกาะอร่อยมาก ทริปครอบครัวที่ลงตัวสุด ๆ ค่ะ',
  },
  {
    name: 'คุณตี้ ทริปเพื่อน', initial: 'ต', trip: 'โปรแกรม 4 เกาะ', from: 'ระยอง', rating: 5,
    text: 'มาเป็นแก๊ง 6 คน ใช้เรือหางยาว ราคาดีมาก ไกด์สนุก แวะกินหมึกย่างริมหาด ไม่ผิดหวังเลยครับ ใครจะมาทะเลตรังแนะนำเจ้านี้',
  },
  {
    name: 'คุณเมย์ คู่รัก', initial: 'ม', trip: 'โปรแกรมถ้ำมรกต', from: 'ขอนแก่น', rating: 5,
    text: 'ทริปแรกของแฟน เขาประทับใจมาก ถ้ำมรกตอลังการมาก คุ้มที่บินมาตรัง ขอบคุณพี่กัปตันที่ถ่ายรูปคู่ให้ด้วยค่ะ เก็บไว้เป็นความทรงจำดี ๆ',
  },
];

/* ---------- TikTok / วิดีโอ ---------- */
const VIDEOS = [
  { id: 1, thumb: IMAGES.videoThumbs[0], title: 'บรรยากาศทริป 4 เกาะ ทะเลตรัง',   views: '128K', url: SITE.tiktokUrl },
  { id: 2, thumb: IMAGES.videoThumbs[1], title: 'ดำน้ำดูปะการังเกาะเชือก',         views: '210K', url: SITE.tiktokUrl },
  { id: 3, thumb: IMAGES.videoThumbs[2], title: 'รีวิวเหมา Speed Boat 12 ท่าน',   views: '92K',  url: SITE.tiktokUrl },
  { id: 4, thumb: IMAGES.videoThumbs[3], title: 'รีวิวลูกค้าบนเรือหางยาว',         views: '64K',  url: SITE.tiktokUrl },
  { id: 5, thumb: IMAGES.videoThumbs[4], title: 'มุมโดรนทะเลตรังหายากมาก',         views: '305K', url: SITE.tiktokUrl },
  { id: 6, thumb: IMAGES.videoThumbs[5], title: 'น้ำใสสุดที่เกาะกระดาน',           views: '188K', url: SITE.tiktokUrl },
  { id: 7, thumb: IMAGES.videoThumbs[6], title: 'อุปกรณ์ดำน้ำ + ทริปครอบครัว',     views: '74K',  url: SITE.tiktokUrl },
  { id: 8, thumb: IMAGES.videoThumbs[7], title: 'ทีมงาน Talay Trang พาเที่ยว',     views: '54K',  url: SITE.tiktokUrl },
];

/* ---------- จุดเด่นบริการ ---------- */
const WHY_US = [
  { icon: 'shield',  title: 'มีออฟฟิศจริง ปลอดภัย',  desc: 'ตัวตนชัดเจน อยู่ในพื้นที่จริง พร้อมรับผิดชอบทุกทริป' },
  { icon: 'star',    title: 'รีวิวลูกค้าจริงทุกเดือน', desc: 'มีคอมเมนต์จริงจากลูกค้าใน Facebook และ TikTok ของเรา' },
  { icon: 'compass', title: 'เจ้าถิ่นทะเลตรังตัวจริง', desc: 'กัปตันและทีมงานเกิดและโตที่ตรัง รู้ทุกซอกเกาะ ทุกฤดูลม' },
  { icon: 'chat',    title: 'ดูแลตั้งแต่ก่อนเที่ยว',   desc: 'แนะนำที่พัก อาหาร โปรแกรม ผ่าน LINE ตอบไว 24 ชม.' },
];

/* ---------- แพ็คเกจทัวร์ตรัง — บริการของเรา (Pill style) ---------- */
const SERVICES = [
  { icon: 'bigBoat',   title: 'เช่าเรือทัวร์ขนาดใหญ่', href: 'boats.html#bigboat' },
  { icon: 'longtail',  title: 'เช่าเรือหางยาว',        href: 'boats.html#longtail' },
  { icon: 'speedboat', title: 'เช่าเรือ Speedboat',    href: 'boats.html#speedboat' },
  { icon: 'bed',       title: 'จองห้องพัก',             href: 'https://line.me/R/ti/p/%40talaytrang', external: true },
  { icon: 'ticket',    title: 'รับจองตั๋วเรือเข้าเกาะ',  href: 'https://line.me/R/ti/p/%40talaytrang', external: true },
  { icon: 'car',       title: 'บริการเช่ารถ',           href: 'https://line.me/R/ti/p/%40talaytrang', external: true },
  { icon: 'briefcase', title: 'จัดประชุมสัมมนา',         href: 'https://line.me/R/ti/p/%40talaytrang', external: true },
  { icon: 'scuba',     title: 'รับสอนดำน้ำลึก',          href: 'programs.html#diving' },
  { icon: 'cutlery',   title: 'รับจัดทำอาหาร',          href: 'https://line.me/R/ti/p/%40talaytrang', external: true },
  { icon: 'edit',      title: 'วางแผนด้วยตัวเอง',        href: 'booking.html' },
  { icon: 'camera',    title: 'สถานที่ถ่ายหนัง',         href: 'https://line.me/R/ti/p/%40talaytrang', external: true },
];

/* ---------- HERO Banner Slider (หน้าแรก) ----------
   วิธีเพิ่มรูป Slide: วางไฟล์ลงโฟลเดอร์ assets/cover hero/
   แล้วเพิ่ม path ลงใน array ด้านล่างนี้ ระบบจะเล่นวนอัตโนมัติ
   (เว้นวรรคในชื่อ folder ต้องเขียนเป็น %20 ใน URL) */
const HERO_SLIDES = [
  { src: 'assets/cover%20hero/slide-01.png', alt: 'เช่าเรือเที่ยวทะเลตรัง — Talay Trang' },
  { src: 'assets/cover%20hero/slide-02.png', alt: 'โปรแกรมเที่ยวทะเลตรัง — ทีมงานเจ้าถิ่น' },
  { src: 'assets/cover%20hero/slide-03.png', alt: 'เช่าเรือหางยาว Speedboat เรือใหญ่ ครบครัน' },
  { src: 'assets/cover%20hero/slide-04.png', alt: 'แพ็คเกจทัวร์ตรัง 4 เกาะ ถ้ำมรกต เกาะกระดาน' },
];

/* ---------- ขั้นตอนจอง 4 ขั้น ---------- */
const STEPS = [
  { n: 1, icon: 'anchor',     title: 'เลือกประเภทเรือ',     desc: 'เรือหางยาว / Speed Boat / เรือใหญ่ ตามจำนวนคนและงบประมาณ' },
  { n: 2, icon: 'route',      title: 'เลือกโปรแกรม',         desc: '4 เกาะ ถ้ำมรกต เกาะกระดาน ดำน้ำ หรือเหมาลำส่วนตัว' },
  { n: 3, icon: 'calculator', title: 'คำนวณราคา + Option',  desc: 'อาหาร ไกด์ ช่างภาพ อุปกรณ์ดำน้ำ รถรับส่ง ราคาขึ้นทันที' },
  { n: 4, icon: 'line',       title: 'ส่งจองเข้า LINE',      desc: 'กดปุ่มเดียว เปิด LINE @talaytrang พร้อมข้อมูลครบ' },
];

/* expose globally */
window.TT = { SITE, IMAGES, PX, NAV_ITEMS, BOATS, OPTIONS, PROGRAMS, REVIEWS, VIDEOS, WHY_US, STEPS, SERVICES, HERO_SLIDES };
