<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Absensi Wajah
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- AREA KAMERA --}}
                <div class="border rounded p-4">
                    <h3 class="font-semibold mb-2">Kamera</h3>
                    <div class="h-64 bg-gray-200 flex items-center justify-center rounded">
                        Kamera OFF
                    </div>
                </div>

                {{-- AREA STATUS & AKSI --}}
                <div class="border rounded p-4">
                    <p class="mb-4">
                        Status:
                        <span class="text-red-600 font-semibold">
                            Belum dikenali
                        </span>
                    </p>

                    <button class="w-full mb-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Absen Masuk
                    </button>

                    <button class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Absen Keluar
                    </button>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>

