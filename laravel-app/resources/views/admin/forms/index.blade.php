@extends('layouts.admin')

@section('header-title', 'Form Internal')
@section('header-subtitle', 'Kelola template form internal')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Template Form</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Buat dan kelola form internal untuk karyawan.</p>
        </div>
        <a href="{{ route('admin.forms.create') }}"
            class="px-4 py-2.5 bg-primary hover:bg-primary/80 text-slate-900 font-bold rounded-xl transition shadow-sm flex items-center gap-2 text-sm">
            <span class="material-icons-round text-[18px]">add</span>
            Buat Form Baru
        </a>
    </div>

    <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Form</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Fields</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Submissions</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Approval</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($templates as $template)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $template->name }}</p>
                                @if($template->description)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate max-w-xs">{{ $template->description }}</p>
                                @endif
                                <p class="text-[10px] text-slate-400 mt-0.5">Oleh: {{ $template->creator->name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-slate-600 dark:text-slate-400">
                                {{ count($template->fields ?? []) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.forms.submissions', $template) }}"
                                    class="text-sm font-bold text-primary hover:underline">{{ $template->submissions_count }}</a>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($template->requires_approval)
                                    <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-peach/30 text-orange-700 dark:text-orange-300 rounded-lg">Ya</span>
                                @else
                                    <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-500 rounded-lg">Tidak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($template->is_active)
                                    <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-primary/30 text-green-700 dark:text-green-300 rounded-lg">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-500 rounded-lg">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.forms.edit', $template) }}"
                                        class="p-1.5 rounded-lg hover:bg-sky/20 text-slate-500 hover:text-blue-600 transition" title="Edit">
                                        <span class="material-icons-round text-[18px]">edit</span>
                                    </a>
                                    <a href="{{ route('admin.forms.submissions', $template) }}"
                                        class="p-1.5 rounded-lg hover:bg-lavender/20 text-slate-500 hover:text-purple-600 transition" title="Submissions">
                                        <span class="material-icons-round text-[18px]">list_alt</span>
                                    </a>
                                    <a href="{{ route('admin.forms.export', $template) }}"
                                        class="p-1.5 rounded-lg hover:bg-primary/20 text-slate-500 hover:text-green-600 transition" title="Export CSV">
                                        <span class="material-icons-round text-[18px]">download</span>
                                    </a>
                                    <form action="{{ route('admin.forms.destroy', $template) }}" method="POST"
                                        onsubmit="return confirm('Hapus template form ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 rounded-lg hover:bg-peach/20 text-slate-500 hover:text-red-600 transition" title="Hapus">
                                            <span class="material-icons-round text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <span class="material-icons-round text-4xl mb-2 block">dynamic_form</span>
                                Belum ada template form. Klik "Buat Form Baru" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $templates->links() }}
    </div>
</div>
@endsection
