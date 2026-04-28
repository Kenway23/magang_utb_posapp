@extends('layouts.owner')

@section('title', 'Riwayat Transaksi - PROShop')
@section('header-title', 'Riwayat Transaksi')
@section('header-subtitle', 'Lihat semua transaksi dari seluruh kasir')

@section('content')
    <div id="riwayatApp">
        <div class="space-y-6">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-2xl font-bold text-indigo-600" id="totalTransaksi">0</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Total Item Terjual</p>
                    <p class="text-2xl font-bold text-blue-600" id="totalItem">0</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-green-600" id="totalPendapatan">Rp 0</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
                    <p class="text-sm text-gray-500">Rata-rata Transaksi</p>
                    <p class="text-2xl font-bold text-purple-600" id="rataRata">Rp 0</p>
                </div>
            </div>

            <!-- Loading -->
            <div id="loading" class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                <p class="mt-2">Memuat data...</p>
            </div>

            <!-- Tabel -->
            <div id="tableContainer" class="bg-white rounded-xl shadow-sm overflow-hidden hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">No</th>
                                <th class="px-6 py-3 text-left">No. Transaksi</th>
                                <th class="px-6 py-3 text-left">Tanggal</th>
                                <th class="px-6 py-3 text-left">Kasir</th>
                                <th class="px-6 py-3 text-center">Item</th>
                                <th class="px-6 py-3 text-right">Total</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(price || 0);
        }

        function showToast(message, type = 'success') {
            // Implementasi toast
            console.log(message);
        }

        async function loadData() {
            try {
                const response = await fetch('/owner/riwayat-transaksi/data');
                const data = await response.json();

                console.log('Response:', data);

                if (data.success) {
                    const transactions = data.data;

                    // Update statistik
                    const totalTransaksi = transactions.length;
                    const totalItem = transactions.reduce((sum, t) => sum + t.total_items, 0);
                    const totalPendapatan = transactions.reduce((sum, t) => sum + t.total_amount, 0);
                    const rata = totalTransaksi > 0 ? totalPendapatan / totalTransaksi : 0;

                    document.getElementById('totalTransaksi').innerText = totalTransaksi;
                    document.getElementById('totalItem').innerText = totalItem;
                    document.getElementById('totalPendapatan').innerHTML = 'Rp ' + formatPrice(totalPendapatan);
                    document.getElementById('rataRata').innerHTML = 'Rp ' + formatPrice(rata);

                    // Render tabel
                    const tbody = document.getElementById('tableBody');
                    tbody.innerHTML = transactions.map((trx, idx) => `
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm">${idx + 1}</td>
                            <td class="px-6 py-3 text-sm font-mono font-semibold text-indigo-600">${trx.transaction_number}</td>
                            <td class="px-6 py-3 text-sm">${trx.date}</td>
                            <td class="px-6 py-3 text-sm"><span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full text-xs">${trx.cashier}</span></td>
                            <td class="px-6 py-3 text-sm text-center font-bold">${trx.total_items}</td>
                            <td class="px-6 py-3 text-sm text-right font-bold text-green-600">Rp ${formatPrice(trx.total_amount)}</td>
                            <td class="px-6 py-3 text-center">
                                <button onclick="showDetail(${trx.id})" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                    `).join('');

                    document.getElementById('loading').classList.add('hidden');
                    document.getElementById('tableContainer').classList.remove('hidden');
                } else {
                    console.error('Error:', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', loadData);
    </script>
@endsection
