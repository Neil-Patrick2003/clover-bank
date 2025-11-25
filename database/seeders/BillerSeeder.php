<?php

namespace Database\Seeders;

use App\Models\Bill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $billers = [
            [
                'biller_code' => 'BATELEC I',
                'biller_name' => 'Batangas 1 Electric Copperative',
                'status' => 'active',
            ],
            [
                'biller_code' => 'NAWASA',
                'biller_name' => 'Nasugbu Water/Sanitation Services',
                'status' => 'active',
            ],
            [
                'biller_code' => 'Cebuana Lhuiller',
                'biller_name' => 'Cebuana Lhuiller',
                'status' => 'active',
            ],
            [
                'biller_code' => 'Bayad Center',
                'biller_name' => 'Bayad Center Online',
                'status' => 'active',
            ],
        ];

        foreach ($billers as $biller) {
            Bill::firstOrCreate(
                ['biller_code' => $biller['biller_code']],
                $biller
            );
        }

        $this->command->info('Billers seeded successfully!');
    }
}