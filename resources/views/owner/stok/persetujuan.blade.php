@extends('layouts.owner')

@section('title', 'Persetujuan Stok - PROShop')

@section('header-title', 'Persetujuan Stok')
@section('header-subtitle', 'Setujui atau tolak permintaan stok')

@section('content')
    <div class="space-y-6">
        {{-- Statistik Persetujuan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Menunggu Persetujuan</p>
                        <p class="text-3xl font-bold text-yellow-600">8</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Disetujui</p>
                        <p class="text-3xl font-bold text-green-600">24</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Ditolak</p>
                        <p class="text-3xl font-bold text-red-600">3</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Permintaan Stok --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-list-ul text-indigo-600"></i>
                    Daftar Permintaan Stok
                </h3>
                <select
                    class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option>Semua</option>
                    <option>Menunggu</option>
                    <option>Disetujui</option>
                    <option>Ditolak</option>
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Diminta Oleh</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">25/04/2026</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Pocari Sweat</td>
                            <td class="px-6 py-3 text-sm text-slate-600">+50 pcs</td>
                            <td class="px-6 py-3 text-sm text-slate-600">Kasir 1</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700"><i
                                        class="fas fa-clock mr-1"></i>Menunggu</span></td>
                            <td class="px-6 py-3">
                                <button class="text-green-600 hover:text-green-800 mr-2"><i
                                        class="fas fa-check-circle"></i></button>
                                <button class="text-red-600 hover:text-red-800"><i class="fas fa-times-circle"></i></button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">24/04/2026</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Goodday</td>
                            <td class="px-6 py-3 text-sm text-slate-600">+20 pack</td>
                            <td class="px-6 py-3 text-sm text-slate-600">Gudang 2</td>
                            <td class="px-6 py-3"><span
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700"><i
                                        class="fas fa-check-circle mr-1"></i>Disetujui</span></td>
                            <td class="px-6 py-3">
                                <button class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-print"></i></button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-sm text-slate-600">23/04/2026</td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-700">Sunlight</td>
                            <td class="px-6 py-3 text-sm text-slate-600">+30 pcs</td>
                            <td class="px-6 py-3 text-sm text-slate-600">Kasir 3</td>
                            <td class="px-6 py-3"><span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700"><i
                                        class="fas fa-times-circle mr-1"></i>Ditolak</span></td>
                            <td class="px-6 py-3">
                                <button class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-print"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
