<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rincian Biaya Layanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h3 { margin: 0; font-size: 14px; }
        .header p { margin: 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #000; padding: 4px; font-size: 11px; }
        table th { text-align: center; }
        .section-title { font-weight: bold; padding: 5px 0; }
        .subtotal { text-align: right; font-weight: bold; padding: 5px; }
        .no-border td { border: none !important; }
        .sign-box { width: 50%; text-align: center; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">

        <div style="position: absolute; top: -10px; left: 10px;">
            <img src="{{ public_path('images/logo_smal.png') }}" alt="Logo" style="width: 90px; height: 90px;">
        </div>
        <div style="margin-left: 130px; text-align: left;">
            <h3 style="margin-bottom: 2px;">RS LNG BADAK</h3>
            <p style="margin-top: 0;">Kel. Satimpo, Kec. Bontang Selatan, Bontang - 75324, Kalimantan Timur</p>
        </div>
        <h4>RINCIAN BIAYA LAYANAN</h4>
    </div>

    <table class="no-border">
        <tr>
            <td>No Registrasi : {{ $pasien->id }}</td>
            <td>No Invoice : INV-{{ $pasien->id }}</td>
        </tr>
        <tr>
            <td>Tanggal : {{ \Carbon\Carbon::parse($pasien->tanggal)->format('d-m-Y') }}</td>
            <td>Tanggal Invoice : {{ now()->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td>Nama Pasien : {{ $pasien->nama_pasien }}</td>
            <td>Perawatan : {{ $pasien->perawatan }}</td>
        </tr>
        <tr>
            <td>MRN : {{ $pasien->mrn ?? '-' }}</td>
            <td>Penjamin : {{ $pasien->Penjamin }}</td>
        </tr>
        <tr>
            <td>Alamat : {{ $pasien->alamat }}</td>
            <td>No. SJP : {{ $pasien->sjp ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td>ICD : {{ $pasien->icd ?? '-' }}</td>
        </tr>
    </table>

    {{-- Tabel Konsul --}}
    <div class="section-title">Konsul</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Jml</th>
                <th>Biaya</th>
                <th>Disc</th>
                <th>Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pasien->konsuls as $row)
            <tr>
                <td>{{ isset($row->tanggal) ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-' }}</td>
                <td>{{ $row->dskp_kons }}</td>
                <td>{{ $row->jmlh_kons }}</td>
                <td>{{ number_format($row->bya_kons,0,',','.') }}</td>
                <td>{{ $row->disc_kons }}</td>
                <td>{{ number_format($row->st_kons,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="subtotal">Subtotal Konsul: Rp {{ number_format($pasien->konsuls->sum('st_kons'),0,',','.') }}</div>

    {{-- Tabel Tindakan --}}
    <div class="section-title">Tindakan</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Jml</th>
                <th>Biaya</th>
                <th>Disc</th>
                <th>Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pasien->tindaks as $row)
            <tr>
                <td>{{ isset($row->tanggal) ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-' }}</td>
                <td>{{ $row->dskp_tindak }}</td>
                <td>{{ $row->jmlh_tindak }}</td>
                <td>{{ number_format($row->bya_tindak,0,',','.') }}</td>
                <td>{{ $row->disc_tindak }}</td>
                <td>{{ number_format($row->st_tindak,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="subtotal">Subtotal Tindakan: Rp {{ number_format($pasien->tindaks->sum('st_tindak'),0,',','.') }}</div>

    {{-- Tabel Alkes --}}
    <div class="section-title">Alkes</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Jml</th>
                <th>Biaya</th>
                <th>Disc</th>
                <th>Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pasien->alkes as $row)
            <tr>
                <td>{{ isset($row->tanggal) ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-' }}</td>
                <td>{{ $row->dskp_alkes }}</td>
                <td>{{ $row->jmlh_alkes }}</td>
                <td>{{ number_format($row->bya_alkes,0,',','.') }}</td>
                <td>{{ $row->disc_alkes }}</td>
                <td>{{ number_format($row->st_alkes,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="subtotal">Subtotal Alkes: Rp {{ number_format($pasien->alkes->sum('st_alkes'),0,',','.') }}</div>

    {{-- Tabel Lain-lain --}}
    <div class="section-title">Lain-lain</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Jml</th>
                <th>Biaya</th>
                <th>Disc</th>
                <th>Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pasien->lainnyas as $row)
            <tr>
                <td>{{ isset($row->tanggal) ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-' }}</td>
                <td>{{ $row->dskp_lainnya }}</td>
                <td>{{ $row->jmlh_lainnaya }}</td>
                <td>{{ number_format($row->bya_lainnya,0,',','.') }}</td>
                <td>{{ $row->disc_lainnya }}</td>
                <td>{{ number_format($row->st_lainnya,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="subtotal">Subtotal Lain-lain: Rp {{ number_format($pasien->lainnyas->sum('st_lainnya'),0,',','.') }}</div>

    {{-- Total --}}
    @php
        $total = $pasien->konsuls->sum('st_kons')
               + $pasien->tindaks->sum('st_tindak')
               + $pasien->alkes->sum('st_alkes')
               + $pasien->lainnyas->sum('st_lainnya');
        $discount = 0; // bisa ditarik dari field pasien / tabel lain
        $dp = 0;
        $grand_total = $total - $discount - $dp;
    @endphp

    <table class="no-border">
        <tr>
            <td>Terbilang: #{{ ucwords(terbilang($grand_total)) }} Rupiah#
            </td>
            <td>
                <table class="no-border">
                    <tr><td>Total (Rp)</td><td>: {{ number_format($total,0,',','.') }}</td></tr>
                    <tr><td>Discount (Rp)</td><td>: {{ number_format($discount,0,',','.') }}</td></tr>
                    <tr><td>Dp (Rp)</td><td>: {{ number_format($dp,0,',','.') }}</td></tr>
                    <tr><td><b>Biaya yang Harus Dibayar</b></td><td>: <b>{{ number_format($grand_total,0,',','.') }}</b></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <br><br>
    <table class="no-border">
        <tr>
            <td class="sign-box">Pasien/ Keluarga Pasien<br><br><br>____________________</td>
            <td class="sign-box">Mengetahui<br>Petugas Kasir<br><br><br>____________________</td>
        </tr>
    </table>
</body>
</html>
