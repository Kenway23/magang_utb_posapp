@extends('layouts.kasir')

@section('title', 'Laporan Penjualan - PROShop')
@section('header-title', 'Laporan Penjualan')
@section('header-subtitle', 'Statistik dan ringkasan penjualan')

@section('content')
    <div x-data="laporanApp()" x-init="init()" x-cloak>
        <div class="space-y-6">

            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl shadow-md p-4 border border-slate-100">
                    <p class="text-slate-500 text-sm">Total Transaksi</p>
                    <p class="text-2xl font-bold text-indigo-600" id="totalTransaksiCount">0</p>
                </div>
                <div class="bg-white rounded-2xl shadow-md p-4 border border-slate-100">
                    <p class="text-slate-500 text-sm">Total Item Terjual</p>
                    <p class="text-2xl font-bold text-blue-600" id="totalItemTerjual">0</p>
                </div>
                <div class="bg-white rounded-2xl shadow-md p-4 border border-slate-100">
                    <p class="text-slate-500 text-sm">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-green-600" id="totalPendapatan">Rp 0</p>
                </div>
            </div>

            <!-- PRODUK TERJUAL -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-5 border-b flex justify-between items-center">
                    <h3 class="font-bold text-lg flex items-center">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i> Produk Terjual
                    </h3>
                    <!-- Tambahan: Menampilkan jumlah produk -->
                    <span class="text-sm text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                        <span x-text="topProducts.length"></span> Produk Terlaris
                    </span>
                </div>
                <div class="p-5">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Produk</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500">Qty Terjual</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(item, idx) in topProducts" :key="idx">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium">
                                            <!-- Tampilkan ranking dengan badge -->
                                            <span x-show="idx === 0"
                                                class="bg-yellow-100 text-yellow-700 w-6 h-6 rounded-full inline-flex items-center justify-center text-xs font-bold">1</span>
                                            <span x-show="idx === 1"
                                                class="bg-gray-100 text-gray-600 w-6 h-6 rounded-full inline-flex items-center justify-center text-xs font-bold">2</span>
                                            <span x-show="idx === 2"
                                                class="bg-orange-100 text-orange-600 w-6 h-6 rounded-full inline-flex items-center justify-center text-xs font-bold">3</span>
                                            <span x-show="idx >= 3" x-text="idx+1" class="text-gray-400 text-sm"></span>
                                        </td>
                                        <td class="px-4 py-3 font-semibold" x-text="item.name"></td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-sm font-medium"
                                                x-text="item.qty"></span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-green-600 font-bold">
                                            Rp <span x-text="formatPrice(item.revenue)"></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="topProducts.length === 0">
                                    <td colspan="4" class="px-4 py-12 text-center text-gray-400">
                                        <i class="fas fa-chart-line text-4xl mb-2 block text-gray-300"></i>
                                        Belum ada data penjualan
                                    </td>
                                </tr>
                            </tbody>
                            <!-- Tambahan footer untuk menampilkan total QTY dan total PENDAPATAN -->
                            <tfoot x-show="topProducts.length > 0" class="bg-gray-50">
                                <tr class="border-t border-gray-200">
                                    <td colspan="2" class="px-4 py-3 text-right text-sm font-semibold text-gray-600">
                                        TOTAL:
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-indigo-600">
                                        <span x-text="topProducts.reduce((sum, p) => sum + p.qty, 0)"></span> item
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-green-600">
                                        Rp <span x-text="formatPrice(topProducts.reduce((sum, p) => sum + p.revenue, 0))"></span>
                                    </td>
                                </tr>
                            </tfoot>
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
                                        total_amount: 25000,
                                        items: [
                                            { name: 'Indomie Goreng', qty: 2, price: 3500 },
                                            { name: 'Teh Botol Sosro', qty: 1, price: 5000 }
                                        ]
                                    },
                                    {
                                        id: 2,
                                        transaction_number: 'TRX-002',
                                        date: '20 Desember 2024',
                                        total_items: 5,
                                        total_amount: 78500,
                                        items: [
                                            { name: 'Indomie Goreng', qty: 3, price: 3500 },
                                            { name: 'Aqua 600ml', qty: 2, price: 4000 },
                                            { name: 'Roma Sari Gandum', qty: 1, price: 6000 }
                                        ]
                                    },
                                    {
                                        id: 3,
                                        transaction_number: 'TRX-003',
                                        date: '21 Desember 2024',
                                        total_items: 2,
                                        total_amount: 12000,
                                        items: [
                                            { name: 'Beng-Beng', qty: 2, price: 3000 }
                                        ]
                                    },
                                    {
                                        id: 4,
                                        transaction_number: 'TRX-004',
                                        date: '21 Desember 2024',
                                        total_items: 4,
                                        total_amount: 45000,
                                        items: [
                                            { name: 'Indomie Goreng', qty: 4, price: 3500 },
                                            { name: 'Teh Botol Sosro', qty: 1, price: 5000 }
                                        ]
                                    }
                                ];
                            }
                            this.calculateTopProducts();
                            this.calculateDailyData();
                            this.updateStatsDisplay();
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

                        updateStatsDisplay() {
                            document.getElementById('totalTransaksiCount').innerText = this.totalTransaksi;
                            document.getElementById('totalItemTerjual').innerText = this.totalItemTerjual;
                            document.getElementById('totalPendapatan').innerText = 'Rp ' + this.formatPrice(this.totalPendapatan);
                        },

                        renderChart() {
                            const ctx = document.getElementById('dailyChart');
                            if (!ctx) return;
                            
                            if (window.myChart) {
                                window.myChart.destroy();
                            }
                            
                            window.myChart = new Chart(ctx, {
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