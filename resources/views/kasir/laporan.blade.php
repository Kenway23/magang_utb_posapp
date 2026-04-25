@extends('layouts.kasir')

@section('title', 'Laporan Penjualan - PROShop')
@section('header-title', 'Laporan Penjualan')
@section('header-subtitle', 'Statistik dan ringkasan penjualan')

@section('content')
    <div x-data="laporanApp()" x-init="init()" x-cloak>
        <div class="space-y-6">

            <!-- KARTU STATISTIK -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-indigo-100 text-sm">Total Transaksi</p>
                            <p class="text-3xl font-bold mt-1" x-text="totalTransaksi"></p>
                        </div>
                        <i class="fas fa-receipt text-3xl text-indigo-200"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-blue-100 text-sm">Item Terjual</p>
                            <p class="text-3xl font-bold mt-1" x-text="totalItemTerjual"></p>
                        </div>
                        <i class="fas fa-boxes text-3xl text-blue-200"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-green-100 text-sm">Total Pendapatan</p>
                            <p class="text-3xl font-bold mt-1">Rp <span x-text="formatPrice(totalPendapatan)"></span></p>
                        </div>
                        <i class="fas fa-money-bill-wave text-3xl text-green-200"></i>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-5 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-purple-100 text-sm">Rata-rata Transaksi</p>
                            <p class="text-3xl font-bold mt-1">Rp <span x-text="formatPrice(rataRata)"></span></p>
                        </div>
                        <i class="fas fa-chart-line text-3xl text-purple-200"></i>
                    </div>
                </div>
            </div>

            <!-- PRODUK TERLARIS -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-5 border-b">
                    <h3 class="font-bold text-lg flex items-center">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i> Produk Terlaris
                    </h3>
                </div>
                <div class="p-5">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Produk</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Qty Terjual</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(item, idx) in topProducts" :key="idx">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm" x-text="idx+1"></td>
                                        <td class="px-4 py-3 font-semibold" x-text="item.name"></td>
                                        <td class="px-4 py-3 text-center"><span
                                                class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-sm"
                                                x-text="item.qty"></span></td>
                                        <td class="px-4 py-3 text-right text-green-600 font-bold">Rp <span
                                                x-text="formatPrice(item.revenue)"></span></td>
                                    </tr>
                                </template>
                                <tr x-show="topProducts.length === 0">
                                    <td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada data penjualan
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function laporanApp() {
            return {
                transactions: [],
                topProducts: [],
                dailyData: [],

                get totalTransaksi() {
                    return this.transactions.length;
                },
                get totalItemTerjual() {
                    return this.transactions.reduce((sum, t) => sum + t.total_items, 0);
                },
                get totalPendapatan() {
                    return this.transactions.reduce((sum, t) => sum + t.total_amount, 0);
                },
                get rataRata() {
                    return this.totalTransaksi ? this.totalPendapatan / this.totalTransaksi : 0;
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                },

                init() {
                    let saved = localStorage.getItem('transactions');
                    if (saved) {
                        this.transactions = JSON.parse(saved);
                    } else {
                        this.transactions = [{
                                id: 1,
                                transaction_number: 'TRX-001',
                                date: '20 Desember 2024',
                                total_items: 3,
                                total_amount: 25000
                            },
                            {
                                id: 2,
                                transaction_number: 'TRX-002',
                                date: '20 Desember 2024',
                                total_items: 5,
                                total_amount: 78500
                            },
                            {
                                id: 3,
                                transaction_number: 'TRX-003',
                                date: '21 Desember 2024',
                                total_items: 2,
                                total_amount: 12000
                            },
                            {
                                id: 4,
                                transaction_number: 'TRX-004',
                                date: '21 Desember 2024',
                                total_items: 4,
                                total_amount: 45000
                            }
                        ];
                    }
                    this.calculateTopProducts();
                    this.calculateDailyData();
                    this.renderChart();
                },

                calculateTopProducts() {
                    let summary = {};
                    this.transactions.forEach(trx => {
                        if (trx.items) {
                            trx.items.forEach(item => {
                                if (!summary[item.name]) summary[item.name] = {
                                    name: item.name,
                                    qty: 0,
                                    revenue: 0
                                };
                                summary[item.name].qty += item.qty;
                                summary[item.name].revenue += (item.price * item.qty);
                            });
                        }
                    });
                    this.topProducts = Object.values(summary).sort((a, b) => b.qty - a.qty).slice(0, 5);
                },

                calculateDailyData() {
                    let daily = {};
                    this.transactions.forEach(trx => {
                        let date = trx.date.split(',')[0];
                        if (!daily[date]) daily[date] = {
                            date: date,
                            total: 0,
                            count: 0
                        };
                        daily[date].total += trx.total_amount;
                        daily[date].count += 1;
                    });
                    this.dailyData = Object.values(daily);
                },

                renderChart() {
                    const ctx = document.getElementById('dailyChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.dailyData.map(d => d.date),
                            datasets: [{
                                label: 'Pendapatan (Rp)',
                                data: this.dailyData.map(d => d.total),
                                borderColor: '#4f46e5',
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true
                        }
                    });
                }
            };
        }
    </script>
@endsection
