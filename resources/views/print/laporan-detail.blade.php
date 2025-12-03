<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perincian Transaksi</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px; 
            margin: 10px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 { 
            margin: 0; 
            font-size: 18px; 
            font-weight: bold;
        }
        .header p { 
            margin: 5px 0; 
            font-size: 11px; 
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-section td {
            padding: 3px 5px;
            font-size: 10px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        table th, table td { 
            border: 1px solid #000; 
            padding: 4px; 
            font-size: 9px; 
            text-align: left;
        }
        table th { 
            text-align: center; 
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #000;
        }
        .summary table {
            width: 100%;
            border: none;
        }
        .summary td {
            border: none;
            padding: 3px 5px;
        }
        .summary td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .jenis-konsul {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        .jenis-tindakan {
            background-color: #e9d5ff;
            color: #6b21a8;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        .jenis-alkes {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        .jenis-resep {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        .jenis-lainnya {
            background-color: #f3f4f6;
            color: #374151;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PERINCIAN TRANSAKSI</h2>
        <p>RS LNG BADAK</p>
        <p>Kel. Satimpo, Kec. Bontang Selatan, Bontang - 75324, Kalimantan Timur</p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td><strong>Periode:</strong> {{ $filterLabel }}</td>
                <td><strong>Tanggal Cetak:</strong> {{ $tanggalCetak }}</td>
            </tr>
        </table>
    </div>

    @if(count($dataDetailTransaksi) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 8%;">Tanggal</th>
                    <th style="width: 8%;">No. Registrasi</th>
                    <th style="width: 15%;">Nama Pasien</th>
                    <th style="width: 8%;">Jenis</th>
                    <th style="width: 20%;">Item</th>
                    <th style="width: 12%;">Dokter</th>
                    <th style="width: 5%;" class="text-center">Jumlah</th>
                    <th style="width: 8%;" class="text-right">Biaya</th>
                    <th style="width: 6%;" class="text-center">Diskon</th>
                    <th style="width: 7%;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataDetailTransaksi as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['tanggal'] }}</td>
                    <td>{{ $item['no_reg'] }}</td>
                    <td>{{ $item['nama_pasien'] }}</td>
                    <td class="text-center">
                        @if($item['jenis'] === 'Konsul')
                            <span class="jenis-konsul">{{ $item['jenis'] }}</span>
                        @elseif($item['jenis'] === 'Tindakan')
                            <span class="jenis-tindakan">{{ $item['jenis'] }}</span>
                        @elseif($item['jenis'] === 'Alat Kesehatan')
                            <span class="jenis-alkes">{{ $item['jenis'] }}</span>
                        @elseif($item['jenis'] === 'Resep')
                            <span class="jenis-resep">{{ $item['jenis'] }}</span>
                        @else
                            <span class="jenis-lainnya">{{ $item['jenis'] }}</span>
                        @endif
                    </td>
                    <td>{{ $item['item'] }}</td>
                    <td>{{ $item['dokter'] }}</td>
                    <td class="text-center">{{ $item['jumlah'] }}</td>
                    <td class="text-right">Rp {{ number_format($item['biaya'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item['disc'] }}</td>
                    <td class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <table>
                <tr>
                    <td><strong>Total Transaksi:</strong></td>
                    <td class="text-right"><strong>{{ $totalTransaksi }} transaksi</strong></td>
                </tr>
                <tr>
                    <td><strong>Total Pemasukan:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Total Item Terjual:</strong></td>
                    <td class="text-right"><strong>{{ $totalItemTerjual }} item</strong></td>
                </tr>
                <tr>
                    <td><strong>Total Item Detail:</strong></td>
                    <td class="text-right"><strong>{{ count($dataDetailTransaksi) }} item</strong></td>
                </tr>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 20px; border: 1px solid #000; margin-top: 20px;">
            <p><strong>Tidak ada data perincian transaksi pada periode yang dipilih</strong></p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem pada {{ $tanggalCetak }}</p>
    </div>
</body>
</html>

