<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TindakanTarif;
use App\Models\TindakanQ;
use App\Models\GrpEselon;
use Carbon\Carbon;

class TindakanTarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tindakanQs = TindakanQ::where('aktif', 'Y')->get();
        $grpEselons = GrpEselon::where('aktif', 'Y')->get();

        if ($tindakanQs->isEmpty()) {
            $this->command->warn('No TindakanQ data found. Please run TindakanQSeeder first.');
            return;
        }

        if ($grpEselons->isEmpty()) {
            $this->command->warn('No GrpEselon data found. Please run GrpEselonSeeder first.');
            return;
        }

        // Base prices for different types of procedures
        $basePrices = [
            'Konsultasi Dokter Umum' => 50000,
            'Konsultasi Dokter Spesialis' => 150000,
            'Pemeriksaan Tekanan Darah' => 15000,
            'Pemeriksaan Gula Darah' => 25000,
            'Pemeriksaan Kolesterol' => 30000,
            'Pemeriksaan Urine Lengkap' => 35000,
            'Pemeriksaan Darah Lengkap' => 50000,
            'Rontgen Thorax' => 100000,
            'USG' => 150000,
            'EKG (Elektrokardiogram)' => 75000,
            'Cabut Gigi' => 75000,
            'Tambal Gigi' => 100000,
            'Scaling Gigi' => 150000,
            'Perawatan Luka' => 50000,
            'Jahit Luka' => 100000,
            'Nebulizer' => 35000,
            'Infus' => 50000,
            'Suntik IM/IV' => 25000,
            'Pemeriksaan Mata' => 75000,
            'Imunisasi' => 80000,
        ];

        // Tariff multipliers based on insurance group
        $tariffMultipliers = [
            'BPJS Kesehatan' => 0.7,
            'Asuransi Swasta' => 1.2,
            'Umum / Mandiri' => 1.0,
            'Pegawai Negeri Sipil' => 0.8,
            'TNI / POLRI' => 0.75,
        ];

        $count = 0;

        foreach ($tindakanQs as $tindakanQ) {
            $basePrice = $basePrices[$tindakanQ->tindakan_q_desc] ?? 50000;

            foreach ($grpEselons as $grpEselon) {
                // Determine multiplier based on group description
                $multiplier = 1.0;
                foreach ($tariffMultipliers as $key => $mult) {
                    if (stripos($grpEselon->grp_eselon_desc, $key) !== false) {
                        $multiplier = $mult;
                        break;
                    }
                }

                $tarif = $basePrice * $multiplier;

                TindakanTarif::create([
                    'tindakan_q_id' => $tindakanQ->id,
                    'eselon_grp_id' => $grpEselon->id,
                    'tarif' => number_format($tarif, 2, '.', ''),
                    'aktif' => 'Y',
                    'update_date' => Carbon::now()->format('Y-m-d'),
                    'update_by' => 'System',
                ]);

                $count++;
            }
        }

        $this->command->info("TindakanTarif data seeded successfully! ({$count} records created)");
    }
}
