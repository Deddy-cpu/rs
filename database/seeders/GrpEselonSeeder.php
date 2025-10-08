<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrpEselon;
use Carbon\Carbon;

class GrpEselonSeeder extends Seeder
{
    public function run(): void
    {
        $grpEselons = [
            [
                'eselon_desc' => 'Eselon I',
                'update_date' => Carbon::now(),
                'updated_by' => 'Admin',
            ],
            [
                'eselon_desc' => 'Eselon II',
                'update_date' => Carbon::now(),
                'updated_by' => 'Admin',
            ],
            [
                'eselon_desc' => 'Eselon III',
                'update_date' => Carbon::now(),
                'updated_by' => 'Admin',
            ],
            [
                'eselon_desc' => 'Eselon IV',
                'update_date' => Carbon::now(),
                'updated_by' => 'Admin',
            ],
            [
                'eselon_desc' => 'Eselon V',
                'update_date' => Carbon::now(),
                'updated_by' => 'Admin',
            ],
        ];

        foreach ($grpEselons as $grpEselon) {
            GrpEselon::create($grpEselon);
        }
    }
}
