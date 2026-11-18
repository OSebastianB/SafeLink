<?php

namespace Database\Seeders;

use App\Models\Criterion;
use App\Models\CriterionScale;
use Illuminate\Database\Seeder;

class CriterionSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            [
                'code' => 'C1',
                'name' => 'Reputasi Domain',
                'description' => 'Menilai apakah URL/domain ditemukan pada sumber intelijen keamanan tepercaya.',
                'weight' => 0.30,
                'sort_order' => 1,
                'scales' => [
                    ['condition_name' => 'Ancaman terkonfirmasi', 'condition_description' => 'Ditemukan indikasi phishing/malicious yang terkonfirmasi', 'score' => 0, 'sort_order' => 1],
                    ['condition_name' => 'Tidak ada kecocokan ancaman', 'condition_description' => 'Ditemukan pada hasil pencarian tanpa kecocokan ancaman (bukan bukti aman)', 'score' => 4, 'sort_order' => 2],
                    ['condition_name' => 'Tidak diketahui', 'condition_description' => 'Reputasi tidak diketahui atau sumber tidak tersedia', 'score' => 2.5, 'sort_order' => 3],
                ],
            ],
            [
                'code' => 'C2',
                'name' => 'Usia Domain',
                'description' => 'Menilai usia domain sejak tanggal registrasi pertama.',
                'weight' => 0.15,
                'sort_order' => 2,
                'scales' => [
                    ['condition_name' => '> 5 tahun', 'condition_description' => 'Lebih dari 5 tahun', 'score' => 5, 'sort_order' => 1],
                    ['condition_name' => '2–5 tahun', 'condition_description' => 'Usia 2 sampai 5 tahun', 'score' => 4, 'sort_order' => 2],
                    ['condition_name' => '1–2 tahun', 'condition_description' => 'Usia 1 sampai 2 tahun', 'score' => 3, 'sort_order' => 3],
                    ['condition_name' => '3–12 bulan', 'condition_description' => 'Usia 3 sampai 12 bulan', 'score' => 2, 'sort_order' => 4],
                    ['condition_name' => '< 3 bulan', 'condition_description' => 'Kurang dari 3 bulan', 'score' => 1, 'sort_order' => 5],
                    ['condition_name' => 'Tidak tersedia', 'condition_description' => 'Data usia tidak tersedia', 'score' => 2.5, 'sort_order' => 6],
                ],
            ],
            [
                'code' => 'C3',
                'name' => 'Penggunaan HTTPS',
                'description' => 'Menilai skema protokol pada URL. HTTPS bukan jaminan keamanan.',
                'weight' => 0.10,
                'sort_order' => 3,
                'scales' => [
                    ['condition_name' => 'HTTPS', 'condition_description' => 'Menggunakan HTTPS', 'score' => 4, 'sort_order' => 1],
                    ['condition_name' => 'HTTP', 'condition_description' => 'Menggunakan HTTP', 'score' => 2, 'sort_order' => 2],
                    ['condition_name' => 'Tidak diketahui', 'condition_description' => 'Skema tidak diketahui', 'score' => 2.5, 'sort_order' => 3],
                ],
            ],
            [
                'code' => 'C4',
                'name' => 'Struktur dan Panjang URL',
                'description' => 'Menilai anomali struktur URL secara deterministik.',
                'weight' => 0.15,
                'sort_order' => 4,
                'scales' => [
                    ['condition_name' => 'Normal', 'condition_description' => 'Struktur normal tanpa anomali berarti', 'score' => 5, 'sort_order' => 1],
                    ['condition_name' => '1 anomali ringan', 'condition_description' => 'Terdapat satu anomali ringan', 'score' => 4, 'sort_order' => 2],
                    ['condition_name' => 'Beberapa anomali ringan', 'condition_description' => 'Terdapat beberapa anomali ringan', 'score' => 3, 'sort_order' => 3],
                    ['condition_name' => 'Mencurigakan', 'condition_description' => 'Terdapat pola struktur yang mencurigakan', 'score' => 2, 'sort_order' => 4],
                    ['condition_name' => 'Anomali berat', 'condition_description' => 'Terdapat beberapa anomali berat', 'score' => 1, 'sort_order' => 5],
                ],
            ],
            [
                'code' => 'C5',
                'name' => 'Penggunaan Alamat IP',
                'description' => 'Menilai apakah host URL menggunakan alamat IP secara langsung.',
                'weight' => 0.10,
                'sort_order' => 5,
                'scales' => [
                    ['condition_name' => 'Nama domain valid', 'condition_description' => 'Menggunakan nama domain yang valid', 'score' => 5, 'sort_order' => 1],
                    ['condition_name' => 'Alamat IP langsung', 'condition_description' => 'Menggunakan alamat IP langsung', 'score' => 1, 'sort_order' => 2],
                    ['condition_name' => 'Tidak dapat dianalisis', 'condition_description' => 'Data tidak dapat dianalisis', 'score' => 2.5, 'sort_order' => 3],
                ],
            ],
            [
                'code' => 'C6',
                'name' => 'Indikator Karakteristik Phishing',
                'description' => 'Menilai pola kata kunci dan peniruan brand pada URL.',
                'weight' => 0.20,
                'sort_order' => 6,
                'scales' => [
                    ['condition_name' => 'Tidak ada indikator', 'condition_description' => 'Tidak ditemukan indikator mencurigakan', 'score' => 5, 'sort_order' => 1],
                    ['condition_name' => '1 indikator ringan', 'condition_description' => 'Ditemukan satu indikator ringan', 'score' => 4, 'sort_order' => 2],
                    ['condition_name' => 'Beberapa pola', 'condition_description' => 'Ditemukan beberapa kata atau pola mencurigakan', 'score' => 3, 'sort_order' => 3],
                    ['condition_name' => 'Indikasi brand', 'condition_description' => 'Indikasi peniruan brand atau kombinasi pola kuat', 'score' => 2, 'sort_order' => 4],
                    ['condition_name' => 'Impersonasi kuat', 'condition_description' => 'Indikasi impersonasi yang sangat kuat', 'score' => 1, 'sort_order' => 5],
                ],
            ],
        ];

        foreach ($criteria as $item) {
            $scales = $item['scales'];
            unset($item['scales']);

            $criterion = Criterion::query()->updateOrCreate(
                ['code' => $item['code']],
                array_merge($item, [
                    'attribute' => 'benefit',
                    'max_score' => 5,
                    'is_active' => true,
                    'is_core' => true,
                ])
            );

            foreach ($scales as $scale) {
                CriterionScale::query()->updateOrCreate(
                    [
                        'criterion_id' => $criterion->id,
                        'condition_name' => $scale['condition_name'],
                    ],
                    array_merge($scale, [
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}
