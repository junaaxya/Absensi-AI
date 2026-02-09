@extends('layouts.absensi')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <x-pastel-card header="Ubah Password">
        <div class="mb-4 text-sm text-text-secondary">
            Pastikan password Anda aman dan tidak mudah ditebak.
        </div>
        
        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label for="current_password" value="Password Lama" />
                <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
            </div>

            <div>
                <x-input-label for="password" value="Password Baru" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('password')" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
            </div>

            <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t border-neutral-stone/30">
                <a href="{{ route('profile.edit') }}" class="text-sm text-text-secondary hover:text-text-primary underline">
                    Batal
                </a>
                <x-primary-button>
                    {{ __('Simpan Password') }}
                </x-primary-button>
            </div>
        </form>
    </x-pastel-card>
</div>
@endsection
