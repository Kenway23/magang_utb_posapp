@extends('layouts.gudang')

@section('title', 'PROShop - Dashboard Gudang')
@section('page-title', 'Dashboard Gudang')
@section('page-subtitle', 'Ringkasan aktivitas dan monitoring stok gudang')

@section('content')
    <!-- STATISTIK CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide">Total Produk</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $totalProduk ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-1">sudah disetujui</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-boxes text-indigo-500 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide">Total Stok Gudang</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalStok ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">seluruh produk</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-warehouse text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide">Stok Menipis</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $produkMenipis ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-1">stok ≤ 10</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-amber-500 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide">Menunggu Approve</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $produkPending ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-1">produk baru</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- TABEL PRODUK -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-boxes text-indigo-500 text-lg"></i>
                        <h3 class="font-semibold text-slate-800">Daftar Produk</h3>
                        <span class="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $produks->count() }}
                            produk</span>
                    </div>
                    <a href="{{ route('gudang.produk.index') }}"
                        class="text-indigo-600 text-xs hover:underline flex items-center gap-1">
                        Kelola Produk <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">No
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Nama Produk</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Kategori</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Stok</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($produks as $index => $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-5 py-3 font-medium text-slate-800">{{ $item->nama_produk }}</td>
                                <td class="px-5 py-3">
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">
                                        {{ $item->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    @if ($item->stok_gudang <= 5)
                                        <span class="inline-flex items-center gap-1 text-red-600 font-semibold">
                                            <i class="fas fa-circle text-red-500 text-[6px]"></i>
                                            {{ $item->stok_gudang }}
                                            <span class="text-red-500 text-xs ml-1">(Kritis)</span>
                                        </span>
                                    @elseif($item->stok_gudang <= 10)
                                        <span class="inline-flex items-center gap-1 text-amber-600 font-semibold">
                                            <i class="fas fa-circle text-amber-500 text-[6px]"></i>
                                            {{ $item->stok_gudang }}
                                            <span class="text-amber-500 text-xs ml-1">(Menipis)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-slate-600">
                                            <i class="fas fa-circle text-emerald-500 text-[6px]"></i>
                                            {{ $item->stok_gudang }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                        <i class="fas fa-check-circle text-xs"></i> Disetujui
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                                    <i class="fas fa-box-open text-4xl mb-2 block text-slate-300"></i>
                                    <span class="text-sm">Belum ada produk yang disetujui</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- AKTIVITAS TERBARU -->
        <div class="space-y-6">
            <!-- Request Tambah Stok -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200">
                <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-arrow-down text-emerald-500 text-lg"></i>
                        <h3 class="font-semibold text-slate-800">Request Tambah Stok</h3>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    @php
                        $latestRequests = \App\Models\TambahStock::with('produk')
                            ->where('requested_by', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    @forelse($latestRequests as $req)
                        <div class="flex justify-between items-start p-3 bg-slate-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-slate-800">
                                    {{ $req->produk->nama_produk ?? 'Produk tidak tersedia' }}</p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-emerald-600 font-semibold text-sm">+{{ $req->jumlah_request }}
                                        pcs</span>
                                    <span class="text-xs text-slate-400">{{ $req->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-1">
                                    @if ($req->status == 'pending')
                                        <span
                                            class="inline-flex items-center gap-1 text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">
                                            <i class="fas fa-clock text-[10px]"></i> Menunggu
                                        </span>
                                    @elseif($req->status == 'approved')
                                        <span
                                            class="inline-flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                            <i class="fas fa-check-circle text-[10px]"></i> Disetujui
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full">
                                            <i class="fas fa-times-circle text-[10px]"></i> Ditolak
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                            <p class="text-sm">Belum ada request tambah stok</p>
                        </div>
                    @endforelse
                </div>
                <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/30">
                    <a href="{{ route('gudang.tambah_stok.index') }}"
                        class="text-indigo-600 text-xs hover:underline flex items-center justify-center gap-1">
                        Lihat Semua Request <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Pengiriman Stok -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200">
                <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-arrow-up text-orange-500 text-lg"></i>
                        <h3 class="font-semibold text-slate-800">Pengiriman Stok</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="text-center py-6 text-slate-400">
                        <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                        <p class="text-sm">Belum ada pengiriman stok</p>
                    </div>
                </div>
                <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/30">
                    <a href="{{ route('gudang.pengiriman.index') }}"
                        class="text-indigo-600 text-xs hover:underline flex items-center justify-center gap-1">
                        Lihat Semua Pengiriman <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- PERINGATAN STOK MENIPIS -->
    @if (($produkMenipis ?? 0) > 0)
        <div class="mt-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-500 text-sm"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-amber-800 text-sm">Peringatan Stok Menipis</h4>
                    <p class="text-sm text-amber-700 mt-0.5">
                        Terdapat <strong class="font-bold">{{ $produkMenipis }} produk</strong> dengan stok di bawah batas
                        minimal (10).
                        Segera lakukan <a href="{{ route('gudang.tambah_stok.index') }}"
                            class="underline font-medium text-amber-800 hover:text-amber-900">request tambah stok</a> untuk
                        menghindari kekosongan stok.
                    </p>
                </div>
            </div>
        </div>
    @endif
@endsection
