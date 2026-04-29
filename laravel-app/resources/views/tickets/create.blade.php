@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="mb-6">
        <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Tiket Saya
        </a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">Buat Tiket Baru</h1>
        <p class="text-sm text-slate-500 mt-1">Ajukan permintaan layanan atau laporkan masalah.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form method="POST" action="{{ route('tickets.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="ticket_category_id" class="block text-sm font-bold text-slate-700 mb-1">Kategori</label>
                <select name="ticket_category_id" id="ticket_category_id" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-sage focus:border-sage">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" data-priority="{{ $cat->default_priority }}" {{ old('ticket_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="subject" class="block text-sm font-bold text-slate-700 mb-1">Subjek</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required maxlength="255"
                    placeholder="Ringkasan singkat masalah atau permintaan"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-sage focus:border-sage" />
            </div>

            <div>
                <label for="description" class="block text-sm font-bold text-slate-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="5" required maxlength="5000"
                    placeholder="Jelaskan detail masalah atau permintaan Anda..."
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-sage focus:border-sage resize-y">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="priority" class="block text-sm font-bold text-slate-700 mb-1">Prioritas</label>
                <select name="priority" id="priority" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-sage focus:border-sage">
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('tickets.index') }}"
                    class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-sage text-slate-900 rounded-xl font-bold text-sm hover:brightness-95 transition-all shadow-sm">
                    Kirim Tiket
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('ticket_category_id')?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const priority = selected.dataset.priority;
        if (priority) {
            document.getElementById('priority').value = priority;
        }
    });
</script>
@endsection
