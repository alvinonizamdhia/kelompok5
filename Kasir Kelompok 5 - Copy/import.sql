CREATE DATABASE IF NOT EXISTS pemesanan;
USE pemesanan;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    image VARCHAR(255)
);

-- Contoh isi produk, silahkan dibuah
INSERT INTO products (name, price, stock) VALUES
('Nasi Goreng', 15000, 20),
('Mie Ayam', 12000, 25),
('Es Teh', 5000, 50),
('Kopi Hitam', 8000, 30);
