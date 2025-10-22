<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        
        // User seeders
        $this->call(KasirSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(DokterSeeder::class);
        
        // Master data seeders
        $this->call(PolisSeeder::class);
        $this->call(GrpEselonSeeder::class);
        $this->call(EselonSeeder::class);
        $this->call(TindakanQSeeder::class);
        $this->call(TindakanTarifSeeder::class);
    }

 

}
