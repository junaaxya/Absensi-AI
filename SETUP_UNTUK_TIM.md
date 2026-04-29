# 🚀 Setup Guide untuk Tim

## 📋 Untuk Developer Baru

Ketika tim Anda clone repository ini, mereka hanya perlu menjalankan:

```bash
# 1. Clone repository
git clone <repository-url>
cd Absensi-AI

# 2. Start aplikasi dengan Docker
docker compose up -d --build

# 3. Tunggu sebentar (build pertama ~5-10 menit)
# Entrypoint script akan otomatis:
# - Fix permissions
# - Build Vite assets
# - Start Apache

# 4. Jalankan migrasi database
docker compose exec laravel php artisan migrate

# 5. (Opsional) Buat user admin
docker compose exec laravel php artisan tinker --execute="
use Spatie\Permission\Models\Role;
Role::firstOrCreate(['name' => 'admin']);
Role::firstOrCreate(['name' => 'user']);
\$user = \App\Models\User::firstOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Admin',
        'username' => 'admin',
        'password' => bcrypt('password'),
        'nip' => '12345',
        'jabatan' => 'Administrator',
        'has_face_data' => false
    ]
);
\$user->assignRole('admin');
echo 'Admin user ready!\n';
"
```

**Selesai!** Aplikasi bisa diakses di http://localhost:8000

## 🔧 Apa yang Terjadi di Balik Layar?

### 1. Docker Build Process
- Dockerfile akan build image dengan semua dependencies
- `npm run build` dijalankan saat build image
- Vite assets (manifest.json, CSS, JS) dibuat di dalam image

### 2. Entrypoint Script (Auto-Fix)
Setiap kali container start, `docker-entrypoint.sh` akan:
```
🔧 Fixing permissions...      → Fix storage/logs permissions
📦 Checking Vite build...      → Cek manifest.json
✅ Vite manifest found         → Atau rebuild jika hilang
🚀 Starting Apache...          → Start web server
```

### 3. Volume Mounts
Docker Compose mount source code untuk development:
- `./laravel-app/app` → Hot reload untuk PHP code
- `./laravel-app/resources` → Hot reload untuk views
- `./laravel-app/public` → Untuk assets

**PENTING**: `/public/build` di-exclude dari git (`.gitignore`), tapi entrypoint script akan auto-generate saat container start.

## 📁 File yang Di-Push ke GitHub

✅ **Yang DI-PUSH**:
- `laravel-app/Dockerfile` (dengan ENTRYPOINT)
- `laravel-app/docker-entrypoint.sh` (script auto-fix)
- `docker-compose.yml`
- Source code (app, routes, resources, dll)
- `package.json`, `composer.json`

❌ **Yang TIDAK DI-PUSH** (sudah di `.gitignore`):
- `/public/build` (Vite assets) → Auto-generated
- `/node_modules` → Auto-installed
- `/vendor` → Auto-installed
- `.env` → Setiap dev buat sendiri
- `/storage/logs` → Runtime files

## 🔄 Workflow Development

### Untuk Developer yang Sudah Setup

```bash
# Start aplikasi
docker compose up -d

# Lihat logs
docker compose logs -f laravel

# Stop aplikasi
docker compose down

# Restart setelah update code
docker compose restart laravel
```

### Jika Ada Update Dependencies

```bash
# Update PHP dependencies
docker compose exec laravel composer install

# Update Node dependencies
docker compose exec laravel npm install

# Rebuild Vite assets (jika perlu)
docker compose exec laravel npm run build
```

### Jika Ada Update Dockerfile

```bash
# Rebuild image
docker compose up -d --build
```

## 🐛 Troubleshooting untuk Tim

### "Permission denied" error
**Solusi**: Restart container, entrypoint script akan auto-fix
```bash
docker compose restart laravel
```

### "Vite manifest not found"
**Solusi**: Entrypoint script akan auto-rebuild, atau manual:
```bash
docker compose exec laravel npm run build
```

