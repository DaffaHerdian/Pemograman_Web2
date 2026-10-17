# KoperasiKu

Aplikasi lokal administrasi koperasi simpan pinjam berbasis Laravel 13: anggota, simpanan, pengajuan dan pencairan pinjaman, angsuran, jatuh tempo, buku kas, serta laporan cetak/PDF/Excel.

## Menjalankan di lokal

Gunakan PHP 8.4.1 atau lebih baru sesuai composer.lock, Composer, MySQL, serta ekstensi PHP yang diminta Composer (termasuk pdo_mysql, pdo_sqlite, mbstring, intl, zip, dan gd).

~~~powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
~~~

Buat database MySQL kosong bernama koperasiku. Sesuaikan DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, dan DB_PASSWORD di .env, lalu isi LOCAL_ADMIN_PASSWORD dengan password admin pilihan sendiri (minimal 8 karakter).

~~~powershell
php artisan migrate
php artisan db:seed --class=UserSeeder
php artisan serve --host=127.0.0.1 --port=8001 --tries=1
~~~

Buka http://127.0.0.1:8001. Email admin: admin@koperasiku.id. Password mengikuti LOCAL_ADMIN_PASSWORD saat seeder dijalankan. Jangan menjalankan seeder berulang pada database yang sudah memiliki akun tersebut.

Aset hasil build sudah disertakan. Untuk mengubah CSS/JavaScript lewat Vite, gunakan Node.js, lalu jalankan npm ci dan npm run build.

## Alur penggunaan

1. Isi identitas dan aturan koperasi pada pengaturan.
2. Verifikasi saldo pembukaan yang benar di Buku Kas.
3. Daftarkan anggota, lalu catat setoran di Simpanan.
4. Buat pengajuan di Pinjaman, setujui, lalu cairkan jika kas mencukupi.
5. Catat pembayaran di Angsuran. Pembayaran sebagian tetap diperbolehkan.
6. Periksa jatuh tempo, buku kas, serta laporan simpanan, pinjaman, dan angsuran.

Lihat FLOW_GUIDE.md untuk panduan lebih lanjut dan BUSINESS_RULES_REVIEW.md untuk aturan demo. Untuk data fiktif pada SQLite terpisah, tersedia php artisan koperasi:demo; perintah tersebut tidak mengisi database utama. Akun latihan menggunakan password publik demo-training-only.

## Pengujian

~~~powershell
php vendor/phpunit/phpunit/phpunit
~~~

Pengujian memakai SQLite :memory:, bukan database MySQL utama.

Repo ini berisi aplikasi untuk pemakaian lokal. .env, database/data transaksi lokal, vendor, node_modules, arsip ZIP, dan paket InfinityFree tidak disertakan. Migrasi dan seeder disertakan untuk instalasi baru; data presentasi lokal tidak disalin ke GitHub.
