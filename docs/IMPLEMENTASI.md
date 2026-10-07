````md
# MASTER PROMPT IMPLEMENTASI KOPERASIKU

Bertindak sebagai **Senior Full-Stack Laravel Developer, UI Implementation Engineer, Database Engineer, dan QA Engineer**. Tugas Anda adalah membangun aplikasi **KoperasiKu — Sistem Informasi Koperasi Simpan Pinjam Berbasis Web** secara end-to-end sampai aplikasi benar-benar dapat dijalankan, digunakan, diuji, dan memiliki tampilan yang sesuai dengan desain yang telah dibuat.

JANGAN langsung coding. Sebelum melakukan perubahan apa pun, lakukan inspeksi project terlebih dahulu, baca seluruh dokumentasi dan referensi UI, periksa environment, pahami kondisi project yang sudah ada, lalu buat rencana implementasi. Setelah itu kerjakan secara bertahap dan lakukan verifikasi pada setiap tahap.

---

# 1. KONTEKS PROJECT

Nama aplikasi:

**KoperasiKu**

Subtitle:

**Simpan Pinjam**

Jenis aplikasi:

**Sistem Informasi Koperasi Simpan Pinjam Berbasis Web**

Tujuan aplikasi adalah membantu Admin koperasi dalam mengelola:

- Data anggota
- Simpanan
- Pinjaman
- Angsuran
- Laporan

Aplikasi ini merupakan sistem administrasi koperasi, bukan aplikasi pinjaman online.

---

# 2. PENGGUNA SISTEM

Sistem hanya memiliki satu jenis pengguna website:

**ADMIN**

Admin bertugas:

- Login
- Melihat Dashboard
- Mengelola data Anggota
- Mencatat Simpanan
- Mencatat Pinjaman
- Mencatat Angsuran
- Melihat Laporan
- Mencetak Laporan
- Logout

## PENTING

**Anggota koperasi bukan user website.**

Anggota adalah entity/data bisnis yang dikelola oleh Admin.

JANGAN membuat:

- Login Anggota
- Dashboard Anggota
- Portal Anggota
- Online Loan Application
- Online Payment
- Payment Gateway

Alur bisnis:

```text
ANGGOTA DATANG KE KOPERASI
        ↓
ADMIN MENCATAT DATA / TRANSAKSI
        ↓
DATABASE
````

---

# 3. SUMBER KEBENARAN PROJECT

Sebelum coding WAJIB membaca:

```text
docs/PERANCANGAN.md
docs/IMPLEMENTASI.md
docs/PROMPT_IMPLEMENTASI.md
```

Kemudian WAJIB memeriksa dan membaca seluruh folder:

```text
assets/koperasiku_admin_panel/
```

Folder tersebut merupakan:

**UI REFERENCE / SOURCE OF TRUTH**

untuk tampilan aplikasi.

Jika tersedia, baca:

```text
code.html
DESIGN.md
gambar
asset
```

Jangan menghapus atau merusak file referensi tersebut.

HTML dari Stitch hanya digunakan sebagai referensi implementasi dan **bukan source code Laravel final**.

Desain harus diimplementasikan kembali menggunakan:

* Laravel Blade
* Tailwind CSS
* Alpine.js

---

# 4. ATURAN MUTLAK

Ikuti aturan berikut selama seluruh proses development.

## Jangan langsung coding

Sebelum coding:

1. Inspect project.
2. Baca dokumentasi.
3. Baca UI reference.
4. Cek environment.
5. Pahami project existing.
6. Buat implementation plan.
7. Baru implementasi.

## Jangan mengarang requirement

Jangan menambahkan fitur yang tidak diminta.

Jika requirement tidak jelas:

* pilih solusi paling sederhana;
* tetap dalam scope;
* jangan membuat arsitektur kompleks;
* jika keputusan mengubah business rule atau database utama, minta konfirmasi user terlebih dahulu.

## Jangan redesign

Jangan mengganti:

* layout;
* warna;
* typography;
* sidebar;
* navbar;
* spacing;
* component;
* modal;
* toast;
* empty state;
* struktur halaman;

dengan desain buatan sendiri.

Implementasikan desain yang sudah tersedia sedekat mungkin.

## Jangan menghapus pekerjaan existing

Sebelum mengubah file existing:

1. Baca file.
2. Pahami fungsi file.
3. Periksa dependensinya.
4. Baru lakukan perubahan.

Jangan overwrite secara membabi buta.

## Jangan berhenti setelah generate code

Kode belum dianggap selesai hanya karena file berhasil dibuat.

WAJIB:

```text
CODE
↓
RUN
↓
TEST
↓
FIX
↓
RUN AGAIN
↓
VERIFY
```

Jangan menyatakan project selesai tanpa verifikasi.

---

# 5. UI REFERENCE YANG TERSEDIA

Lakukan filesystem inspection terhadap:

```text
assets/koperasiku_admin_panel/
```

Referensi yang sudah tersedia antara lain:

```text
cetak_download_laporan_modal_koperasiku/
dashboard_koperasiku/
data_anggota_empty_state_koperasiku/
data_anggota_koperasiku/
data_anggota_success_toast_koperasiku/
delete_anggota_confirmation_koperasiku/
detail_anggota_modal_koperasiku/
detail_pinjaman_modal_koperasiku/
edit_anggota_modal_koperasiku/
form_anggota_koperasiku/
koperasiku_modern_admin/
laporan_koperasiku/
login_admin_koperasiku/
logo_koperasiku/
print_preview_laporan_a4_koperasiku/
professional_friendly_indonesian_cooper.../
tambah_angsuran_modal_koperasiku/
tambah_pinjaman_modal_koperasiku/
tambah_simpanan_modal_koperasiku/
transaksi_empty_state_koperasiku/
transaksi_koperasiku/
```

Daftar di atas bukan alasan untuk mengabaikan folder lain. Jika filesystem memiliki folder tambahan, periksa juga.

Untuk setiap folder relevan:

1. Baca `code.html` jika tersedia.
2. Baca `DESIGN.md` jika tersedia.
3. Periksa asset/gambar.
4. Pahami layout.
5. Pahami component.
6. Pahami state.
7. Pahami interaksi.
8. Implementasikan ke Laravel.

Jangan mengubah folder referensi kecuali user meminta secara eksplisit.

---

# 6. MAPPING UI

Gunakan mapping berikut sebagai panduan:

```text
login_admin_koperasiku/
→ Login Admin

