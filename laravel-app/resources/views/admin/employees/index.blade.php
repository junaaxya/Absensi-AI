@extends('layouts.admin')

@section('header-title', 'Data Karyawan')
@section('header-subtitle', 'Kelola Data Perangkat Desa')

@section('content')

    <!-- ACTIONS -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <div class="relative w-full md:w-auto flex-1 max-w-md">
            <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input type="text" placeholder="Cari karyawan..."
                class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-card-dark border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-sage text-sm dark:text-white shadow-sm" />
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <button onclick="window.location='{{ route('employees.create') }}'"
                class="flex-1 md:flex-none bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-4 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 hover:opacity-90 transition-all shadow-lg shadow-slate-900/20 active:scale-95">
                <span class="material-icons-round text-lg">add</span>
                Tambah
            </button>
            <button
                class="flex-1 md:flex-none bg-white dark:bg-card-dark text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 px-4 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95">
                <span class="material-icons-round text-lg">filter_list</span>
                Filter
            </button>
        </div>
    </div>

    <!-- EMPLOYEES GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- New Employee Card (Mobile First) -->
        @foreach($employees as $employee)
            <div
                class="group bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-800 p-6 hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-slate-900/50 hover:border-sage transition-all duration-300 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-sage/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110">
                </div>

                {{-- <div class="relative mb-6">
                    <div class="w-20 h-20 rounded-2xl bg-slate-100 mx-auto overflow-hidden shadow-md">
                        <img src="https://i.pravatar.cc/150?u={{ $employee->id }}" alt="User"
                            class="w-full h-full object-cover" />
                    </div>
                    <div class="absolute -bottom-2 -right-2 bg-green-500 border-2 border-white w-5 h-5 rounded-full"
                        title="Active"></div>
                </div> --}}

                <div class="relative mb-6">
                    <div class="w-20 h-20 rounded-2xl bg-slate-100 mx-auto overflow-hidden shadow-md">
                        @if($employee->foto)
                            <img src="{{ asset('storage/' . $employee->foto) }}" alt="{{ $employee->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-slate-200 text-slate-400">
                                <span class="material-icons-round text-4xl">person</span>
                            </div>
                        @endif
                    </div>
                </div>


                <div class="text-center mb-6">
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-1">{{ $employee->name }}</h3>
                    <p class="text-sm text-sage font-medium uppercase tracking-wide">{{ $employee->jabatan }}</p>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <span class="material-icons-round text-lg text-slate-300">email</span>
                        <span class="truncate">{{ $employee->email }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <span class="material-icons-round text-lg text-slate-300">phone</span>
                        <span>{{ $employee->no_hp }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <span class="material-icons-round text-lg text-slate-300">badge</span>
                        <span>NIP: {{ $employee->nip ?? '-' }}</span>
                    </div>
                </div>

                <div class="flex gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button onclick="window.location='{{ route('employees.edit', $employee->id) }}'"
                        class="flex-1 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        Edit
                    </button>
                    <button onclick="confirmDelete('{{ $employee->id }}', '{{ $employee->name }}')"
                        class="flex-1 py-2 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-500 font-bold text-xs hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                        Hapus
                    </button>

                    <form id="delete-form-{{ $employee->id }}" action="{{ route('employees.destroy', $employee->id) }}"
                        method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- PAGINATION -->
    <div class="mt-8">
        {{ $employees->links() }}
    </div>

@endsection

@push('scripts')
    <script>
        function confirmDelete(id, name) {
            if (confirm('Apakah Anda yakin ingin menghapus karyawan ' + name + '?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endpush