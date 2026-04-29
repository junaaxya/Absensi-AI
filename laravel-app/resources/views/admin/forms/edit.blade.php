@extends('layouts.admin')

@section('header-title', 'Edit Form')
@section('header-subtitle', 'Form Builder')

@section('content')
<div x-data="formBuilder({{ json_encode(collect($template->fields ?? [])->map(function($f, $i) {
    return [
        'id' => $i + 1,
        'type' => $f['type'] ?? 'text',
        'name' => $f['name'] ?? '',
        'label' => $f['label'] ?? '',
        'required' => $f['required'] ?? false,
        'options' => $f['options'] ?? [],
        'optionsText' => implode("\n", $f['options'] ?? []),
        'placeholder' => $f['placeholder'] ?? '',
        'validation_rules' => $f['validation_rules'] ?? '',
    ];
})->values()) }}, {{ $template->requires_approval ? 'true' : 'false' }}, {{ json_encode($template->approval_roles ?? []) }})" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.forms.index') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 flex items-center gap-1 mb-1">
                <span class="material-icons-round text-[16px]">arrow_back</span> Kembali
            </a>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit: {{ $template->name }}</h1>
        </div>
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

    <form action="{{ route('admin.forms.update', $template) }}" method="POST" @submit="prepareSubmit()">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Panel: Field Type Palette --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-3 text-sm">Tambah Field</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="type in fieldTypes" :key="type.value">
                            <button type="button" @click="addField(type.value)"
                                class="flex items-center gap-2 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-primary hover:bg-primary/10 transition text-left group">
                                <span class="material-icons-round text-[18px] text-slate-400 group-hover:text-primary" x-text="type.icon"></span>
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white" x-text="type.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-3 text-sm">Info Form</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Nama Form *</label>
                            <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Deskripsi</label>
                            <textarea name="description" rows="3"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm">{{ old('description', $template->description) }}</textarea>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="requires_approval" value="1" x-model="requiresApproval"
                                    class="rounded text-primary focus:ring-primary">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Perlu Approval</span>
                            </label>
                        </div>
                        <div x-show="requiresApproval" x-transition class="space-y-2">
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400">Role yang Bisa Approve</label>
                            <template x-for="role in availableRoles" :key="role">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" :value="role" x-model="approvalRoles"
                                        class="rounded text-primary focus:ring-primary">
                                    <span class="text-sm text-slate-700 dark:text-slate-300" x-text="role"></span>
                                </label>
                            </template>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}
                                    class="rounded text-primary focus:ring-primary">
                                <span class="text-sm text-slate-700 dark:text-slate-300">Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Center + Right: Form Preview & Field Editor --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white dark:bg-card-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">Preview Form</h3>
                        <span class="text-xs text-slate-400" x-text="fields.length + ' field'"></span>
                    </div>

                    <div x-show="fields.length === 0" class="py-12 text-center text-slate-400">
                        <span class="material-icons-round text-4xl mb-2 block">dynamic_form</span>
                        <p class="text-sm">Klik tipe field di panel kiri untuk menambahkan.</p>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(field, index) in fields" :key="field.id">
                            <div class="border rounded-xl p-4 transition-all"
                                :class="selectedField === index ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300'"
                                @click="selectedField = index">

                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="material-icons-round text-[16px] text-slate-400" x-text="getFieldIcon(field.type)"></span>
                                        <span class="text-sm font-bold text-slate-900 dark:text-white" x-text="field.label || 'Untitled'"></span>
                                        <span class="text-[10px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 rounded font-mono" x-text="field.type"></span>
                                        <span x-show="field.required" class="text-rose-500 text-xs font-bold">*</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click.stop="moveField(index, -1)" :disabled="index === 0"
                                            class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 transition">
                                            <span class="material-icons-round text-[16px] text-slate-400">arrow_upward</span>
                                        </button>
                                        <button type="button" @click.stop="moveField(index, 1)" :disabled="index === fields.length - 1"
                                            class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 transition">
                                            <span class="material-icons-round text-[16px] text-slate-400">arrow_downward</span>
                                        </button>
                                        <button type="button" @click.stop="removeField(index)"
                                            class="p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-400 hover:text-red-500 transition">
                                            <span class="material-icons-round text-[16px]">close</span>
                                        </button>
                                    </div>
                                </div>

                                <div x-show="selectedField === index" x-transition class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700 space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Label</label>
                                            <input type="text" x-model="field.label"
                                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Field</label>
                                            <input type="text" x-model="field.name"
                                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm font-mono">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Placeholder</label>
                                            <input type="text" x-model="field.placeholder"
                                                class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm">
                                        </div>
                                        <div class="flex items-end">
                                            <label class="flex items-center gap-2 cursor-pointer pb-2">
                                                <input type="checkbox" x-model="field.required"
                                                    class="rounded text-primary focus:ring-primary">
                                                <span class="text-sm text-slate-700 dark:text-slate-300">Wajib diisi</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div x-show="['select', 'radio'].includes(field.type)">
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Opsi (satu per baris)</label>
                                        <textarea x-model="field.optionsText" @input="field.options = field.optionsText.split('\n').filter(o => o.trim())" rows="3"
                                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 focus:border-primary focus:ring-primary text-sm font-mono"
                                            placeholder="Opsi 1&#10;Opsi 2&#10;Opsi 3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <input type="hidden" name="fields" :value="JSON.stringify(fields.map(f => ({name: f.name, label: f.label, type: f.type, required: f.required, options: f.options, placeholder: f.placeholder, validation_rules: f.validation_rules})))">
                <input type="hidden" name="approval_roles" :value="JSON.stringify(approvalRoles)">

                <div class="flex justify-end">
                    <button type="submit" :disabled="fields.length === 0"
                        class="px-6 py-3 bg-primary hover:bg-primary/80 text-slate-900 font-bold rounded-xl transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <span class="material-icons-round text-[18px]">save</span>
                        Perbarui Template
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function formBuilder(existingFields = [], requiresApproval = false, approvalRoles = []) {
    return {
        fields: existingFields,
        selectedField: null,
        requiresApproval: requiresApproval,
        approvalRoles: approvalRoles || [],
        availableRoles: ['Direktur', 'Vice President', 'Manager', 'Supervisor', 'Team Leader'],
        fieldTypes: [
            { value: 'text', label: 'Teks', icon: 'text_fields' },
            { value: 'textarea', label: 'Teks Panjang', icon: 'notes' },
            { value: 'number', label: 'Angka', icon: 'pin' },
            { value: 'date', label: 'Tanggal', icon: 'calendar_today' },
            { value: 'select', label: 'Dropdown', icon: 'arrow_drop_down_circle' },
            { value: 'radio', label: 'Pilihan', icon: 'radio_button_checked' },
            { value: 'checkbox', label: 'Centang', icon: 'check_box' },
            { value: 'file', label: 'File Upload', icon: 'attach_file' },
        ],

        addField(type) {
            const id = Date.now() + Math.random();
            const count = this.fields.filter(f => f.type === type).length + 1;
            const label = this.fieldTypes.find(t => t.value === type)?.label || type;
            this.fields.push({
                id,
                type,
                name: type + '_' + count,
                label: label + ' ' + count,
                required: false,
                options: [],
                optionsText: '',
                placeholder: '',
                validation_rules: '',
            });
            this.selectedField = this.fields.length - 1;
        },

        removeField(index) {
            this.fields.splice(index, 1);
            if (this.selectedField === index) this.selectedField = null;
            else if (this.selectedField > index) this.selectedField--;
        },

        moveField(index, direction) {
            const newIndex = index + direction;
            if (newIndex < 0 || newIndex >= this.fields.length) return;
            const temp = this.fields[index];
            this.fields[index] = this.fields[newIndex];
            this.fields[newIndex] = temp;
            this.selectedField = newIndex;
        },

        getFieldIcon(type) {
            return this.fieldTypes.find(t => t.value === type)?.icon || 'text_fields';
        },

        prepareSubmit() {
            // Fields are already bound via hidden input
        }
    };
}
</script>
@endpush
@endsection
