<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasien;
use App\Models\Psn;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing Psn records
        $psns = Psn::take(5)->get();
        
        if ($psns->isEmpty()) {
            $this->command->info('No Psn records found. Please run PsnSeeder first.');
            return;
        }

        $kunjunganTypes = ['umum', 'gigi', 'kia', 'laboratorium', 'apotek'];
        
        foreach ($psns as $index => $psn) {
            $kunjungan = $kunjunganTypes[$index % count($kunjunganTypes)];
            
            Pasien::create([
                'psn_id' => $psn->id,
                'no_reg' => 'REG' . date('Ymd') . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'tgl_reg' => now()->subDays(rand(1, 30)),
                'nm_p' => $psn->nm_p,
                'mrn' => 'MRN' . date('Ymd') . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'almt_B' => $psn->almt_B,
                'no_inv' => 'INV' . date('Ymd') . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'tgl_inv' => now()->subDays(rand(1, 30)),
                'perawatan' => ucfirst($kunjungan),
                'penjamin' => ['BPJS', 'Umum', 'Asuransi'][rand(0, 2)],
                'no_sjp' => 'SJP' . date('Ymd') . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'icd' => 'ICD' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'kunjungan' => $kunjungan,
            ]);
        }
        
        $this->command->info('PasienSeeder completed successfully!');
    }
}
