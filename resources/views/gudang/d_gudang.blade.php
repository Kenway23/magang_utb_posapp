@extends('layouts.gudang')

@section('title', 'PROShop - Dashboard Gudang')
@section('page-title', 'Beranda Gudang')
@section('page-subtitle', 'Ringkasan aktivitas dan stok gudang')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tabel Stok -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-slate-800">
                    <i class="fas fa-boxes text-indigo-600 mr-2"></i>Stok Produk
                </h3>
                <a href="{{ route('gudang.produk.index') }}" class="text-indigo-600 text-sm hover:underline">
                    Kelola Produk →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-3 text-left">No</th>
                            <th class="p-3 text-left">Nama Produk</th>
                            <th class="p-3 text-left">Kategori</th>
                            <th class="p-3 text-left">Stok</th>
                            <th class="p-3 text-left">Stok Minimal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produks as $index => $item)
                            <tr class="border-b border-slate-100">
                                <td class="p-3">{{ $index + 1 }}</td>
                                <td class="p-3 font-medium">{{ $item->nama_produk }}</td>
                                <td class="p-3">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                <td class="p-3 {{ $item->stok_gudang < 10 ? 'text-red-600 font-bold' : '' }}">
                                    {{ $item->stok_gudang }}
                                </td>
                                <td class="p-3">10</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-3 text-center text-gray-500">Belum ada produk</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6 card">
                <h3 class="text-lg font-semibold mb-4">
                    <i class="fas fa-arrow-down text-green-600 mr-2"></i>Penerimaan Terbaru
                </h3>
                <div class="space-y-3">
                    @php
                        $incomingTransactions = [
                            ['product' => 'Rocky Rasa Coklat', 'qty' => 50, 'time' => '15 menit yang lalu'],
                            ['product' => 'Indomie Goreng', 'qty' => 30, 'time' => '1 jam yang lalu'],
                            ['product' => 'Teh Botol Sosro', 'qty' => 20, 'time' => '2 jam yang lalu'],
                        ];
                    @endphp
                    @foreach ($incomingTransactions as $t)
                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                            <div>
                                <span class="font-medium">{{ $t['product'] }}</span>
                                <span class="text-green-600 ml-2 font-semibold">+{{ $t['qty'] }} pcs</span>
                            </div>
                            <span class="text-xs text-slate-500">{{ $t['time'] }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('gudang.stok.penerimaan') }}" class="mt-4 text-indigo-600 text-sm hover:underline block">
                    Lihat Semua Penerimaan →
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 card">
                <h3 class="text-lg font-semibold mb-4">
                    <i class="fas fa-arrow-up text-red-600 mr-2"></i>Pengiriman Terbaru
                </h3>
                <div class="space-y-3">
                    @php
                        $outgoingTransactions = [
                            ['product' => 'Indomie Goreng', 'qty' => 46, 'time' => '8 menit yang lalu'],
                            ['product' => 'Rocky Coklat', 'qty' => 5, 'time' => '30 menit yang lalu'],
                            ['product' => 'Teh Botol', 'qty' => 10, 'time' => '3 jam yang lalu'],
                        ];
                    @endphp
                    @foreach ($outgoingTransactions as $t)
                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                            <div>
                                <span class="font-medium">{{ $t['product'] }}</span>
                                <span class="text-red-600 ml-2 font-semibold">-{{ $t['qty'] }} pcs</span>
                            </div>
                            <span class="text-xs text-slate-500">{{ $t['time'] }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('gudang.stok.pengiriman') }}" class="mt-4 text-indigo-600 text-sm hover:underline block">
                    Lihat Semua Pengiriman →
                </a>
            </div>
        </div>
    </div>

    <!-- Peringatan Stok Menipis -->
    <div class="mt-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 text-xl"></i>
            <div>
                <h4 class="font-semibold text-amber-800">Peringatan Stok Menipis</h4>
                <p class="text-sm text-amber-700 mt-1">
                    Terdapat beberapa produk dengan stok di bawah batas minimal.
                    Segera lakukan penyesuaian atau pemesanan.
                </p>
            </div>
        </div>
    </div>
@endsection
