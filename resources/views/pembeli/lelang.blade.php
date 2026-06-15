<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Halaman Lelang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="mb-4 font-bold text-center">Lelang Ikan Sedang Berlangsung</h3>

                    {{-- ── Panel Pencarian & Filter ─────────────────────────── --}}
                    <form method="GET" action="{{ route('lelang.index') }}" id="filterForm">
                        <div class="card border mb-4" style="border-radius: 10px;">
                            <div class="card-body pb-2">

                                {{-- Baris 1: Search + Sort + Tombol --}}
                                <div class="row g-3 align-items-end mb-2">

                                    {{-- Search --}}
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">
                                            <i class="bi bi-search me-1"></i>Cari Jenis Ikan
                                        </label>
                                        <input
                                            type="text"
                                            name="search"
                                            id="searchInput"
                                            class="form-control"
                                            placeholder="Contoh: Tuna, Tongkol, Cakalang…"
                                            value="{{ request('search') }}"
                                            autocomplete="off"
                                            list="jenisIkanSuggestions"
                                        >
                                        <datalist id="jenisIkanSuggestions">
                                            @foreach($jenisIkanList as $jenis)
                                                <option value="{{ $jenis }}">
                                            @endforeach
                                        </datalist>
                                    </div>

                                    {{-- Sort --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">
                                            <i class="bi bi-sort-down me-1"></i>Urutkan
                                        </label>
                                        <select name="sort" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                            <option value="latest"     {{ request('sort', 'latest') == 'latest'     ? 'selected' : '' }}>Terbaru</option>
                                            <option value="waktu_asc"  {{ request('sort')           == 'waktu_asc'  ? 'selected' : '' }}>Waktu Selesai Terdekat</option>
                                            <option value="harga_asc"  {{ request('sort')           == 'harga_asc'  ? 'selected' : '' }}>Harga Terendah</option>
                                            <option value="harga_desc" {{ request('sort')           == 'harga_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                                            <option value="berat_asc"  {{ request('sort')           == 'berat_asc'  ? 'selected' : '' }}>Berat Teringan</option>
                                            <option value="berat_desc" {{ request('sort')           == 'berat_desc' ? 'selected' : '' }}>Berat Terberat</option>
                                        </select>
                                    </div>

                                    {{-- Tombol Cari + Reset --}}
                                    <div class="col-md-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <i class="bi bi-search me-1"></i>Cari
                                        </button>
                                        @if(request()->hasAny(['search', 'harga_min', 'harga_max', 'berat_min', 'sort']))
                                            <a href="{{ route('lelang.index') }}" class="btn btn-outline-secondary" title="Reset filter">
                                                <i class="bi bi-x-lg"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- Baris 2: Filter Lanjutan (collapsible) --}}
                                <div>
                                    <button
                                        class="btn btn-sm btn-link text-decoration-none ps-0 text-secondary"
                                        type="button"
                                        id="btnFilterLanjutan"
                                        onclick="toggleFilterLanjutan()"
                                    >
                                        <i class="bi bi-sliders me-1"></i>
                                        Filter Lanjutan
                                        <i id="iconChevron" class="bi bi-chevron-down ms-1" style="font-size:.75rem; transition: transform .2s;"></i>
                                    </button>

                                    <div
                                        id="filterLanjutan"
                                        style="{{ request()->hasAny(['harga_min', 'harga_max', 'berat_min']) ? '' : 'display:none;' }}"
                                    >
                                        <div class="row g-3 mt-1 pb-2">

                                            {{-- Harga Awal Min --}}
                                            <div class="col-sm-4">
                                                <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">
                                                    Harga Awal Min (Rp)
                                                </label>
                                                <input
                                                    type="number"
                                                    name="harga_min"
                                                    class="form-control"
                                                    placeholder="0"
                                                    min="0"
                                                    value="{{ request('harga_min') }}"
                                                >
                                            </div>

                                            {{-- Harga Awal Max --}}
                                            <div class="col-sm-4">
                                                <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">
                                                    Harga Awal Max (Rp)
                                                </label>
                                                <input
                                                    type="number"
                                                    name="harga_max"
                                                    class="form-control"
                                                    placeholder="Tidak terbatas"
                                                    min="0"
                                                    value="{{ request('harga_max') }}"
                                                >
                                            </div>

                                            {{-- Berat Min --}}
                                            <div class="col-sm-4">
                                                <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">
                                                    Berat Minimum (kg)
                                                </label>
                                                <input
                                                    type="number"
                                                    name="berat_min"
                                                    class="form-control"
                                                    placeholder="0"
                                                    min="0"
                                                    step="0.1"
                                                    value="{{ request('berat_min') }}"
                                                >
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>

                    {{-- ── Ringkasan Hasil ─────────────────────────────────── --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <p class="text-muted mb-0" style="font-size:.9rem;">
                            Menampilkan <strong>{{ $produk->count() }}</strong> produk lelang
                            @if(request('search'))
                                untuk "<strong>{{ request('search') }}</strong>"
                            @endif
                        </p>

                        {{-- Badge filter aktif --}}
                        <div class="d-flex flex-wrap gap-1">
                            @if(request('harga_min'))
                                <span class="badge bg-light text-dark border">
                                    Harga ≥ Rp{{ number_format(request('harga_min'), 0, ',', '.') }}
                                </span>
                            @endif
                            @if(request('harga_max'))
                                <span class="badge bg-light text-dark border">
                                    Harga ≤ Rp{{ number_format(request('harga_max'), 0, ',', '.') }}
                                </span>
                            @endif
                            @if(request('berat_min'))
                                <span class="badge bg-light text-dark border">
                                    Berat ≥ {{ request('berat_min') }} kg
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- ── Daftar Produk ────────────────────────────────────── --}}
                    <div class="row justify-content-center w-100 gx-4 gy-4">
                        @forelse($produk as $item)
                            <div class="col-md-6 col-lg-4 mb-4 d-flex align-items-stretch">
                                <div class="card h-100 shadow border-0 w-100">

                                    @if($item->foto)
                                        <img src="{{ asset('storage/' . $item->foto) }}"
                                             class="card-img-top img-fluid rounded-top"
                                             alt="{{ $item->jenis_ikan }}"
                                             style="height:250px; width:100%; object-fit:cover;">
                                    @else
                                        <img src="https://via.placeholder.com/400x250?text=No+Image"
                                             class="card-img-top img-fluid rounded-top"
                                             alt="No Image"
                                             style="height:250px; width:100%; object-fit:cover;">
                                    @endif

                                    <div class="card-body">
                                        <h5 class="card-title"><strong>Nama Ikan:</strong> {{ $item->jenis_ikan }}</h5>
                                        <p class="mb-1"><strong>Berat:</strong> {{ $item->berat }} kg</p>
                                        <p class="mb-1"><strong>Harga Awal:</strong> Rp{{ number_format($item->harga_awal, 0, ',', '.') }}</p>
                                        <p class="mb-1">
                                            <strong>Waktu Selesai:</strong>
                                            @if($item->waktu_selesai)
                                                <span id="countdown-{{ $item->id }}"
                                                      data-timestamp="{{ $item->waktu_selesai->timestamp * 1000 }}">
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </p>
                                        <p class="mb-1"><strong>Deskripsi:</strong> {{ Str::limit($item->deskripsi, 100) }}</p>
                                    </div>

                                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                                        <a href="{{ route('lelang.show', $item->id) }}" class="btn btn-primary">
                                            Ikuti Lelang
                                        </a>
                                        @if(auth()->check() && auth()->user()->isAdmin())
                                            @if($item->status_lelang == 'dibuka')
                                                <form action="{{ route('lelang.selesai', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-danger"
                                                            onclick="return confirm('Apakah Anda yakin ingin mengakhiri lelang produk ini?')">
                                                        Selesai Lelang
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                @if(request()->hasAny(['search', 'harga_min', 'harga_max', 'berat_min']))
                                    <div class="alert alert-warning">
                                        <i class="bi bi-search me-2"></i>
                                        Tidak ada produk yang cocok dengan filter yang dipilih.
                                        <a href="{{ route('lelang.index') }}" class="alert-link ms-1">Hapus filter</a>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        Belum ada produk yang tersedia untuk dilelang saat ini.
                                    </div>
                                @endif
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // ── Toggle Filter Lanjutan ────────────────────────────────
        function toggleFilterLanjutan() {
            const panel   = document.getElementById('filterLanjutan');
            const chevron = document.getElementById('iconChevron');
            const isOpen  = panel.style.display !== 'none';

            panel.style.display     = isOpen ? 'none' : 'block';
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        // Set state awal chevron jika filter lanjutan sudah terbuka
        (function () {
            const panel   = document.getElementById('filterLanjutan');
            const chevron = document.getElementById('iconChevron');
            if (panel && panel.style.display !== 'none') {
                chevron.style.transform = 'rotate(180deg)';
            }
        })();

        // ── Countdown timer (update tiap detik) ───────────────────
        function updateCountdown() {
            document.querySelectorAll('[id^="countdown-"]').forEach(el => {
                const endTime    = parseInt(el.getAttribute('data-timestamp'));
                const difference = endTime - Date.now();

                if (difference > 0) {
                    const days    = Math.floor(difference / (1000 * 60 * 60 * 24));
                    const hours   = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                    let text = '';
                    if (days > 0)  text += days + ' hari ';
                    if (hours > 0) text += hours + ' jam ';
                    text += minutes + ' mnt ' + String(seconds).padStart(2, '0') + ' dtk';

                    el.textContent = text;
                } else {
                    el.textContent = 'Lelang Selesai';
                    el.classList.add('text-danger', 'fw-semibold');
                }
            });
        }

        setInterval(updateCountdown, 1000);
        updateCountdown(); // langsung jalankan agar tidak ada jeda 1 detik pertama

        // ── Submit form saat tekan Enter di kolom search ──────────
        document.getElementById('searchInput')?.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });
    </script>
</x-app-layout>