dashboard_koperasiku/
→ Dashboard

data_anggota_koperasiku/
→ Data Anggota

data_anggota_empty_state_koperasiku/
→ Empty State Anggota

data_anggota_success_toast_koperasiku/
→ Success Toast

detail_anggota_modal_koperasiku/
→ Detail Anggota

edit_anggota_modal_koperasiku/
→ Edit Anggota

delete_anggota_confirmation_koperasiku/
→ Delete Confirmation

form_anggota_koperasiku/
→ Form Anggota

transaksi_koperasiku/
→ Transaksi

transaksi_empty_state_koperasiku/
→ Empty State Transaksi

tambah_simpanan_modal_koperasiku/
→ Tambah Simpanan

tambah_pinjaman_modal_koperasiku/
→ Tambah Pinjaman

tambah_angsuran_modal_koperasiku/
→ Tambah Angsuran

detail_pinjaman_modal_koperasiku/
→ Detail Pinjaman

laporan_koperasiku/
→ Laporan

cetak_download_laporan_modal_koperasiku/
→ Cetak / Download Laporan

print_preview_laporan_a4_koperasiku/
→ Print Preview A4

logo_koperasiku/
→ Logo / Brand
```

---

# 7. DESIGN SYSTEM

Gunakan Design System berikut secara konsisten.

## Brand

```text
KoperasiKu
Simpan Pinjam
```

## Font

```text
Inter
```

## Color Palette

```text
Primary          #0F766E
Primary Light    #CCFBF1
Secondary        #14B8A6
Background       #F8FAFC
Surface          #FFFFFF
Main Text        #0F172A
Secondary Text   #64748B
Border           #E2E8F0
Success          #16A34A
Warning          #F59E0B
Danger           #DC2626
Info             #2563EB
```

## Typography

```text
H1       32px Bold
H2       24px Semibold
H3       20px Semibold
Body     14px Regular
Caption  12px Regular
```

Gunakan warna, typography, spacing, button, input, card, table, modal, badge, dan component sesuai Design System dan referensi UI.

---

# 8. GLOBAL LAYOUT

Setelah login, gunakan:

```text
Sidebar
+
Header / Navbar
+
Main Content
```

Struktur sidebar:

```text
KoperasiKu
Simpan Pinjam

MAIN
Dashboard

DATA
Anggota
Form Anggota

TRANSAKSI
Transaksi

LAPORAN
Laporan
```

Sidebar, header, navigation, profile dropdown, dan logout harus mengikuti desain referensi.

---

# 9. TECH STACK

Gunakan:

```text
Laravel
PHP
MySQL
Blade
Tailwind CSS
Alpine.js
Eloquent ORM
Vite
```

Jangan mengganti stack menjadi React, Vue, Next.js, Express, atau Node.js backend kecuali user secara eksplisit meminta.

---

# 10. INSPEKSI ENVIRONMENT

Project berada di:

```text
C:\laragon\www\koperasiku
```

Sebelum melakukan setup, periksa:

```text
PHP
Composer
Node.js
npm
Git
Laravel
MySQL
```

Jika Laravel sudah ada:

**gunakan project Laravel yang tersedia.**

Jangan membuat project Laravel kedua.

Jika Laravel belum tersedia:

* setup Laravel;
* jangan menghapus dokumentasi;
* jangan menghapus asset referensi;
* jangan menghapus pekerjaan existing.

Gunakan environment yang tersedia.

---

# 11. DATABASE

Gunakan MySQL.

Database:

```text
koperasiku
```

Tabel inti hanya:

```text
users
anggota
simpanan
pinjaman
angsuran
```

Jangan membuat tabel:

```text
dashboard
laporan
```

karena Dashboard dan Laporan adalah hasil query/agregasi.

Jangan membuat tabel `transaksi` generik tanpa requirement baru yang jelas.

---

# 12. TABEL USERS

Digunakan untuk authentication Admin.

Gunakan struktur authentication Laravel yang sesuai.

Password WAJIB menggunakan hashing Laravel:

```php
Hash::make()
```

Verifikasi menggunakan:

```php
Hash::check()
```

JANGAN menggunakan:

```text
MD5
SHA1
SHA256 manual
plaintext password
```

untuk penyimpanan password.

---

# 13. TABEL ANGGOTA

Gunakan field minimal:

```text
id_anggota
nama
nik
telepon
jenis_kelamin
alamat
tanggal_bergabung
status
created_at
updated_at
```

Primary key:

```text
id_anggota
```

Status:

```text
Aktif
Tidak Aktif
```

---

# 14. TABEL SIMPANAN

Field minimal:

```text
id_simpanan
id_anggota
tanggal
jenis_simpanan
jumlah
created_at
updated_at
```

Foreign key:

```text
id_anggota → anggota.id_anggota
```

Jenis simpanan:

```text
Pokok
Wajib
Sukarela
```

---

# 15. TABEL PINJAMAN

Field minimal:

```text
id_pinjaman
id_anggota
tanggal
jumlah_pinjaman
tenor
angsuran_per_bulan
sisa_pinjaman
status
created_at
updated_at
```

Foreign key:

```text
id_anggota → anggota.id_anggota
```

Status:

```text
Berjalan
Lunas
```

---

# 16. TABEL ANGSURAN

Field minimal:

```text
id_angsuran
id_pinjaman
tanggal
angsuran_ke
jumlah_bayar
sisa_pinjaman
created_at
updated_at
```

Foreign key:

```text
id_pinjaman → pinjaman.id_pinjaman
```

---

# 17. ELOQUENT RELATIONSHIP

Implementasikan:

```text
Anggota
 ├── hasMany Simpanan
 └── hasMany Pinjaman

