<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Dinas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Error validasi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('dinas.store') }}">
                        @csrf

                        {{-- Nama --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Dinas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 @error('name') border-red-400 @enderror"
                                placeholder="Contoh: Dinas Kelautan dan Perikanan Banyuwangi"
                                required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 @error('email') border-red-400 @enderror"
                                placeholder="email@dinas.go.id"
                                required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Telepon --}}
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Telepon
                            </label>
                            <input type="text" id="phone" name="phone"
                                value="{{ old('phone') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                                placeholder="08xxxxxxxx">
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-4">
                            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat
                            </label>
                            <textarea id="alamat" name="alamat" rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                                placeholder="Alamat lengkap kantor dinas">{{ old('alamat') }}</textarea>
                        </div>

                        {{-- Password --}}
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 @error('password') border-red-400 @enderror"
                                required>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                                required>
                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                Simpan
                            </button>
                            <a href="{{ route('dinas.index') }}"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded">
                                Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>