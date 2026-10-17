# Alur aplikasi KoperasiKu

Aplikasi aktif: http://127.0.0.1:8001. Jalankan aplikasi dari folder utama repo ini.

## Persiapan sekali di awal
1. Pengaturan: periksa identitas koperasi dan kebijakan pinjaman.
2. Buku Kas: verifikasi saldo pembukaan berdasarkan uang yang benar-benar tersedia. Jangan mengarang saldo dari data lama.
3. Anggota: tambah anggota dan pastikan status aktif.

## Dua layanan anggota
Simpanan dan pinjaman adalah layanan terpisah. Anggota tidak harus meminjam dan aplikasi tidak menetapkan setoran sebagai syarat pengajuan.

### Simpanan
Anggota > Simpanan > Catat Setoran > pilih anggota/jenis/tanggal/jumlah > simpan. Saldo anggota dan kas bertambah.
Simpanan > Tarik Sukarela > pilih anggota/tanggal/jumlah > simpan. Saldo sukarela dan kas harus cukup; keduanya berkurang.

### Pinjaman
1. Pinjaman > Buat pengajuan. Pilih anggota, tanggal pengajuan, pokok, dan tenor. Simpan; status Menunggu persetujuan dan belum ada uang keluar.
2. Pinjaman > Tinjau pengajuan. Pilih setujui/tolak, isi alasan, dan konfirmasi. Persetujuan belum mengurangi kas.
3. Pinjaman > Catat pencairan. Isi tanggal dan konfirmasi setelah dana benar-benar diserahkan. Kas harus cukup. Jadwal tagihan dibuat saat pencairan.
4. Pinjaman > Bayar angsuran membuka menu Angsuran dengan pinjaman terpilih. Bisa juga langsung Angsuran > Catat Pembayaran. Masukkan dana yang benar-benar diterima. Kas bertambah dan tagihan berkurang.
5. Ulangi pembayaran sampai Lunas, atau Pinjaman > Tinjau pelunasan untuk pelunasan sekaligus.
Simulasi tersedia dari halaman Pinjaman dan formulir pengajuan; simulasi tidak membuat transaksi.

## Pemeriksaan setelah transaksi
- Jatuh Tempo: jadwal dan tunggakan pinjaman yang sudah cair.
- Buku Kas: mutasi uang, kuitansi, dan koreksi/pembatalan beralasan.
- Laporan: rekap dan ekspor.
- Aktivitas Admin: jejak perubahan.
- Backup Database: penyimpanan cadangan oleh admin.

## Yang dirapikan atau dihapus
| Sebelumnya | Sekarang |
| --- | --- |
| Transaksi memuat tab Simpanan, Pinjaman, dan Angsuran | Simpanan dan Angsuran memiliki menu/halaman masing-masing |
| Daftar pinjaman di Transaksi dan Pengajuan | Satu daftar di menu Pinjaman; daftar duplikat dihapus |
| Form pinjaman di modal Transaksi | Satu formulir Buat pengajuan dari Pinjaman |
| Form Anggota berdiri sendiri di sidebar | Tombol Tambah Anggota pada halaman Anggota |
| Simulasi berdiri sendiri di sidebar | Tombol pada Pinjaman |
| Sandbox Pembayaran | Menu, halaman, dan route GET dihapus; belum ada pembayaran online operasional |

Endpoint simulasi developer lokal dan webhook yang menolak posting tetap terisolasi dari alur operasional. Tidak ada integrasi pembayaran online aktif.
Tautan lama /pinjaman dan /simpanan?tab=pinjaman diarahkan ke daftar Pinjaman; tab angsuran lama diarahkan ke /angsuran. URL /pengajuan tetap melayani halaman Pinjaman agar tautan keputusan/pencairan existing kompatibel.
Tidak ada penghapusan anggota, pinjaman, simpanan, angsuran, ledger, atau riwayat keuangan.

## Validasi
43 tes / 516 assertions lulus. Kompilasi Blade berhasil. Pemeriksaan browser tujuh tampilan desktop/ponsel di 8001 tanpa overflow halaman. Akun dan sesi QA sementara dibersihkan.
