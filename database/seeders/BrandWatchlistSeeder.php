<?php

namespace Database\Seeders;

use App\Models\BrandWatchlist;
use Illuminate\Database\Seeder;

class BrandWatchlistSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'brand_name' => 'PayPal',
                'official_domain' => 'paypal.com',
                'keywords' => ['paypal'],
                'notes' => 'Data contoh watchlist (sintetis untuk demonstrasi).',
            ],
            [
                'brand_name' => 'Google',
                'official_domain' => 'google.com',
                'keywords' => ['google', 'gmail'],
                'notes' => 'Data contoh watchlist (sintetis untuk demonstrasi).',
            ],
            [
                'brand_name' => 'Microsoft',
                'official_domain' => 'microsoft.com',
                'keywords' => ['microsoft', 'outlook', 'office365'],
                'notes' => 'Data contoh watchlist (sintetis untuk demonstrasi).',
            ],
            [
                'brand_name' => 'Apple',
                'official_domain' => 'apple.com',
                'keywords' => ['apple', 'icloud'],
                'notes' => 'Data contoh watchlist (sintetis untuk demonstrasi).',
            ],
            [
                'brand_name' => 'Facebook',
                'official_domain' => 'facebook.com',
                'keywords' => ['facebook', 'meta'],
                'notes' => 'Data contoh watchlist (sintetis untuk demonstrasi).',
            ],
        ];

        foreach ($brands as $brand) {
            BrandWatchlist::query()->updateOrCreate(
                [
                    'brand_name' => $brand['brand_name'],
                    'official_domain' => $brand['official_domain'],
                ],
                array_merge($brand, ['is_active' => true])
            );
        }
    }
}
