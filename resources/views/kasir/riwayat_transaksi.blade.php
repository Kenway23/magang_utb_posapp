@extends('layouts.kasir')

@section('title', 'Riwayat Transaksi - PROShop')
@section('header-title', 'Riwayat Transaksi')
@section('header-subtitle', 'Lihat semua transaksi yang telah Anda lakukan')

@section('content')
    <div x-data="riwayatApp()" x-init="init()" x-cloak>
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        <div class="space-y-6">
            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-2xl font-bold text-indigo-600" x-text="totalTransaksi">0</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Total Item Terjual</p>
                    <p class="text-2xl font-bold text-blue-600" x-text="totalItemTerjual">0</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-green-600">Rp <span x-text="formatPrice(totalPendapatan)">0</span></p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
                    <p class="text-sm text-gray-500">Rata-rata Transaksi</p>
                    <p class="text-2xl font-bold text-purple-600">Rp <span x-text="formatPrice(rataRata)">0</span></p>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <input type="text" x-model="searchNo" @input="filterData" placeholder="Cari No. Transaksi..."
                        class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="date" x-model="dariTanggal" @change="filterData"
                        class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="date" x-model="sampaiTanggal" @change="filterData"
                        class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button @click="filterData"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-search mr-1"></i> Cari
                    </button>
                    <button @click="resetFilter"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Loading -->
            <div x-show="loading" class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                <p class="mt-2 text-gray-500">Memuat data...</p>
            </div>

            <!-- Tabel Transaksi -->
            <div x-show="!loading" class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Transaksi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(trx, idx) in filteredTransactions" :key="trx.id">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 text-sm" x-text="idx+1"></td>
                                    <td class="px-6 py-3 text-sm font-mono text-indigo-600 font-semibold"
                                        x-text="trx.transaction_number"></td>
                                    <td class="px-6 py-3 text-sm" x-text="trx.date"></td>
                                    <td class="px-6 py-3 text-sm">
                                        <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full text-xs"
                                            x-text="trx.cashier"></span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-center font-bold" x-text="trx.total_items"></td>
                                    <td class="px-6 py-3 text-sm text-right font-bold text-green-600">Rp <span
                                            x-text="formatPrice(trx.total_amount)"></span></td>
                                    <td class="px-6 py-3 text-center">
                                        <button @click="showDetail(trx)"
                                            class="text-indigo-600 hover:text-indigo-800 transition">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredTransactions.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-2 block"></i>
                                    Belum ada transaksi
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MODAL DETAIL -->
        <div x-show="showDetailModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-auto animate-modal">
                <div class="p-5 border-b sticky top-0 bg-white">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-lg">
                            <i class="fas fa-receipt text-indigo-600 mr-2"></i>Detail Transaksi
                        </h3>
                        <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-5" x-html="detailContent"></div>
                <div class="p-5 border-t flex justify-end">
                    <button @click="showDetailModal = false"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- TOAST NOTIFICATION -->
        <div id="toastNotification" class="toast-notification">
            <div class="flex items-center p-4">
                <div id="toastIcon" class="flex-shrink-0 mr-3"><i class="fas fa-check-circle text-xl"></i></div>
                <div class="flex-1">
                    <p id="toastMessage" class="text-sm font-medium text-slate-800"></p>
                </div>
                <button onclick="hideToast()" class="flex-shrink-0 ml-3 text-slate-400 hover:text-slate-600"><i
                        class="fas fa-times"></i></button>
            </div>
        </div>
    </div>

    <style>
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 320px;
            max-width: 400px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-success {
            border-left: 4px solid #10b981;
        }

        .toast-error {
            border-left: 4px solid #ef4444;
        }

        .toast-warning {
            border-left: 4px solid #f59e0b;
        }

        @keyframes modalAnim {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-modal {
            animation: modalAnim 0.2s ease-out;
        }
    </style>

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
                loading: true,

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
                    return new Intl.NumberFormat('id-ID').format(price || 0);
                },

                showToast(message, type = 'success') {
                    const toast = document.getElementById('toastNotification');
                    const toastIcon = document.getElementById('toastIcon');
                    const toastMessage = document.getElementById('toastMessage');
                    toast.className = 'toast-notification';
                    if (type === 'success') {
                        toast.classList.add('toast-success');
                        toastIcon.innerHTML = '<i class="fas fa-check-circle text-emerald-500 text-xl"></i>';
                    } else if (type === 'error') {
                        toast.classList.add('toast-error');
                        toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-rose-500 text-xl"></i>';
                    } else {
                        toast.classList.add('toast-warning');
                        toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-amber-500 text-xl"></i>';
                    }
                    toastMessage.innerHTML = message;
                    toast.classList.add('show');
                    setTimeout(() => toast.classList.remove('show'), 3000);
                },

                filterData() {
                    let filtered = [...this.transactions];

                    // Filter by No Transaksi
                    if (this.searchNo) {
                        filtered = filtered.filter(t =>
                            t.transaction_number.toLowerCase().includes(this.searchNo.toLowerCase())
                        );
                    }

                    // 🔥 PERBAIKAN FILTER TANGGAL - konversi ke timestamp
                    if (this.dariTanggal) {
                        const dariTimestamp = new Date(this.dariTanggal).getTime();
                        filtered = filtered.filter(t => {
                            const dateParts = t.date.split(' ')[0].split('/');
                            const tglTransaksi = new Date(`${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`)
                                .getTime();
                            return tglTransaksi >= dariTimestamp;
                        });
                    }

                    if (this.sampaiTanggal) {
                        const sampaiTimestamp = new Date(this.sampaiTanggal).getTime();
                        filtered = filtered.filter(t => {
                            const dateParts = t.date.split(' ')[0].split('/');
                            const tglTransaksi = new Date(`${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`)
                                .getTime();
                            return tglTransaksi <= sampaiTimestamp;
                        });
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
                        itemsHtml = `
                            <div class="mt-4">
                                <h4 class="font-bold mb-2">Daftar Item:</h4>
                                <table class="min-w-full border rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Produk</th>
                                            <th class="px-3 py-2 text-center">Qty</th>
                                            <th class="px-3 py-2 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${trx.items.map(item => `
                                                        <tr class="border-t">
                                                            <td class="px-3 py-2">${item.name}</td>
                                                            <td class="px-3 py-2 text-center">${item.qty}</td>
                                                            <td class="px-3 py-2 text-right">Rp ${this.formatPrice(item.price * item.qty)}</td>
                                                        </tr>
                                                    `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }

                    this.detailContent = `
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-400">No. Transaksi</p>
                                    <p class="font-semibold font-mono">${trx.transaction_number}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Tanggal</p>
                                    <p class="font-semibold">${trx.date}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Kasir</p>
                                    <p class="font-semibold">${trx.cashier}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Total Item</p>
                                    <p class="font-semibold">${trx.total_items} item</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Total Belanja</p>
                                    <p class="font-bold text-green-600">Rp ${this.formatPrice(trx.total_amount)}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Bayar</p>
                                    <p class="font-semibold">Rp ${this.formatPrice(trx.payment_amount)}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Kembalian</p>
                                    <p class="font-bold text-green-600">Rp ${this.formatPrice(trx.change_amount)}</p>
                                </div>
                            </div>
                            ${itemsHtml}
                        </div>
                    `;
                    this.showDetailModal = true;
                },

                async init() {
                    this.loading = true;
                    try {
                        const response = await fetch('/kasir/riwayat-data');
                        const data = await response.json();
                        if (data.success) {
                            this.transactions = data.data;
                            this.filteredTransactions = [...this.transactions];
                        } else {
                            this.showToast(data.message || 'Gagal memuat data', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showToast('Terjadi kesalahan pada server', 'error');
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        function hideToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) toast.classList.remove('show');
        }
    </script>
@endsection
