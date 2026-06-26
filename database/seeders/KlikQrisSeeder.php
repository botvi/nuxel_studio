<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\CoinPackage;

class KlikQrisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed KlikQRIS credentials
        Setting::set('klikqris_api_key', '3dITo83plK3zctKmoYSK2DCBUo5MhNV9sgdTAGuI');
        Setting::set('klikqris_merchant_id', '178032012018');

        // Seed default coin packages
        $packages = [
            [
                'coin_amount' => 50,
                'price' => 5000,
                'description' => 'Paket Pemula - Sangat cocok untuk mencoba game',
            ],
            [
                'coin_amount' => 100,
                'price' => 10000,
                'description' => 'Paket Hemat - Paling populer untuk pemain kasual',
            ],
            [
                'coin_amount' => 200,
                'price' => 20000,
                'description' => 'Paket Pro - Dapatkan koin melimpah untuk bersenang-senang',
            ],
            [
                'coin_amount' => 500,
                'price' => 50000,
                'description' => 'Paket Sultan - Koin maksimal untuk dominasi total',
            ],
        ];

        foreach ($packages as $pkg) {
            CoinPackage::updateOrCreate(
                ['coin_amount' => $pkg['coin_amount']],
                [
                    'price' => $pkg['price'],
                    'description' => $pkg['description']
                ]
            );
        }
    }
}
