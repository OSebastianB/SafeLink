# Dokumentasi Keamanan SafeLink

## Prinsip utama

SafeLink memproses URL dari pengguna. Oleh karena itu sistem dirancang agar **tidak pernah** menjadikan URL target sebagai tujuan koneksi jaringan aplikasi.

## Kontrol yang diimplementasikan

1. Tidak ada HTTP GET/HEAD ke URL yang dianalisis.
2. Tidak ada follow redirect / iframe preview dari URL target.
3. Validasi ketat: protokol HTTP/HTTPS saja, panjang maksimum 2048, tolak kredensial tertanam, tolak karakter kontrol.
4. Provider eksternal (RDAP) opsional dan hanya ke endpoint allowlist.
5. API key tidak diekspos ke frontend.
6. Password di-hash (Laravel hashed cast / bcrypt).
7. CSRF protection aktif pada form web.
8. Otorisasi role admin melalui middleware backend.
9. Eloquent/query binding untuk mencegah SQL injection.
10. Blade escaping untuk mencegah XSS.

## Batasan pemeriksaan

- Localhost/IP privat dapat diparse sebagai input, tetapi tidak dihubungi.
- Reputasi unknown ditampilkan sebagai data tidak tersedia (skor netral), bukan “clean”.
- Ancaman terkonfirmasi memunculkan peringatan prioritas di luar agregasi SAW.
