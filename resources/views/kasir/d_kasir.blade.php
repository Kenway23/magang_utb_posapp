@extends('layouts.kasir')

@section('title', 'Beranda - PROShop Kasir')
@section('header-title', 'Beranda')
@section('header-subtitle', 'Selamat bekerja!')

@section('content')
    <div x-data="posApp()" x-init="init()" x-cloak>
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
                            <template x-for="cat in categories" :key="cat.name">
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
                                    <!-- Icon dengan warna kategori -->
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                        :class="product.bgColor">
                                        <i class="fas fa-box text-white text-xl"></i>
                                    </div>
                                    <!-- Info Produk -->
                                    <div class="flex-1">
                                        <div class="font-bold text-slate-700" x-text="product.name"></div>
                                        <div class="text-2xl font-bold mt-1" :class="product.priceColor">
                                            Rp <span x-text="formatPrice(product.price || 0)"></span>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Stok: <span x-text="product.stock || 0"></span>
                                            <span x-show="(product.stock || 0) <= 5" class="text-red-500 ml-1">⚠️</span>
                                        </div>
                                    </div>
                                    <!-- SIMBOL KATEGORI DI KANAN ATAS (bukan tombol keranjang) -->
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
                                            x-text="formatPrice(item.price || 0)"></span></div>
                                    <div class="text-xs text-gray-400">x <span x-text="item.qty"></span> = Rp <span
                                            x-text="formatPrice((item.price || 0) * item.qty)"></span></div>
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
    </div>

    <script>
        function posApp() {
            return {
                products: [],
                filteredProducts: [],
                searchProduct: '',
                selectedCategory: 'Semua',
                categories: [{
                        name: 'Semua',
                        icon: 'fas fa-th-large',
                        color: 'bg-gray-600'
                    },
                    {
                        name: 'Makanan',
                        icon: 'fas fa-utensils',
                        color: 'bg-red-600'
                    },
                    {
                        name: 'Minuman',
                        icon: 'fas fa-mug-hot',
                        color: 'bg-blue-600'
                    },
                    {
                        name: 'Makanan Ringan',
                        icon: 'fas fa-cookie-bite',
                        color: 'bg-yellow-600'
                    },
                    {
                        name: 'Produk Kesehatan',
                        icon: 'fas fa-heartbeat',
                        color: 'bg-green-600'
                    },
                    {
                        name: 'Produk Kebersihan',
                        icon: 'fas fa-soap',
                        color: 'bg-cyan-600'
                    },
                    {
                        name: 'Kebutuhan Harian',
                        icon: 'fas fa-home',
                        color: 'bg-purple-600'
                    },
                    {
                        name: 'Makanan Siap Saji',
                        icon: 'fas fa-hamburger',
                        color: 'bg-orange-600'
                    },
                    {
                        name: 'Produk Segar & Beku',
                        icon: 'fas fa-snowflake',
                        color: 'bg-teal-600'
                    },
                    {
                        name: 'Kebutuhan Ibu & Anak',
                        icon: 'fas fa-baby-carriage',
                        color: 'bg-pink-600'
                    },
                    {
                        name: 'Makanan Hewan',
                        icon: 'fas fa-paw',
                        color: 'bg-amber-600'
                    },
                    {
                        name: 'Mainan',
                        icon: 'fas fa-gamepad',
                        color: 'bg-lime-600'
                    },
                    {
                        name: 'Kecantikan',
                        icon: 'fas fa-female',
                        color: 'bg-rose-600'
                    },
                    {
                        name: 'Perawatan Diri',
                        icon: 'fas fa-spa',
                        color: 'bg-emerald-600'
                    }
                ],
                cart: [],
                paymentAmount: 0,
                paymentAmountFormatted: '',
                transactions: [],
                lastTransaction: null,
                showSuccessModal: false,
                showCancelModal: false,

                get cartTotal() {
                    if (!this.cart.length) return 0;
                    return this.cart.reduce((sum, item) => sum + ((item.price || 0) * (item.qty || 0)), 0);
                },
                get changeAmount() {
                    return (this.paymentAmount || 0) - this.cartTotal;
                },

                formatPrice(price) {
                    if (price === undefined || price === null || isNaN(price)) return '0';
                    return new Intl.NumberFormat('id-ID').format(price);
                },

                parseRupiah(str) {
                    if (!str) return 0;
                    let num = parseInt(str.replace(/\./g, '').replace(/[^0-9]/g, '')) || 0;
                    return isNaN(num) ? 0 : num;
                },

                formatPaymentInput(e) {
                    let value = e.target.value;
                    let rawNumber = this.parseRupiah(value);
                    this.paymentAmount = rawNumber;
                    this.paymentAmountFormatted = this.formatPrice(rawNumber);
                    e.target.value = this.paymentAmountFormatted;
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

                init() {
                    const categoryColors = {
                        'Makanan': {
                            bg: 'bg-red-600',
                            border: 'border-red-200',
                            badge: 'bg-red-600',
                            price: 'text-red-600',
                            borderTop: 'border-t-red-600',
                            bgLight: 'bg-red-50',
                            categoryIcon: '🍚'
                        },
                        'Minuman': {
                            bg: 'bg-blue-600',
                            border: 'border-blue-200',
                            badge: 'bg-blue-600',
                            price: 'text-blue-600',
                            borderTop: 'border-t-blue-600',
                            bgLight: 'bg-blue-50',
                            categoryIcon: '🥤'
                        },
                        'Makanan Ringan': {
                            bg: 'bg-yellow-600',
                            border: 'border-yellow-200',
                            badge: 'bg-yellow-600',
                            price: 'text-yellow-600',
                            borderTop: 'border-t-yellow-600',
                            bgLight: 'bg-yellow-50',
                            categoryIcon: '🍿'
                        },
                        'Produk Kesehatan': {
                            bg: 'bg-green-600',
                            border: 'border-green-200',
                            badge: 'bg-green-600',
                            price: 'text-green-600',
                            borderTop: 'border-t-green-600',
                            bgLight: 'bg-green-50',
                            categoryIcon: '💊'
                        },
                        'Produk Kebersihan': {
                            bg: 'bg-cyan-600',
                            border: 'border-cyan-200',
                            badge: 'bg-cyan-600',
                            price: 'text-cyan-600',
                            borderTop: 'border-t-cyan-600',
                            bgLight: 'bg-cyan-50',
                            categoryIcon: '🧼'
                        },
                        'Kebutuhan Harian': {
                            bg: 'bg-purple-600',
                            border: 'border-purple-200',
                            badge: 'bg-purple-600',
                            price: 'text-purple-600',
                            borderTop: 'border-t-purple-600',
                            bgLight: 'bg-purple-50',
                            categoryIcon: '📦'
                        },
                        'Makanan Siap Saji': {
                            bg: 'bg-orange-600',
                            border: 'border-orange-200',
                            badge: 'bg-orange-600',
                            price: 'text-orange-600',
                            borderTop: 'border-t-orange-600',
                            bgLight: 'bg-orange-50',
                            categoryIcon: '🍔'
                        },
                        'Produk Segar & Beku': {
                            bg: 'bg-teal-600',
                            border: 'border-teal-200',
                            badge: 'bg-teal-600',
                            price: 'text-teal-600',
                            borderTop: 'border-t-teal-600',
                            bgLight: 'bg-teal-50',
                            categoryIcon: '❄️'
                        },
                        'Kebutuhan Ibu & Anak': {
                            bg: 'bg-pink-600',
                            border: 'border-pink-200',
                            badge: 'bg-pink-600',
                            price: 'text-pink-600',
                            borderTop: 'border-t-pink-600',
                            bgLight: 'bg-pink-50',
                            categoryIcon: '👶'
                        },
                        'Makanan Hewan': {
                            bg: 'bg-amber-600',
                            border: 'border-amber-200',
                            badge: 'bg-amber-600',
                            price: 'text-amber-600',
                            borderTop: 'border-t-amber-600',
                            bgLight: 'bg-amber-50',
                            categoryIcon: '🐕'
                        },
                        'Mainan': {
                            bg: 'bg-lime-600',
                            border: 'border-lime-200',
                            badge: 'bg-lime-600',
                            price: 'text-lime-600',
                            borderTop: 'border-t-lime-600',
                            bgLight: 'bg-lime-50',
                            categoryIcon: '🎮'
                        },
                        'Kecantikan': {
                            bg: 'bg-rose-600',
                            border: 'border-rose-200',
                            badge: 'bg-rose-600',
                            price: 'text-rose-600',
                            borderTop: 'border-t-rose-600',
                            bgLight: 'bg-rose-50',
                            categoryIcon: '💄'
                        },
                        'Perawatan Diri': {
                            bg: 'bg-emerald-600',
                            border: 'border-emerald-200',
                            badge: 'bg-emerald-600',
                            price: 'text-emerald-600',
                            borderTop: 'border-t-emerald-600',
                            bgLight: 'bg-emerald-50',
                            categoryIcon: '🧴'
                        }
                    };

                    this.products = [{
                            id: 1,
                            name: 'Indomie Goreng',
                            category: 'Makanan',
                            price: 3800,
                            stock: 108
                        },
                        {
                            id: 2,
                            name: 'Indomie Kuah',
                            category: 'Makanan',
                            price: 3800,
                            stock: 75
                        },
                        {
                            id: 3,
                            name: 'Teh Botol Sosro',
                            category: 'Minuman',
                            price: 5000,
                            stock: 85
                        },
                        {
                            id: 4,
                            name: 'Aqua 600ml',
                            category: 'Minuman',
                            price: 4000,
                            stock: 120
                        },
                        {
                            id: 5,
                            name: 'Pocky Coklat',
                            category: 'Makanan Ringan',
                            price: 7900,
                            stock: 80
                        },
                        {
                            id: 6,
                            name: 'Roma Kelapa',
                            category: 'Makanan Ringan',
                            price: 6000,
                            stock: 65
                        },
                        {
                            id: 7,
                            name: 'Paracetamol',
                            category: 'Produk Kesehatan',
                            price: 5000,
                            stock: 50
                        },
                        {
                            id: 8,
                            name: 'Sabun Lifebuoy',
                            category: 'Produk Kebersihan',
                            price: 3500,
                            stock: 45
                        },
                        {
                            id: 9,
                            name: 'Rinso Bubuk',
                            category: 'Kebutuhan Harian',
                            price: 20700,
                            stock: 100
                        },
                        {
                            id: 10,
                            name: 'Indomie Cup',
                            category: 'Makanan Siap Saji',
                            price: 8000,
                            stock: 60
                        },
                        {
                            id: 11,
                            name: 'Daging Sapi Segar',
                            category: 'Produk Segar & Beku',
                            price: 120000,
                            stock: 15
                        },
                        {
                            id: 12,
                            name: 'Pampers Baby',
                            category: 'Kebutuhan Ibu & Anak',
                            price: 45000,
                            stock: 25
                        },
                        {
                            id: 13,
                            name: 'Whiskas',
                            category: 'Makanan Hewan',
                            price: 25000,
                            stock: 30
                        },
                        {
                            id: 14,
                            name: 'Lego Bricks',
                            category: 'Mainan',
                            price: 150000,
                            stock: 10
                        },
                        {
                            id: 15,
                            name: 'Lipstik Matte',
                            category: 'Kecantikan',
                            price: 35000,
                            stock: 40
                        },
                        {
                            id: 16,
                            name: 'Shampo Sunsilk',
                            category: 'Perawatan Diri',
                            price: 12000,
                            stock: 55
                        }
                    ];

                    this.products = this.products.map(p => ({
                        ...p,
                        ...categoryColors[p.category]
                    }));
                    this.filteredProducts = [...this.products];
                    this.transactions = JSON.parse(localStorage.getItem('transactions') || '[]');

                    let savedCart = localStorage.getItem('posCart');
                    if (savedCart) {
                        try {
                            this.cart = JSON.parse(savedCart);
                            this.cart = this.cart.filter(item => item.price > 0);
                        } catch (e) {
                            this.cart = [];
                        }
                    }
                },

                saveCart() {
                    localStorage.setItem('posCart', JSON.stringify(this.cart));
                },

                addToCart(product) {
                    if (!product || product.stock <= 0) {
                        alert('Stok habis!');
                        return;
                    }
                    if (!product.price || product.price <= 0) {
                        alert('Harga produk tidak valid!');
                        return;
                    }

                    let existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        if (existing.qty >= product.stock) {
                            alert('Stok tidak cukup!');
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
                    this.saveCart();
                },

                updateQty(index, newQty) {
                    if (newQty < 1) {
                        this.removeItem(index);
                        return;
                    }
                    let item = this.cart[index];
                    if (!item) return;
                    if (newQty > item.maxStock) {
                        alert('Stok hanya ' + item.maxStock);
                        return;
                    }
                    item.qty = newQty;
                    this.saveCart();
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                    this.saveCart();
                },

                processPayment() {
                    if (this.cart.length === 0) {
                        alert('Keranjang kosong!');
                        return;
                    }
                    if (!this.paymentAmount || this.paymentAmount <= 0) {
                        alert('Masukkan jumlah bayar!');
                        return;
                    }
                    if (this.changeAmount < 0) {
                        alert('Uang kurang Rp ' + this.formatPrice(Math.abs(this.changeAmount)));
                        return;
                    }

                    let now = new Date();
                    let transactionNumber = 'TRX-' + now.getFullYear() + (now.getMonth() + 1).toString().padStart(2, '0') +
                        now.getDate().toString().padStart(2, '0') + '-' + Math.floor(Math.random() * 10000).toString()
                        .padStart(4, '0');

                    let newTransaction = {
                        id: Date.now(),
                        transaction_number: transactionNumber,
                        date: now.toLocaleString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }),
                        cashier: 'Kasir',
                        items: [...this.cart],
                        total_amount: this.cartTotal,
                        payment_amount: this.paymentAmount,
                        change_amount: this.changeAmount
                    };

                    this.cart.forEach(cartItem => {
                        let product = this.products.find(p => p.id === cartItem.id);
                        if (product) product.stock -= cartItem.qty;
                    });

                    this.transactions.unshift(newTransaction);
                    localStorage.setItem('transactions', JSON.stringify(this.transactions));

                    this.lastTransaction = newTransaction;
                    this.cart = [];
                    this.paymentAmount = 0;
                    this.paymentAmountFormatted = '';
                    this.saveCart();
                    this.filterProducts();
                    this.showSuccessModal = true;
                },

                printTransaction() {
                    let transaction = this.lastTransaction;
                    if (!transaction) return;
                    let self = this;
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
                            <div>Kasir: ${transaction.cashier}</div>
                            <hr>
                        </div>
                        <table width="100%">
                            <thead><tr><th>Item</th><th class="text-right">Qty</th><th class="text-right">Total</th></tr></thead>
                            <tbody>
                                ${transaction.items.map(item => `<tr><td>${item.name}</td><td class="text-right">${item.qty}</td><td class="text-right">Rp ${self.formatPrice(item.price * item.qty)}</td></tr>`).join('')}
                            </tbody>
                            <tfoot>
                                <tr><td colspan="2"><strong>Total</strong></td><td class="text-right"><strong>Rp ${self.formatPrice(transaction.total_amount)}</strong></td></tr>
                                <tr><td colspan="2">Bayar</td><td class="text-right">Rp ${self.formatPrice(transaction.payment_amount)}</td></tr>
                                <tr><td colspan="2">Kembalian</td><td class="text-right">Rp ${self.formatPrice(transaction.change_amount)}</td></tr>
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
                    this.saveCart();
                    this.showCancelModal = false;
                }
            };
        }
    </script>
@endsection