Simpanan
 └── belongsTo Anggota

Pinjaman
 ├── belongsTo Anggota
 └── hasMany Angsuran

Angsuran
 └── belongsTo Pinjaman
```

Gunakan Eloquent relationship.

---

# 18. BUSINESS FLOW

## Anggota

```text
Admin
↓
Tambah Anggota
↓
Data tersimpan
```

## Simpanan

```text
Pilih Anggota
↓
Pilih Jenis Simpanan
↓
Masukkan Jumlah
↓
Simpan
```

## Pinjaman

Pinjaman bukan pengajuan online.

```text
Anggota datang ke koperasi
↓
Admin mencatat pinjaman
↓
Pinjaman tersimpan
↓
Status = Berjalan
```

## Angsuran

```text
Pilih Pinjaman
↓
Masukkan pembayaran
↓
Sistem menghitung sisa
↓
Simpan Angsuran
```

## Pinjaman Lunas

Jika:

```text
sisa_pinjaman <= 0
```

maka:

```text
sisa_pinjaman = 0
status = Lunas
```

Pinjaman tidak boleh menghasilkan sisa negatif.

---

# 19. BATAS PINJAMAN

Untuk kebutuhan project akademik ini gunakan:

```text
Rp10.000.000
```

sebagai **batas simulasi aplikasi**.

Ini bukan klaim sebagai batas maksimal nasional dan bukan otomatis merupakan kebijakan koperasi tertentu.

Jika:

```text
jumlah_pinjaman > Rp10.000.000
```

maka validation harus menolak.

Jika:

```text
jumlah_pinjaman = Rp10.000.000
```

maka diperbolehkan.

Jangan membuat aturan hukum atau business rule tambahan yang tidak ditentukan.

---

# 20. ATURAN ANGSURAN

Ketika Admin membuat angsuran:

1. Pastikan pinjaman ada.
2. Pastikan status pinjaman `Berjalan`.
3. Jumlah pembayaran > 0.
4. Jumlah pembayaran <= sisa pinjaman.
5. Tentukan nomor angsuran berikutnya.
6. Hitung sisa pinjaman.
7. Simpan angsuran.
8. Update pinjaman.
9. Jika sisa = 0, ubah status menjadi `Lunas`.

Gunakan database transaction jika diperlukan untuk menjaga konsistensi data.

Contoh:

```text
Pinjaman = Rp5.000.000
Bayar = Rp500.000
Sisa = Rp4.500.000
```

Pembayaran berikutnya:

```text
Rp4.500.000
-
Rp500.000
=
Rp4.000.000
```

Jika pembayaran terakhir membuat sisa:

```text
Rp0
```

maka:

```text
Status = Lunas
```

Pembayaran yang lebih besar dari sisa harus ditolak.

Pinjaman `Lunas` tidak dapat menerima angsuran baru.

---

# 21. HALAMAN LOGIN

Referensi:

```text
assets/koperasiku_admin_panel/login_admin_koperasiku/
```

Fitur:

```text
Username/email
Password
Login
Validation
Error message
Logout
```

Login berhasil:

```text
/login
↓
/dashboard
```

User yang belum login tidak boleh mengakses halaman internal.

---

# 22. DASHBOARD

Referensi:

```text
assets/koperasiku_admin_panel/dashboard_koperasiku/
```

Dashboard harus mengambil data dari database.

Informasi minimal:

```text
Total Anggota
Total Simpanan
Total Pinjaman
Total Pinjaman Berjalan
Total Pinjaman Lunas
Transaksi Terbaru
Quick Actions
```

Jangan menggunakan angka hard-coded untuk statistik utama setelah database tersedia.

---

# 23. DATA ANGGOTA

Referensi:

```text
assets/koperasiku_admin_panel/data_anggota_koperasiku/
```

Fitur:

```text
List Anggota
Search
Tambah
Detail
Edit
Delete
Empty State
Success Toast
```

Data:

```text
ID Anggota
Nama
NIK
Telepon
Jenis Kelamin
Alamat
Tanggal Bergabung
Status
```

---

# 24. CRUD ANGGOTA

Implementasikan:

```text
CREATE
READ
UPDATE
DELETE
```

Sebelum menghapus anggota, periksa apakah anggota memiliki data transaksi.

Jangan menyebabkan foreign key error atau menghapus data historis secara sembarangan.

Jika anggota memiliki transaksi yang membuat penghapusan tidak aman, gunakan pendekatan yang aman seperti menolak penghapusan dengan pesan yang jelas atau mekanisme yang sesuai.

---

# 25. FORM ANGGOTA

Referensi:

```text
assets/koperasiku_admin_panel/form_anggota_koperasiku/
```

Field:

```text
Nama
NIK
Telepon
Jenis Kelamin
Alamat
Tanggal Bergabung
Status
```

Gunakan Laravel validation.

---

# 26. DETAIL ANGGOTA

Referensi:

```text
assets/koperasiku_admin_panel/detail_anggota_modal_koperasiku/
```

Tampilkan minimal:

```text
ID Anggota
Nama
NIK
Telepon
Jenis Kelamin
Alamat
Tanggal Bergabung
Status
```

Jika desain menampilkan ringkasan transaksi, gunakan data aktual database.

---

# 27. EDIT ANGGOTA

Referensi:

```text
assets/koperasiku_admin_panel/edit_anggota_modal_koperasiku/
```

Implementasikan sesuai desain.

Setelah berhasil:

```text
Update Database
↓
Success Toast
↓
Data Terbaru Tampil
```

---

# 28. DELETE ANGGOTA

Referensi:

```text
assets/koperasiku_admin_panel/delete_anggota_confirmation_koperasiku/
```

Jangan langsung menghapus saat tombol Delete ditekan.

Gunakan confirmation.

---

# 29. EMPTY STATE DAN TOAST

Referensi:

```text
data_anggota_empty_state_koperasiku/
data_anggota_success_toast_koperasiku/
transaksi_empty_state_koperasiku/
```

Jika data kosong, gunakan Empty State sesuai desain.

Setelah operasi berhasil, tampilkan Success Toast sesuai desain.

Jangan menampilkan Success Toast jika operasi gagal.

---

# 30. TRANSAKSI

Referensi:

```text
assets/koperasiku_admin_panel/transaksi_koperasiku/
```

Transaksi terdiri dari:

```text
Simpanan
Pinjaman
Angsuran
```

Gunakan desain yang telah dibuat.

---

# 31. TAMBAH SIMPANAN

Referensi:

```text
assets/koperasiku_admin_panel/tambah_simpanan_modal_koperasiku/
```

Field:

```text
Anggota
Tanggal
Jenis Simpanan
Jumlah
```

Jenis:

```text
Pokok
Wajib
Sukarela
```

Validation:

```text
Anggota wajib
Tanggal valid
Jenis valid
Jumlah > 0
```

Setelah berhasil:

```text
Database updated
↓
Success feedback
↓
Data tampil
```

---

# 32. TAMBAH PINJAMAN

Referensi:

```text
assets/koperasiku_admin_panel/tambah_pinjaman_modal_koperasiku/
```

Field:

```text
Anggota
Tanggal
Jumlah Pinjaman
Tenor
Angsuran per Bulan
```

Sistem harus:

* memvalidasi anggota;
* memvalidasi jumlah;
* menerapkan batas maksimal;
* memvalidasi tenor;
* menentukan angsuran;
* mengisi sisa pinjaman;
* menetapkan status `Berjalan`.

Saat dibuat:

```text
sisa_pinjaman = jumlah_pinjaman
status = Berjalan
```

---

# 33. DETAIL PINJAMAN

Referensi:

```text
assets/koperasiku_admin_panel/detail_pinjaman_modal_koperasiku/
```

Tampilkan:

```text
Nama Anggota
ID Pinjaman
Tanggal
Jumlah Pinjaman
Tenor
Angsuran per Bulan
Total Dibayar
Sisa Pinjaman
Status
Riwayat Angsuran
```

Riwayat harus berasal dari database.

---

# 34. TAMBAH ANGSURAN

Referensi:

```text
assets/koperasiku_admin_panel/tambah_angsuran_modal_koperasiku/
```

Field:

```text
Pinjaman
Tanggal
Jumlah Bayar
```

Sistem menentukan:

```text
Angsuran Ke
Sisa Pinjaman
```

Jangan meminta Admin memasukkan sisa pinjaman secara manual jika dapat dihitung sistem.

---

# 35. LAPORAN

Referensi:

```text
assets/koperasiku_admin_panel/laporan_koperasiku/
```

Laporan dapat menampilkan:

```text
Data Anggota
Simpanan
Pinjaman
Angsuran
```

Gunakan data aktual database.

Jika desain memiliki filter, implementasikan filter tersebut.

Jangan menambahkan filter kompleks yang tidak diperlukan.

---

# 36. CETAK / DOWNLOAD LAPORAN

Referensi:

```text
assets/koperasiku_admin_panel/cetak_download_laporan_modal_koperasiku/
```

Implementasikan action:

```text
Cetak
Download
```

Jika PDF belum menjadi requirement utama, prioritaskan print browser terlebih dahulu.

Jangan memasang dependency PDF kompleks tanpa alasan.

---

# 37. PRINT PREVIEW

Referensi:

```text
assets/koperasiku_admin_panel/print_preview_laporan_a4_koperasiku/
```

Format:

**A4**

Saat print:

* Sidebar tidak ikut tercetak.
* Navbar tidak ikut tercetak.
* Data laporan terbaca.
* Header laporan jelas.
* Layout tidak rusak.
* Ukuran halaman A4.
* Gunakan CSS print yang sesuai.

---

# 38. ALPINE.JS

Gunakan Alpine.js untuk interaksi ringan:

```text
Modal
Dropdown
Sidebar Toggle
Toast
Confirmation
Filter UI
Tab
Form State
```

Jangan membuat JavaScript kompleks jika Alpine.js sudah cukup.

---

# 39. TAILWIND CSS

Gunakan Tailwind CSS.

Pertahankan Design System:

```text
Color
Typography
Spacing
Button
Input
Card
Table
Modal
Badge
Shadow
Border
```

Jangan membuat warna acak.

Jangan terlalu banyak menggunakan inline style.

---

# 40. STRUKTUR LARAVEL

Gunakan struktur Laravel standar seperti:

```text
app/
├── Models/
│   ├── User.php
│   ├── Anggota.php
│   ├── Simpanan.php
│   ├── Pinjaman.php
│   └── Angsuran.php
│
└── Http/
    └── Controllers/
        ├── AuthController.php
        ├── DashboardController.php
        ├── AnggotaController.php
        ├── SimpananController.php
        ├── PinjamanController.php
        ├── AngsuranController.php
        └── LaporanController.php

