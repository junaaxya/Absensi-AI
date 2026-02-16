
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Manajemen Karyawan - Sistem Absensi</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined|Material+Icons+Round" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#C8D5B9", // Sage
                        sky: "#B8D4E3",
                        peach: "#F5D5CB",
                        lavender: "#D4C5E2",
                        danger: "#F5D5CB", // reuse peach for danger context per design
                        secondary: "#D4C5E2",
                        accent: "#B8D4E3",
                        highlight: "#D4C5E2", // Lavender alias
                        "background-light": "#F8F9FA",
                        "background-dark": "#121212",
                        "card-dark": "#1E1E1E",
                    },
                    fontFamily: {
                        display: ["Plus Jakarta Sans", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "12px",
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        [x-cloak] { display: none !important; }
        .filled-icon { font-variation-settings: 'FILL' 1; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 transition-colors duration-300" x-data="{ 
    detailOpen: false, 
    addOpen: false,
    faceOpen: false,
    selected: null,
    selectedFiles: [],
    previews: [],
    newEmployee: { username: '' },
    
    openDetail(employee) {
        this.selected = employee;
        this.detailOpen = true;
    },
    
    openFaceManagement() {
        this.detailOpen = false;
        this.faceOpen = true;
        this.selectedFiles = [];
        this.previews = [];
    },

    handleFiles(files) {
        const newFiles = Array.from(files);
        const availableSlots = 6 - this.selectedFiles.length;
        const filesToAdd = newFiles.slice(0, availableSlots);
        
        this.selectedFiles = [...this.selectedFiles, ...filesToAdd];
        
        filesToAdd.forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previews.push({
                    name: file.name,
                    url: e.target.result
                });
            };
            reader.readAsDataURL(file);
        });
    },

    removeFile(index) {
        this.selectedFiles.splice(index, 1);
        this.previews.splice(index, 1);
    },

    async uploadFaces() {
        if (this.selectedFiles.length < 1) {
            alert('Pilih minimal 1 foto wajah!');
            return;
        }

        const formData = new FormData();
        formData.append('user_id', this.selected.id);
        this.selectedFiles.forEach((file) => {
            formData.append('photos[]', file);
        });

        const token = document.querySelector('input[name=_token]').value;

        try {
            const btn = document.getElementById('uploadBtn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class=\'animate-pulse\'>Memproses...</span>';

            const response = await fetch('/api/face/register', { 
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token
                },
                body: formData
            });

            if (!response.ok) throw new Error('Gagal mengupload data wajah');

            alert('Data wajah berhasil disimpan!');
            this.faceOpen = false;
            window.location.reload(); 
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan: ' + error.message);
        } finally {
            const btn = document.getElementById('uploadBtn');
            if(btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    }
}">

<div class="flex min-h-screen">
    <!-- SIDEBAR -->
    <aside class="w-72 bg-white dark:bg-card-dark border-r border-slate-200 dark:border-slate-800 flex flex-col fixed h-full z-20 transition-all hidden md:flex">
        <div class="p-8">
            <div class="flex items-center gap-4 mb-10">
                <div class="w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden">
                    @if(Auth::user()->foto)
                        <img alt="Profile" class="w-full h-full object-cover" src="{{ asset('storage/' . Auth::user()->foto) }}"/>
                    @else
                        <span class="material-icons-round text-slate-500">person</span>
                    @endif
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white leading-tight uppercase">{{ Auth::user()->name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium tracking-wide uppercase">Admin</p>
                </div>
            </div>
            <nav class="space-y-2">
                <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" href="{{ route('admin.dashboard') }}">
                    <span class="material-icons-outlined text-[20px]">dashboard</span>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" href="{{ route('admin.attendance') }}">
                    <span class="material-icons-outlined text-[20px]">fingerprint</span>
                    <span class="font-medium">Absensi</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" href="{{ route('admin.absence.index') }}">
                    <span class="material-icons-outlined text-[20px]">event_busy</span>
                    <span class="font-medium">Manajemen Ketidakhadiran</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-lavender text-slate-900 font-semibold shadow-sm shadow-lavender/50" href="{{ route('employees.index') }}">
                    <span class="material-icons-outlined text-[20px]">groups</span>
                    <span class="font-medium">Manajemen Karyawan</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" href="{{ route('admin.settings.index') }}">
                    <span class="material-icons-outlined text-[20px]">settings</span>
                    <span class="font-medium">Pengaturan Sistem</span>
                </a>
            </nav>
        </div>
        <div class="mt-auto p-8">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-all">
                    <span class="material-icons-outlined text-[20px]">logout</span>
                    <span class="font-bold">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 md:ml-72">
        <!-- TOPBAR -->
        <header class="h-20 bg-white/80 dark:bg-card-dark/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-10 sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-900 dark:bg-white rounded-xl flex items-center justify-center">
                    <span class="material-icons-outlined text-white dark:text-slate-900">radio_button_checked</span>
                </div>
                <h1 class="text-xl font-bold tracking-tight dark:text-white">Sistem Absensi</h1>
            </div>
            <div class="flex items-center gap-4">
                <button class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300" onclick="document.documentElement.classList.toggle('dark')">
                    <span class="material-icons-outlined dark:hidden">dark_mode</span>
                    <span class="material-icons-outlined hidden dark:block">light_mode</span>
                </button>
            </div>
        </header>

        <div class="p-10 max-w-7xl mx-auto">
            
            @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
                <div class="animate-fade-in">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-2">Manajemen Karyawan</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-lg">Kelola Data Karyawan dan Hak Akses Sistem</p>
                </div>
            </div>

            <!-- FILTER BAR -->
            <form method="GET" action="{{ route('employees.index') }}" class="bg-white dark:bg-card-dark p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm mb-8">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-grow max-w-md">
                        <span class="material-icons-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input name="q" value="{{ request('q') }}" class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:ring-primary focus:border-primary dark:text-white transition-all" placeholder="Cari Nama atau NIP..." type="text"/>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Jabatan:</span>
                            <select name="jabatan" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 pl-4 pr-10 focus:ring-primary focus:border-primary dark:text-white text-sm">
                                <option value="">Semua</option>
                                <option value="Karyawan" {{ request('jabatan') == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                                <option value="Staff" {{ request('jabatan') == 'Staff' ? 'selected' : '' }}>Staff</option>
                                <option value="Manager" {{ request('jabatan') == 'Manager' ? 'selected' : '' }}>Manager</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Role:</span>
                            <select name="role" class="rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 pl-4 pr-10 focus:ring-primary focus:border-primary dark:text-white text-sm">
                                <option value="">Semua</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="karyawan" {{ request('role') == 'karyawan' ? 'selected' : '' }}>User</option>
                            </select>
                        </div>
                        <button type="submit" class="p-3 bg-slate-200 hover:bg-slate-300 rounded-xl transition-all">
                            <span class="material-icons-outlined text-slate-600">filter_alt</span>
                        </button>
                    </div>
                    <button type="button" @click="addOpen = true" class="ml-auto flex items-center gap-2 px-6 py-3 bg-primary hover:brightness-95 text-slate-900 font-bold rounded-2xl transition-all shadow-md shadow-primary/20">
                        <span class="material-icons-outlined">add</span>
                        Tambah Karyawan
                    </button>
                </div>
            </form>

            <!-- TABLE -->
            <div class="bg-white dark:bg-card-dark rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-5 text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">No</th>
                                <th class="px-6 py-5 text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-5 text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">NIP</th>
                                <th class="px-6 py-5 text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jabatan</th>
                                <th class="px-6 py-5 text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-5 text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Wajah</th>
                                <th class="px-6 py-5 text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($employees as $index => $employee)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-5 font-medium text-slate-900 dark:text-white">{{ $employees->firstItem() + $index }}</td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center overflow-hidden">
                                            @if($employee->foto)
                                                <img src="{{ asset('storage/' . $employee->foto) }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-xs font-bold text-slate-500">{{ strtoupper(substr($employee->name, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <span class="font-semibold text-slate-900 dark:text-white">{{ $employee->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-slate-600 dark:text-slate-400">{{ $employee->username }}</td>
                                <td class="px-6 py-5 text-slate-600 dark:text-slate-400">{{ $employee->jabatan ?? '-' }}</td>
                                <td class="px-6 py-5">
                                    <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-600 dark:text-slate-400 uppercase">
                                        {{ $employee->role === 'karyawan' ? 'User' : 'Admin' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    @if($employee->has_face_data)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary/20 text-slate-800 text-xs font-bold">
                                            <span class="material-icons-outlined text-sm">check_circle</span>
                                            Terdaftar
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-peach text-slate-800 text-xs font-bold">
                                            <span class="material-icons-outlined text-sm">cancel</span>
                                            Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openDetail({{ $employee->toJson() }})" class="flex items-center gap-1 px-4 py-2 bg-sky text-slate-800 text-xs font-bold rounded-xl hover:brightness-95 transition-all">
                                            <span class="material-icons-outlined text-sm">info</span>
                                            Detail
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="p-6 border-t border-slate-100 dark:border-slate-800">
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </main>

    <!-- MODAL DETAIL KARYAWAN -->
    <div x-show="detailOpen" x-cloak class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="detailOpen = false" class="bg-white dark:bg-slate-900 w-full max-w-3xl rounded-3xl shadow-2xl overflow-hidden border border-slate-100 dark:border-slate-800 animate-in fade-in zoom-in duration-300 max-h-[90vh] overflow-y-auto">
            
            <form x-bind:action="'/employees/' + selected?.id" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-start sticky top-0 bg-white dark:bg-slate-900 z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-secondary flex items-center justify-center text-white">
                            <span class="material-icons-round text-3xl">person</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Detail Karyawan</h2>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Informasi dan Pengaturan Akun Karyawan</p>
                        </div>
                    </div>
                    <button type="button" @click="detailOpen = false" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                        <span class="material-icons-round text-slate-400">close</span>
                    </button>
                </div>

                <div class="p-8 space-y-8">
                    <!-- Section Identitas -->
                    <section class="space-y-4">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-icons-round text-primary text-xl">badge</span>
                            <h3 class="font-bold text-slate-800 dark:text-white uppercase tracking-wider text-xs">Identitas Karyawan</h3>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-100 dark:border-slate-800">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-12">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                                    <input type="text" name="name" x-model="selected.name" class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-primary focus:border-primary">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">NIP / Username</label>
                                    <input type="text" name="username" x-model="selected.username" class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-primary focus:border-primary">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</label>
                                    <input type="email" name="email" x-model="selected.email" class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:ring-primary focus:border-primary">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal Bergabung</label>
                                    <p class="text-slate-700 dark:text-slate-200 font-medium py-2" x-text="new Date(selected?.created_at).toLocaleDateString('id-ID')"></p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Role Sistem</label>
                                    <select name="role" x-model="selected.role" class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-primary focus:border-primary transition-all">
                                        <option value="karyawan">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jabatan</label>
                                    <select name="jabatan" x-model="selected.jabatan" class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-primary focus:border-primary transition-all">
                                        <option value="Karyawan">Karyawan</option>
                                        <option value="Staff">Staff</option>
                                        <option value="Manager">Manager</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Reset Password Area -->
                            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Reset Password (Opsional)</label>
                                <div class="flex gap-4">
                                    <input type="password" name="password" placeholder="Password Baru" class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                                    <input type="password" name="password_confirmation" placeholder="Konfirmasi" class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl text-sm">
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <section class="space-y-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="material-icons-round text-secondary text-xl">login</span>
                                <h3 class="font-bold text-slate-800 dark:text-white uppercase tracking-wider text-xs">Data Akun Login</h3>
                            </div>
                            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border-2 border-accent/30 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-slate-500">Username</span>
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="selected?.username"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-slate-500">Password</span>
                                    <span class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-200">••••••••</span>
                                </div>
                            </div>
                        </section>
                        <section class="space-y-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="material-icons-round text-primary text-xl">face</span>
                                <h3 class="font-bold text-slate-800 dark:text-white uppercase tracking-wider text-xs">Status Data Wajah</h3>
                            </div>
                            <div class="bg-primary/10 dark:bg-primary/5 p-5 rounded-2xl border-2 border-primary/30 flex items-center justify-center">
                                <div class="flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm shadow-sm"
                                     :class="selected?.has_face_data ? 'bg-primary text-slate-800' : 'bg-danger text-slate-800'">
                                    <span class="material-icons-round text-sm" x-text="selected?.has_face_data ? 'check_circle' : 'cancel'"></span>
                                    <span x-text="selected?.has_face_data ? 'Terdaftar' : 'Belum Terdaftar'"></span>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="flex flex-col md:flex-row gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="openFaceManagement()" class="flex-1 bg-secondary hover:bg-[#C4B5D2] text-slate-700 py-3.5 px-6 rounded-2xl font-bold flex items-center justify-center gap-2 transition-all active:scale-95 shadow-sm">
                            <span class="material-icons-round">face_retouching_natural</span>
                            Kelola Data Wajah
                        </button>
                        <button type="submit" class="flex-1 bg-primary hover:bg-[#B8C5A9] text-slate-800 py-3.5 px-6 rounded-2xl font-bold flex items-center justify-center gap-2 transition-all active:scale-95 shadow-sm">
                            <span class="material-icons-round">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="px-8 pb-8 flex justify-center">
                <form x-bind:action="'/employees/' + selected?.id" method="POST" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold flex items-center gap-2 transition-colors">
                        <span class="material-icons-round">delete_outline</span>
                        Hapus Karyawan
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- MODAL KELOLA WAJAH -->
    <div x-show="faceOpen" x-cloak class="fixed inset-0 bg-slate-900/40 backdrop-blur-md flex items-center justify-center p-4 z-[60]">
        <div @click.away="faceOpen = false" class="w-full max-w-4xl bg-white dark:bg-slate-900 rounded-[32px] shadow-2xl overflow-hidden border border-white dark:border-slate-800 flex flex-col max-h-[90vh]">
            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Kelola Data Wajah Karyawan</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-1">Pendaftaran dan Pengelolaan Data Wajah Untuk Absensi</p>
                </div>
                <button @click="faceOpen = false" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                    <span class="material-icons-round text-slate-400">close</span>
                </button>
            </div>
            
            <div class="p-8 overflow-y-auto space-y-8">
                <!-- Identity Card -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/50">
                    <div class="flex items-center gap-6">
                        <div class="w-24 h-24 rounded-2xl bg-white dark:bg-slate-800 border-4 border-white dark:border-slate-700 shadow-sm overflow-hidden flex items-center justify-center">
                            <span class="material-icons-round text-slate-300 text-5xl filled-icon">account_circle</span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-3 flex-1">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Nama Lengkap</p>
                                <p class="text-slate-700 dark:text-slate-200 font-semibold" x-text="selected?.name"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">NIP / Karyawan</p>
                                <p class="text-slate-700 dark:text-slate-200 font-semibold" x-text="selected?.username"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Email</p>
                                <p class="text-slate-700 dark:text-slate-200 font-semibold" x-text="selected?.email"></p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Role Sistem</p>
                                <p class="text-slate-700 dark:text-slate-200 font-semibold flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-secondary"></span>
                                    <span x-text="selected?.role"></span>
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Jabatan</p>
                                <p class="text-slate-700 dark:text-slate-200 font-semibold" x-text="selected?.jabatan"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Status Card -->
                    <div class="bg-primary/10 dark:bg-primary/5 border border-primary/20 dark:border-primary/10 rounded-2xl p-6 flex flex-col justify-center items-center text-center">
                        <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center mb-3 shadow-sm shadow-primary/30">
                            <span class="material-icons-round text-white filled-icon" x-text="selected?.has_face_data ? 'verified' : 'cancel'"></span>
                        </div>
                        <p class="text-primary font-bold text-lg" x-text="selected?.has_face_data ? 'Terdaftar' : 'Belum Terdaftar'"></p>
                        <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">Jumlah Foto: <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedFiles.length"></span></p>

                    </div>
                </div>

                    <!-- Upload Area -->
                    <div class="md:col-span-2 border-2 border-dashed border-secondary/40 dark:border-secondary/20 bg-secondary/5 dark:bg-secondary/5 rounded-2xl p-6 flex flex-col items-center justify-center group hover:border-secondary transition-colors cursor-pointer" onclick="document.getElementById('face-upload').click()">
                        <div class="w-14 h-14 bg-secondary/20 dark:bg-secondary/10 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <span class="material-icons-round text-secondary text-3xl">cloud_upload</span>
                        </div>
                        <button class="bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold px-6 py-2.5 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mb-3 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors pointer-events-none">
                            Pilih Foto Wajah
                        </button>
                        <p class="text-xs text-slate-500 dark:text-slate-400 text-center leading-relaxed">
                            Unggah 6 Foto Wajah Dengan Posisi<br/>Sesuai Instruksi Admin
                        </p>
                        <input type="file" id="face-upload" class="hidden" multiple accept="image/*" @change="handleFiles($event.target.files)">
                    </div>
                </div>

                <!-- Dataset List -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-highlight rounded-full"></span>
                            Dataset Wajah Baru (Preview)
                        </h3>
                        <button @click="selectedFiles = []; previews = []" class="bg-accent text-slate-700 font-semibold px-4 py-2 rounded-xl text-sm flex items-center gap-2 hover:brightness-95 transition-all shadow-sm">
                            <span class="material-icons-round text-[18px]">delete_sweep</span>
                            Bersihkan
                        </button>
                    </div>
                    <div class="overflow-hidden border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-bold tracking-widest">
                                <tr>
                                    <th class="px-6 py-4">No</th>
                                    <th class="px-6 py-4">Foto</th>
                                    <th class="px-6 py-4">Nama File</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-600 dark:text-slate-300">
                                <template x-for="(preview, index) in previews">
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="px-6 py-3 font-medium" x-text="index + 1"></td>
                                        <td class="px-6 py-3">
                                            <div class="w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-700">
                                                <img :src="preview.url" class="w-full h-full object-cover">
                                            </div>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="truncate max-w-[150px] block" x-text="preview.name"></span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <button @click="removeFile(index)" class="w-8 h-8 rounded-lg bg-accent/20 text-accent-dark hover:bg-accent/40 transition-colors flex items-center justify-center inline-flex">
                                                <span class="material-icons-round text-[18px]">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="previews.length === 0">
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">Belum ada foto yang dipilih.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 flex justify-center">
                <button id="uploadBtn" @click="uploadFaces()" class="bg-primary text-white font-bold px-10 py-4 rounded-2xl flex items-center gap-3 shadow-lg shadow-primary/30 hover:shadow-primary/40 hover:-translate-y-0.5 transition-all">
                    <span class="material-icons-round">sync</span>
                    Proses & Simpan Data Wajah
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH KARYAWAN (NEW) -->
    <div x-show="addOpen" x-cloak class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="addOpen = false" class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-white/20 dark:border-slate-800 transition-all transform scale-100">
            
            <div class="px-6 py-5 border-b border-highlight/20 dark:border-slate-800 flex justify-between items-start bg-gradient-to-r from-white to-accent/10 dark:from-slate-900 dark:to-slate-800">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <span class="material-icons-round text-primary">person_add</span>
                        Tambah Karyawan
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Isi Data Identitas dan Akun Karyawan</p>
                </div>
                <button @click="addOpen = false" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors group">
                    <span class="material-icons-round text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200">close</span>
                </button>
            </div>

            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div class="p-6 max-h-[70vh] overflow-y-auto space-y-8">
                    
                    <!-- Identitas -->
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-1 w-8 bg-highlight rounded-full"></div>
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Data Identitas</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-300 ml-1">Nama Lengkap</label>
                                <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-accent focus:border-accent transition-all" placeholder="Contoh: John Doe"/>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-300 ml-1">NIP / Karyawan</label>
                                <input type="text" name="username" required x-model="newEmployee.username" class="w-full px-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-accent focus:border-accent transition-all" placeholder="Masukkan NIP"/>
                            </div>
                            <div class="space-y-1.5 md:col-span-1">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-300 ml-1">Email</label>
                                <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-accent focus:border-accent transition-all" placeholder="email@perusahaan.com"/>
                            </div>
                            <div class="space-y-1.5 md:col-span-1">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-300 ml-1">Jabatan</label>
                                <select name="jabatan" class="w-full px-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-accent focus:border-accent transition-all appearance-none">
                                    <option disabled="" selected="" value="">Pilih Jabatan</option>
                                    <option value="Karyawan">Karyawan</option>
                                    <option value="Staff">Staff</option>
                                    <option value="Manager">Manager</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Akun Login -->
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-1 w-8 bg-highlight rounded-full"></div>
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Data Akun Login</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-300 ml-1">Username</label>
                                <div class="relative">
                                    <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">alternate_email</span>
                                    <input type="text" x-model="newEmployee.username" disabled class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed focus:ring-0"/>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-300 ml-1">Password</label>
                                <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-accent focus:border-accent transition-all" placeholder="••••••••"/>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-300 ml-1">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-accent focus:border-accent transition-all" placeholder="••••••••"/>
                            </div>
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-300 ml-1">Role</label>
                                <select name="role" required class="w-full px-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-accent focus:border-accent transition-all">
                                    <option value="karyawan">Karyawan</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Info Wajah -->
                    <section class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="material-icons-round text-slate-400">face</span>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Informasi Data Wajah</h3>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1 bg-secondary/30 dark:bg-secondary/10 text-slate-700 dark:text-secondary rounded-full border border-secondary/50">
                                <span class="material-icons-round text-[14px]">error_outline</span>
                                <span class="text-[11px] font-bold tracking-tight">BELUM TERDAFTAR</span>
                            </div>
                        </div>
                        <p class="text-[12px] text-slate-400 dark:text-slate-500 italic leading-relaxed">
                            <span class="material-icons-round text-[14px] align-middle mr-1">info</span>
                            Daftar wajah dapat didaftarkan pada menu kelola wajah setelah data karyawan berhasil disimpan.
                        </p>
                    </section>
                </div>

                <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 bg-white dark:bg-slate-900">
                    <button @click="addOpen = false" class="px-6 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-secondary/20 dark:hover:bg-slate-800 rounded-xl transition-all flex items-center gap-2" type="button">
                        Batal
                    </button>
                    <button class="px-8 py-2.5 bg-primary hover:bg-primary/90 text-slate-800 text-sm font-bold rounded-xl transition-all shadow-lg shadow-primary/20 hover:shadow-primary/40 flex items-center gap-2" type="submit">
                        <span class="material-icons-round text-lg">save</span>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

</body>
</html>
