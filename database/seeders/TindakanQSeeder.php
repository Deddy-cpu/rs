<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TindakanQ;
use Carbon\Carbon;

class TindakanQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tindakanData = [
            ['tindakan_q_desc' => 'Konsultasi Dokter Umum', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Konsultasi Dokter Spesialis', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Pemeriksaan Tekanan Darah', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Pemeriksaan Gula Darah', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Pemeriksaan Kolesterol', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Pemeriksaan Urine Lengkap', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Pemeriksaan Darah Lengkap', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Rontgen Thorax', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'USG', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'EKG (Elektrokardiogram)', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Cabut Gigi', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Tambal Gigi', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Scaling Gigi', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Perawatan Luka', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Jahit Luka', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Nebulizer', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Infus', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Suntik IM/IV', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Pemeriksaan Mata', 'aktif' => 'Y'],
            ['tindakan_q_desc' => 'Imunisasi', 'aktif' => 'Y'],
        ];

        foreach ($tindakanData as $data) {
            TindakanQ::create([
                'tindakan_q_desc' => $data['tindakan_q_desc'],
                'aktif' => $data['aktif'],
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ]);
        }

        $this->command->info('TindakanQ data seeded successfully!');
    }
}
