# Pent.T Shop POS — Laravel
ระบบ POS ใช้ Laravel 12, PHP 8.2+ และ MySQL/MariaDB จาก XAMPP

## เปลี่ยนฐานข้อมูลจาก SQLite เป็น XAMPP
1. เปิด XAMPP Control Panel แล้วกด Start ที่ MySQL
2. เข้า phpMyAdmin แล้วสร้างฐานข้อมูลชื่อ `cafe_pos` โดยเลือก collation `utf8mb4_unicode_ci`
3. เปิด Terminal ในโฟลเดอร์ `laravel-pos` แล้วรัน `npm run setup` เพื่อสร้างตารางและข้อมูลสินค้าเริ่มต้น
4. หากต้องการเก็บข้อมูลที่เคยบันทึกไว้ใน SQLite ให้รัน `npm run import:sqlite` หนึ่งครั้ง เพื่อคัดลอกสินค้า สต็อก สมาชิก พนักงาน และประวัติการขาย
5. รัน `npm run serve` แล้วเปิด http://127.0.0.1:8000/login

ครั้งถัดไปเปิด XAMPP และ Start MySQL ก่อน จากนั้นรัน `npm run serve`

บัญชีเริ่มต้น: `admin@pentt.local` / `password` (ควรเปลี่ยนรหัสผ่านก่อนใช้งานจริง)