database/
├── migrations/
├── seeders/
└── factories/

resources/
├── views/
│   ├── layouts/
│   ├── components/
│   ├── auth/
│   ├── dashboard/
│   ├── anggota/
│   ├── transaksi/
│   └── laporan/
│
├── css/
└── js/

routes/
└── web.php

public/
storage/
docs/
assets/
```

Struktur dapat disesuaikan dengan standar Laravel apabila terdapat alasan teknis yang jelas.

---

# 41. ROUTING

Minimal:

```text
/login
/logout

/dashboard

/anggota
/anggota/create
/anggota/{id}
/anggota/{id}/edit

/simpanan

/pinjaman
/pinjaman/{id}

 /angsuran

/laporan
```

Gunakan route naming yang konsisten.

Semua halaman internal wajib dilindungi authentication middleware.

---

# 42. CONTROLLER

Minimal:

```text
AuthController
DashboardController
AnggotaController
SimpananController
PinjamanController
AngsuranController
LaporanController
```

Gunakan resource controller jika sesuai.

Jangan menaruh seluruh logic aplikasi dalam satu controller.

---

# 43. VALIDATION

Gunakan Laravel Validation.

## Anggota

```text
Nama wajib
NIK wajib
Telepon valid
Jenis Kelamin valid
Alamat valid
Tanggal Bergabung valid
Status valid
```

## Simpanan

```text
Anggota wajib
Tanggal wajib
Jenis Simpanan wajib
Jumlah numerik
Jumlah > 0
```

## Pinjaman

```text
Anggota wajib
Tanggal wajib
Jumlah > 0
Jumlah <= Rp10.000.000
Tenor valid
Angsuran valid
```

## Angsuran

```text
Pinjaman wajib
Tanggal wajib
Jumlah > 0
Jumlah <= sisa pinjaman
Pinjaman harus Berjalan
```

---

# 44. SECURITY

Gunakan mekanisme keamanan Laravel:

```text
Authentication
CSRF Protection
Password Hashing
Validation
Mass Assignment Protection
Output Escaping
Protected Routes
```

Jangan menyimpan:

```text
.env
API Keys
Secret Keys
Production Password
Credentials
```

di repository.

---

# 45. SEEDER

Buat data development/testing:

```text
1 Admin
5-10 Anggota Dummy
Beberapa Simpanan
Beberapa Pinjaman
Beberapa Angsuran
```

Gunakan data dummy.

Jangan menggunakan data pribadi nyata.

Seeder harus dapat dijalankan tanpa error.

---

# 46. DASHBOARD DAN LAPORAN

Dashboard dan Laporan bukan tabel database.

Gunakan query terhadap:

```text
anggota
simpanan
pinjaman
angsuran
```

Contoh statistik:

```text
Total Anggota
= COUNT anggota

