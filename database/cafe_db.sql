-- ฐานข้อมูลร้าน Pent.T Shop (คาเฟ่)
-- import ไฟล์นี้ใน phpMyAdmin

CREATE DATABASE IF NOT EXISTS cafe_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cafe_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(20) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    detail VARCHAR(255) DEFAULT NULL,
    image VARCHAR(100) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

INSERT INTO products (name, category, price, detail, image) VALUES
('ชานมต้นตำหรับ', 'drink', 40.00, 'สูตรดั้งเดิมของร้าน', 'products/p06.jpg'),
('โกโก้ลาเต้', 'drink', 45.00, 'โกโก้ผสมนมสด', 'products/p19.jpg'),
('ชานมวนิลา', 'drink', 40.00, 'ชานมหอมกลิ่นวานิลา', 'products/p07.jpg'),
('Darkโกโก้', 'drink', 50.00, NULL, 'products/p01.jpg'),
('ชานมเย็น', 'drink', 40.00, NULL, 'products/p08.jpg'),
('ชาเขียว', 'drink', 40.00, NULL, 'products/p09.jpg'),
('บลูโซดา', 'drink', 30.00, NULL, 'products/p10.jpg'),
('แดงโซดา', 'drink', 30.00, NULL, 'products/p16.jpg'),
('sheak salad', 'food', 109.00, NULL, 'products/p02.jpg'),
('คริสปี้ชิค สลัดอกไก่', 'food', 109.00, NULL, 'products/p03.jpg'),
('คริสปี้ชิค สลัดไข่ต้ม', 'food', 109.00, NULL, 'products/p04.jpg'),
('คริสปี้ชิค สลัดไส้กรอก', 'food', 109.00, NULL, 'products/p05.jpg'),
('แซนวิสBraekfast', 'food', 55.00, NULL, 'products/p13.jpg'),
('แซนวิสแฮมพริกเผา', 'food', 55.00, NULL, 'products/p14.jpg'),
('แซนวิสไก่เกาหลี', 'food', 55.00, NULL, 'products/p15.jpg'),
('วิชชี่โทส ปูอัด', 'food', 59.00, NULL, 'products/p11.jpg'),
('วิชชี่โทส', 'food', 59.00, NULL, 'products/p12.jpg'),
('แซบลุยสวน', 'food', 89.00, NULL, 'products/p18.jpg'),
('โรลลี่ไก่กรอบ', 'food', 60.00, NULL, 'products/p20.jpg'),
('ไก่คาราเกะ', 'food', 45.00, NULL, 'products/p21.jpg'),
('ไส้กรอก', 'food', 35.00, NULL, 'products/p22.jpg');
