<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Eselon;
use Carbon\Carbon;

class EselonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grpEselons = \App\Models\GrpEselon::all();

        if ($grpEselons->isEmpty()) {
            $this->command->warn('No GrpEselon data found. Please run GrpEselonSeeder first.');
            return;
        }

        $eselonData = [];

        // Create eselon records for each group
        foreach ($grpEselons as $grpEselon) {
            // Create variations based on the group type
            $baseDesc = $grpEselon->grp_eselon_desc;
            
            $eselonData[] = [
                'eselon_desc' => $baseDesc,
                'grp_eselon_id' => $grpEselon->id,
                'aktif' => 'Y',
                'update_date' => Carbon::now()->format('Y-m-d'),
                'update_by' => 'System',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // Insert all eselon records
        foreach ($eselonData as $data) {
            Eselon::create($data);
        }

        $this->command->info('Eselon data seeded successfully!');
    }
}
