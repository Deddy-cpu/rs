<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $existingAdmin = User::where('email', 'admin@rs.com')->first();
        
        if ($existingAdmin) {
            echo "Admin user already exists with email: admin@rs.com\n";
            echo "Updating existing admin...\n";
            
            $existingAdmin->update([
                'name' => 'Administrator',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]);
            
            echo "Admin user updated successfully!\n";
        } else {
            // Create new admin user
            $admin = User::create([
                'name' => 'Administrator',
                'email' => 'admin@rs.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
            
            echo "Admin user created successfully!\n";
            echo "Email: admin@rs.com\n";
            echo "Password: admin123\n";
            echo "User ID: {$admin->id}\n";
        }
        
        echo "\n=== ADMIN LOGIN CREDENTIALS ===\n";
        echo "Email: admin@rs.com\n";
        echo "Password: admin123\n";
        echo "Role: admin\n";
        echo "===============================\n";
    }
}
