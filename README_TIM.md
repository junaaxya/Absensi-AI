# Quick Start untuk Tim

## 🚀 Setup Pertama Kali (Developer Baru)

```bash
# 1. Clone repository
git clone <repository-url>
cd Absensi-AI

# 2. Start semua services
docker compose up -d --build

# 3. Tunggu build selesai (~5-10 menit pertama kali)
# Cek progress:
docker compose logs -f laravel

# 4. Jalankan migrasi database
docker compose exec laravel php artisan migrate

# 5. Buat user admin (opsional)
docker compose exec laravel php artisan tinker --execute="
use Spatie\Permission\Models\Role;
Role::firstOrCreate(['name' => 'admin']);
\$user = \App\Models\User::firstOrCreate(['email' => 'admin@example.com'], ['name' => 'Admin', 'username' => 'admin', 'password' => bcrypt('password'), 'nip' => '12345', 'jabatan' => 'Administrator', 'has_face_data' => false]);
\$user->assignRole('admin');
echo 'Done!\n';
"
```

**Selesai!** Buka http://localhost:8000

## 📝 Yang Perlu Diketahui

### ✅ File yang DI-PUSH ke GitHub:
- Source code (app, routes, resources)
- Dockerfile & docker-entrypoint.sh
- package.json, composer.json
- Dokumentasi

### ❌ File yang TIDAK DI-PUSH (auto-generated):
- `/public/build` → Auto-generated saat container start
- `/node_modules` → Auto-installed
- `/vendor` → Auto-installed
- `.env` → Setiap dev buat sendiri

### 🔧 Entrypoint Script (Auto-Fix)
Setiap container start, otomatis:
- ✅ Fix permissions
- ✅ Build Vite assets (jika belum ada)
- ✅ Start Apache

**Tidak perlu manual intervention!**

## 🔄 Daily Workflow

```bash
# Start
docker compose up -d

# Stop
docker compose down

# Restart setelah update code
docker compose restart laravel

# Lihat logs
docker compose logs -f laravel
```

## 🐛 Troubleshooting

**Permission error?**
```bash
docker compose restart laravel  # Auto-fix
```

**Vite manifest not found?**
```bash
docker compose restart laravel  # Auto-rebuild
```

**Port sudah digunakan?**
```bash
sudo lsof -i :8000  # Cek port
docker compose down # Stop dulu
```

## 📚 Dokumentasi Lengkap

Lihat `SETUP_UNTUK_TIM.md` untuk dokumentasi lengkap.

---

**Quick Start**: Clone → `docker compose up -d --build` → Migrate → Done! 🎉
