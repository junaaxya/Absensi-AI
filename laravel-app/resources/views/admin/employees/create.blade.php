@extends('layouts.admin')

@section('header-title', 'Tambah Karyawan')
@section('header-subtitle', 'Menambahkan Data Perangkat Desa Baru')

@section('content')

    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-card-dark rounded-2xl p-8 shadow-sm border border-slate-200 dark:border-slate-800">

            <div class="mb-8 pb-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white">Form Data Karyawan</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Lengkapi informasi di bawah ini untuk
                        menambahkan karyawan baru.</p>
                </div>
                <button onclick="history.back()"
                    class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="material-icons-round">arrow_back</span>
                </button>
            </div>

            <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Foto Profil -->
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Foto Profil (Opsional)</label>
                        <input type="file" name="foto" accept="image/*"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sage/10 file:text-sage hover:file:bg-sage/20" />
                        @error('foto')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('name') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Contoh: Budi Santoso" />
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jabatan -->
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('jabatan') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="Contoh: Staff Administrasi" />
                        @error('jabatan')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departemen -->
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Departemen</label>
                        <select name="department_id"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('department_id') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('email') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="nama@email.com" />
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('username') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400"
                            placeholder="username_karyawan" />
                        @error('username')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Role Pengguna</label>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Admin -->
                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="Direktur" class="peer sr-only" {{ old('role') == 'admin' ? 'checked' : '' }}>
                                <div
                                    class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-sage peer-checked:bg-sage/10 transition-all flex items-center justify-center gap-2">
                                    <div
                                        class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-sage peer-checked:bg-sage">
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Admin</span>
                                </div>
                            </label>
                            <!-- Manager -->
                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="Manager" class="peer sr-only" {{ old('role') == 'manager' ? 'checked' : '' }}>
                                <div
                                    class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-sage peer-checked:bg-sage/10 transition-all flex items-center justify-center gap-2">
                                    <div
                                        class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-sage peer-checked:bg-sage">
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Manager</span>
                                </div>
                            </label>
                            <!-- Staf -->
                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="Staf" class="peer sr-only" {{ old('role') == 'staf' ? 'checked' : '' }}>
                                <div
                                    class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-sage peer-checked:bg-sage/10 transition-all flex items-center justify-center gap-2">
                                    <div
                                        class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-sage peer-checked:bg-sage">
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Staf</span>
                                </div>
                            </label>
                            <!-- Karyawan -->
                            <label class="cursor-pointer relative">
                                <input type="radio" name="role" value="Staf" class="peer sr-only" {{ old('role', 'karyawan') == 'karyawan' ? 'checked' : '' }}>
                                <div
                                    class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-sage peer-checked:bg-sage/10 transition-all flex items-center justify-center gap-2">
                                    <div
                                        class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-sage peer-checked:bg-sage">
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">Karyawan</span>
                                </div>
                            </label>
                        </div>
                        @error('role')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('password') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium"
                            placeholder="********" />
                        @error('password')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Konfirmasi
                            Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium"
                            placeholder="********" />
                    </div>

                <!-- Face Recognition Section -->
                <div class="mb-6" x-data="faceCapture()">
                    <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4">Data Wajah (Face Recognition)</h4>
                    
                    <div class="flex gap-4 mb-4">
                        <button type="button" @click="mode = 'camera'; startCamera()"
                            :class="mode === 'camera' ? 'bg-sage text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
                            class="px-4 py-2 rounded-xl font-bold transition-colors">
                            Ambil dari Kamera
                        </button>
                        <button type="button" @click="mode = 'upload'; stopCamera()"
                            :class="mode === 'upload' ? 'bg-sage text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
                            class="px-4 py-2 rounded-xl font-bold transition-colors">
                            Upload File
                        </button>
                    </div>

                    <!-- Upload Mode -->
                    <div x-show="mode === 'upload'" class="p-6 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50 dark:bg-slate-800/50">
                        <input type="file" name="face_photos[]" multiple accept="image/*"
                            class="w-full text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sage/10 file:text-sage hover:file:bg-sage/20" />
                        <p class="text-sm text-slate-500 mt-2">Pilih beberapa foto wajah untuk akurasi yang lebih baik.</p>
                    </div>

                    <!-- Camera Mode -->
                    <div x-show="mode === 'camera'" class="space-y-4">
                        <div class="relative rounded-2xl overflow-hidden bg-black aspect-video max-w-md mx-auto">
                            <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                            <canvas x-ref="canvas" style="display:none"></canvas>
                        </div>
                        
                        <div class="flex justify-center">
                            <button type="button" @click="takePhoto"
                                class="px-6 py-3 rounded-xl bg-sage text-white font-bold hover:opacity-90 transition-all shadow-lg shadow-sage/20 active:scale-95 flex items-center gap-2">
                                <span class="material-icons-round">photo_camera</span>
                                Ambil Foto
                            </button>
                        </div>

                        <!-- Thumbnails -->
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4 mt-4" x-show="photos.length > 0">
                            <template x-for="(photo, index) in photos" :key="index">
                                <div class="relative aspect-square rounded-xl overflow-hidden border-2 border-sage">
                                    <img :src="photo" class="w-full h-full object-cover" />
                                    <button type="button" @click="removePhoto(index)"
                                        class="absolute top-1 right-1 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center hover:bg-rose-600 transition-colors">
                                        <span class="material-icons-round text-sm">close</span>
                                    </button>
                                    <input type="hidden" name="base64_faces[]" :value="photo">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button type="button" onclick="history.back()"
                        class="px-6 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 dark:shadow-none active:scale-95">
                        Simpan Karyawan
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('faceCapture', () => ({
            mode: 'camera',
            stream: null,
            photos: [],
            
            init() {
                this.startCamera();
            },
            
            async startCamera() {
                if (this.mode !== 'camera') return;
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    this.$refs.video.srcObject = this.stream;
                } catch (err) {
                    console.error("Error accessing camera:", err);
                    alert("Tidak dapat mengakses kamera. Pastikan izin diberikan.");
                }
            },
            
            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                    this.stream = null;
                }
            },
            
            takePhoto() {
                const video = this.$refs.video;
                const canvas = this.$refs.canvas;
                
                if (!video.videoWidth) return;
                
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                this.photos.push(dataUrl);
            },
            
            removePhoto(index) {
                this.photos.splice(index, 1);
            }
        }));
    });
</script>
@endpush