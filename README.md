# KoperasiKu - Sistem Informasi Koperasi Simpan Pinjam

![KoperasiKu](https://img.shields.io/badge/Laravel-v13-red)
![PHP](https://img.shields.io/badge/PHP-8.4-blue)
![License](https://img.shields.io/badge/license-MIT-green)

Sistem informasi berbasis web untuk mengelola administrasi koperasi simpan pinjam. Dibangun dengan Laravel, Tailwind CSS, dan Alpine.js.

## 🎯 Fitur Utama

### 1. **Manajemen Anggota**
- ✅ CRUD data anggota lengkap
- ✅ Pencarian dan filter anggota
- ✅ Detail profil anggota dengan ringkasan keuangan
- ✅ Status anggota (Aktif/Tidak Aktif)

### 2. **Transaksi Simpanan**
- ✅ Pencatatan simpanan (Pokok, Wajib, Sukarela)
- ✅ Riwayat simpanan per anggota
- ✅ Total simpanan real-time

### 3. **Transaksi Pinjaman**
- ✅ Pencatatan pinjaman dengan validasi batas maksimal (Rp 10.000.000)
- ✅ Perhitungan tenor dan angsuran
- ✅ Status pinjaman (Berjalan/Lunas)
- ✅ Detail pinjaman dengan progress pembayaran

### 4. **Transaksi Angsuran**
- ✅ Pencatatan pembayaran angsuran
- ✅ Auto-update sisa pinjaman
- ✅ Auto-update status menjadi "Lunas" ketika sisa = 0
- ✅ Database transaction untuk konsistensi data
- ✅ Validasi pembayaran tidak boleh melebihi sisa

### 5. **Dashboard**
- ✅ Statistik real-time (Total Anggota, Simpanan, Pinjaman, Angsuran)
- ✅ Distribusi status pinjaman
- ✅ Transaksi terbaru
- ✅ Quick actions

### 6. **Laporan**
- ✅ Laporan Data Anggota
- ✅ Laporan Simpanan
- ✅ Laporan Pinjaman
- ✅ Laporan Angsuran
- ✅ Filter berdasarkan tanggal, jenis, dan status
- ✅ Cetak laporan (Print-ready A4)

## 🚀 Teknologi

- **Backend:** Laravel 13.x
- **Frontend:** Blade Templates, Tailwind CSS 3.x, Alpine.js 3.x
- **Database:** MySQL / SQLite
- **Icons:** Material Symbols
- **Fonts:** Inter

## 📋 Persyaratan Sistem

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL 8.0+ atau SQLite
- Git

## 🛠️ Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd koperasiku
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koperasiku
DB_USERNAME=root
DB_PASSWORD=
```

Atau gunakan SQLite:
```env
DB_CONNECTION=sqlite
```

### 5. Jalankan Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan Development Server
```bash
php artisan serve
```

Aplikasi dapat diakses di: `http://localhost:8000`

## 👤 Login Default

Setelah menjalankan seeder, gunakan kredensial berikut:

- **Email:** admin@koperasiku.id
- **Password:** admin123

## 📊 Struktur Database

### Tabel Utama

#### `users`
- Authentication admin

#### `anggota`
- id_anggota (PK)
- nama
- nik (unique)
- telepon
- jenis_kelamin
- alamat
- tanggal_bergabung
- status

#### `simpanan`
- id_simpanan (PK)
- id_anggota (FK → anggota)
- tanggal
- jenis_simpanan (Pokok/Wajib/Sukarela)
- jumlah

#### `pinjaman`
- id_pinjaman (PK)
- id_anggota (FK → anggota)
- tanggal
- jumlah_pinjaman
- tenor
- angsuran_per_bulan
- sisa_pinjaman
- status (Berjalan/Lunas)

#### `angsuran`
- id_angsuran (PK)
- id_pinjaman (FK → pinjaman)
- tanggal
- angsuran_ke
- jumlah_bayar
- sisa_pinjaman

### Relationship
```
Anggota → hasMany → Simpanan
Anggota → hasMany → Pinjaman
Pinjaman → hasMany → Angsuran
```

## 🎨 Design System

### Color Palette
- **Primary:** `#0F766E` (Teal)
- **Primary Light:** `#CCFBF1`
- **Secondary:** `#14B8A6`
- **Success:** `#16A34A`
- **Warning:** `#F59E0B`
- **Danger:** `#DC2626`
- **Info:** `#2563EB`

### Typography
- **Font:** Inter
- **H1:** 32px Bold
- **H2:** 24px Semibold
- **Body:** 14px Regular

## 📱 Fitur UI/UX

- ✅ Responsive design (Mobile, Tablet, Desktop)
- ✅ Modern & Clean interface
- ✅ Interactive modals dengan Alpine.js
- ✅ Success/Error toast notifications
- ✅ Empty states untuk data kosong
- ✅ Loading states
- ✅ Confirmation dialogs
- ✅ Print-ready reports

## 🔒 Keamanan

- ✅ Password hashing dengan bcrypt
- ✅ CSRF Protection
- ✅ SQL Injection prevention (Eloquent ORM)
- ✅ Mass assignment protection
- ✅ XSS prevention (Blade escaping)
- ✅ Authentication middleware
- ✅ Form validation

## 📖 Business Rules

### Pinjaman
- Maksimal pinjaman: **Rp 10.000.000**
- Status otomatis berubah menjadi "Lunas" ketika sisa pinjaman = 0
- Pinjaman yang sudah lunas tidak dapat menerima angsuran baru

### Angsuran
- Jumlah pembayaran harus > 0
- Jumlah pembayaran tidak boleh melebihi sisa pinjaman
- Nomor angsuran otomatis increment
- Sisa pinjaman otomatis ter-update
- Menggunakan database transaction untuk konsistensi

### Anggota
- Anggota dengan transaksi tidak dapat dihapus (soft delete dengan status)
- NIK harus unique
- Status: Aktif / Tidak Aktif

## 🧪 Testing

```bash
# Jalankan tests
php artisan test

# Dengan coverage
php artisan test --coverage
```

## 📦 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` di `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Setup SSL/HTTPS
- [ ] Configure proper database credentials
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Setup proper file permissions
- [ ] Configure backup strategy

## 📝 Dokumentasi Tambahan

Lihat folder `/docs` untuk dokumentasi lengkap:
- `PERANCANGAN.md` - Perancangan sistem dan ERD
- `IMPLEMENTASI.md` - Panduan implementasi lengkap

## 👨‍💻 Development

### Struktur Folder
```
koperasiku/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── AnggotaController.php
│   │   ├── SimpananController.php
│   │   ├── PinjamanController.php
│   │   ├── AngsuranController.php
│   │   └── LaporanController.php
│   └── Models/
│       ├── User.php
│       ├── Anggota.php
│       ├── Simpanan.php
│       ├── Pinjaman.php
│       └── Angsuran.php
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── auth/login.blade.php
│   ├── dashboard/index.blade.php
│   ├── anggota/
│   ├── transaksi/
│   └── laporan/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/web.php
└── docs/
```

## 🤝 Contributing

Kontribusi sangat diterima! Silakan buat Pull Request atau buka Issue untuk bug reports dan feature requests.

## 📄 License

MIT License - lihat file `LICENSE` untuk detail.

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Alpine.js
- Material Symbols Icons
- Google Fonts (Inter)

## 📞 Kontak & Support

Untuk pertanyaan atau dukungan, silakan buka issue di repository ini.

---

**KoperasiKu** - Digitalisasi Administrasi Koperasi Simpan Pinjam 🏦
