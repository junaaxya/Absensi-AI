# 🌿 Sistem Absensi Wajah Berbasis AI (Face Recognition)

Sistem absensi modern yang mengintegrasikan framework **Laravel 12** sebagai aplikasi web utama dan **Flask** sebagai microservice untuk pengenalan wajah menggunakan teknologi **YOLOv8** dan **InsightFace**.

Sistem ini dirancang dengan antarmuka pengguna (UI) yang menenangkan menggunakan palet warna **Pastel & Neutral** (Sage, Sky, Peach, Lavender) untuk memberikan pengalaman pengguna yang intuitif dan profesional.

---

## ✨ Fitur Utama

- 👤 **Manajemen Karyawan**: Pengelolaan data identitas, jabatan, dan hak akses.
- 📸 **Registrasi Wajah (Dataset)**: Pendaftaran data wajah dengan pengambilan foto (hingga 6 foto) untuk akurasi tinggi.
- 🎯 **Absensi Otomatis**: Pengenalan wajah real-time untuk Check-in dan Check-out.
- 📝 **Pengajuan Izin**: Sistem manajemen ketidakhadiran (Izin/Sakit/Cuti).
- 📊 **Dashboard Real-time**: Statistik kehadiran harian untuk admin dan ringkasan untuk karyawan.
- 🎨 **Modern Pastel UI**: Desain responsif dengan estetika modern yang lembut di mata.

---

## 🛠️ Tech Stack

- **Frontend/Backend**: [Laravel 12](https://laravel.com/) (PHP 8.2+)
- **Face Recognition Service**: [Flask](https://flask.palletsprojects.com/) (Python 3.10+)
- **AI Models**: 
  - **YOLOv8**: Untuk deteksi wajah yang cepat dan akurat.
  - **InsightFace**: Untuk ekstraksi embedding wajah (buffalo_l model).
- **Database**: MySQL 8.0
- **UI Styling**: Tailwind CSS (Custom Pastel Palette)
- **Containerization**: Docker & Docker Compose

---

## 📋 Prasyarat

Sebelum memulai, pastikan Anda telah menginstal:
- [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/)

---

## 🚀 Panduan Instalasi & Setup

Ikuti langkah-langkah berikut untuk menjalankan sistem di lingkungan lokal:

### 1. Clone Repositori
```bash
git clone <repository-url>
cd absensi
```

### 2. Konfigurasi Environment (Laravel)
Salin file `.env.example` menjadi `.env` di folder `laravel-app/`:
```bash
cd laravel-app
cp .env.example .env
```
Pastikan variabel `FLASK_SERVICE_URL` mengarah ke service Docker:
```env
FLASK_SERVICE_URL=http://face-service:5000
```

### 3. Menjalankan Docker Compose
Kembali ke root direktori dan jalankan semua layanan:
```bash
cd ..
docker compose up -d --build
```
*Tunggu hingga proses build selesai (estimasi 5-10 menit untuk download model AI).*

### 4. Setup Database & Assets
Jalankan migrasi database dan build assets untuk UI:
```bash
# Menjalankan Migrasi
docker compose exec laravel php artisan migrate

# Membangun Assets Tailwind (Penting untuk UI Pastel)
docker compose exec laravel npm install
docker compose exec laravel npm run build
```

---

## 📖 Panduan Penggunaan

### Akses Layanan
- **Web App**: [http://localhost:8000](http://localhost:8000)
- **Face Service (Internal API)**: [http://localhost:5000](http://localhost:5000)

### 1. Registrasi Akun
1. Buka halaman registrasi di `/register`.
2. Isi data diri (NIP digunakan sebagai username default).
3. Setelah login, Anda akan diarahkan ke Dashboard.

### 2. Registrasi Wajah (Dataset & Embedding)
Untuk melakukan absensi, data wajah harus didaftarkan terlebih dahulu:

**Alur Mandiri (Karyawan):**
- Masuk ke menu **Profile** -> **Edit Profile**.
- Pilih bagian **Kelola Data Wajah**.
- Unggah hingga 6 foto wajah dengan posisi berbeda (depan, sedikit miring, ekspresi netral).

**Alur Admin (Manajemen Karyawan):**
- Admin masuk ke menu **Karyawan**.
- Klik tombol **👤 Kelola Wajah** pada baris karyawan yang dimaksud.
- Unggah foto wajah atau ambil foto langsung melalui antarmuka yang disediakan.

**Cara Kerja di Balik Layar:**
1. Foto diunggah ke Laravel.
2. Laravel mengirimkan foto tersebut ke **Face Service (Flask)**.
3. Flask mendeteksi wajah (YOLOv8) dan mengekstrak fitur unik (Embedding) menggunakan InsightFace.
4. Embedding disimpan sebagai file `.npy` (NumPy) dengan ID user tersebut.
5. Status `has_face_data` di database akan berubah menjadi `true`.

### 3. Proses Absensi
- Klik tombol **Absen Masuk** atau **Absen Keluar** di Dashboard.
- Kamera akan aktif. Pastikan wajah berada di area frame.
- Sistem akan mengenali wajah dan melakukan validasi ke server Laravel secara otomatis.

---

## 🎨 Tema & Estetika (Pastel UI)

Aplikasi ini menggunakan tema **Pastel Modern** yang dikonfigurasi melalui Tailwind CSS:
- 🌿 **Sage** (`#C8D5B9`): Digunakan untuk tombol utama dan elemen sukses.
- 🌊 **Sky** (`#B8D4E3`): Digunakan untuk elemen informasi dan aksen biru.
- 🍑 **Peach** (`#F5D5CB`): Digunakan untuk peringatan dan elemen interaktif.
- 🔮 **Lavender** (`#D4C5E2`): Digunakan untuk elemen sekunder dan brand.

---

## 🛠️ Troubleshooting

- **UI Terlihat Berantakan?**
  Pastikan Anda telah menjalankan `npm run build` di dalam container laravel untuk memproses custom colors Tailwind.
- **Kamera Tidak Muncul di Face Service?**
  Pastikan browser memberikan izin akses kamera dan aplikasi berjalan di `localhost` atau `https`.
- **Face Service Gagal Terhubung?**
  Cek logs Docker: `docker compose logs -f face-service`. Pastikan model `yolov8n.pt` sudah terunduh dengan benar di folder service.
- **Masalah Izin Folder (Linux):**
  ```bash
  sudo chmod -R 775 laravel-app/storage laravel-app/bootstrap/cache
  ```

---

## 📁 Struktur Folder Utama

```text
absensi/
├── laravel-app/         # Aplikasi Web (Laravel 12)
│   ├── app/Http/        # Logic & Controllers
│   ├── resources/views/ # UI Templates (Blade)
│   └── public/          # Assets
├── face-service/        # Microservice AI (Flask)
│   ├── embeddings/      # Database vektor wajah (.npy)
│   ├── face_service.py  # Entry point Flask
│   └── yolov8n.pt       # Model deteksi wajah
└── docker-compose.yml   # Konfigurasi Orkestrasi
```

---

## 📄 Lisensi

Proyek ini dikembangkan untuk tujuan Penelitian/Internal. Seluruh hak cipta model AI (InsightFace) mengikuti kebijakan lisensi pengembang aslinya.