Total Simpanan
= SUM simpanan.jumlah

Total Pinjaman
= SUM pinjaman.jumlah_pinjaman

Pinjaman Berjalan
= berdasarkan status Berjalan

Pinjaman Lunas
= berdasarkan status Lunas
```

Sesuaikan dengan desain.

Jangan menyimpan hasil agregasi ke tabel `dashboard` atau `laporan`.

---

# 47. SEARCH ANGGOTA

Search minimal berdasarkan:

```text
Nama
NIK
ID Anggota
```

Gunakan query database yang sesuai.

---

# 48. RESPONSIVE

Aplikasi harus dapat digunakan pada:

```text
Desktop
Tablet
Mobile
```

Desktop menjadi prioritas utama karena merupakan admin panel.

Pastikan:

* sidebar responsive;
* tabel tetap usable;
* modal tidak keluar layar;
* form tetap usable;
* navbar tidak rusak;
* tidak ada horizontal overflow yang tidak diperlukan.

---

# 49. ERROR HANDLING

Jika validation gagal:

* tampilkan pesan jelas;
* pertahankan input yang valid;
* jangan menghilangkan data form;
* gunakan style sesuai desain.

Jangan menampilkan error teknis mentah kepada user jika tidak diperlukan.

---

# 50. DATABASE TRANSACTION

Untuk operasi yang mengubah beberapa data sekaligus, terutama pencatatan angsuran, gunakan database transaction jika diperlukan.

Contoh:

```text
Tambah Angsuran
↓
Insert Angsuran
↓
Update Pinjaman
↓
Commit
```

Jika gagal:

```text
Rollback
```

Tujuannya menjaga konsistensi data.

---

# 51. DATA INTEGRITY

Pastikan:

```text
Simpanan selalu memiliki anggota valid.

Pinjaman selalu memiliki anggota valid.

Angsuran selalu memiliki pinjaman valid.

Angsuran tidak boleh melebihi sisa pinjaman.

Sisa pinjaman tidak boleh negatif.

Pinjaman dengan sisa 0 harus Lunas.

Pinjaman Lunas tidak dapat menerima angsuran baru.

