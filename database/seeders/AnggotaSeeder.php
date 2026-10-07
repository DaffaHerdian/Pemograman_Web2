<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Angsuran;

class AnggotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 dummy members
        $anggota1 = Anggota::create([
            'nama' => 'Budi Santoso',
            'nik' => '3201012801850001',
            'telepon' => '081234567890',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Merdeka No. 123, Jakarta',
            'tanggal_bergabung' => '2023-01-15',
            'status' => 'Aktif',
        ]);

        $anggota2 = Anggota::create([
            'nama' => 'Siti Nurhaliza',
            'nik' => '3201012802900002',
            'telepon' => '082345678901',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Sudirman No. 45, Jakarta',
            'tanggal_bergabung' => '2023-02-20',
            'status' => 'Aktif',
        ]);

        $anggota3 = Anggota::create([
            'nama' => 'Ahmad Hidayat',
            'nik' => '3201012803880003',
            'telepon' => '083456789012',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Gatot Subroto No. 78, Bandung',
            'tanggal_bergabung' => '2023-03-10',
            'status' => 'Aktif',
        ]);

        $anggota4 = Anggota::create([
            'nama' => 'Dewi Lestari',
            'nik' => '3201012804920004',
            'telepon' => '084567890123',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Ahmad Yani No. 56, Surabaya',
            'tanggal_bergabung' => '2023-04-05',
            'status' => 'Aktif',
        ]);

        $anggota5 = Anggota::create([
            'nama' => 'Rudi Hartono',
            'nik' => '3201012805870005',
            'telepon' => '085678901234',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Diponegoro No. 90, Semarang',
            'tanggal_bergabung' => '2023-05-12',
            'status' => 'Aktif',
        ]);

        $anggota6 = Anggota::create([
            'nama' => 'Rina Wijaya',
            'nik' => '3201012806910006',
            'telepon' => '086789012345',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Gajah Mada No. 34, Yogyakarta',
            'tanggal_bergabung' => '2023-06-18',
            'status' => 'Aktif',
        ]);

        $anggota7 = Anggota::create([
            'nama' => 'Eko Prasetyo',
            'nik' => '3201012807890007',
            'telepon' => '087890123456',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Thamrin No. 67, Jakarta',
            'tanggal_bergabung' => '2023-07-22',
            'status' => 'Aktif',
        ]);

        $anggota8 = Anggota::create([
            'nama' => 'Maya Sari',
            'nik' => '3201012808930008',
            'telepon' => '088901234567',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Pahlawan No. 12, Medan',
            'tanggal_bergabung' => '2023-08-30',
            'status' => 'Aktif',
        ]);

        $anggota9 = Anggota::create([
            'nama' => 'Agus Salim',
            'nik' => '3201012809860009',
            'telepon' => '089012345678',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Veteran No. 89, Malang',
            'tanggal_bergabung' => '2023-09-14',
            'status' => 'Tidak Aktif',
        ]);

        $anggota10 = Anggota::create([
            'nama' => 'Linda Kusuma',
            'nik' => '3201012810940010',
            'telepon' => '081123456789',
            'jenis_kelamin' => 'Perempuan',
            'alamat' => 'Jl. Pemuda No. 23, Surabaya',
            'tanggal_bergabung' => '2023-10-08',
            'status' => 'Aktif',
        ]);

        // Create Simpanan for some members
        Simpanan::create([
            'id_anggota' => $anggota1->id_anggota,
            'tanggal' => '2023-01-15',
            'jenis_simpanan' => 'Pokok',
            'jumlah' => 1000000,
        ]);

        Simpanan::create([
            'id_anggota' => $anggota1->id_anggota,
            'tanggal' => '2023-02-15',
            'jenis_simpanan' => 'Wajib',
            'jumlah' => 500000,
        ]);

        Simpanan::create([
            'id_anggota' => $anggota2->id_anggota,
            'tanggal' => '2023-02-20',
            'jenis_simpanan' => 'Pokok',
            'jumlah' => 1000000,
        ]);

        Simpanan::create([
            'id_anggota' => $anggota2->id_anggota,
            'tanggal' => '2023-03-20',
            'jenis_simpanan' => 'Wajib',
            'jumlah' => 500000,
        ]);

        Simpanan::create([
            'id_anggota' => $anggota3->id_anggota,
            'tanggal' => '2023-03-10',
            'jenis_simpanan' => 'Pokok',
            'jumlah' => 1000000,
        ]);

        Simpanan::create([
            'id_anggota' => $anggota3->id_anggota,
            'tanggal' => '2023-04-10',
            'jenis_simpanan' => 'Sukarela',
            'jumlah' => 2000000,
        ]);

        // Create Pinjaman for some members
        $pinjaman1 = Pinjaman::create([
            'id_anggota' => $anggota1->id_anggota,
            'tanggal' => '2023-03-01',
            'jumlah_pinjaman' => 5000000,
            'tenor' => 10,
            'angsuran_per_bulan' => 500000,
            'sisa_pinjaman' => 2500000,
            'status' => 'Berjalan',
        ]);

        $pinjaman2 = Pinjaman::create([
            'id_anggota' => $anggota2->id_anggota,
            'tanggal' => '2023-04-01',
            'jumlah_pinjaman' => 3000000,
            'tenor' => 6,
            'angsuran_per_bulan' => 500000,
            'sisa_pinjaman' => 0,
            'status' => 'Lunas',
        ]);

        $pinjaman3 = Pinjaman::create([
            'id_anggota' => $anggota3->id_anggota,
            'tanggal' => '2023-05-01',
            'jumlah_pinjaman' => 8000000,
            'tenor' => 12,
            'angsuran_per_bulan' => 666667,
            'sisa_pinjaman' => 6000000,
            'status' => 'Berjalan',
        ]);

        // Create Angsuran for Pinjaman
        Angsuran::create([
            'id_pinjaman' => $pinjaman1->id_pinjaman,
            'tanggal' => '2023-04-01',
            'angsuran_ke' => 1,
            'jumlah_bayar' => 500000,
            'sisa_pinjaman' => 4500000,
        ]);

        Angsuran::create([
            'id_pinjaman' => $pinjaman1->id_pinjaman,
            'tanggal' => '2023-05-01',
            'angsuran_ke' => 2,
            'jumlah_bayar' => 500000,
            'sisa_pinjaman' => 4000000,
        ]);

        Angsuran::create([
            'id_pinjaman' => $pinjaman1->id_pinjaman,
            'tanggal' => '2023-06-01',
            'angsuran_ke' => 3,
            'jumlah_bayar' => 500000,
            'sisa_pinjaman' => 3500000,
        ]);

        Angsuran::create([
            'id_pinjaman' => $pinjaman1->id_pinjaman,
            'tanggal' => '2023-07-01',
            'angsuran_ke' => 4,
            'jumlah_bayar' => 500000,
            'sisa_pinjaman' => 3000000,
        ]);

        Angsuran::create([
            'id_pinjaman' => $pinjaman1->id_pinjaman,
            'tanggal' => '2023-08-01',
            'angsuran_ke' => 5,
            'jumlah_bayar' => 500000,
            'sisa_pinjaman' => 2500000,
        ]);

        // Angsuran untuk pinjaman lunas
        for ($i = 1; $i <= 6; $i++) {
            Angsuran::create([
                'id_pinjaman' => $pinjaman2->id_pinjaman,
                'tanggal' => date('Y-m-d', strtotime('2023-05-01 +' . ($i - 1) . ' months')),
                'angsuran_ke' => $i,
                'jumlah_bayar' => 500000,
                'sisa_pinjaman' => 3000000 - ($i * 500000),
            ]);
        }

        Angsuran::create([
            'id_pinjaman' => $pinjaman3->id_pinjaman,
            'tanggal' => '2023-06-01',
            'angsuran_ke' => 1,
            'jumlah_bayar' => 1000000,
            'sisa_pinjaman' => 7000000,
        ]);

        Angsuran::create([
            'id_pinjaman' => $pinjaman3->id_pinjaman,
            'tanggal' => '2023-07-01',
            'angsuran_ke' => 2,
            'jumlah_bayar' => 1000000,
            'sisa_pinjaman' => 6000000,
        ]);
    }
}
