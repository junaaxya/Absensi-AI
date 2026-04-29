@extends('layouts.admin')

@section('header-title', 'Kategori Tiket')
@section('header-subtitle', 'Kelola kategori tiket layanan')

@section('content')
<div x-data="{ showCreate: false, editId: null }" class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Kategori Tiket</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola kategori dan SLA tiket layanan.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.tickets.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                <span class="material-icons-round text-base">arrow_back</span>
                Kembali
            </a>
            <button @click="showCreate = !showCreate"
                class="flex items-center gap-2 px-4 py-2.5 bg-sage text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all">
                <span class="material-icons-round text-base">add</span>
                Tambah Kategori
            </button>
        </div>
    </div>

    <div x-show="showCreate" x-transition class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Tambah Kategori Baru</h3>
        <form method="POST" action="{{ route('admin.tickets.categories') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage" />
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kode</label>
                <input type="text" name="code" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage" placeholder="e.g. IT, HR, GA" />
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Deskripsi</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage resize-y"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Prioritas Default</label>
                <select name="default_priority" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">SLA Response (jam)</label>
                    <input type="number" name="sla_response_hours" value="24" min="1" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">SLA Resolution (jam)</label>
                    <input type="number" name="sla_resolution_hours" value="72" min="1" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-sage" />
                </div>
            </div>
            <div class="md:col-span-2 flex justify-end gap-3">
                <button type="button" @click="showCreate = false" class="px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-xl font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-sage text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all">Simpan</button>
            </div>
        </form>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-4 py-3 rounded-2xl">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Kode</th>
                        <th class="px-6 py-4">Prioritas Default</th>
                        <th class="px-6 py-4">SLA Response</th>
                        <th class="px-6 py-4">SLA Resolution</th>
                        <th class="px-6 py-4">Tiket</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($categories as $cat)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4 font-semibold text-slate-800 dark:text-white">
                                <template x-if="editId === {{ $cat->id }}">
                                    <form method="POST" action="{{ url('/admin/tickets/categories/' . $cat->id) }}" class="flex items-center gap-2" id="edit-form-{{ $cat->id }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $cat->name }}" required class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm w-32" />
                                        <input type="hidden" name="code" value="{{ $cat->code }}" />
                                        <input type="hidden" name="default_priority" value="{{ $cat->default_priority }}" />
                                        <input type="hidden" name="sla_response_hours" value="{{ $cat->sla_response_hours }}" />
                                        <input type="hidden" name="sla_resolution_hours" value="{{ $cat->sla_resolution_hours }}" />
                                        <input type="hidden" name="is_active" value="{{ $cat->is_active ? '1' : '0' }}" />
                                        <button type="submit" class="text-green-600 hover:text-green-700"><span class="material-icons-round text-base">check</span></button>
                                        <button type="button" @click="editId = null" class="text-slate-400 hover:text-slate-600"><span class="material-icons-round text-base">close</span></button>
                                    </form>
                                </template>
                                <template x-if="editId !== {{ $cat->id }}">
                                    <span>{{ $cat->name }}</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400">{{ $cat->code }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $pClass = match($cat->default_priority) {
                                        'critical' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
                                        'high' => 'bg-peach text-slate-900',
                                        'medium' => 'bg-sky/30 text-slate-900',
                                        'low' => 'bg-sage/50 text-slate-900',
                                        default => 'bg-slate-100 text-slate-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $pClass }}">{{ ucfirst($cat->default_priority) }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $cat->sla_response_hours }}h</td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $cat->sla_resolution_hours }}h</td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">{{ $cat->tickets_count }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $cat->is_active ? 'bg-sage/50 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <button @click="editId = {{ $cat->id }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                        <span class="material-icons-round text-base">edit</span>
                                    </button>
                                    @if($cat->tickets_count === 0)
                                        <form method="POST" action="{{ url('/admin/tickets/categories/' . $cat->id) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">
                                                <span class="material-icons-round text-base">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <span class="material-icons-round text-4xl mb-2 block">category</span>
                                <p class="font-medium">Belum ada kategori</p>
                                <p class="text-sm">Tambahkan kategori tiket untuk memulai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