Nomor angsuran konsisten.
```

---

# 52. URUTAN IMPLEMENTASI

WAJIB mengikuti urutan:

```text
PHASE 0
INSPECTION
↓
PHASE 1
DOCUMENTATION REVIEW
↓
PHASE 2
UI REFERENCE REVIEW
↓
PHASE 3
ENVIRONMENT CHECK
↓
PHASE 4
LARAVEL FOUNDATION
↓
PHASE 5
DATABASE
↓
PHASE 6
AUTHENTICATION
↓
PHASE 7
GLOBAL LAYOUT
↓
PHASE 8
DASHBOARD
↓
PHASE 9
ANGGOTA
↓
PHASE 10
SIMPANAN
↓
PHASE 11
PINJAMAN
↓
PHASE 12
ANGSURAN
↓
PHASE 13
LAPORAN
↓
PHASE 14
UI STATES
↓
PHASE 15
RESPONSIVE
↓
PHASE 16
AUTOMATED TESTING
↓
PHASE 17
MANUAL VERIFICATION
↓
PHASE 18
FINAL VERIFICATION
```

Jangan melewati phase yang memiliki error blocking.

---

# 53. PHASE 0 — INSPECTION

Periksa:

```text
Project files
Laravel status
PHP version
Composer
Node
npm
Git
MySQL
Environment
```

Jangan melakukan perubahan besar.

Definition of Done:

```text
[ ] Struktur project diketahui
[ ] Status Laravel diketahui
[ ] Environment diketahui
[ ] Dependency diketahui
[ ] Existing files diketahui
```

---

# 54. PHASE 1 — DOCUMENTATION REVIEW

Baca:

```text
docs/PERANCANGAN.md
docs/IMPLEMENTASI.md
docs/PROMPT_IMPLEMENTASI.md
```

Definition of Done:

```text
[ ] Scope dipahami
[ ] UI requirement dipahami
[ ] Database dipahami
[ ] Business flow dipahami
```

---

# 55. PHASE 2 — UI REFERENCE REVIEW

Periksa:

```text
assets/koperasiku_admin_panel/
```

Definition of Done:

```text
[ ] Semua folder UI diperiksa
[ ] code.html diperiksa jika tersedia
[ ] DESIGN.md diperiksa jika tersedia
[ ] Halaman dipetakan
[ ] State dipetakan
[ ] Component dipahami
```

---

# 56. PHASE 3 — ENVIRONMENT

Pastikan:

```text
PHP
Composer
Node
npm
Laravel
MySQL
```

tersedia dan dapat digunakan.

Definition of Done:

```text
[ ] Laravel dapat dijalankan
[ ] Composer berjalan
[ ] npm berjalan
[ ] MySQL dapat diakses
```

---

# 57. PHASE 4 — LARAVEL FOUNDATION

Pastikan:

```text
.env
routes
config
database
resources
app
```

tersedia dan berfungsi.

Definition of Done:

```text
[ ] Laravel booting
[ ] Route dasar dapat diakses
[ ] Tidak ada error konfigurasi utama
```

---

# 58. PHASE 5 — DATABASE

Buat:

```text
users
anggota
simpanan
pinjaman
angsuran
```

Buat:

```text
Migration
Models
Relationships
Factories jika diperlukan
Seeders
```

Definition of Done:

```text
[ ] Migration berhasil
[ ] Foreign key berhasil
[ ] Models berhasil
[ ] Relationships berhasil
[ ] Seeder berhasil
```

---

# 59. PHASE 6 — AUTHENTICATION

Implementasikan:

```text
Login
Logout
Protected Routes
Password Hashing
Validation
```

Definition of Done:

```text
[ ] Admin dapat login
[ ] Password aman
[ ] Admin dapat logout
[ ] Guest tidak dapat mengakses Dashboard
```

---

# 60. PHASE 7 — GLOBAL LAYOUT

Implementasikan:

```text
Sidebar
Header
Navigation
Responsive Layout
Profile Dropdown
Logout Confirmation jika ada
```

Definition of Done:

```text
[ ] Layout sesuai desain
[ ] Sidebar sesuai desain
[ ] Navigation bekerja
[ ] Responsive
```

---

# 61. PHASE 8 — DASHBOARD

Implementasikan:

```text
Statistic Cards
Quick Actions
Recent Transactions
Database Data
```

Definition of Done:

```text
[ ] Dashboard tampil
[ ] Data database tampil
[ ] Statistik benar
[ ] Quick Actions bekerja
```

---

# 62. PHASE 9 — ANGGOTA

Implementasikan:

```text
List
Search
Create
Read
Update
Delete
Detail Modal
Edit Modal
Delete Confirmation
Empty State
Success Toast
```

Definition of Done:

```text
[ ] CRUD berhasil
[ ] Validation berhasil
[ ] Search berhasil
[ ] Modal bekerja
[ ] Empty State bekerja
[ ] Toast bekerja
```

---

# 63. PHASE 10 — SIMPANAN

Implementasikan:

```text
List
Tambah Simpanan
Validation
Database
```

Definition of Done:

```text
[ ] Simpanan dapat dibuat
[ ] Data masuk database
[ ] Jenis simpanan benar
[ ] Jumlah benar
```

---

# 64. PHASE 11 — PINJAMAN

Implementasikan:

```text
Tambah Pinjaman
Validation
Batas maksimal
Detail Pinjaman
Status
Sisa Pinjaman
```

Definition of Done:

```text
[ ] Pinjaman dapat dibuat
[ ] Batas Rp10.000.000 diterapkan
[ ] Sisa pinjaman benar
[ ] Status Berjalan benar
[ ] Detail pinjaman tampil
```

---

# 65. PHASE 12 — ANGSURAN

Implementasikan:

```text
Tambah Angsuran
Nomor Angsuran
Sisa Pinjaman
Update Pinjaman
Status Lunas
Riwayat Angsuran
```

Definition of Done:

```text
[ ] Angsuran dapat dibuat
[ ] Nomor angsuran benar
[ ] Sisa pinjaman benar
[ ] Tidak boleh overpayment
[ ] Lunas otomatis
[ ] Riwayat tampil
```

---

# 66. PHASE 13 — LAPORAN

Implementasikan:

```text
Laporan
Filter
Cetak
Download jika diperlukan
Print Preview
A4
```

Definition of Done:

```text
[ ] Laporan tampil
[ ] Data berasal dari database
[ ] Filter bekerja
[ ] Print Preview bekerja
[ ] Format A4 bekerja
```

---

# 67. PHASE 14 — UI STATES

Pastikan seluruh state berikut diimplementasikan:

```text
Detail Anggota Modal
Edit Anggota Modal
Delete Anggota Confirmation
Tambah Simpanan Modal
Tambah Pinjaman Modal
Tambah Angsuran Modal
Detail Pinjaman Modal
Cetak/Download Laporan Modal
Print Preview A4
Success Toast
Empty State Anggota
Empty State Transaksi
Admin Profile Dropdown
Logout Confirmation
Dashboard Quick Actions
```

Semua state harus benar-benar dapat digunakan, bukan hanya dibuat secara visual.

---

# 68. PHASE 15 — RESPONSIVE

Test:

```text
Desktop
Tablet
Mobile
```

Definition of Done:

```text
[ ] Tidak ada layout rusak
[ ] Sidebar responsive
[ ] Table usable
[ ] Modal usable
[ ] Form usable
[ ] Navigation usable
```

---

# 69. PHASE 16 — AUTOMATED TESTING

Jalankan command yang sesuai dengan kondisi project.

Minimal:

```bash
php artisan migrate
php artisan db:seed
php artisan test
npm run build
```

Jika database memang merupakan database development KoperasiKu dan aman untuk di-reset, dapat menggunakan:

```bash
php artisan migrate:fresh --seed
```

Jangan menjalankan command destructive terhadap database yang bukan database development.

Definition of Done:

```text
[ ] Migration berhasil
[ ] Seeder berhasil
[ ] Automated test berhasil
[ ] Frontend build berhasil
[ ] Tidak ada error utama
```

---

# 70. PHASE 17 — MANUAL VERIFICATION

Lakukan testing melalui browser.

Test minimal:

```text
Login
Logout