### Database connection error
**Solusi**: Tunggu MySQL healthcheck selesai (~30 detik)
```bash
docker compose logs mysql
```

### Port sudah digunakan
**Solusi**: Stop container lain atau ubah port di `docker-compose.yml`
```bash
# Cek port yang digunakan
sudo lsof -i :8000
sudo lsof -i :5000
sudo lsof -i :3306
```

## 🔐 Environment Variables

Setiap developer harus buat `.env` sendiri (tidak di-push ke git):

```bash
# Copy dari example
cp laravel-app/.env.example laravel-app/.env

# Edit sesuai kebutuhan
# Tapi untuk Docker, default values sudah OK
```

**PENTING**: `.env` sudah di-exclude dari git, jadi setiap developer bisa punya konfigurasi sendiri.

## 🎯 Best Practices untuk Tim

1. **Jangan commit `/public/build`** - Sudah di `.gitignore`, biarkan auto-generated
2. **Jangan commit `.env`** - Setiap dev punya config sendiri
3. **Commit `docker-entrypoint.sh`** - Ini penting untuk auto-fix
4. **Commit `Dockerfile` changes** - Semua dev butuh update yang sama
5. **Run migrations setelah pull** - Jika ada migration baru
6. **Rebuild image jika Dockerfile berubah** - `docker compose up -d --build`

## 📊 Struktur yang Di-Push ke Git

```
Absensi-AI/
├── docker-compose.yml              ✅ Push
├── SETUP_UNTUK_TIM.md             ✅ Push (dokumentasi ini)
├── PERMANENT_FIX_APPLIED.md       ✅ Push (dokumentasi fix)
├── laravel-app/
│   ├── Dockerfile                  ✅ Push (dengan ENTRYPOINT)
│   ├── docker-entrypoint.sh        ✅ Push (auto-fix script)
│   ├── app/                        ✅ Push (source code)
│   ├── routes/                     ✅ Push
│   ├── resources/                  ✅ Push
│   ├── public/
│   │   ├── index.php              ✅ Push
│   │   └── build/                 ❌ TIDAK (auto-generated)
│   ├── storage/
│   │   └── logs/                  ❌ TIDAK (runtime)
│   ├── vendor/                    ❌ TIDAK (composer install)
│   ├── node_modules/              ❌ TIDAK (npm install)
│   ├── .env                       ❌ TIDAK (per-developer)
│   ├── .env.example               ✅ Push (template)
│   ├── package.json               ✅ Push
│   └── composer.json              ✅ Push
└── face-service/
    ├── Dockerfile                  ✅ Push
    ├── face_service.py             ✅ Push
    └── requirements.txt            ✅ Push
```

## 🎓 Penjelasan untuk Tim

### Kenapa `/public/build` tidak di-push?

1. **File besar** - Build assets bisa 1-5 MB, tidak efisien di git
2. **Auto-generated** - Setiap developer bisa generate sendiri
3. **Conflict prone** - Sering conflict jika di-commit
4. **Docker handles it** - Entrypoint script auto-generate saat container start

### Kenapa pakai Entrypoint Script?

1. **Zero manual steps** - Developer baru tinggal `docker compose up`
2. **Consistent environment** - Semua dev punya setup yang sama
3. **Auto-fix issues** - Permission dan build errors auto-resolve
4. **Production-ready** - Bisa deploy ke production dengan setup yang sama

## 🚀 Quick Start untuk Tim Baru

```bash
# 1. Clone
git clone <repo-url> && cd Absensi-AI

# 2. Start
docker compose up -d --build

# 3. Migrate
docker compose exec laravel php artisan migrate

# 4. Access
open http://localhost:8000
```

**That's it!** 🎉

---

**Dibuat**: 2026-04-28  
**Maintainer**: Tim Development  
**Status**: ✅ Production Ready
