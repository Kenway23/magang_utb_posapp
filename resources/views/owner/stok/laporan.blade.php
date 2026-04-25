@extends('layouts.owner')

@section('title', 'Laporan Stok - PROShop')

@section('header-title', 'Laporan Stok')
@section('header-subtitle', 'Lihat laporan stok barang secara lengkap')

@section('content')
    <div class="space-y-6">
        {{-- Filter Laporan --}}
        <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-4">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date"
                        class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                    <input type="date"
                        class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <select
                        class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option>Semua</option>
                        <option>Makanan</option>
                        <option>Minuman</option>
                        <option>Kebersihan</option>
                        <option>Kesehatan</option>
                    </select>
                </div>
                <div>
                    <button
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
                <div>
                    <button
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </div>
                <div>
                    <button
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-download"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>

        {{-- Ringkasan Stok --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                <p class="text-slate-500 text-sm">Total Stok Masuk</p>
                <p class="text-2xl font-bold text-green-600">+1.250</p>
                <p class="text-xs text-slate-400 mt-1">Bulan ini</p>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                <p class="text-slate-500 text-sm">Total Stok Keluar</p>
                <p class="text-2xl font-bold text-red-600">-890</p>
                <p class="text-xs text-slate-400 mt-1">Bulan ini</p>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                <p class="text-slate-500 text-sm">Stok Saat Ini</p>
                <p class="text-2xl font-bold text-indigo-600">3.420</p>
                <p class="text-xs text-slate-400 mt-1">Total keseluruhan</p>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                <p class="text-slate-500 text-sm">Produk Menipis</p>
                <p class="text-2xl font-bold text-orange-600">12</p>
                <p class="text-xs text-slate-400 mt-1">Perlu restok</p>
            </div>
        </div>

        {{-- Tabel Laporan Stok --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-indigo-600"></i>
                    Detail Stok Produk
                </h3>
                <div class="relative">
                    <input type="text" placeholder="Cari produk..."
                        class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
                    <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-sm"></i>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Stok Awal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Stok Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Stok Keluar</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Stok Akhir</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">1</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Sunlight 690ml</td>
                            <td class="px-6 py-3 text-sm text-slate-600">Kebersihan</td>
                            <td class="px-6 py-3 text-sm text-slate-600">25</td>
                            <td class="px-6 py-3 text-sm text-green-600">+50</td>
                            <td class="px-6 py-3 text-sm text-red-600">-35</td>
                            <td class="px-6 py-3 text-sm font-semibold">40</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Aman</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">2</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Tolak Angin</td>
                            <td class="px-6 py-3 text-sm text-slate-600">Kesehatan</td>
                            <td class="px-6 py-3 text-sm text-slate-600">15</td>
                            <td class="px-6 py-3 text-sm text-green-600">+30</td>
                            <td class="px-6 py-3 text-sm text-red-600">-25</td>
                            <td class="px-6 py-3 text-sm font-semibold">20</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Menipis</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">3</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Rinso Bubuk</td>
                            <td class="px-6 py-3 text-sm text-slate-600">Kebersihan</td>
                            <td class="px-6 py-3 text-sm text-slate-600">40</td>
                            <td class="px-6 py-3 text-sm text-green-600">+20</td>
                            <td class="px-6 py-3 text-sm text-red-600">-0</td>
                            <td class="px-6 py-3 text-sm font-semibold">60</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Aman</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">4</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Kayu Putih</td>
                            <td class="px-6 py-3 text-sm text-slate-600">Kesehatan</td>
                            <td class="px-6 py-3 text-sm text-slate-600">30</td>
                            <td class="px-6 py-3 text-sm text-green-600">+25</td>
                            <td class="px-6 py-3 text-sm text-red-600">-5</td>
                            <td class="px-6 py-3 text-sm font-semibold">50</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Aman</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">5</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Japota Honey</td>
                            <td class="px-6 py-3 text-sm text-slate-600">Makanan</td>
                            <td class="px-6 py-3 text-sm text-slate-600">50</td>
                            <td class="px-6 py-3 text-sm text-green-600">+20</td>
                            <td class="px-6 py-3 text-sm text-red-600">-0</td>
                            <td class="px-6 py-3 text-sm font-semibold">70</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Aman</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">6</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Pocky Coklat</td>
                            <td class="px-6 py-3 text-sm text-slate-600">Makanan</td>
                            <td class="px-6 py-3 text-sm text-slate-600">30</td>
                            <td class="px-6 py-3 text-sm text-green-600">+50</td>
                            <td class="px-6 py-3 text-sm text-red-600">-5</td>
                            <td class="px-6 py-3 text-sm font-semibold">75</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Aman</span></td>
                        </tr>
                    </tbody>
                    </tr>
            </div>
        </div>
    </div>
@endsection
