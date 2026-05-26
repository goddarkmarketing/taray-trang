# Talay Trang — ระบบหลังบ้าน (CMS)

## URL

| หน้า | URL (XAMPP) |
|------|-------------|
| **Admin Login** | http://localhost/1495/admin/login.php |
| **Dashboard** | http://localhost/1495/admin/ |
| **API ข้อมูล** | http://localhost/1495/api/data.php |

## เข้าสู่ระบบ (ครั้งแรก)

- **Username:** `admin`
- **Password:** `talaytrang2026`

เปลี่ยนรหัสผ่านทันทีที่เมนู **เปลี่ยนรหัสผ่าน**

## แก้ไขได้ทุกส่วน

| เมนู Admin | สิ่งที่แก้ |
|------------|-----------|
| ข้อมูลเว็บ | เบอร์โทร, LINE, Facebook, TikTok, ที่อยู่, แผนที่ |
| เมนูนำทาง | ลิงก์เมนูหลัก |
| แบนเนอร์หน้าแรก | สไลด์ Hero |
| บริการ (Pills) | ปุ่มบริการบน Hero |
| ประเภทเรือ | เรือหางยาว / Speed / เรือใหญ่ + ราคา |
| โปรแกรมทัวร์ | โปรแกรมทั้งหมด |
| ตัวเลือกจอง | อาหาร, ไกด์, รถรับ (หน้า booking) |
| รีวิว | รีวิวลูกค้า |
| วิดีโอ | TikTok cards |
| จุดเด่น | Why us |
| ขั้นตอนจอง | 4 ขั้นตอน |
| บทความ | เพิ่ม/แก้/ลบ + Markdown |
| หน้าเกี่ยวกับเรา | Hero, เรื่องราว, Trust strip, CTA |
| การ์ดหน้าแรก | การ์ด Custom Trip + คะแนน/รีวิวเรือ & โปรแกรม |
| SEO / Meta | title, description, og:tags ทุกหน้า |
| รูปภาพ (Registry) | Hero, การ์ดเรือ, โปรแกรม (IMAGES) |
| อัปโหลดรูป | อัปโหลดเข้า `assets/uploads/` |

## โครงสร้างไฟล์

```
data/site.json          ← ข้อมูลทั้งเว็บ (แก้ผ่าน admin)
data/admin-credentials.json  ← รหัสผ่าน (สร้างอัตโนมัติ)
api/data.php            ← API อ่านข้อมูล (หน้าบ้านใช้)
assets/js/data-fallback.js   ← สำรองเมื่อ API ล้ม
assets/js/data.js       ← โหลดข้อมูลเข้า window.TT
admin/                  ← หลังบ้านทั้งหมด
```

## บทความ — Markdown

```
## หัวข้อย่อย

ย่อหน้าปกติ

> คำคม

- รายการ 1
- รายการ 2

![คำอธิบาย](https://url-รูป.jpg)
```

## หลังบันทึก

- อัปเดต `data/site.json` ทันที
- อัปเดต `assets/js/data-fallback.js` อัตโนมัติ
- รีเฟรชหน้าเว็บ (Ctrl+F5) เพื่อเห็นผล

## Migrate ข้อมูลใหม่จาก data.js เดิม

```bash
node tools/migrate-to-json.js
```

## ความปลอดภัย

- อย่า commit `data/admin-credentials.json`
- เปลี่ยนรหัสผ่าน default ก่อน deploy จริง
- บน production ควรใช้ HTTPS
