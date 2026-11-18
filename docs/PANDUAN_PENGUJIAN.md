# Panduan Pengujian SafeLink

## 1. Pengujian black box

| No | Skenario | Hasil diharapkan |
|----|----------|------------------|
| 1 | Buka landing page | Halaman tampil, form scan tersedia |
| 2 | Scan URL valid | Redirect ke hasil, skor C1–C6 + SAW tampil |
| 3 | Scan URL kosong/invalid | Validasi error |
| 4 | Login user | Masuk dashboard user |
| 5 | User buka `/admin` | 403 |
| 6 | Admin ubah bobot total ≠ 1 | Ditolak |
| 7 | Admin unggah CSV evaluasi | Dataset tersimpan, metrik dapat dihitung |

## 2. Pengujian perhitungan SAW

Bandingkan output sistem dengan perhitungan manual:

1. Ambil skor tiap kriteria dari halaman hasil.
2. Hitung \(r = skor/5\).
3. Hitung \(V = \sum w \cdot r\).
4. Hitung Risk Index = \((1-V)\times100\).
5. Cocokkan kategori sesuai ambang.

Jalankan unit test:

```bash
php artisan test --filter=SawCalculationServiceTest
```

## 3. Pengujian fitur admin

- CRUD kriteria & skala
- Update bobot + log perubahan
- Import/reputasi domain
- Brand watchlist
- Pengaturan ambang risiko
- Evaluasi dataset

## 4. Pengujian keamanan

- Pastikan tidak ada HTTP request ke URL target saat scan (`Http::assertNothingSent()`).
- Tolak kredensial tertanam pada URL.
- Role middleware melindungi seluruh route admin.
- Output Blade ter-escape (XSS).
- Input berbahaya tidak menyebabkan SQL error.

```bash
php artisan test --filter=SecurityScanTest
```

## 5. Pengujian evaluasi dataset

1. Siapkan CSV dengan kolom `url,label`.
2. Label hanya `phishing` atau `legitimate`.
3. Jalankan mode binary dan multiclass.
4. Catat Accuracy, Precision, Recall, F1.

**Aturan binary eksplisit:** High Risk = phishing; Low/Medium = legitimate.

## 6. Format laporan hasil tes

| ID | Jenis | Skenario | Status | Catatan |
|----|-------|----------|--------|---------|
| T01 | Unit | Normalisasi SAW | Pass/Fail | |
| T02 | Feature | Guest scan | Pass/Fail | |
| T03 | Security | No HTTP to target | Pass/Fail | |
