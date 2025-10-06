<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@rs.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create other role users for testing
        User::create([
            'name' => 'Dr. John Doe',
            'email' => 'dokter@rs.com',
            'password' => Hash::make('password'),
            'role' => 'dokter',
        ]);

        User::create([
            'name' => 'Kasir Staff',
            'email' => 'kasir@rs.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);

        User::create([
            'name' => 'Pendaftaran Staff',
            'email' => 'pendaftaran@rs.com',
            'password' => Hash::make('password'),
            'role' => 'pendaftaran',
        ]);
    }
}
