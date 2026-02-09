# 🚀 Panduan Menjalankan Proyek dengan Docker

## Prasyarat

- **Docker Engine** (versi terbaru) ✅
- **Docker Compose v2** (sudah include di Docker terbaru) ✅
- RAM 8GB+ (punya 16GB ✅)
- SSD ✅

---

## Quick Start

### 1. Masuk ke folder proyek

```bash
cd "/home/steven/Downloads/skripsi_presensi_wenni - Copy (2)/skripsi_presensi_wenni - Copy"
```

### 2. Build dan jalankan

```bash
docker compose up --build
```

> ⏱️ Build pertama ~10-20 menit

### 3. Jalankan migrasi (terminal baru)

```bash
docker compose exec laravel php artisan migrate
```

### 4. (Opsional) Buat user admin

```bash
docker compose exec laravel php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'username' => 'admin',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);
```

---

## Akses

| Service | URL |
|---------|-----|
| Laravel | http://localhost:8000 |
| Flask   | http://localhost:5000 |

---

## Perintah Penting

```bash
# Jalankan background
docker compose up -d --build

# Lihat log
docker compose logs -f

# Log spesifik
docker compose logs -f laravel
docker compose logs -f flask
docker compose logs -f mysql

# Stop
docker compose down

# Stop + hapus database
docker compose down -v

# Masuk container
docker compose exec laravel bash
docker compose exec flask bash
```

---

## Troubleshooting

### Port sudah digunakan
```bash
sudo lsof -i :8000
sudo kill -9 <PID>
```

### Database connection refused
Tunggu MySQL healthcheck selesai (~30 detik).

### Permission denied
```bash
sudo chown -R $USER:$USER .
chmod -R 775 absensi-face/storage absensi-face/bootstrap/cache
```

### Rebuild dari awal
```bash
docker compose down -v
docker system prune -f
docker compose up --build
```
