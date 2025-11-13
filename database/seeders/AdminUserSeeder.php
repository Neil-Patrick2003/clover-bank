<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        if (User::where('username', 'admin1337')->exists()) {
            $this->command->info('Admin user already exists!');
            return;
        }

        // Create admin user with the exact schema structure
        $admin = new User();
        $admin->role = 'admin';
        $admin->username = 'admin1337';
        $admin->email = 'admin1337@cloverbank.com';
        $admin->password = Hash::make('admin123'); // Please change this after first login
        $admin->status = 'active';
        $admin->save();

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin1337@cloverbank.com');
        $this->command->info('Password: admin123');
        $this->command->warn('IMPORTANT: Change this password after first login!');
    }
}
