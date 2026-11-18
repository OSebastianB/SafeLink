# Metodologi SAW pada SafeLink

## Pengertian SAW

Simple Additive Weighting (SAW) adalah metode pengambilan keputusan multi-kriteria yang menjumlahkan nilai kriteria yang telah dinormalisasi dan dibobot. SafeLink menggunakan SAW untuk menghasilkan **nilai keamanan** suatu URL berdasarkan enam indikator.

## Tujuan penggunaan SAW

Memberikan penilaian awal (preliminary risk assessment) terhadap potensi phishing pada URL secara transparan, dapat diaudit, dan dapat dikalibrasi.

## Definisi alternatif

Setiap URL yang diperiksa adalah satu alternatif. Penilaian dilakukan **per URL secara individual**, bukan perbandingan ranking antar banyak URL pada satu sesi.

## Definisi kriteria

| Kode | Nama | Bobot | Atribut |
|------|------|------:|---------|
| C1 | Reputasi Domain | 0.30 | Benefit |
| C2 | Usia Domain | 0.15 | Benefit |
| C3 | Penggunaan HTTPS | 0.10 | Benefit |
| C4 | Struktur dan Panjang URL | 0.15 | Benefit |
| C5 | Penggunaan Alamat IP | 0.10 | Benefit |
| C6 | Indikator Karakteristik Phishing | 0.20 | Benefit |

Semua kriteria bersifat **benefit** karena skor merepresentasikan tingkat keamanan relatif (0–5), bukan skor risiko mentah.

## Skala penilaian

Skor berada pada rentang 0 sampai 5:

- 5 = kondisi relatif paling aman pada kriteria tersebut
- 1 = kondisi mencurigakan
- 0 = indikator ancaman sangat kuat (khusus reputasi terkonfirmasi)
- 2.5 = skor netral jika data tidak tersedia

## Rumus normalisasi

Karena penilaian bersifat individual, SafeLink memakai **skala referensi tetap**:

\[
r_{ij} = \frac{x_{ij}}{5}
\]

Jangan menormalisasi terhadap max/min dari daftar URL lain.

## Rumus preferensi (nilai keamanan)

\[
V_i = \sum_{j=1}^{n} w_j \cdot r_{ij}
\]

dengan \(\sum w_j = 1\).

## Indeks risiko

\[
\text{Risk Index} = (1 - V_i) \times 100
\]

Nilai preferensi SAW **tidak** ditampilkan langsung sebagai persentase risiko.

## Kategori hasil (default)

| Indeks risiko | Kategori |
|---------------|----------|
| 0–20 | Risiko Rendah |
| >20–50 | Risiko Sedang |
| >50–100 | Risiko Tinggi |

Ambang dapat diubah administrator melalui pengaturan aplikasi.

## Contoh perhitungan manual

Misalkan skor: C1=4, C2=2.5, C3=4, C4=5, C5=5, C6=5.

Normalisasi: 0.8, 0.5, 0.8, 1.0, 1.0, 1.0

Kontribusi:

- C1: 0.30×0.8 = 0.24
- C2: 0.15×0.5 = 0.075
- C3: 0.10×0.8 = 0.08
- C4: 0.15×1.0 = 0.15
- C5: 0.10×1.0 = 0.10
- C6: 0.20×1.0 = 0.20

\(V = 0.845\)

Risk Index = \((1-0.845)\times100 = 15.5\) → **Risiko Rendah**

## Penanganan data tidak tersedia

- Skor netral 2.5
- Status `unavailable`
- Ditampilkan sebagai peringatan
- Dihitung dalam persentase cakupan data terpisah dari risk index
- Jika cakupan < 50%, tampilkan peringatan keandalan

## Aturan interpretasi hasil

1. HTTPS saja tidak berarti aman.
2. Tidak ditemukan di threat intel ≠ terbukti aman.
3. Jika threat intel mengonfirmasi phishing/malicious, tampilkan **peringatan prioritas** di luar agregasi SAW.
4. Hasil historis memakai snapshot bobot saat perhitungan.