Dashboard

Tambah Anggota
Detail Anggota
Edit Anggota
Delete Anggota
Search Anggota

Tambah Simpanan

Tambah Pinjaman
Detail Pinjaman

Tambah Angsuran
Pembayaran sebagian
Pembayaran penuh

Status Lunas

Laporan
Filter
Print Preview
```

---

# 71. TEST CASE WAJIB

## Login

Valid credentials:

```text
Login berhasil
→ Dashboard
```

Password salah:

```text
Login gagal
→ Error message
```

---

## Anggota

Tambah data valid:

```text
Berhasil
→ Database bertambah
```

Data wajib kosong:

```text
Validation error
```

Edit:

```text
Data berubah
```

Delete:

```text
Confirmation
→ Data terhapus jika aman
```

---

## Simpanan

Contoh:

```text
Jumlah = Rp100.000
```

Expected:

```text
Data berhasil tersimpan
```

---

## Pinjaman

Test:

```text
Rp5.000.000
```

Expected:

```text
Sisa = Rp5.000.000
Status = Berjalan
```

Test:

```text
Rp10.000.000
```

Expected:

```text
Diperbolehkan
```

Test:

```text
Rp10.000.001
```

Expected:

```text
Ditolak
```

---

## Angsuran

Pinjaman:

```text
Rp5.000.000
```

Bayar:

```text
Rp500.000
```

Expected:

```text
Sisa = Rp4.500.000
Status = Berjalan
```

Pembayaran terakhir:

```text
Sisa = Rp0
Status = Lunas
```

Overpayment:

```text
Sisa = Rp500.000
Bayar = Rp600.000
```

Expected:

```text
Ditolak
```

---

# 72. ERROR HANDLING

Jika ditemukan error:

1. Baca error lengkap.
2. Tentukan penyebab.
3. Periksa file terkait.
4. Perbaiki penyebab.
5. Jalankan ulang command.
6. Test ulang.
7. Pastikan error benar-benar hilang.

Jangan menyembunyikan error dengan workaround sementara.

Jangan mengatakan:

> "Seharusnya sudah berhasil."

Tanpa melakukan verifikasi.

---

# 73. GIT SAFETY

Jangan melakukan command destructive tanpa alasan.

JANGAN menjalankan:

```bash
git reset --hard
git clean -fd
```

atau command lain yang dapat menghapus pekerjaan existing tanpa memastikan aman.

Jangan menghapus perubahan user.

Jangan commit:

```text
.env
vendor/
node_modules/
credentials
API keys
secret keys
```

Pastikan `.gitignore` sesuai Laravel.

---

# 74. KODE DAN ARSITEKTUR

Kode harus:

* mudah dibaca;
* konsisten;
* mengikuti Laravel convention;
* menggunakan naming yang jelas;
* tidak memiliki dead code;
* tidak memiliki credential hard-coded;
* menggunakan Eloquent jika sesuai;
* menggunakan Laravel Validation;
* menggunakan Blade;
* menggunakan Tailwind;
* menggunakan Alpine.js;
* tidak overengineering.

Jangan membuat arsitektur kompleks hanya agar terlihat canggih.

---

# 75. JIKA REQUIREMENT BERTENTANGAN

Gunakan prioritas:

```text
1. Instruksi eksplisit user
2. PROMPT_IMPLEMENTASI.md
3. IMPLEMENTASI.md
4. PERANCANGAN.md
5. UI Reference Stitch/Figma
6. Laravel conventions
7. General best practice
```

Jika keputusan tersebut mengubah:

* struktur database;
* business flow;
* role;
* fitur utama;
* desain utama;

jangan mengambil keputusan besar secara diam-diam. Minta konfirmasi user.

---

# 76. FINAL ACCEPTANCE CRITERIA

Project hanya dianggap selesai jika seluruh checklist berikut terpenuhi.

## Environment

```text
[ ] Laravel berjalan
[ ] PHP berjalan
[ ] Composer berjalan
[ ] npm berjalan
[ ] MySQL terhubung
```

## Authentication

```text
[ ] Admin dapat login
[ ] Password menggunakan hashing
[ ] Logout bekerja
[ ] Protected route bekerja
```

## Database

```text
[ ] users
[ ] anggota
[ ] simpanan
[ ] pinjaman
[ ] angsuran
[ ] Foreign key
[ ] Eloquent relationship
[ ] Seeder
```

## Anggota

```text
[ ] List
[ ] Search
[ ] Create
[ ] Detail
[ ] Edit
[ ] Delete
[ ] Validation
[ ] Empty State
[ ] Success Toast
```

## Simpanan

```text
[ ] Tambah
[ ] Validation
[ ] Database
[ ] Jenis simpanan
[ ] Jumlah
```

## Pinjaman

```text
[ ] Tambah
[ ] Validation
[ ] Batas Rp10.000.000
[ ] Sisa pinjaman
[ ] Status Berjalan
[ ] Detail
```

## Angsuran

```text
[ ] Tambah
[ ] Nomor angsuran
[ ] Sisa pinjaman
[ ] Validation
[ ] Tidak boleh overpayment
[ ] Status otomatis Lunas
[ ] Riwayat
```

## Dashboard

```text
[ ] Statistik database
[ ] Quick Actions
[ ] Recent data
```

## Laporan

```text
[ ] Laporan
[ ] Filter
[ ] Print
[ ] Print Preview
[ ] A4
```

## UI

```text
[ ] Login sesuai referensi
[ ] Dashboard sesuai referensi
[ ] Anggota sesuai referensi
[ ] Form sesuai referensi
[ ] Transaksi sesuai referensi
[ ] Laporan sesuai referensi
[ ] Modal sesuai referensi
[ ] Toast sesuai referensi
[ ] Empty State sesuai referensi
[ ] Responsive
```

## Build

```text
[ ] npm run build berhasil
[ ] Tidak ada error Laravel utama
[ ] Tidak ada error JavaScript utama
[ ] Aplikasi dapat dibuka melalui browser
```

---

# 77. FINAL REPORT

Setelah implementasi selesai, tampilkan laporan:

```text
PROJECT STATUS
==============

