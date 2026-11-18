# Arsitektur Sistem SafeLink

## Arsitektur aplikasi

SafeLink dibangun dengan Laravel (MVC) dan memisahkan logika bisnis ke lapisan **Services**:

- **Controllers**: menerima request, validasi form request, mengembalikan response/view.
- **Services**: parsing URL, ekstraksi fitur, scoring kriteria, perhitungan SAW, evaluasi dataset.
- **Models/Eloquent**: akses database.
- **Blade + Bootstrap 5**: antarmuka publik, user, dan admin.

## Alur pemrosesan URL

```text
Input URL
   │
   ▼
UrlParserService (validasi + parse, tanpa HTTP ke target)
   │
   ▼
UrlFeatureExtractionService (fitur struktur URL)
   │
   ▼
CriteriaScoringService (C1–C6)
   │  ├─ LocalThreatIntelligenceProvider (C1)
   │  ├─ DomainAgeProvider (C2, opsional RDAP allowlist)
   │  └─ heuristic lokal (C3–C6)
   ▼
SawCalculationService (normalisasi tetap + preferensi + risk index)
   │
   ▼
Persist Scan + ScanCriterionResult (snapshot bobot)
   │
   ▼
Tampilan hasil + penjelasan
```

## Keamanan pemrosesan

Sistem **tidak** melakukan GET/HEAD/redirect/iframe terhadap URL yang diperiksa. Provider eksternal hanya boleh mengarah ke endpoint yang di-allowlist (misalnya RDAP), bukan host dari input pengguna.

## Arsitektur database (ringkas)

- `users` — akun + role
- `criteria`, `criterion_scales` — kriteria & skala
- `scans`, `scan_criterion_results` — hasil pemeriksaan + snapshot
- `threat_intelligence` — reputasi lokal
- `brand_watchlists` — daftar brand resmi
- `app_settings` — ambang kategori & konfigurasi
- `evaluation_datasets`, `evaluation_dataset_rows` — modul evaluasi
- `weight_change_logs` — jejak perubahan bobot

## Peran pengguna

| Peran | Kemampuan |
|-------|-----------|
| Guest | Scan URL, lihat hasil guest |
| User | Scan, riwayat milik sendiri, dashboard |
| Admin | Manajemen kriteria/bobot/reputasi/brand/evaluasi/pengguna |

## Service utama

| Service | Tanggung jawab |
|---------|----------------|
| `UrlParserService` | Validasi & parsing aman |
| `UrlFeatureExtractionService` | Fitur struktur URL |
| `CriteriaScoringService` | Skor C1–C6 |
| `SawCalculationService` | Perhitungan SAW |
| `RiskClassificationService` | Kategori risiko |
| `UrlScanService` | Orkestrasi end-to-end |
| `DatasetEvaluationService` | Accuracy/Precision/Recall/F1 |
