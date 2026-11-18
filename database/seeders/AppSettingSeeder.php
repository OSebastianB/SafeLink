<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'risk_low_max',
                'setting_value' => '20',
                'description' => 'Ambang maksimum indeks risiko untuk kategori Rendah (inklusif).',
            ],
            [
                'setting_key' => 'risk_medium_max',
                'setting_value' => '50',
                'description' => 'Ambang maksimum indeks risiko untuk kategori Sedang (inklusif).',
            ],
            [
                'setting_key' => 'app_disclaimer',
                'setting_value' => 'Hasil pemeriksaan merupakan estimasi risiko berdasarkan indikator yang tersedia. Hasil ini bukan jaminan mutlak bahwa suatu website aman atau berbahaya.',
                'description' => 'Disclaimer default hasil scan.',
            ],
            [
                'setting_key' => 'min_data_coverage_warning',
                'setting_value' => '50',
                'description' => 'Persentase cakupan data minimum sebelum menampilkan peringatan keandalan.',
            ],
        ];

        foreach ($settings as $setting) {
            AppSetting::query()->updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
