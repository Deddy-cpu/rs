<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Dokter;
use Illuminate\Support\Facades\Hash;

class DokterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test dokter users
        $dokterUsers = [
            [
                'name' => 'Dr. Sarah Wilson',
                'email' => 'dr.sarah.wilson@rs.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ],
            [
                'name' => 'Dr. Michael Brown',
                'email' => 'dr.michael.brown@rs.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ],
            [
                'name' => 'Dr. Lisa Garcia',
                'email' => 'dr.lisa.garcia@rs.com',
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ],
        ];

        foreach ($dokterUsers as $userData) {
            // Create user
            $user = User::create($userData);
            
            // Create corresponding dokter record
            Dokter::create([
                'user_id' => $user->id,
                'nama_dokter' => $user->name,
                'aktif' => 'Ya',
                'role_id' => null,
            ]);
            
            echo "Created dokter: {$user->name} with ID: {$user->id}\n";
        }
    }
}
