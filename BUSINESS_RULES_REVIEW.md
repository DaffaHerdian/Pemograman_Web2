# Aturan bisnis yang disetujui

Persetujuan pengguna diterima 16 Oktober 2026 di chat. Target database dipilih **koperasiku yang sudah ada, setelah backup**, bukan database demo baru. Tidak membuat/mengisi database demo MySQL terpisah. Tes otomatis menggunakan SQLite in-memory; satu tes integrasi MySQL memakai fixture fiktif dalam transaksi yang seluruhnya di-rollback.

| Parameter | Aturan transaksi baru |
| --- | --- |
| Bunga | Flat 1% per bulan; versi awal berlabel DEMO |
| Presisi | Perhitungan BigDecimal, rupiah bulat HALF_UP, selisih cicilan terakhir. Cicilan sangat kecil dibatasi sisa komponennya agar tidak negatif. Penyimpanan tetap DECIMAL(15,2) |
| Jatuh tempo | Bulanan dari tanggal pencairan, clamp akhir bulan tanpa bergeser pada hari libur |
| Alokasi | Jadwal tertua terlebih dahulu, bunga lalu pokok pada setiap jadwal |
| Denda / biaya admin | Nol |
| Pelunasan dini | Tetap membayar seluruh bunga kontrak tersisa; quotation diperiksa ulang saat posting |
| Limit / tenor | Maksimal Rp10 juta, 1–36 bulan; satu pengajuan/pinjaman aktif per anggota Aktif |
| Pencairan | Pengajuan harus disetujui, anggota aktif, kas cukup; jadwal dan kas keluar baru dicatat saat pencairan |
| Kas pembukaan | Tidak ditebak dari simpanan atau histori. Admin memasukkan kas terverifikasi, sumber/alasan dan konfirmasi di Buku Kas sebelum posting |
| Tanggal posting | Asia/Jakarta, tidak di masa depan, tidak sebelum pembukaan atau mutasi kas terakhir; menjaga saldo kronologis |
| Simpanan | Jenis Pokok/Wajib/Sukarela existing dipertahankan; penarikan hanya Sukarela, saldo anggota dan kas harus cukup. Saldo pecahan legacy tetap presisi dua desimal |

Pinjaman lama ditandai workflow `legacy`. Nilai pokok/sisa historis dipertahankan, tanpa bunga atau jadwal baru hasil tebakan. Pembayaran baru atas pinjaman legacy mengurangi sisa historis, masuk ledger sesudah pembukaan kas, dan dapat direversal. Koreksi transaksi lama yang belum mempunyai referensi ledger memerlukan rekonsiliasi tersendiri.

Perubahan bunga/limit/tenor melalui Pengaturan memerlukan konfirmasi admin dan membuat versi baru. Pengajuan menyimpan snapshot; versi kontrak yang sudah ada tidak berubah.

Jasa simpanan existing tetap memakai rumus rata-rata saldo harian positif, dua desimal HALF_UP, periode bulanan Asia/Jakarta. Source migrasi mengandung tarif contoh 0.25%, tetapi **posting dinonaktifkan** karena pengguna belum menyetujui tarif dan aktivasinya. Tidak ada bunga simpanan yang otomatis dibukukan.

Pembukaan bisa direversal sebelum pernah dipakai transaksi operasional, lalu diverifikasi ulang. Setelah ada histori operasional, perubahan pembukaan memerlukan rekonsiliasi; sistem tidak menghapus histori agar pembukaan dapat diganti.
