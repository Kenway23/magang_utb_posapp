@extends('layouts.owner')

@section('title', 'Beranda Owner - PROShop')
@section('header-title', 'Beranda')
@section('header-subtitle', 'Selamat datang, ' . Auth::user()->name . '! Kelola toko Anda dengan mudah')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-md p-6 card-hover border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Produk</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ number_format($totalProduk ?? 0) }}</p>
                </div>
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-boxes text-indigo-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-md p-6 card-hover border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">Rp
                        {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-rupiah-sign text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-md p-6 card-hover border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Transaksi</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ number_format($totalTransaksi ?? 0) }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-receipt text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Aktivitas Terakhir -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-clock text-indigo-600 mr-2"></i>Aktivitas Terakhir</h3>
                <a href="{{ route('owner.riwayat_transaksi') }}" class="text-indigo-600 text-sm">Lihat Semua <i
                        class="fas fa-arrow-right text-xs"></i></a>
            </div>
            <div class="divide-y divide-slate-100 max-h-[400px] overflow-y-auto">
                @forelse($aktivitasTerakhir ?? [] as $aktivitas)
                    <div class="px-6 py-3 flex items-start gap-3">
                        <div
                            class="w-8 h-8 rounded-full flex items-center justify-center {{ $aktivitas['icon_color'] ?? 'bg-gray-100 text-gray-600' }}">
                            <i class="fas {{ $aktivitas['icon'] ?? 'fa-circle' }} text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-sm">{{ $aktivitas['title'] ?? 'Aktivitas' }}</p>
                            <p class="text-xs text-slate-500">{{ $aktivitas['description'] ?? '-' }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $aktivitas['time'] ?? '' }} • oleh
                                {{ $aktivitas['user'] ?? 'Sistem' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-slate-400">
                        <i class="fas fa-inbox text-3xl mb-2 block"></i>
                        <p class="text-sm">Belum ada aktivitas</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Penjualan Terbaik -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-trophy text-yellow-500 mr-2"></i>Penjualan Terbaik</h3>
                <a href="{{ route('owner.laporan_penjualan') }}" class="text-indigo-600 text-sm">Lihat Semua <i
                        class="fas fa-arrow-right text-xs"></i></a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($penjualanTerbaik ?? [] as $index => $item)
                    <div class="px-6 py-3">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-sm font-bold {{ $index == 0 ? 'text-yellow-500' : ($index == 1 ? 'text-gray-400' : ($index == 2 ? 'text-orange-500' : 'text-slate-400')) }}">
                                    {{ $index == 0 ? '🥇' : ($index == 1 ? '🥈' : ($index == 2 ? '🥉' : $index + 1)) }}
                                </span>
                                <span
                                    class="font-semibold text-sm">{{ $item->nama_produk ?? ($item['nama_produk'] ?? '-') }}</span>
                            </div>
                            <span class="text-green-600 font-semibold text-sm">Rp
                                {{ number_format($item->total_pendapatan ?? ($item['total_pendapatan'] ?? 0), 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Terjual:
                            {{ number_format($item->total_terjual ?? ($item['total_terjual'] ?? 0)) }} pcs</p>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-slate-400">
                        <i class="fas fa-chart-line text-3xl mb-2 block"></i>
                        <p class="text-sm">Belum ada data penjualan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Stok Rendah -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100 mb-6">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-semibold"><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>Stok Rendah</h3>
            <a href="{{ route('owner.stok.laporan_approval') }}" class="text-indigo-600 text-sm">Lihat Semua <i
                    class="fas fa-arrow-right text-xs"></i></a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold">No</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold">Produk</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold">Kategori</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold">Stok</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($produkStokRendah ?? [] as $index => $produk)
                        @php
                            $status = '';
                            $statusClass = '';
                            if ($produk->stok_gudang <= 0) {
                                $status = 'Habis';
                                $statusClass = 'bg-red-100 text-red-700';
                            } elseif ($produk->stok_gudang <= 5) {
                                $status = 'Kritis';
                                $statusClass = 'bg-red-100 text-red-700';
                            } else {
                                $status = 'Menipis';
                                $statusClass = 'bg-orange-100 text-orange-700';
                            }
                            $stokClass =
                                $produk->stok_gudang <= 0
                                    ? 'text-red-600 font-bold'
                                    : ($produk->stok_gudang <= 5
                                        ? 'text-red-600'
                                        : 'text-orange-600');
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 font-medium text-sm">{{ $produk->nama_produk }}</td>
                            <td class="px-4 py-2 text-sm">{{ $produk->kategori->nama_kategori ?? '-' }}</td>
                            <td class="px-4 py-2 text-center text-sm {{ $stokClass }}">{{ $produk->stok_gudang }} pcs
                            </td>
                            <td class="px-4 py-2 text-center"><span
                                    class="px-2 py-0.5 text-xs rounded-full {{ $statusClass }}">{{ $status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                <i class="fas fa-check-circle text-2xl mb-1 block text-green-500"></i>
                                <p class="text-sm">Semua stok aman</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pengguna Terbaru -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-semibold"><i class="fas fa-user-plus text-green-600 mr-2"></i>Pengguna Terbaru</h3>
            <a href="{{ route('owner.pengguna.index') }}" class="text-indigo-600 text-sm">Lihat Semua <i
                    class="fas fa-arrow-right text-xs"></i></a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 divide-x divide-slate-100">
            @forelse($penggunaTerbaru ?? [] as $user)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-sm">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $user->role->nama_role ?? '-' }}</p>
                        <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}
                        </p>
                    </div>
                    <button
                        onclick="showInfo('{{ $user->name }} bergabung {{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}')"
                        class="text-indigo-600 text-sm hover:text-indigo-800">
                        Detail
                    </button>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 col-span-3">
                    <i class="fas fa-users text-3xl mb-1 block"></i>
                    <p class="text-sm">Belum ada pengguna</p>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>

    <script>
        function showInfo(message) {
            alert('ℹ️ ' + message);
        }
    </script>
@endsection
