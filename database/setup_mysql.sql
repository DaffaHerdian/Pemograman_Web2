-- KoperasiKu Database Setup
-- Jalankan script ini di phpMyAdmin atau MySQL client

-- Buat database
CREATE DATABASE IF NOT EXISTS `koperasiku` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- Gunakan database
USE `koperasiku`;

-- Database berhasil dibuat
-- Selanjutnya jalankan: php artisan migrate --seed
