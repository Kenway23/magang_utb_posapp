@extends('layouts.owner')

@section('title', 'Produk - PROShop')

@section('header-title', 'Kategori Produk')
@section('header-subtitle', 'Kelola produk berdasarkan kategori')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Sidebar Kategori --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-tags text-indigo-600"></i>
                        Kategori Owner
                    </h3>
                </div>
                <div class="p-3 space-y-1">
                    <div class="px-3 py-2 rounded-lg text-slate-600 text-sm font-medium">
                        <i class="fas fa-home w-4 h-4 mr-2"></i> Beranda
                    </div>

                    <div class="mt-2">
                        <div class="px-3 py-2 rounded-lg text-slate-700 font-semibold text-sm">
                            <i class="fas fa-box w-4 h-4 mr-2"></i> Produk
                        </div>
                        <div class="ml-6 mt-1 space-y-1">
                            <a href="#"
                                class="category-item flex items-center gap-2 px-3 py-2 rounded-lg text-slate-600 text-sm transition-all duration-200 category-active">
                                <i class="fas fa-circle text-[6px]"></i>
                                Kabagari
                            </a>
                            <a href="#"
                                class="category-item flex items-center gap-2 px-3 py-2 rounded-lg text-slate-600 text-sm transition-all duration-200 hover:bg-slate-50">
                                <i class="fas fa-circle text-[6px]"></i>
                                Sabun
                            </a>
                        </div>
                    </div>

                    <div class="px-3 py-2 rounded-lg text-slate-600 text-sm font-medium mt-2">
                        <i class="fas fa-warehouse w-4 h-4 mr-2"></i> Stok
                    </div>
                    <div class="px-3 py-2 rounded-lg text-slate-600 text-sm font-medium">
                        <i class="fas fa-history w-4 h-4 mr-2"></i> Riwayat Transaksi
                    </div>
                    <div class="px-3 py-2 rounded-lg text-slate-600 text-sm font-medium">
                        <i class="fas fa-chart-line w-4 h-4 mr-2"></i> Laporan Penjualan
                    </div>
                    <div class="px-3 py-2 rounded-lg text-slate-600 text-sm font-medium">
                        <i class="fas fa-users w-4 h-4 mr-2"></i> Pengguna
                    </div>
                </div>
            </div>
        </div>

        {{-- Produk Satuan (Kabagari) --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-boxes text-indigo-600"></i>
                        Produk Satuan - Kabagari
                    </h3>
                    <button
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Tambah Produk
                    </button>
                </div>

                <div class="divide-y divide-slate-100">
                    <!-- Tolak Angin -->
                    <div class="px-6 py-4 product-card transition-all duration-200">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-capsules text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-700">Tolak Angin</p>
                                    <p class="text-xs text-slate-400">Kategori: Kesehatan</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Harga</p>
                                    <p class="font-semibold text-green-600">Rp 4.700</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Stok</p>
                                    <p class="font-semibold text-slate-700">20</p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="text-indigo-600 hover:text-indigo-800"><i
                                            class="fas fa-edit"></i></button>
                                    <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rinso Bubuk -->
                    <div class="px-6 py-4 product-card transition-all duration-200">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-soap text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-700">Rinso Bubuk</p>
                                    <p class="text-xs text-slate-400">Kategori: Kebersihan</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Harga</p>
                                    <p class="font-semibold text-green-600">Rp 12.500</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Stok</p>
                                    <p class="font-semibold text-red-600">5</p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="text-indigo-600 hover:text-indigo-800"><i
                                            class="fas fa-edit"></i></button>
                                    <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kayu Putih -->
                    <div class="px-6 py-4 product-card transition-all duration-200">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-leaf text-emerald-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-700">Kayu Putih</p>
                                    <p class="text-xs text-slate-400">Kategori: Kesehatan</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Harga</p>
                                    <p class="font-semibold text-green-600">Rp 8.900</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Stok</p>
                                    <p class="font-semibold text-orange-600">7</p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="text-indigo-600 hover:text-indigo-800"><i
                                            class="fas fa-edit"></i></button>
                                    <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pocky Coklat -->
                    <div class="px-6 py-4 product-card transition-all duration-200">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-cookie-bite text-amber-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-700">Pocky Coklat</p>
                                    <p class="text-xs text-slate-400">Kategori: Makanan</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Harga</p>
                                    <p class="font-semibold text-green-600">Rp 5.500</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Stok</p>
                                    <p class="font-semibold text-red-600">1</p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="text-indigo-600 hover:text-indigo-800"><i
                                            class="fas fa-edit"></i></button>
                                    <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keputir -->
                    <div class="px-6 py-4 product-card transition-all duration-200">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-apple-alt text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-700">Keputir</p>
                                    <p class="text-xs text-slate-400">Kategori: Buah</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Harga</p>
                                    <p class="font-semibold text-green-600">Rp 3.200</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Stok</p>
                                    <p class="font-semibold text-green-600">35</p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="text-indigo-600 hover:text-indigo-800"><i
                                            class="fas fa-edit"></i></button>
                                    <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
