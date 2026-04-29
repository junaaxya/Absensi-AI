@extends('layouts.admin')

@section('header-title', 'Edit Aset')
@section('header-subtitle', 'Perbarui informasi aset')

@section('content')

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.assets.show', $asset) }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium">
                <span class="material-icons-round text-lg">arrow_back</span>
                Kembali ke Detail Aset
            </a>
        </div>

        <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6 md:p-8">
            <form method="POST" action="{{ route('admin.assets.update', $asset) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Aset <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                        @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kode Aset <span class="text-rose-500">*</span></label>
                        <input type="text" name="asset_code" value="{{ old('asset_code', $asset->asset_code) }}" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white font-mono" />
                        @error('asset_code') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori <span class="text-rose-500">*</span></label>
                        <select name="asset_category_id" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('asset_category_id', $asset->asset_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('asset_category_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nomor Seri</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}"
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                        @error('serial_number') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kondisi <span class="text-rose-500">*</span></label>
                        <select name="condition" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">
                            <option value="baik" {{ old('condition', $asset->condition) === 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak_ringan" {{ old('condition', $asset->condition) === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak_berat" {{ old('condition', $asset->condition) === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            <option value="hilang" {{ old('condition', $asset->condition) === 'hilang' ? 'selected' : '' }}>Hilang</option>
                            <option value="dihapuskan" {{ old('condition', $asset->condition) === 'dihapuskan' ? 'selected' : '' }}>Dihapuskan</option>
                        </select>
                        @error('condition') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date->format('Y-m-d')) }}" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                        @error('purchase_date') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Harga Pembelian <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                            <input type="number" name="purchase_price" value="{{ old('purchase_price', $asset->purchase_price) }}" required min="0" step="0.01"
                                class="w-full pl-12 pr-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                        </div>
                        @error('purchase_price') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nilai Saat Ini</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                            <input type="number" name="current_value" value="{{ old('current_value', $asset->current_value) }}" min="0" step="0.01"
                                class="w-full pl-12 pr-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                        </div>
                        @error('current_value') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Lokasi</label>
                        <input type="text" name="location" value="{{ old('location', $asset->location) }}"
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white" />
                        @error('location') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">{{ old('description', $asset->description) }}</textarea>
                        @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan</label>
                        <textarea name="notes" rows="2"
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white">{{ old('notes', $asset->notes) }}</textarea>
                        @error('notes') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Foto Aset</label>
                        @if($asset->photo)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $asset->photo) }}" alt="{{ $asset->name }}" class="w-32 h-32 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                            </div>
                        @endif
                        <input type="file" name="photo" accept="image/*"
                            class="w-full px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-800 dark:file:text-slate-300" />
                        @error('photo') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('admin.assets.show', $asset) }}"
                        class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95">
                        Perbarui Aset
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
