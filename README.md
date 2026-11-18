# SafeLink – Smart URL Risk Assessment System

Sistem Pendukung Keputusan berbasis web untuk menilai potensi phishing pada URL menggunakan metode **Simple Additive Weighting (SAW)**.

> SafeLink adalah alat penilaian risiko berbasis indikator, **bukan** jaminan mutlak bahwa suatu website aman atau berbahaya. Sistem **tidak membuka** URL yang diperiksa.

## Fitur

- Landing page + URL checker (guest & user)
- Penilaian 6 kriteria (C1–C6) + perhitungan SAW dinamis
- Hasil transparan: skor, normalisasi, bobot snapshot, kontribusi, penjelasan
- Dashboard user + riwayat + export CSV
- Dashboard admin: kriteria, bobot, skala, reputasi domain, brand watchlist, evaluasi dataset, pengguna, pengaturan risiko
- Autentikasi role `admin` / `user`

## Teknologi

- PHP 8.2+, Laravel 12
- MySQL / MariaDB
- Blade, Bootstrap 5, Bootstrap Icons, Chart.js

## Persyaratan

- PHP 8.2+ (ekstensi: openssl, pdo_mysql, mbstring, tokenizer, xml, ctype, json, fileinfo, zip)
- Composer
- MySQL/MariaDB
- (Opsional) Node.js jika ingin membangun aset Vite; UI saat ini memakai CDN Bootstrap

## Instalasi

```bash
composer install
copy .env.example .env   # atau sesuaikan .env yang ada
php artisan key:generate
```

### Konfigurasi database (.env)

```env
APP_NAME=SafeLink
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=safelink
DB_USERNAME=root
DB_PASSWORD=

SAFELINK_ADMIN_NAME="Administrator"
SAFELINK_ADMIN_EMAIL=admin@safelink.test
SAFELINK_ADMIN_PASSWORD=GantiPasswordAman!
SAFELINK_RDAP_ENABLED=false
SAFELINK_THREAT_PROVIDER=local
```

Buat database:

```sql
CREATE DATABASE safelink CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Migrasi & seeder

```bash
php artisan migrate --seed
```

Akun default dari seeder (jika password diset di `.env`):

- Admin: `admin@safelink.test` / nilai `SAFELINK_ADMIN_PASSWORD`
- Demo user: `user@safelink.test` / `password`

## Menjalankan aplikasi

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Provider eksternal

- Reputasi default: database lokal (`threat_intelligence`).
- RDAP usia domain: opsional (`SAFELINK_RDAP_ENABLED=true`), hanya endpoint allowlist.
- Jika provider gagal/nonaktif → status unknown + skor netral 2.5 (bukan clean).

## Testing

```bash
php artisan test
```

## Dokumentasi tugas akhir

- `docs/METODOLOGI_SAW.md`
- `docs/ARSITEKTUR_SISTEM.md`
- `docs/PANDUAN_PENGUJIAN.md`
- `docs/BATASAN_PENELITIAN.md`
- `docs/KEAMANAN.md`

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Total bobot ≠ 1 | Perbaiki di menu Manajemen Bobot |
| Class not found | `composer dump-autoload` |
| Migration gagal | Pastikan DB `safelink` sudah dibuat |
| zip missing saat composer | Aktifkan `extension=zip` di `php.ini` |

## Lisensi

Proyek akademik Tugas Akhir.
