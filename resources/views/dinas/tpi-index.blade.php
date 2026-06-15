<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(Auth::user()->isDinas())
                {{ __('TPI Saya') }}
            @else
                {{ __('Manajemen TPI') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <a href="{{ route('tpi.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah TPI
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama TPI</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                                    {{-- Kolom dinas hanya tampil untuk admin --}}
                                    @if(Auth::user()->isAdmin())
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dinas</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($tpiList as $tpi)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ $tpi->name }}</div>
                                            @if ($tpi->alamat)
                                                <div class="text-sm text-gray-500">{{ Str::limit($tpi->alamat, 40) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $tpi->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $tpi->phone ?? '-' }}
                                        </td>
                                        {{-- Nama dinas hanya untuk admin --}}
                                        @if(Auth::user()->isAdmin())
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                @if($tpi->dinas)
                                                    <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-0.5 rounded">
                                                        {{ $tpi->dinas->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 italic">Tidak ada</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="{{ $tpi->status ? 'bg-green-500' : 'bg-red-500' }} text-white px-2 py-1 rounded text-xs">
                                                {{ $tpi->status ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('tpi.edit', $tpi->id) }}"
                                                    class="text-blue-500 hover:text-blue-700 text-sm">Edit</a>

                                                <form action="{{ route('tpi.toggle-status', $tpi->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="text-sm {{ $tpi->status ? 'text-yellow-500 hover:text-yellow-700' : 'text-green-500 hover:text-green-700' }}"
                                                        onclick="return confirm('Yakin ubah status TPI ini?')">
                                                        {{ $tpi->status ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->isAdmin() ? 7 : 6 }}" class="px-6 py-8 text-center text-gray-400">
                                            Belum ada data TPI.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($tpiList->hasPages())
                        <div class="mt-4">
                            {{ $tpiList->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>