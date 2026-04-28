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

            .product-card {
                transition: all 0.2s ease;
            }

            .product-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .product-image {
                transition: transform 0.3s ease;
            }

            .product-card:hover .product-image {
                transform: scale(1.05);
            }

            .cart-item {
                transition: background 0.2s ease;
            }

            .cart-item:hover {
                background-color: #f8fafc;
            }

            .scroll-area {
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f1f5f9;
            }

            .scroll-area::-webkit-scrollbar {
                width: 6px;
            }

            .scroll-area::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 10px;
            }

            .scroll-area::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
        </style>

        <!-- STATISTIK CARD -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-xs font-medium">Transaksi Hari Ini</p>
                        <p class="text-2xl font-bold text-indigo-600" x-text="transaksiHariIni">0</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-receipt text-indigo-500 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-emerald-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-xs font-medium">Pendapatan Hari Ini</p>
                        <p class="text-2xl font-bold text-emerald-600" x-text="formatPrice(pendapatanHariIni)">Rp 0</p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-xs font-medium">Produk Tersedia</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="products.length">0</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-boxes text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-xs font-medium">Item di Keranjang</p>
                        <p class="text-2xl font-bold text-purple-600" x-text="cart.length">0</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-purple-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- KOLOM KIRI: PRODUK (2/3) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <!-- Header -->
                    <div class="p-4 border-b bg-white sticky top-0 z-10">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <h2 class="font-bold text-lg flex items-center gap-2">
                                <i class="fas fa-box text-indigo-500"></i> Semua Produk
                            </h2>
                            <div class="relative w-full sm:w-64">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" x-model="searchProduct" @input="filterProducts"
                                    placeholder="Cari produk..."
                                    class="w-full border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                        </div>
                    </div>

                    <!-- Filter Kategori -->
                    <div class="p-3 border-b bg-slate-50/50">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="cat in categories" :key="cat.id">
                                <button @click="selectedCategory = cat.name; filterProducts()"
                                    :class="selectedCategory === cat.name ? cat.color + ' text-white shadow-md' :
                                        'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200">
                                    <i :class="cat.icon" class="mr-1 text-xs"></i>
                                    <span x-text="cat.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Grid Produk -->
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[500px] overflow-y-auto scroll-area">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <div @click="addToCart(product)"
                                class="product-card border rounded-xl p-3 cursor-pointer bg-white transition-all duration-200 hover:shadow-lg"
                                :class="product.borderColor">
                                <div class="flex gap-3">
                                    <!-- Gambar -->
                                    <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                        <template x-if="product.image">
                                            <img :src="product.image" :alt="product.name"
                                                class="w-full h-full object-cover product-image">
                                        </template>
                                        <template x-if="!product.image">
                                            <div class="w-full h-full flex items-center justify-center"
                                                :class="product.bgColor">
                                                <i class="fas fa-box text-white text-2xl"></i>
                                            </div>
                                        </template>
                                    </div>
                                    <!-- Info -->
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-slate-800 text-sm line-clamp-2" x-text="product.name">
                                        </h3>
                                        <div class="text-lg font-bold mt-1" :class="product.priceColor">
                                            Rp <span x-text="formatPrice(product.price)"></span>
                                        </div>
                                        <div class="flex justify-between items-center mt-2">
                                            <div class="text-xs text-slate-400">
                                                Stok: <span x-text="product.stock"></span>
                                                <span x-show="product.stock <= 5" class="text-red-500 ml-1">⚠️</span>
                                            </div>
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-slate-600"
                                                x-text="product.categoryIcon"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="filteredProducts.length === 0" class="col-span-2 text-center py-12 text-slate-400">
                            <i class="fas fa-search text-4xl mb-3 block"></i>
                            <p>Produk tidak ditemukan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: KERANJANG (1/3) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm sticky top-5 overflow-hidden">
                    <!-- Header Keranjang -->
                    <div class="p-4 border-b bg-gradient-to-r from-orange-50 to-white">
                        <h2 class="font-bold text-lg flex items-center gap-2">
                            <i class="fas fa-shopping-cart text-orange-500"></i>
                            Keranjang Belanja
                            <span x-show="cart.length > 0"
                                class="ml-auto bg-orange-500 text-white text-xs rounded-full px-2 py-0.5"
                                x-text="cart.length"></span>
                        </h2>
                    </div>

                    <!-- List Item -->
                    <div class="p-3 max-h-[380px] overflow-y-auto scroll-area">
                        <template x-if="cart.length === 0">
                            <div class="text-center py-12 text-slate-400">
                                <i class="fas fa-cart-plus text-5xl mb-3 block text-slate-300"></i>
                                <p class="text-sm">Keranjang kosong</p>
                                <p class="text-xs mt-1">Klik produk untuk menambahkan</p>
                            </div>
                        </template>

                        <template x-for="(item, idx) in cart" :key="idx">
                            <div class="cart-item flex justify-between items-center py-3 px-2 rounded-lg transition">
                                <div class="flex-1">
                                    <h4 class="font-medium text-sm text-slate-800" x-text="item.name"></h4>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-xs font-semibold text-indigo-600">Rp <span
                                                x-text="formatPrice(item.price)"></span></span>
                                        <span class="text-xs text-slate-400">x <span x-text="item.qty"></span></span>
                                        <span class="text-xs font-semibold text-green-600">Rp <span
                                                x-text="formatPrice(item.price * item.qty)"></span></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button @click="updateQty(idx, item.qty - 1)"
                                        class="w-7 h-7 bg-slate-100 rounded-lg hover:bg-slate-200 transition flex items-center justify-center">
                                        <i class="fas fa-minus text-xs text-slate-600"></i>
                                    </button>
                                    <span class="w-8 text-center font-semibold text-sm" x-text="item.qty"></span>
                                    <button @click="updateQty(idx, item.qty + 1)"
                                        class="w-7 h-7 bg-slate-100 rounded-lg hover:bg-slate-200 transition flex items-center justify-center">
                                        <i class="fas fa-plus text-xs text-slate-600"></i>
                                    </button>
                                    <button @click="removeItem(idx)"
                                        class="ml-1 w-7 h-7 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition flex items-center justify-center">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Total -->
                    <div class="p-4 border-t bg-slate-50/80">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-slate-700">Total Belanja</span>
                            <span class="text-2xl font-bold text-indigo-600">Rp <span
                                    x-text="formatPrice(cartTotal)"></span></span>
                        </div>
                    </div>

                    <!-- Form Bayar -->
                    <div x-show="cart.length > 0" class="p-4 space-y-3 border-t">
                        <div>
                            <label class="text-xs font-medium text-slate-600 block mb-1">
                                <i class="fas fa-money-bill-wave text-green-500 mr-1"></i>Jumlah Bayar
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                                <input type="text" x-model="paymentAmountFormatted" @input="formatPaymentInput"
                                    class="w-full border border-slate-200 rounded-lg pl-10 pr-4 py-2.5 text-right text-base font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                    placeholder="0">
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-3">
                            <div class="flex justify-between items-center">
                                <span class="text-green-700 font-medium text-sm">Kembalian</span>
                                <span class="text-xl font-bold text-green-600">Rp <span
                                        x-text="formatPrice(changeAmount)"></span></span>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button @click="processPayment"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle text-sm"></i> BAYAR
                            </button>
                            <button @click="cancelTransaction"
                                class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                <i class="fas fa-times-circle text-sm"></i> BATAL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL SUKSES -->
        <div x-show="showSuccessModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6 animate-modal">
                <div class="text-center">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">✅ Transaksi Berhasil!</h3>
                    <div class="bg-slate-50 rounded-lg p-4 mb-4 text-left text-sm space-y-1">
                        <p><strong>No. Transaksi:</strong> <span class="font-mono text-indigo-600"
                                x-text="lastTransaction?.transaction_number"></span></p>
                        <p><strong>Tanggal:</strong> <span x-text="lastTransaction?.date"></span></p>
                        <p class="border-t pt-2 mt-2"><strong>Total Belanja:</strong> <span
                                class="font-bold text-green-600">Rp <span
                                    x-text="formatPrice(lastTransaction?.total_amount)"></span></span></p>
                        <p><strong>Bayar:</strong> Rp <span x-text="formatPrice(lastTransaction?.payment_amount)"></span>
                        </p>
                        <p><strong>Kembalian:</strong> <span class="font-bold text-green-600">Rp <span
                                    x-text="formatPrice(lastTransaction?.change_amount)"></span></span></p>
                    </div>
                    <div class="flex gap-3">
                        <button @click="printTransaction"
                            class="flex-1 bg-slate-600 hover:bg-slate-700 text-white py-2 rounded-lg transition text-sm">
                            <i class="fas fa-print mr-2"></i> Cetak Struk
                        </button>
                        <button @click="closeSuccessModal"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg transition text-sm">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL BATAL -->
        <div x-show="showCancelModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-sm w-full mx-4 p-6 animate-modal">
                <div class="text-center">
                    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-question-circle text-red-500 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Batalkan Transaksi?</h3>
                    <p class="text-slate-500 text-sm mb-4">Yakin ingin membatalkan transaksi ini?</p>
                    <div class="bg-red-50 rounded-lg p-3 mb-4">
                        <p class="text-xs text-slate-600">Total yang akan dibatalkan:</p>
                        <p class="text-lg font-bold text-red-600">Rp <span x-text="formatPrice(cartTotal)"></span></p>
                    </div>
                    <div class="flex gap-3">
                        <button @click="confirmCancel"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg transition text-sm">
                            Ya, Batalkan
                        </button>
                        <button @click="showCancelModal = false"
                            class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 py-2 rounded-lg transition text-sm">
                            Kembali
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- LOADING MODAL -->
        <div x-show="showLoadingModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-xs w-full mx-4 p-6 text-center animate-modal">
                <div
                    class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-3">
                </div>
                <p class="text-slate-600 text-sm" x-text="loadingMessage">Memproses...</p>
            </div>
        </div>

        <!-- TOAST NOTIFICATION -->
        <div id="toastNotification" class="toast-notification">
            <div class="flex items-center p-3">
                <div id="toastIcon" class="flex-shrink-0 mr-3"><i class="fas fa-check-circle text-lg"></i></div>
                <div class="flex-1">
                    <p id="toastMessage" class="text-sm font-medium text-slate-800"></p>
                </div>
                <button onclick="hideToast()" class="flex-shrink-0 ml-3 text-slate-400 hover:text-slate-600"><i
                        class="fas fa-times"></i></button>
            </div>
        </div>

        <style>
            @keyframes modalAnim {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            .animate-modal {
                animation: modalAnim 0.2s ease-out;
            }

            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>

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
                            toastIcon.innerHTML = '<i class="fas fa-check-circle text-emerald-500 text-lg"></i>';
                        } else if (type === 'error') {
                            toast.classList.add('toast-error');
                            toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-rose-500 text-lg"></i>';
                        } else {
                            toast.classList.add('toast-warning');
                            toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-amber-500 text-lg"></i>';
                        }
                        toastMessage.innerHTML = message;
                        toast.classList.add('show');
                        setTimeout(() => toast.classList.remove('show'), 3000);
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
                        this.showToast(`${product.name} ditambahkan`, 'success');
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
                        this.showToast(`${item.name} dihapus`, 'info');
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
                                .header { text-align: center; border-bottom: 1px dashed #000; margin-bottom: 15px; }
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
                                </div>
                                <hr>
                                <table>
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
    </div>
@endsection
