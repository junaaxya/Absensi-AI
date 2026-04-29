@extends('layouts.absensi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('forms.index') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $template->name }}</h1>
        @if($template->description)
            <p class="text-slate-500 dark:text-slate-400 mt-1">{{ $template->description }}</p>
        @endif
    </div>

    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4">
            <ul class="text-sm text-red-600 dark:text-red-400 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('forms.store', $template) }}" method="POST" enctype="multipart/form-data"
        class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-6 space-y-5">
        @csrf

        @foreach($template->fields ?? [] as $field)
            <div>
                <label for="field_{{ $field['name'] }}" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    {{ $field['label'] ?? $field['name'] }}
                    @if(!empty($field['required']))
                        <span class="text-rose-500">*</span>
                    @endif
                </label>

                @switch($field['type'] ?? 'text')
                    @case('textarea')
                        <textarea name="field_{{ $field['name'] }}" id="field_{{ $field['name'] }}" rows="4"
                            placeholder="{{ $field['placeholder'] ?? '' }}"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm"
                            {{ !empty($field['required']) ? 'required' : '' }}>{{ old('field_' . $field['name']) }}</textarea>
                        @break

                    @case('number')
                        <input type="number" name="field_{{ $field['name'] }}" id="field_{{ $field['name'] }}"
                            value="{{ old('field_' . $field['name']) }}"
                            placeholder="{{ $field['placeholder'] ?? '' }}"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm"
                            {{ !empty($field['required']) ? 'required' : '' }}>
                        @break

                    @case('date')
                        <input type="date" name="field_{{ $field['name'] }}" id="field_{{ $field['name'] }}"
                            value="{{ old('field_' . $field['name']) }}"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm"
                            {{ !empty($field['required']) ? 'required' : '' }}>
                        @break

                    @case('select')
                        <select name="field_{{ $field['name'] }}" id="field_{{ $field['name'] }}"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm"
                            {{ !empty($field['required']) ? 'required' : '' }}>
                            <option value="">-- Pilih --</option>
                            @foreach($field['options'] ?? [] as $option)
                                <option value="{{ $option }}" {{ old('field_' . $field['name']) === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        @break

                    @case('radio')
                        <div class="space-y-2 mt-1">
                            @foreach($field['options'] ?? [] as $option)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="field_{{ $field['name'] }}" value="{{ $option }}"
                                        class="text-primary focus:ring-primary"
                                        {{ old('field_' . $field['name']) === $option ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                        @break

                    @case('checkbox')
                        <label class="flex items-center gap-2 cursor-pointer mt-1">
                            <input type="checkbox" name="field_{{ $field['name'] }}" value="1"
                                class="rounded text-primary focus:ring-primary"
                                {{ old('field_' . $field['name']) ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $field['placeholder'] ?? 'Ya' }}</span>
                        </label>
                        @break

                    @case('file')
                        <input type="file" name="field_{{ $field['name'] }}" id="field_{{ $field['name'] }}"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-primary/20 file:text-slate-700 hover:file:bg-primary/30"
                            {{ !empty($field['required']) ? 'required' : '' }}>
                        @break

                    @default
                        <input type="text" name="field_{{ $field['name'] }}" id="field_{{ $field['name'] }}"
                            value="{{ old('field_' . $field['name']) }}"
                            placeholder="{{ $field['placeholder'] ?? '' }}"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm"
                            {{ !empty($field['required']) ? 'required' : '' }}>
                @endswitch

                @error('field_' . $field['name'])
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
            <button type="submit"
                class="w-full px-6 py-3 bg-primary hover:bg-primary/80 text-slate-900 font-bold rounded-xl transition shadow-sm">
                Kirim Form
            </button>
        </div>
    </form>
</div>
@endsection
