# 🌍 Global Supply Chain Risk Intelligence (LogisticsCtrl)

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

**LogisticsCtrl** adalah sebuah platform aplikasi web komprehensif berbasis **Laravel** yang dirancang untuk memantau, menganalisis, dan memvisualisasikan berbagai risiko dalam rantai pasokan global (*Global Supply Chain*). Aplikasi ini mensimulasikan dampak dari cuaca, inflasi, nilai tukar mata uang, kemacetan pelabuhan, dan sentimen geopolitik terhadap keputusan logistik dan rute pengiriman.

---

## ✨ Fitur Utama

Aplikasi ini memiliki berbagai dasbor analitik interaktif yang memproses data dari lebih dari 200 negara dan pelabuhan secara *real-time*:

1. 📊 **Global Pulse Dashboard**
   - Ringkasan KPI (Populasi, GDP, Inflasi, Nilai Tukar).
   - Pemetaan interaktif dan tren risiko secara umum.
2. 🛡️ **Risk Scoring Engine**
   - Algoritma perhitungan skor risiko berbasis 4 komponen utama: *Weather*, *Inflation*, *Exchange Rate*, dan *News Sentiment*.
3. 🌩️ **Global Weather Monitor**
   - Peta cuaca dunia interaktif (menggunakan **Leaflet.js**) untuk melacak titik hujan, badai (*storm*), angin kencang, beserta panduan status navigasi kapal.
4. ⚖️ **Import Analyzer (Decision Support)**
   - Fitur unggulan untuk mensimulasikan keputusan impor antar pelabuhan (Negara Asal ➡️ Negara Tujuan).
   - Mengkalkulasi 5 Pilar Risiko Utama: Cuaca, Nilai Tukar, Geopolitik, Kemacetan Pelabuhan, dan Inflasi, untuk memberikan rekomendasi keputusan akhir.
5. 🔄 **Country Comparison**
   - Dasbor visual untuk membandingkan matriks ekonomi dan risiko secara *head-to-head* antara dua negara (misal: Germany vs Australia).
6. 📈 **Data Visualization Dashboard**
   - Representasi grafik komprehensif menggunakan **Chart.js** untuk melihat tren GDP, Inflasi, Nilai Tukar, dan Tren Risiko secara global.
7. ⭐ **Favorite Monitoring (Watchlist)**
   - Kemampuan bagi *user* untuk menyimpan dan memantau secara khusus negara-negara tertentu yang menjadi prioritas.
8. 🛠️ **Admin Dashboard**
   - Panel manajemen data (CRUD) untuk entitas *Users*, *Ports*, dan *Articles* dengan dukungan *polling real-time* agar tabel selalu sinkron secara otomatis.

---

## 💻 Teknologi yang Digunakan

- **Backend Framework:** Laravel (PHP)
- **Frontend Engine:** Laravel Blade Templates
- **Styling:** Native/Vanilla CSS (Desain Modern, *Glassmorphism*, *Card-based layout*)
- **Interaktivitas:** Vanilla JavaScript (Fetch API, DOM Manipulation)
- **Data Visualization:** Chart.js
- **Interactive Maps:** Leaflet.js & CartoDB Maps
- **Icons:** Tabler Icons

---

## 🚀 Cara Instalasi & Menjalankan Proyek

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di *local environment* Anda:

### 1. Kloning Repositori (Jika menggunakan Git)
```bash
git clone <url-repo-anda>
cd global-supply-chain-dashboard
```

### 2. Instalasi Dependensi
Pastikan Anda sudah menginstal **Composer** dan **Node.js/NPM**.
```bash
composer install
npm install
npm run build
```

### 3. Konfigurasi Database
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Lalu, buka file `.env` dan sesuaikan pengaturan *database* Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
```

### 4. *Generate Key* & Migrasi Database
Jalankan perintah ini untuk membuat *application key* dan memigrasi struktur *database* beserta data awal (*seeder*):
```bash
php artisan key:generate
php artisan migrate --seed
```

### 5. Jalankan Aplikasi
Jalankan *server* lokal Laravel:
```bash
php artisan serve
```
Akses aplikasi melalui browser di: **`http://127.0.0.1:8000`**

---

## 🎨 UI/UX Design Note

Aplikasi ini sangat mengedepankan nilai estetika (*Premium & Rich Aesthetics*). Hindari menggunakan UI *default* browser. Semua *cards*, tabel, formulir, dan tombol telah dibuat secara *custom* menggunakan properti CSS tingkat lanjut (`backdrop-filter`, `linear-gradient`, `box-shadow`) untuk menghasilkan pengalaman *dashboard* logistik setingkat *Enterprise*.

---

## 📄 Lisensi
Proyek ini dikembangkan sebagai bagian dari UAS / Pembelajaran (Pemrograman Web 2). Bebas untuk dimodifikasi dan dikembangkan lebih lanjut.
