@extends('layouts.kasir')

@section('title', 'Dashboard & POS - PROShop Kasir')
@section('header-title', 'Dashboard & Point of Sale')
@section('header-subtitle', 'Ringkasan aktivitas dan transaksi penjualan')

@section('content')
    <div x-data="posApp()" x-init="init()" x-cloak>
        <style>
            [x-cloak] {
                display: none !important;
            }

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

            .toast-info {
                border-left: 4px solid #3b82f6;
            }
        </style>

        <!-- STATISTIK CARD (Dashboard) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Transaksi Hari Ini</p>
                        <p class="text-2xl font-bold text-indigo-600" x-text="transaksiHariIni">0</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-receipt text-indigo-500 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-emerald-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Pendapatan Hari Ini</p>
                        <p class="text-2xl font-bold text-emerald-600" x-text="formatPrice(pendapatanHariIni)">Rp 0</p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Produk Tersedia</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="products.length">0</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-boxes text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm">Item di Keranjang</p>
                        <p class="text-2xl font-bold text-purple-600" x-text="cart.length">0</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-purple-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- KOLOM KIRI: PRODUK -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm">
                    <div
                        class="p-4 border-b flex justify-between items-center flex-wrap gap-2 sticky top-0 bg-white rounded-t-xl z-10">
                        <h2 class="font-bold text-lg">
                            <i class="fas fa-box text-indigo-500 mr-2"></i>Semua Produk
                        </h2>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="searchProduct" @input="filterProducts"
                                placeholder="Cari produk..."
                                class="border rounded-lg pl-10 pr-4 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>

                    <!-- FILTER KATEGORI -->
                    <div class="p-4 border-b bg-gray-50">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="cat in categories" :key="cat.id">
                                <button @click="selectedCategory = cat.name; filterProducts()"
                                    :class="selectedCategory === cat.name ? cat.color + ' text-white' :
                                        'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                                    class="px-3 py-2 rounded-lg text-xs font-medium transition whitespace-nowrap">
                                    <i :class="cat.icon" class="mr-1"></i>
                                    <span x-text="cat.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- GRID PRODUK -->
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[550px] overflow-y-auto">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <div @click="addToCart(product)"
                                class="border rounded-xl p-4 cursor-pointer hover:shadow-md hover:scale-[1.02] transition-all duration-200 bg-white"
                                :class="product.borderColor">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                        :class="product.bgColor">
                                        <i class="fas fa-box text-white text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-slate-700" x-text="product.name"></div>
                                        <div class="text-2xl font-bold mt-1" :class="product.priceColor">
                                            Rp <span x-text="formatPrice(product.price)"></span>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Stok: <span x-text="product.stock"></span>
                                            <span x-show="product.stock <= 5" class="text-red-500 ml-1">⚠️</span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs px-2 py-1 rounded-full text-white" :class="product.badgeColor"
                                            x-text="product.categoryIcon"></span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="filteredProducts.length === 0" class="col-span-2 text-center py-12 text-gray-400">
                            <i class="fas fa-search text-5xl mb-3 block"></i>
                            <p>Produk tidak ditemukan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: KERANJANG & PEMBAYARAN -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm sticky top-5">
                    <div class="p-4 border-b bg-orange-50 rounded-t-xl">
                        <h2 class="font-bold text-lg flex items-center">
                            <i class="fas fa-shopping-cart text-orange-500 mr-2"></i>
                            Keranjang Belanja
                            <span x-show="cart.length > 0"
                                class="ml-2 bg-orange-500 text-white text-xs rounded-full px-2 py-0.5"
                                x-text="cart.length"></span>
                        </h2>
                    </div>

                    <div class="p-4 max-h-[350px] overflow-y-auto">
                        <template x-if="cart.length === 0">
                            <div class="text-center py-12 text-gray-400">
                                <i class="fas fa-cart-plus text-5xl mb-3 block"></i>
                                <p>Keranjang kosong</p>
                                <p class="text-xs mt-1">Klik produk untuk menambahkan</p>
                            </div>
                        </template>

                        <template x-for="(item, idx) in cart" :key="idx">
                            <div class="flex justify-between items-center py-3 border-b">
                                <div class="flex-1">
                                    <div class="font-medium text-sm" x-text="item.name"></div>
                                    <div class="text-sm font-bold text-indigo-600">Rp <span
                                            x-text="formatPrice(item.price)"></span></div>
                                    <div class="text-xs text-gray-400">x <span x-text="item.qty"></span> = Rp <span
                                            x-text="formatPrice(item.price * item.qty)"></span></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="updateQty(idx, item.qty - 1)"
                                        class="w-7 h-7 bg-gray-100 rounded-lg hover:bg-gray-200">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="w-8 text-center font-bold" x-text="item.qty"></span>
                                    <button @click="updateQty(idx, item.qty + 1)"
                                        class="w-7 h-7 bg-gray-100 rounded-lg hover:bg-gray-200">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                    <button @click="removeItem(idx)" class="ml-2 text-red-400 hover:text-red-600">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="p-4 border-t bg-gray-50">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg">Total Belanja</span>
                            <span class="text-2xl font-bold text-indigo-600">Rp <span
                                    x-text="formatPrice(cartTotal)"></span></span>
                        </div>
                    </div>

                    <div x-show="cart.length > 0" class="p-4 space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">
                                <i class="fas fa-money-bill-wave text-green-500 mr-1"></i>Jumlah Bayar
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="text" x-model="paymentAmountFormatted" @input="formatPaymentInput"
                                    class="w-full border rounded-lg pl-10 pr-4 py-3 text-right text-xl font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                    placeholder="0">
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-3">
                            <div class="flex justify-between items-center">
                                <span class="text-green-700 font-medium">Kembalian</span>
                                <span class="text-2xl font-bold text-green-600">Rp <span
                                        x-text="formatPrice(changeAmount)"></span></span>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button @click="processPayment"
                                class="flex-1 bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition text-lg">
                                <i class="fas fa-check-circle mr-2"></i> BAYAR
                            </button>
                            <button @click="cancelTransaction"
                                class="flex-1 bg-red-500 text-white py-3 rounded-lg font-bold hover:bg-red-600 transition">
                                <i class="fas fa-times-circle mr-2"></i> BATAL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL SUKSES -->
        <div x-show="showSuccessModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">✅ Transaksi Berhasil!</h3>
                <div class="bg-gray-100 rounded-lg p-4 mb-4 text-left space-y-2">
                    <p><strong>No. Transaksi:</strong> <span class="font-mono"
                            x-text="lastTransaction?.transaction_number"></span></p>
                    <p><strong>Tanggal:</strong> <span x-text="lastTransaction?.date"></span></p>
                    <p><strong>Total Belanja:</strong> <span class="font-bold text-green-600">Rp <span
                                x-text="formatPrice(lastTransaction?.total_amount)"></span></span></p>
                    <p><strong>Bayar:</strong> Rp <span x-text="formatPrice(lastTransaction?.payment_amount)"></span></p>
                    <p><strong>Kembalian:</strong> <span class="font-bold text-green-600">Rp <span
                                x-text="formatPrice(lastTransaction?.change_amount)"></span></span></p>
                </div>
                <div class="flex gap-3">
                    <button @click="printTransaction"
                        class="flex-1 bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-print mr-2"></i> Cetak Struk
                    </button>
                    <button @click="closeSuccessModal"
                        class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">OK</button>
                </div>
            </div>
        </div>

        <!-- MODAL BATAL -->
        <div x-show="showCancelModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-sm w-full mx-4 p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-question-circle text-red-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Batalkan Transaksi?</h3>
                <p class="text-gray-500 mb-4">Yakin ingin membatalkan transaksi ini?</p>
                <div class="bg-red-50 rounded-lg p-3 mb-4">
                    <p class="text-sm text-gray-600">Total yang akan dibatalkan:</p>
                    <p class="text-xl font-bold text-red-600">Rp <span x-text="formatPrice(cartTotal)"></span></p>
                </div>
                <div class="flex gap-3">
                    <button @click="confirmCancel" class="flex-1 bg-red-600 text-white py-2 rounded-lg">Ya,
                        Batalkan</button>
                    <button @click="showCancelModal = false" class="flex-1 bg-gray-200 py-2 rounded-lg">Kembali</button>
                </div>
            </div>
        </div>

        <!-- LOADING MODAL -->
        <div x-show="showLoadingModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-xs w-full mx-4 p-6 text-center">
                <div
                    class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-3">
                </div>
                <p class="text-slate-600" x-text="loadingMessage">Memproses...</p>
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

    <script>
        function posApp() {
            return {
                products: [],
                filteredProducts: [],
                categories: [],
                searchProduct: '',
                selectedCategory: 'Semua',
                cart: [],
                paymentAmount: 0,
                paymentAmountFormatted: '',
                lastTransaction: null,
                showSuccessModal: false,
                showCancelModal: false,
                showLoadingModal: false,
                loadingMessage: 'Memproses...',
                loading: false,
                transaksiHariIni: 0,
                pendapatanHariIni: 0,

                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },
                get changeAmount() {
                    return this.paymentAmount - this.cartTotal;
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price || 0);
                },

                parseRupiah(str) {
                    if (!str) return 0;
                    return parseInt(str.replace(/\./g, '').replace(/[^0-9]/g, '')) || 0;
                },

                formatPaymentInput(e) {
                    let rawNumber = this.parseRupiah(e.target.value);
                    this.paymentAmount = rawNumber;
                    this.paymentAmountFormatted = this.formatPrice(rawNumber);
                },

                filterProducts() {
                    let filtered = [...this.products];
                    if (this.selectedCategory !== 'Semua') {
                        filtered = filtered.filter(p => p.category === this.selectedCategory);
                    }
                    if (this.searchProduct) {
                        filtered = filtered.filter(p => p.name.toLowerCase().includes(this.searchProduct.toLowerCase()));
                    }
                    this.filteredProducts = filtered;
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
                    } else if (type === 'warning') {
                        toast.classList.add('toast-warning');
                        toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-amber-500 text-xl"></i>';
                    } else {
                        toast.classList.add('toast-info');
                        toastIcon.innerHTML = '<i class="fas fa-info-circle text-blue-500 text-xl"></i>';
                    }
                    toastMessage.innerHTML = message;
                    toast.classList.add('show');
                    setTimeout(() => toast.classList.remove('show'), 3000);
                },

                hideToast() {
                    const toast = document.getElementById('toastNotification');
                    if (toast) toast.classList.remove('show');
                },

                showLoading(message = 'Memproses...') {
                    this.loadingMessage = message;
                    this.showLoadingModal = true;
                    document.body.style.overflow = 'hidden';
                },

                hideLoading() {
                    this.showLoadingModal = false;
                    document.body.style.overflow = 'auto';
                },

                async loadDashboardStats() {
                    try {
                        const response = await fetch('/kasir/dashboard-stats');
                        const data = await response.json();
                        if (data.success) {
                            this.transaksiHariIni = data.transaksi_hari_ini;
                            this.pendapatanHariIni = data.pendapatan_hari_ini;
                        }
                    } catch (error) {
                        console.error('Error loading stats:', error);
                    }
                },

                async loadCategories() {
                    try {
                        const response = await fetch('/kasir/categories');
                        const data = await response.json();
                        if (data.success) {
                            this.categories = data.data;
                        }
                    } catch (error) {
                        console.error('Error loading categories:', error);
                        this.showToast('Gagal memuat kategori', 'error');
                    }
                },

                async loadProducts() {
                    try {
                        const response = await fetch('/kasir/products');
                        const data = await response.json();
                        if (data.success) {
                            this.products = data.data;
                            this.filteredProducts = [...this.products];
                        }
                    } catch (error) {
                        console.error('Error loading products:', error);
                        this.showToast('Gagal memuat produk', 'error');
                    }
                },

                async init() {
                    this.showLoading('Memuat data...');
                    await this.loadDashboardStats();
                    await this.loadCategories();
                    await this.loadProducts();
                    this.hideLoading();
                },

                addToCart(product) {
                    if (product.stock <= 0) {
                        this.showToast('Stok habis!', 'warning');
                        return;
                    }

                    let existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        if (existing.qty >= product.stock) {
                            this.showToast('Stok tidak cukup!', 'warning');
                            return;
                        }
                        existing.qty++;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            qty: 1,
                            maxStock: product.stock
                        });
                    }
                    this.showToast(`${product.name} ditambahkan ke keranjang`, 'success');
                },

                updateQty(index, newQty) {
                    if (newQty < 1) {
                        this.removeItem(index);
                        return;
                    }
                    let item = this.cart[index];
                    if (newQty > item.maxStock) {
                        this.showToast(`Stok hanya ${item.maxStock}`, 'warning');
                        return;
                    }
                    item.qty = newQty;
                },

                removeItem(index) {
                    const item = this.cart[index];
                    this.cart.splice(index, 1);
                    this.showToast(`${item.name} dihapus dari keranjang`, 'info');
                },

                async processPayment() {
                    if (this.cart.length === 0) {
                        this.showToast('Keranjang kosong!', 'warning');
                        return;
                    }
                    if (!this.paymentAmount || this.paymentAmount <= 0) {
                        this.showToast('Masukkan jumlah bayar!', 'warning');
                        return;
                    }
                    if (this.changeAmount < 0) {
                        this.showToast(`Uang kurang Rp ${this.formatPrice(Math.abs(this.changeAmount))}`, 'error');
                        return;
                    }

                    this.showLoading('Memproses pembayaran...');

                    const transactionData = {
                        items: this.cart.map(item => ({
                            id: item.id,
                            qty: item.qty
                        })),
                        payment_amount: this.paymentAmount
                    };

                    try {
                        const response = await fetch('/kasir/transaction', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(transactionData)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.lastTransaction = data.data;
                            this.cart = [];
                            this.paymentAmount = 0;
                            this.paymentAmountFormatted = '';
                            await this.loadProducts();
                            await this.loadDashboardStats();
                            this.hideLoading();
                            this.showSuccessModal = true;
                            this.showToast(data.message, 'success');
                        } else {
                            this.hideLoading();
                            this.showToast(data.message || 'Gagal memproses transaksi', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.hideLoading();
                        this.showToast('Terjadi kesalahan pada server', 'error');
                    }
                },

                printTransaction() {
                    let transaction = this.lastTransaction;
                    if (!transaction) return;

                    let printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <!DOCTYPE html>
                        <html>
                        <head><title>Struk Transaksi</title>
                        <style>
                            body { font-family: monospace; padding: 20px; max-width: 300px; margin: 0 auto; }
                            .header { text-align: center; border-bottom: 1px dashed #000; }
                            .store-name { font-size: 18px; font-weight: bold; }
                            hr { border: none; border-top: 1px dashed #000; margin: 10px 0; }
                            table { width: 100%; }
                            .text-right { text-align: right; }
                            .footer { text-align: center; margin-top: 20px; font-size: 10px; }
                        </style>
                        </head>
                        <body>
                            <div class="header">
                                <div class="store-name">PROShop</div>
                                <div>${transaction.transaction_number}</div>
                                <div>${transaction.date}</div>
                                <div>Kasir: {{ Auth::user()->name ?? 'Kasir' }}</div>
                                <hr>
                            </div>
                            <table width="100%">
                                <thead><tr><th>Item</th><th class="text-right">Qty</th><th class="text-right">Total</th></tr></thead>
                                <tbody>
                                    ${this.cart.map(item => `<tr><td>${item.name}</td><td class="text-right">${item.qty}</td><td class="text-right">Rp ${this.formatPrice(item.price * item.qty)}</td></tr>`).join('')}
                                </tbody>
                                <tfoot>
                                    <tr><td colspan="2"><strong>Total</strong></td><td class="text-right"><strong>Rp ${this.formatPrice(transaction.total_amount)}</strong></td></tr>
                                    <tr><td colspan="2">Bayar</td><td class="text-right">Rp ${this.formatPrice(transaction.payment_amount)}</td></tr>
                                    <tr><td colspan="2">Kembalian</td><td class="text-right">Rp ${this.formatPrice(transaction.change_amount)}</td></tr>
                                </tfoot>
                            </table>
                            <hr>
                            <div class="footer">Terima Kasih!<br>~ Selamat Belanja Kembali ~</div>
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                    printWindow.print();
                },

                closeSuccessModal() {
                    this.showSuccessModal = false;
                    this.lastTransaction = null;
                },

                cancelTransaction() {
                    if (this.cart.length > 0) this.showCancelModal = true;
                },

                confirmCancel() {
                    this.cart = [];
                    this.paymentAmount = 0;
                    this.paymentAmountFormatted = '';
                    this.showCancelModal = false;
                    this.showToast('Transaksi dibatalkan', 'info');
                }
            };
        }

        function hideToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) toast.classList.remove('show');
        }
    </script>
@endsection
