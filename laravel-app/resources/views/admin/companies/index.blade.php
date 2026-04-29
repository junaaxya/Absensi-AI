@extends('layouts.admin')

@section('header-title', 'Manajemen Perusahaan')
@section('header-subtitle', 'Kelola perusahaan dan cabang organisasi Anda')

@section('content')

    <div x-data="{ showDeleteModal: false, deleteData: {} }">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <form method="GET" action="{{ route('admin.companies.index') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative">
                    <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari perusahaan..."
                        class="w-full sm:w-64 pl-10 pr-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white shadow-sm" />
                </div>
                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white shadow-sm">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </form>
            <a href="{{ route('admin.companies.create') }}"
                class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95 shrink-0">
                <span class="material-icons-round text-lg">add</span>
                Tambah Perusahaan
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($companies as $company)
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-xl transition-all relative group">

                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3 flex-1 min-w-0 pr-4">
                            @if($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}"
                                    class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0" />
                            @else
                                <div class="w-12 h-12 rounded-xl bg-lavender/30 dark:bg-lavender/10 flex items-center justify-center shrink-0">
                                    <span class="material-icons-round text-lavender-600 dark:text-lavender-400 text-xl">business</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('admin.companies.show', $company) }}" class="font-bold text-slate-900 dark:text-white text-lg leading-tight hover:text-sky-600 transition-colors line-clamp-1">
                                    {{ $company->name }}
                                </a>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $company->code }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <a href="{{ route('admin.companies.edit', $company) }}"
                                class="p-2 rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <span class="material-icons-round text-lg">edit</span>
                            </a>
                            <button @click="deleteData = { id: {{ $company->id }}, name: '{{ addslashes($company->name) }}' }; showDeleteModal = true"
                                class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-4">
                        @if($company->is_headquarters)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                Kantor Pusat
                            </span>
                        @endif
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide {{ $company->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ $company->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    @if($company->address)
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">
                            <span class="material-icons-round text-sm align-middle mr-1">location_on</span>
                            {{ $company->address }}
                        </p>
                    @endif

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-1.5 text-sm text-slate-500">
                            <span class="material-icons-round text-base">store</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $company->branches_count }}</span>
                            <span>Cabang</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-sm text-slate-500">
                            <span class="material-icons-round text-base">people</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $company->employees_count }}</span>
                            <span>Karyawan</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-icons-round text-slate-400 text-4xl">business</span>
                    </div>
                    <h3 class="text-slate-900 dark:text-white font-bold text-lg">Belum ada perusahaan</h3>
                    <p class="text-slate-500 text-sm mt-1">Tambahkan perusahaan pertama untuk mulai mengelola organisasi.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $companies->links() }}
        </div>

        {{-- Delete Modal --}}
        <div x-show="showDeleteModal" style="display: none;" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 w-full max-w-sm shadow-2xl text-center" @click.outside="showDeleteModal = false">
                <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-icons-round text-rose-500 text-3xl">delete_forever</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Hapus Perusahaan?</h3>
                <p class="text-slate-500 text-sm mb-6">
                    Anda akan menghapus perusahaan "<span x-text="deleteData.name" class="font-bold text-slate-700 dark:text-slate-300"></span>". Data akan di-soft delete.
                </p>
                <form :action="`{{ route('admin.companies.index') }}/${deleteData.id}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-3 justify-center">
                        <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-rose-500 text-white rounded-xl font-bold hover:bg-rose-600 transition-all shadow-lg shadow-rose-500/30 active:scale-95">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
