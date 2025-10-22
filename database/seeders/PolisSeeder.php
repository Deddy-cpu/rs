<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Polis;
use Carbon\Carbon;

class PolisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $polis = [
            [
                'poli_desc' => 'Poli Umum',
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ],
            [
                'poli_desc' => 'Poli Gigi',
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ],
            [
                'poli_desc' => 'KIA (Kesehatan Ibu dan Anak)',
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ],
            [
                'poli_desc' => 'Laboratorium',
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ],
            [
                'poli_desc' => 'Apotek',
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ],
            [
                'poli_desc' => 'Poli Mata',
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ],
            [
                'poli_desc' => 'Poli Jantung',
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ],
            [
                'poli_desc' => 'Poli Kulit dan Kelamin',
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ],
            [
                'poli_desc' => 'Poli THT',
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ],
            [
                'poli_desc' => 'Poli Bedah',
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
            ],
        ];

        foreach ($polis as $poliData) {
            Polis::create($poliData);
        }

        $this->command->info('Polis data seeded successfully!');
    }
}