Laravel:
READY / NOT READY

Database:
READY / NOT READY

Authentication:
READY / NOT READY

Dashboard:
READY / NOT READY

Anggota:
READY / NOT READY

Simpanan:
READY / NOT READY

Pinjaman:
READY / NOT READY

Angsuran:
READY / NOT READY

Laporan:
READY / NOT READY

UI:
READY / NOT READY

Responsive:
READY / NOT READY

Testing:
PASSED / FAILED

Build:
PASSED / FAILED
```

Kemudian tampilkan:

```text
Files created:
...

Files modified:
...

Dependencies installed:
...

Tests executed:
...

Known issues:
...
```

Jika masih ada issue penting:

**STATUS = INCOMPLETE**

Jangan menyatakan project selesai.

---

# 78. KONDISI SELESAI

Project hanya boleh dinyatakan:

**COMPLETED**

jika:

1. Laravel berjalan.
2. Database terhubung.
3. Migration berhasil.
4. Seeder berhasil.
5. Admin dapat login.
6. Dashboard bekerja.
7. CRUD Anggota bekerja.
8. Simpanan bekerja.
9. Pinjaman bekerja.
10. Angsuran bekerja.
11. Status Lunas bekerja.
12. Laporan bekerja.
13. Print Preview bekerja.
14. UI mengikuti referensi.
15. Responsive.
16. Build berhasil.
17. Testing berhasil.
18. Tidak ada error utama yang belum diselesaikan.

Jika salah satu poin penting belum terpenuhi:

**STATUS = INCOMPLETE**

---

# 79. INSTRUKSI MULAI

Mulai sekarang dengan:

**PHASE 0 — INSPECTION**

Jangan langsung membuat seluruh kode.

Lakukan secara berurutan:

```text
INSPECT PROJECT
↓
READ DOCUMENTATION
↓
READ ALL UI REFERENCES
↓
CHECK ENVIRONMENT
↓
CREATE IMPLEMENTATION PLAN
↓
SETUP / REPAIR FOUNDATION
↓
DATABASE
↓
AUTHENTICATION
↓
LAYOUT
↓
DASHBOARD
↓
ANGGOTA
↓
SIMPANAN
↓
PINJAMAN
↓
ANGSURAN
↓
LAPORAN
↓
UI STATES
↓
RESPONSIVE
↓
TESTING
↓
FIX ERRORS
↓
FINAL VERIFICATION
```

Jangan redesign UI.

Jangan mengarang fitur.

Jangan menghapus referensi.

Jangan merusak pekerjaan existing.

Jangan melewati error.

Jangan menyatakan selesai tanpa testing.

Bangun **KoperasiKu** sampai benar-benar dapat dijalankan dan diverifikasi.

```
```
