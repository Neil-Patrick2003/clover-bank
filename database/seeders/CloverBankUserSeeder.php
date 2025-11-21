<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\KycProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CloverBankUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'jdelacruz',
                'email' => 'juan.delacruz@example.com',
                'password' => 'jdelacruz123$',
                'balance' => 50000.00,
                'id_type' => 'passport',
                'id_number' => 'P12345678',
                'id_expiry' => now()->addYears(5)->format('Y-m-d')
            ],
            [
                'username' => 'msantos',
                'email' => 'maria.santos@example.com',
                'password' => 'msantos123$',
                'balance' => 125000.75,
                'id_type' => 'driver_license',
                'id_number' => 'DL98765432',
                'id_expiry' => now()->addYears(5)->format('Y-m-d')
            ],
            [
                'username' => 'rcruz',
                'email' => 'robert.cruz@example.com',
                'password' => 'robert123$',
                'balance' => 75000.50,
                'id_type' => 'sss',
                'id_number' => '01-2345678-9',
                'id_expiry' => now()->addYears(5)->format('Y-m-d')
            ],
            [
                'username' => 'areyes',
                'email' => 'ana.reyes@example.com',
                'password' => 'areyes123$',
                'balance' => 200000.00,
                'id_type' => 'passport',
                'id_number' => 'PP87654321',
                'id_expiry' => now()->addYears(5)->format('Y-m-d')
            ],
            [
                'username' => 'cgonzales',
                'email' => 'carlos.gonzales@example.com',
                'password' => 'cgonzales123$',
                'balance' => 30000.25,
                'id_type' => 'other',
                'id_number' => 'TIN-123456789',
                'id_expiry' => now()->addYears(5)->format('Y-m-d')
            ]
        ];

        foreach ($users as $userData) {
            // Check if user already exists
            if (User::where('username', $userData['username'])->exists()) {
                $this->command->info("User {$userData['username']} already exists!");
                continue;
            }

            // Create user
            $user = new User();
            $user->role = 'customer';
            $user->username = $userData['username'];
            $user->email = $userData['email'];
            $user->password = Hash::make($userData['password']);
            $user->status = 'active';
            $user->save();

            // Create bank account
            $account = new Account();
            $account->user_id = $user->id;
            $account->account_number = 'CB' . now()->format('Ymd') . str_pad($user->id, 6, '0', STR_PAD_LEFT);
            $account->currency = 'PHP';
            $account->balance = $userData['balance'];
            $account->status = 'open';
            $account->save();

            // Create KYC profile
            $kyc = new KycProfile();
            $kyc->user_id = $user->id;
            $kyc->kyc_level = 'standard';
            $kyc->id_type = $userData['id_type'];
            $kyc->id_number = $userData['id_number'];
            $kyc->id_expiry = $userData['id_expiry'];
            $kyc->save();

            // Store user data for table display
            $userAccounts[] = [
                'username' => $userData['username'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'account_number' => $account->account_number,
                'balance' => 'PHP ' . number_format($userData['balance'], 2),
                'kyc_status' => 'standard'
            ];
            
            $this->command->info("User '{$userData['username']}' created successfully!");
        }
        
        // Display all users in a table
        $this->command->info("\n" . str_repeat("=", 120));
        $this->command->info("CLOVER BANK TEST USERS");
        $this->command->info(str_repeat("=", 120));
        
        $headers = ['Username', 'Email', 'Password', 'Account Number', 'Balance', 'KYC Level'];
        $rows = [];
        
        foreach ($userAccounts as $user) {
            $rows[] = [
                $user['username'],
                $user['email'],
                $user['password'],
                $user['account_number'],
                $user['balance'],
                $user['kyc_status']
            ];
        }
        
        $this->command->table($headers, $rows);
    }
}
