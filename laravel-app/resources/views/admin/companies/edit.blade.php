@extends('layouts.admin')

@section('header-title', 'Edit Perusahaan')
@section('header-subtitle', 'Perbarui informasi perusahaan')

@section('content')

    <div class="max-w-3xl mx-auto">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white">Edit: {{ $company->name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Perbarui informasi perusahaan di bawah ini.</p>
            </div>
            <a href="{{ route('admin.companies.show', $company) }}"
                class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-icons-round">arrow_back</span>
            </a>
        </div>

        <form action="{{ route('admin.companies.update', $company) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
                <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sage">business</span> Informasi Perusahaan
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Logo Perusahaan</label>
                        @if($company->logo)
                            <div class="mb-3 flex items-center gap-3">
                                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}"
                                    class="w-16 h-16 rounded-xl object-cover border border-slate-200 dark:border-slate-700" />
                                <span class="text-xs text-slate-500">Logo saat ini</span>
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sage/10 file:text-sage hover:file:bg-sage/20" />
                        @error('logo')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Perusahaan <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('name') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400" />
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kode Perusahaan <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $company->code) }}" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('code') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400 uppercase" />
                        @error('code')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alamat</label>
                        <textarea name="address" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('address') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400">{{ old('address', $company->address) }}</textarea>
                        @error('address')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('phone') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400" />
                        @error('phone')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('email') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400" />
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Website</label>
                        <input type="url" name="website" value="{{ old('website', $company->website) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('website') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400" />
                        @error('website')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">NPWP</label>
                        <input type="text" name="npwp" value="{{ old('npwp', $company->npwp) }}"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('npwp') ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200 dark:border-slate-700' }} rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium placeholder:text-slate-400" />
                        @error('npwp')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-card-dark rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
                <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-sage">account_tree</span> Hierarki & Status
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Perusahaan Induk</label>
                        <select name="parent_id"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sage focus:border-sage transition-all text-slate-900 dark:text-white font-medium">
                            <option value="">-- Tidak Ada (Perusahaan Utama) --</option>
                            @foreach($parentCompanies as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $company->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }} ({{ $parent->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_headquarters" value="1" {{ old('is_headquarters', $company->is_headquarters) ? 'checked' : '' }}
                                class="w-5 h-5 rounded-lg border-slate-300 text-sage focus:ring-sage" />
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Kantor Pusat (Headquarters)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $company->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 rounded-lg border-slate-300 text-sage focus:ring-sage" />
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.companies.show', $company) }}"
                    class="px-6 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 dark:shadow-none active:scale-95">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

@endsection
