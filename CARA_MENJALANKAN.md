# 🚀 CARA MENJALANKAN KOPERASIKU - PANDUAN LENGKAP

## ⚠️ MASALAH SAAT INI
Server error karena konfigurasi database. Mari kita perbaiki!

---

## 📋 LANGKAH-LANGKAH SETUP

### **STEP 1: START LARAGON & MYSQL** ⭐ PENTING!

1. **Buka aplikasi Laragon** di Windows
2. **Klik tombol "Start All"** atau **"Start"**
3. Tunggu sampai MySQL dan Apache menyala (lampu hijau)
4. Pastikan status: **MySQL: Running** ✅

---

### **STEP 2: BUAT DATABASE VIA PHPMYADMIN**

#### Cara 1: Via Browser (RECOMMENDED)
1. Buka browser
2. Ketik: **http://localhost/phpmyadmin**
3. Klik tab **"SQL"** di bagian atas
4. Copy-paste SQL ini:

```sql
CREATE DATABASE IF NOT EXISTS `koperasiku` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;
```

5. Klik tombol **"Go"** atau **"Kirim"**
6. Database `koperasiku` akan muncul di sidebar kiri ✅

#### Cara 2: Via Command Line
Buka terminal/cmd di folder project, lalu jalankan:

```bash
php artisan db:create koperasiku
```

---

### **STEP 3: JALANKAN MIGRATION & SEEDER**

Di terminal/cmd, jalankan:

```bash
php artisan migrate:fresh --seed
```

**Output yang benar:**
```
✅ Dropping all tables
✅ Creating migration table
✅ Running migrations (7 tables)
✅ Seeding database (Admin + 10 Anggota)
```

---

### **STEP 4: RESTART SERVER LARAVEL**

1. **Stop server yang error** (tekan Ctrl+C di terminal yang running server)
2. **Jalankan ulang:**

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

3. Tunggu sampai muncul:
```
INFO  Server running on [http://0.0.0.0:8000]
```

---

### **STEP 5: BUKA APLIKASI** 🎉

1. Buka browser
2. Ketik: **http://localhost:8000**
3. Login dengan:
   ```
   Email    : admin@koperasiku.id
   Password : admin123
   ```

---

## 🔍 VERIFIKASI DATABASE DI PHPMYADMIN

Setelah migration, cek di phpMyAdmin:

1. Buka **http://localhost/phpmyadmin**
2. Klik database **koperasiku** di sidebar kiri
3. Anda akan melihat **7 tabel**:
   - ✅ users (1 record - Admin)
   - ✅ anggota (10 records)
   - ✅ simpanan (beberapa records)
   - ✅ pinjaman (3 records)
   - ✅ angsuran (beberapa records)
   - ✅ cache, sessions (sistem)

---

## 🛠️ TROUBLESHOOTING

### Problem 1: "MySQL Connection Refused"
**Solusi:**
- Buka Laragon
- Klik "Start All"
- Tunggu MySQL menyala (indikator hijau)

### Problem 2: "Database 'koperasiku' doesn't exist"
**Solusi:**
- Buka phpMyAdmin: http://localhost/phpmyadmin
- Buat database manual via SQL tab
- Atau jalankan file: `database/setup_mysql.sql`

### Problem 3: "Access denied for user 'root'@'localhost'"
**Solusi:**
- Di `.env`, pastikan `DB_PASSWORD=` kosong (tanpa password)
- Atau isi dengan password MySQL Laragon Anda

### Problem 4: Server masih error
**Solusi:**
```bash
# Hapus cache
php artisan config:clear
php artisan cache:clear

# Restart server
php artisan serve
```

---

## 📝 QUICK COMMAND REFERENCE

```bash
# Start server
php artisan serve

# Reset database (hati-hati, hapus semua data!)
php artisan migrate:fresh --seed

# Cek koneksi database
php artisan tinker
>>> DB::connection()->getPdo();

# Clear cache
php artisan config:clear
php artisan cache:clear

# Lihat routes
php artisan route:list
```

---

## ✅ CHECKLIST SEBELUM AKSES

- [ ] Laragon sudah jalan
- [ ] MySQL service aktif (hijau)
- [ ] Database `koperasiku` sudah dibuat di phpMyAdmin
- [ ] Migration sudah dijalankan (`php artisan migrate:fresh --seed`)
- [ ] Server Laravel running (`php artisan serve`)
- [ ] Buka http://localhost:8000
- [ ] Login dengan admin@koperasiku.id / admin123

---

## 🎯 STRUKTUR DATABASE SETELAH MIGRATION

```
koperasiku/
├── users (1 admin)
├── anggota (10 anggota dummy)
│   ├── ID: 1-10
│   ├── Nama, NIK, Telepon
│   └── Status: Aktif
├── simpanan
│   ├── Pokok, Wajib, Sukarela
│   └── Linked ke anggota
├── pinjaman
│   ├── Jumlah, Tenor, Status
│   └── 2 Berjalan, 1 Lunas
├── angsuran
│   ├── Pembayaran per pinjaman
│   └── Auto-update sisa
└── cache, sessions, jobs (sistem)
```

---

## 📞 BANTUAN LEBIH LANJUT

Jika masih error, cek:
1. File log: `storage/logs/laravel.log`
2. Pastikan port 3306 (MySQL) tidak dipakai aplikasi lain
3. Restart Laragon completely

---

**Setelah semua langkah di atas, aplikasi akan berjalan normal!** 🚀

**URL Akses:**
- Aplikasi: http://localhost:8000
- phpMyAdmin: http://localhost/phpmyadmin
- Database: koperasiku

**Login:**
- Email: admin@koperasiku.id
- Password: admin123
