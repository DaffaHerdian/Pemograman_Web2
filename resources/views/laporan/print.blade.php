<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - KoperasiKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body class="bg-white p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8 pb-4 border-b-2 border-gray-800">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">KOPERASIKU</h1>
            <p class="text-sm text-gray-600">Sistem Informasi Koperasi Simpan Pinjam</p>
            <p class="text-xs text-gray-500 mt-2">Jl. Contoh No. 123, Jakarta | Telp: (021) 1234567</p>
        </div>
        
        <!-- Judul Laporan -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 text-center mb-2">
                LAPORAN 
                @if($jenis == 'anggota')
                    DATA ANGGOTA
                @elseif($jenis == 'simpanan')
                    SIMPANAN
                @elseif($jenis == 'pinjaman')
                    PINJAMAN
                @elseif($jenis == 'angsuran')
                    ANGSURAN
                @endif
            </h2>
            <p class="text-sm text-gray-600 text-center">Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
        </div>
        
        <!-- Data Laporan -->
        @if($jenis == 'anggota')
            <table class="w-full text-sm border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-left">ID</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Nama</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">NIK</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Telepon</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Tanggal Bergabung</th>
                        <th class="border border-gray-300 px-3 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->id_anggota }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->nama }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->nik }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->telepon }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->tanggal_bergabung->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center">{{ $item->status }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        @elseif($jenis == 'simpanan')
            <table class="w-full text-sm border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-left">Tanggal</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Anggota</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Jenis</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->anggota->nama }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->jenis_simpanan }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="3" class="border border-gray-300 px-3 py-2 text-right">TOTAL:</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp{{ number_format($data->sum('jumlah'), 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            
        @elseif($jenis == 'pinjaman')
            <table class="w-full text-sm border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-left">Tanggal</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Anggota</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Jumlah</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Sisa</th>
                        <th class="border border-gray-300 px-3 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->anggota->nama }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp{{ number_format($item->jumlah_pinjaman, 0, ',', '.') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp{{ number_format($item->sisa_pinjaman, 0, ',', '.') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center">{{ $item->status }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="2" class="border border-gray-300 px-3 py-2 text-right">TOTAL:</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp{{ number_format($data->sum('jumlah_pinjaman'), 0, ',', '.') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp{{ number_format($data->sum('sisa_pinjaman'), 0, ',', '.') }}</td>
                        <td class="border border-gray-300 px-3 py-2"></td>
                    </tr>
                </tbody>
            </table>
            
        @elseif($jenis == 'angsuran')
            <table class="w-full text-sm border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-left">Tanggal</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Anggota</th>
                        <th class="border border-gray-300 px-3 py-2 text-center">Angsuran Ke</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Jumlah</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Sisa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $item->pinjaman->anggota->nama }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center">{{ $item->angsuran_ke }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp{{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp{{ number_format($item->sisa_pinjaman, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="3" class="border border-gray-300 px-3 py-2 text-right">TOTAL:</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">Rp{{ number_format($data->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                        <td class="border border-gray-300 px-3 py-2"></td>
                    </tr>
                </tbody>
            </table>
        @endif
        
        <!-- Footer -->
        <div class="mt-8 pt-4 border-t border-gray-300">
            <div class="flex justify-between items-start">
                <div class="text-sm text-gray-600">
                    <p class="font-semibold">Catatan:</p>
                    <p>Laporan ini dicetak secara otomatis oleh sistem KoperasiKu</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-16">Jakarta, {{ now()->format('d F Y') }}</p>
                    <p class="text-sm text-gray-900 font-semibold border-t border-gray-900 pt-1">Penanggung Jawab</p>
                </div>
            </div>
        </div>
        
        <!-- Print Button -->
        <div class="no-print mt-6 text-center">
            <button onclick="window.print()" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all">
                Cetak Laporan
            </button>
        </div>
    </div>
</body>
</html>
