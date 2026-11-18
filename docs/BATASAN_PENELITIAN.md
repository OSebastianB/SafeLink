# Batasan Penelitian SafeLink

## Batasan sistem

1. SafeLink adalah **alat penilaian risiko berbasis indikator**, bukan penentu absolut phishing.
2. Sistem **tidak membuka** URL yang diperiksa (mencegah SSRF dan risiko eksekusi konten berbahaya).
3. Tidak melakukan validasi sertifikat SSL secara kriptografis; HTTPS hanya dinilai dari skema URL.
4. Usia domain bergantung pada ketersediaan RDAP/provider; default netral jika tidak tersedia.
5. Reputasi domain mengandalkan basis data lokal/admin dan provider yang dikonfigurasi; bukan jaminan intelijen global real-time.

## Keterbatasan sumber reputasi

- Tidak ditemukan di database ≠ aman.
- Data kedaluwarsa tidak dipakai otomatis sebagai valid.
- Provider eksternal dapat gagal; sistem harus fallback ke status unknown.

## Risiko false positive / false negative

- Kata seperti `login`/`verify` dapat muncul pada situs sah → potensi false positive.
- URL phishing baru/domain tua dengan struktur “bersih” dapat lolos deteksi heuristik → false negative.
- Ambang kategori default (20/50) perlu dikalibrasi dengan dataset validasi terpisah.

## Keterbatasan skala heuristik

Skala C4 dan C6 bersifat rule-based deterministik. Aturan ini transparan untuk laporan akademik, namun tidak menggantikan model deteksi berbasis machine learning atau sandboxing.

## Keterbatasan data domain

IDN/punycode ditangani secara best-effort. Beberapa TLD multi-level memakai heuristik eTLD+1 sederhana.

## Pernyataan penting

SafeLink **tidak menjamin** keamanan mutlak suatu website dan **tidak menggantikan** antivirus, browser protection, atau investigasi forensik keamanan.
