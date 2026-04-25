@extends('layouts.owner')

@section('title', 'Penerimaan Stok - PROShop')

@section('header-title', 'Penerimaan Stok')
@section('header-subtitle', 'Catat penerimaan stok barang baru')

@section('content')
    <div class="space-y-6">
        {{-- Form Penerimaan Stok --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-arrow-down text-green-600"></i>
                    Form Penerimaan Stok
                </h3>
            </div>
            <div class="p-6">
                <form class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Produk</label>
                            <select
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option>Pilih Produk</option>
                                <option>Sunlight 690ml</option>
                                <option>Tolak Angin</option>
                                <option>Rinso Bubuk</option>
                                <option>Kayu Putih</option>
                                <option>Japota Honey</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                            <input type="number" placeholder="Masukkan jumlah"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                            <input type="date"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Supplier</label>
                            <input type="text" placeholder="Nama supplier"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                            <textarea rows="3" placeholder="Catatan tambahan..."
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="reset"
                            class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition">Reset</button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Penerimaan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Riwayat Penerimaan Stok --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-600"></i>
                    Riwayat Penerimaan Stok
                </h3>
                <div class="relative">
                    <input type="text" placeholder="Cari..."
                        class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
                    <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-sm"></i>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">24/04/2026</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Sunlight 690ml</td>
                            <td class="px-6 py-3 text-sm text-green-600 font-semibold">+50</td>
                            <td class="px-6 py-3 text-sm text-slate-600">PT. Unilever</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700"><i
                                        class="fas fa-check-circle mr-1"></i>Selesai</span></td>
                            <td class="px-6 py-3"><button class="text-indigo-600 hover:text-indigo-800"><i
                                        class="fas fa-print"></i></button></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">23/04/2026</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Tolak Angin</td>
                            <td class="px-6 py-3 text-sm text-green-600 font-semibold">+30</td>
                            <td class="px-6 py-3 text-sm text-slate-600">PT. Sido Muncul</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700"><i
                                        class="fas fa-check-circle mr-1"></i>Selesai</span></td>
                            <td class="px-6 py-3"><button class="text-indigo-600 hover:text-indigo-800"><i
                                        class="fas fa-print"></i></button></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">22/04/2026</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Pocky Coklat</td>
                            <td class="px-6 py-3 text-sm text-green-600 font-semibold">+100</td>
                            <td class="px-6 py-3 text-sm text-slate-600">PT. Ezaki Glico</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700"><i
                                        class="fas fa-check-circle mr-1"></i>Selesai</span></td>
                            <td class="px-6 py-3"><button class="text-indigo-600 hover:text-indigo-800"><i
                                        class="fas fa-print"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
