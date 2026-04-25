@extends('layouts.kasir')

@section('title', 'Riwayat Transaksi - PROShop')
@section('header-title', 'Riwayat Transaksi')
@section('header-subtitle', 'Lihat semua transaksi yang telah dilakukan')

@section('content')
    <div x-data="riwayatApp()" x-init="init()" x-cloak>
        <div class="space-y-6">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-2xl font-bold text-indigo-600" x-text="totalTransaksi"></p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Total Item Terjual</p>
                    <p class="text-2xl font-bold text-blue-600" x-text="totalItemTerjual"></p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-green-600">Rp <span x-text="formatPrice(totalPendapatan)"></span></p>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="text" x-model="searchNo" placeholder="Cari No. Transaksi..."
                        class="border rounded-lg px-4 py-2">
                    <input type="date" x-model="dariTanggal" class="border rounded-lg px-4 py-2">
                    <input type="date" x-model="sampaiTanggal" class="border rounded-lg px-4 py-2">
                    <div class="flex gap-2">
                        <button @click="filterData" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Cari</button>
                        <button @click="resetFilter" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Reset</button>
                    </div>
                </div>
            </div>

            <!-- Tabel Transaksi -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">No. Transaksi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Kasir</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500">Item</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(trx, idx) in filteredTransactions" :key="trx.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm" x-text="idx+1"></td>
                                    <td class="px-6 py-3 text-sm font-mono text-indigo-600" x-text="trx.transaction_number">
                                    </td>
                                    <td class="px-6 py-3 text-sm" x-text="trx.date"></td>
                                    <td class="px-6 py-3 text-sm"><span
                                            class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full text-xs"
                                            x-text="trx.cashier"></span></td>
                                    <td class="px-6 py-3 text-sm text-center font-bold" x-text="trx.total_items"></td>
                                    <td class="px-6 py-3 text-sm text-right font-bold text-green-600">Rp <span
                                            x-text="formatPrice(trx.total_amount)"></span></td>
                                    <td class="px-6 py-3 text-center">
                                        <button @click="showDetail(trx)" class="text-indigo-600 hover:text-indigo-800">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredTransactions.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">Belum ada transaksi</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MODAL DETAIL -->
        <div x-show="showDetailModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-auto">
                <div class="p-5 border-b sticky top-0 bg-white">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-lg">Detail Transaksi</h3>
                        <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600"><i
                                class="fas fa-times text-xl"></i></button>
                    </div>
                </div>
                <div class="p-5" x-html="detailContent"></div>
                <div class="p-5 border-t flex justify-end">
                    <button @click="showDetailModal = false"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function riwayatApp() {
            return {
                transactions: [],
                filteredTransactions: [],
                searchNo: '',
                dariTanggal: '',
                sampaiTanggal: '',
                showDetailModal: false,
                detailContent: '',

                get totalTransaksi() {
                    return this.filteredTransactions.length;
                },
                get totalItemTerjual() {
                    return this.filteredTransactions.reduce((sum, t) => sum + t.total_items, 0);
                },
                get totalPendapatan() {
                    return this.filteredTransactions.reduce((sum, t) => sum + t.total_amount, 0);
                },
                get rataRata() {
                    return this.totalTransaksi ? this.totalPendapatan / this.totalTransaksi : 0;
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                },

                filterData() {
                    let filtered = [...this.transactions];
                    if (this.searchNo) {
                        filtered = filtered.filter(t => t.transaction_number.toLowerCase().includes(this.searchNo
                            .toLowerCase()));
                    }
                    if (this.dariTanggal) {
                        filtered = filtered.filter(t => t.date.split(' ')[0] >= this.dariTanggal);
                    }
                    if (this.sampaiTanggal) {
                        filtered = filtered.filter(t => t.date.split(' ')[0] <= this.sampaiTanggal);
                    }
                    this.filteredTransactions = filtered;
                },

                resetFilter() {
                    this.searchNo = '';
                    this.dariTanggal = '';
                    this.sampaiTanggal = '';
                    this.filteredTransactions = [...this.transactions];
                },

                showDetail(trx) {
                    let itemsHtml = '';
                    if (trx.items && trx.items.length > 0) {
                        itemsHtml =
                            `<div class="mt-4"><h4 class="font-bold mb-2">Daftar Item:</h4><table class="min-w-full border"><thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left">Produk</th><th class="px-3 py-2 text-center">Qty</th><th class="px-3 py-2 text-right">Subtotal</th></tr></thead><tbody>`;
                        trx.items.forEach(item => {
                            itemsHtml +=
                                `<tr><td class="px-3 py-2">${item.name}</td><td class="px-3 py-2 text-center">${item.qty}</td><td class="px-3 py-2 text-right">Rp ${this.formatPrice(item.price * item.qty)}</td></tr>`;
                        });
                        itemsHtml += `</tbody></table></div>`;
                    }

                    this.detailContent = `
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div><p class="text-xs text-gray-400">No. Transaksi</p><p class="font-semibold">${trx.transaction_number}</p></div>
                            <div><p class="text-xs text-gray-400">Tanggal</p><p class="font-semibold">${trx.date}</p></div>
                            <div><p class="text-xs text-gray-400">Kasir</p><p class="font-semibold">${trx.cashier}</p></div>
                            <div><p class="text-xs text-gray-400">Total Item</p><p class="font-semibold">${trx.total_items} item</p></div>
                            <div><p class="text-xs text-gray-400">Total Belanja</p><p class="font-bold text-green-600">Rp ${this.formatPrice(trx.total_amount)}</p></div>
                            <div><p class="text-xs text-gray-400">Bayar</p><p class="font-semibold">Rp ${this.formatPrice(trx.payment_amount)}</p></div>
                            <div><p class="text-xs text-gray-400">Kembalian</p><p class="font-bold text-green-600">Rp ${this.formatPrice(trx.change_amount)}</p></div>
                        </div>
                        ${itemsHtml}
                    </div>
                `;
                    this.showDetailModal = true;
                },

                init() {
                    let saved = localStorage.getItem('transactions');
                    if (saved) {
                        this.transactions = JSON.parse(saved);
                    } else {
                        // Data sampel
                        this.transactions = [{
                                id: 1,
                                transaction_number: 'TRX-20241220-0001',
                                date: '20 Desember 2024, 10:30',
                                cashier: 'Kasir 1',
                                total_items: 3,
                                total_amount: 25000,
                                payment_amount: 50000,
                                change_amount: 25000,
                                items: [{
                                    name: 'Indomie Goreng',
                                    price: 3800,
                                    qty: 2
                                }, {
                                    name: 'Teh Botol',
                                    price: 5000,
                                    qty: 1
                                }]
                            },
                            {
                                id: 2,
                                transaction_number: 'TRX-20241220-0002',
                                date: '20 Desember 2024, 14:15',
                                cashier: 'Kasir 1',
                                total_items: 5,
                                total_amount: 78500,
                                payment_amount: 100000,
                                change_amount: 21500,
                                items: []
                            },
                            {
                                id: 3,
                                transaction_number: 'TRX-20241221-0003',
                                date: '21 Desember 2024, 09:45',
                                cashier: 'Kasir 1',
                                total_items: 2,
                                total_amount: 12000,
                                payment_amount: 20000,
                                change_amount: 8000,
                                items: []
                            }
                        ];
                    }
                    this.filteredTransactions = [...this.transactions];
                }
            };
        }
    </script>
@endsection
