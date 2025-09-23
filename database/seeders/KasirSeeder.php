<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasien;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Carbon\Carbon;

class KasirSeeder extends Seeder
{
    public function run(): void
    {
        // contoh pasien
        $pasien = Pasien::create([
            'nama_pasien' => 'Budi Santoso',
        ]);

        // contoh transaksi untuk pasien
        $transaksi = Transaksi::create([
            'pasien_id'     => $pasien->id,
            'tanggal'       => Carbon::now(),
            'jns_perawatan' => 'Rawat Jalan',
            'dokter'        => 'Dr. Andi',
        ]);

        // detail transaksi (tindakan)
        DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'tindakan'     => 'Pemeriksaan Umum',
            'jumlah'       => 1,
            'deskripsi'    => 'Cek kondisi kesehatan umum',
            'resep'        => null,
        ]);

        // detail transaksi (obat/resep)
        DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'tindakan'     => 'Obat Flu',
            'jumlah'       => 2,
            'deskripsi'    => 'Parasetamol 500mg',
            'resep'        => '2x1 hari',
        ]);
    }
}
