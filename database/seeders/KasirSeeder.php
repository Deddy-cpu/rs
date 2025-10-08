<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Psn;
use App\Models\Kunjungan;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Carbon\Carbon;

class KasirSeeder extends Seeder
{
    public function run(): void
    {
        // contoh pasien
        $psn = Psn::create([
            'nm_p' => 'Budi Santoso',
            'nik' => '1234567890123456',
            'no_bpjs' => '0001234567890',
            'agm' => 'Islam',
            'tgl_lahir' => '1990-01-01',
            'kelamin' => 'L',
            'almt_L' => 'Jl. Contoh No. 123',
            'almt_B' => 'Jakarta',
        ]);

        // contoh kunjungan
        $kunjungan = Kunjungan::create([
            'psn_id' => $psn->id,
            'no_reg' => 'REG001',
            'tgl_reg' => Carbon::now(),
            'nm_p' => 'Budi Santoso',
            'mrn' => 'MRN001',
            'almt_B' => 'Jakarta',
            'no_inv' => 'INV001',
            'tgl_inv' => Carbon::now(),
            'perawatan' => 'Rawat Jalan',
            'penjamin' => 'Umum',
            'no_sjp' => 'SJP001',
            'icd' => 'Z00.0',
            'kunjungan' => 'umum',
        ]);

        // contoh transaksi untuk kunjungan
        $transaksi = Transaksi::create([
            'kunjungan_id' => $kunjungan->id,
            'tanggal' => Carbon::now(),
            'total_biaya' => 150000,
            'status' => 'pending',
        ]);

        // detail transaksi (konsultasi)
        DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'resep' => 'Konsultasi',
            'jumlah' => 1,
            'deskripsi' => 'Pemeriksaan Umum',
            'biaya' => 100000,
        ]);

        // detail transaksi (tindakan)
        DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'resep' => 'Tindakan',
            'jumlah' => 1,
            'deskripsi' => 'Tensi Darah',
            'biaya' => 50000,
        ]);
    }
}
