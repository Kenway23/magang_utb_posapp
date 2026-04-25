@extends('layouts.kasir')

@section('title', 'Manajemen Produk - PROShop')
@section('header-title', 'Manajemen Produk')
@section('header-subtitle', 'Kelola data produk (Tambah, Edit, Hapus)')

@section('content')
    <div x-data="produkApp()" x-init="init()" x-cloak>
        <div class="space-y-6">

            <!-- Tombol Tambah & Pencarian -->
            <div class="bg-white rounded-xl shadow-sm p-4 flex justify-between items-center flex-wrap gap-3">
                <button @click="openModal()"
                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Produk
                </button>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="search" @input="filterProduk" placeholder="Cari produk..."
                        class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <!-- Filter Kategori (13 Kategori) -->
            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="flex flex-wrap gap-2">
                    <template x-for="cat in categories" :key="cat.name">
                        <button @click="selectedCategory = cat.name; filterProduk()"
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
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-200 border-t-4"
                        :class="product.borderTopColor">

                        <!-- Header Card -->
                        <div class="p-4" :class="product.bgLight">
                            <div class="flex justify-between items-start">
                                <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-2xl"
                                    :class="product.bgColor">
                                    <i class="fas fa-box"></i>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full text-white" :class="product.badgeColor"
                                    x-text="product.category"></span>
                            </div>
                            <h3 class="font-bold text-lg text-gray-800 mt-3" x-text="product.name"></h3>
                            <div class="text-2xl font-bold mt-2" :class="product.priceColor">
                                Rp <span x-text="formatPrice(product.price)"></span>
                            </div>
                        </div>

                        <!-- Footer Card -->
                        <div class="p-4 border-t flex justify-between items-center" :class="product.borderColor">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-boxes text-gray-400"></i>
                                <span class="text-sm"
                                    :class="product.stock <= 5 ? 'text-red-600 font-bold' : 'text-gray-600'">
                                    Stok: <span x-text="product.stock"></span>
                                </span>
                                <span x-show="product.stock <= 5" class="text-red-500 text-sm">⚠️</span>
                            </div>
                            <div class="flex gap-2">
                                <button @click="editProduct(product)"
                                    class="text-blue-600 hover:text-blue-800 transition p-1">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="confirmDelete(product)"
                                    class="text-red-600 hover:text-red-800 transition p-1">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="filteredProducts.length === 0" class="col-span-full text-center py-16 text-gray-400">
                    <i class="fas fa-box-open text-6xl mb-3 block"></i>
                    <p class="text-lg">Belum ada produk</p>
                    <p class="text-sm mt-1">Klik tombol "Tambah Produk" untuk menambahkan</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-chart-line mr-1"></i> Total <span x-text="filteredProducts.length"></span> produk
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-money-bill-wave mr-1"></i> Total nilai inventaris: Rp <span
                        x-text="formatPrice(totalInventoryValue)"></span>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH/EDIT PRODUK -->
        <div x-show="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-md w-full mx-4">
                <div class="p-5 border-b flex justify-between items-center" :class="modalHeaderColor">
                    <h3 class="font-bold text-lg" x-text="modalTitle"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600"><i
                            class="fas fa-times"></i></button>
                </div>
                <form @submit.prevent="saveProduct">
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Produk <span
                                    class="text-red-500">*</span></label>
                            <input type="text" x-model="form.name" required
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Kategori</label>
                            <select x-model="form.category" @change="updateCategoryColor"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="Makanan">🍚 Makanan</option>
                                <option value="Minuman">🥤 Minuman</option>
                                <option value="Makanan Ringan">🍿 Makanan Ringan</option>
                                <option value="Produk Kesehatan">💊 Produk Kesehatan</option>
                                <option value="Produk Kebersihan">🧼 Produk Kebersihan</option>
                                <option value="Kebutuhan Harian">📦 Kebutuhan Harian</option>
                                <option value="Makanan Siap Saji">🍔 Makanan Siap Saji</option>
                                <option value="Produk Segar & Beku">❄️ Produk Segar & Beku</option>
                                <option value="Kebutuhan Ibu & Anak">👶 Kebutuhan Ibu & Anak</option>
                                <option value="Makanan Hewan">🐕 Makanan Hewan</option>
                                <option value="Mainan">🎮 Mainan</option>
                                <option value="Kecantikan">💄 Kecantikan</option>
                                <option value="Perawatan Diri">🧴 Perawatan Diri</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Harga (Rp)</label>
                            <input type="number" x-model="form.price" required min="0"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Stok</label>
                            <input type="number" x-model="form.stock" required min="0"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>

                        <!-- Preview Warna -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-2">Preview warna kategori:</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg" :class="previewColor.bg"></div>
                                <span class="text-sm" x-text="previewColor.name"></span>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 border-t flex gap-3">
                        <button type="submit" class="flex-1 text-white py-2 rounded-lg transition"
                            :class="submitButtonColor">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <button type="button" @click="closeModal()"
                            class="flex-1 bg-gray-200 py-2 rounded-lg hover:bg-gray-300">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL KONFIRMASI HAPUS -->
        <div x-show="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-sm w-full mx-4 p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Hapus Produk?</h3>
                <p class="text-gray-500 mb-4">Yakin ingin menghapus "<span x-text="productToDelete?.name"
                        class="font-bold text-red-600"></span>"?</p>
                <div class="flex gap-3">
                    <button @click="deleteProduct()" class="flex-1 bg-red-600 text-white py-2 rounded-lg">Hapus</button>
                    <button @click="showDeleteModal = false" class="flex-1 bg-gray-200 py-2 rounded-lg">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function produkApp() {
            return {
                products: [],
                filteredProducts: [],
                search: '',
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
                showModal: false,
                showDeleteModal: false,
                modalTitle: 'Tambah Produk',
                isEdit: false,
                form: {
                    id: null,
                    name: '',
                    category: 'Makanan',
                    price: 0,
                    stock: 0
                },
                productToDelete: null,

                // Warna per kategori (13 Kategori)
                categoryColors: {
                    'Makanan': {
                        bg: 'bg-red-600',
                        bgLight: 'bg-red-50',
                        borderTop: 'border-t-red-600',
                        border: 'border-red-100',
                        badge: 'bg-red-600',
                        price: 'text-red-600',
                        modalHeader: 'border-red-200',
                        submitBtn: 'bg-red-600 hover:bg-red-700',
                        preview: {
                            bg: 'bg-red-600',
                            name: 'Merah (Makanan)'
                        }
                    },
                    'Minuman': {
                        bg: 'bg-blue-600',
                        bgLight: 'bg-blue-50',
                        borderTop: 'border-t-blue-600',
                        border: 'border-blue-100',
                        badge: 'bg-blue-600',
                        price: 'text-blue-600',
                        modalHeader: 'border-blue-200',
                        submitBtn: 'bg-blue-600 hover:bg-blue-700',
                        preview: {
                            bg: 'bg-blue-600',
                            name: 'Biru (Minuman)'
                        }
                    },
                    'Makanan Ringan': {
                        bg: 'bg-yellow-600',
                        bgLight: 'bg-yellow-50',
                        borderTop: 'border-t-yellow-600',
                        border: 'border-yellow-100',
                        badge: 'bg-yellow-600',
                        price: 'text-yellow-600',
                        modalHeader: 'border-yellow-200',
                        submitBtn: 'bg-yellow-600 hover:bg-yellow-700',
                        preview: {
                            bg: 'bg-yellow-600',
                            name: 'Kuning (Makanan Ringan)'
                        }
                    },
                    'Produk Kesehatan': {
                        bg: 'bg-green-600',
                        bgLight: 'bg-green-50',
                        borderTop: 'border-t-green-600',
                        border: 'border-green-100',
                        badge: 'bg-green-600',
                        price: 'text-green-600',
                        modalHeader: 'border-green-200',
                        submitBtn: 'bg-green-600 hover:bg-green-700',
                        preview: {
                            bg: 'bg-green-600',
                            name: 'Hijau (Produk Kesehatan)'
                        }
                    },
                    'Produk Kebersihan': {
                        bg: 'bg-cyan-600',
                        bgLight: 'bg-cyan-50',
                        borderTop: 'border-t-cyan-600',
                        border: 'border-cyan-100',
                        badge: 'bg-cyan-600',
                        price: 'text-cyan-600',
                        modalHeader: 'border-cyan-200',
                        submitBtn: 'bg-cyan-600 hover:bg-cyan-700',
                        preview: {
                            bg: 'bg-cyan-600',
                            name: 'Cyan (Produk Kebersihan)'
                        }
                    },
                    'Kebutuhan Harian': {
                        bg: 'bg-purple-600',
                        bgLight: 'bg-purple-50',
                        borderTop: 'border-t-purple-600',
                        border: 'border-purple-100',
                        badge: 'bg-purple-600',
                        price: 'text-purple-600',
                        modalHeader: 'border-purple-200',
                        submitBtn: 'bg-purple-600 hover:bg-purple-700',
                        preview: {
                            bg: 'bg-purple-600',
                            name: 'Ungu (Kebutuhan Harian)'
                        }
                    },
                    'Makanan Siap Saji': {
                        bg: 'bg-orange-600',
                        bgLight: 'bg-orange-50',
                        borderTop: 'border-t-orange-600',
                        border: 'border-orange-100',
                        badge: 'bg-orange-600',
                        price: 'text-orange-600',
                        modalHeader: 'border-orange-200',
                        submitBtn: 'bg-orange-600 hover:bg-orange-700',
                        preview: {
                            bg: 'bg-orange-600',
                            name: 'Oranye (Makanan Siap Saji)'
                        }
                    },
                    'Produk Segar & Beku': {
                        bg: 'bg-teal-600',
                        bgLight: 'bg-teal-50',
                        borderTop: 'border-t-teal-600',
                        border: 'border-teal-100',
                        badge: 'bg-teal-600',
                        price: 'text-teal-600',
                        modalHeader: 'border-teal-200',
                        submitBtn: 'bg-teal-600 hover:bg-teal-700',
                        preview: {
                            bg: 'bg-teal-600',
                            name: 'Teal (Produk Segar & Beku)'
                        }
                    },
                    'Kebutuhan Ibu & Anak': {
                        bg: 'bg-pink-600',
                        bgLight: 'bg-pink-50',
                        borderTop: 'border-t-pink-600',
                        border: 'border-pink-100',
                        badge: 'bg-pink-600',
                        price: 'text-pink-600',
                        modalHeader: 'border-pink-200',
                        submitBtn: 'bg-pink-600 hover:bg-pink-700',
                        preview: {
                            bg: 'bg-pink-600',
                            name: 'Pink (Kebutuhan Ibu & Anak)'
                        }
                    },
                    'Makanan Hewan': {
                        bg: 'bg-amber-600',
                        bgLight: 'bg-amber-50',
                        borderTop: 'border-t-amber-600',
                        border: 'border-amber-100',
                        badge: 'bg-amber-600',
                        price: 'text-amber-600',
                        modalHeader: 'border-amber-200',
                        submitBtn: 'bg-amber-600 hover:bg-amber-700',
                        preview: {
                            bg: 'bg-amber-600',
                            name: 'Amber (Makanan Hewan)'
                        }
                    },
                    'Mainan': {
                        bg: 'bg-lime-600',
                        bgLight: 'bg-lime-50',
                        borderTop: 'border-t-lime-600',
                        border: 'border-lime-100',
                        badge: 'bg-lime-600',
                        price: 'text-lime-600',
                        modalHeader: 'border-lime-200',
                        submitBtn: 'bg-lime-600 hover:bg-lime-700',
                        preview: {
                            bg: 'bg-lime-600',
                            name: 'Hijau Muda (Mainan)'
                        }
                    },
                    'Kecantikan': {
                        bg: 'bg-rose-600',
                        bgLight: 'bg-rose-50',
                        borderTop: 'border-t-rose-600',
                        border: 'border-rose-100',
                        badge: 'bg-rose-600',
                        price: 'text-rose-600',
                        modalHeader: 'border-rose-200',
                        submitBtn: 'bg-rose-600 hover:bg-rose-700',
                        preview: {
                            bg: 'bg-rose-600',
                            name: 'Merah Muda (Kecantikan)'
                        }
                    },
                    'Perawatan Diri': {
                        bg: 'bg-emerald-600',
                        bgLight: 'bg-emerald-50',
                        borderTop: 'border-t-emerald-600',
                        border: 'border-emerald-100',
                        badge: 'bg-emerald-600',
                        price: 'text-emerald-600',
                        modalHeader: 'border-emerald-200',
                        submitBtn: 'bg-emerald-600 hover:bg-emerald-700',
                        preview: {
                            bg: 'bg-emerald-600',
                            name: 'Hijau Zamrud (Perawatan Diri)'
                        }
                    }
                },

                get modalHeaderColor() {
                    return this.categoryColors[this.form.category]?.modalHeader || 'border-gray-200';
                },
                get submitButtonColor() {
                    return this.categoryColors[this.form.category]?.submitBtn || 'bg-indigo-600 hover:bg-indigo-700';
                },
                get previewColor() {
                    return this.categoryColors[this.form.category]?.preview || {
                        bg: 'bg-gray-600',
                        name: 'Abu-abu'
                    };
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                },

                filterProduk() {
                    let filtered = [...this.products];
                    if (this.selectedCategory !== 'Semua') {
                        filtered = filtered.filter(p => p.category === this.selectedCategory);
                    }
                    if (this.search) {
                        filtered = filtered.filter(p => p.name.toLowerCase().includes(this.search.toLowerCase()));
                    }
                    this.filteredProducts = filtered;
                },

                updateCategoryColor() {
                    this.$forceUpdate();
                },

                init() {
                    let saved = localStorage.getItem('products_pos');
                    if (saved) {
                        this.products = JSON.parse(saved);
                    } else {
                        this.products = [
                            // Makanan (Merah)
                            {
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
                            // Minuman (Biru)
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
                            // Makanan Ringan (Kuning)
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
                            // Produk Kesehatan (Hijau)
                            {
                                id: 7,
                                name: 'Paracetamol',
                                category: 'Produk Kesehatan',
                                price: 5000,
                                stock: 50
                            },
                            {
                                id: 8,
                                name: 'Promag',
                                category: 'Produk Kesehatan',
                                price: 5500,
                                stock: 45
                            },
                            // Produk Kebersihan (Cyan)
                            {
                                id: 9,
                                name: 'Sabun Lifebuoy',
                                category: 'Produk Kebersihan',
                                price: 3500,
                                stock: 45
                            },
                            // Kebutuhan Harian (Ungu)
                            {
                                id: 10,
                                name: 'Rinso Bubuk',
                                category: 'Kebutuhan Harian',
                                price: 20700,
                                stock: 100
                            },
                            // Makanan Siap Saji (Oranye)
                            {
                                id: 11,
                                name: 'Indomie Cup',
                                category: 'Makanan Siap Saji',
                                price: 8000,
                                stock: 60
                            },
                            // Produk Segar & Beku (Teal)
                            {
                                id: 12,
                                name: 'Daging Sapi Segar',
                                category: 'Produk Segar & Beku',
                                price: 120000,
                                stock: 15
                            },
                            // Kebutuhan Ibu & Anak (Pink)
                            {
                                id: 13,
                                name: 'Pampers Baby',
                                category: 'Kebutuhan Ibu & Anak',
                                price: 45000,
                                stock: 25
                            },
                            // Makanan Hewan (Amber)
                            {
                                id: 14,
                                name: 'Whiskas',
                                category: 'Makanan Hewan',
                                price: 25000,
                                stock: 30
                            },
                            // Mainan (Lime)
                            {
                                id: 15,
                                name: 'Lego Bricks',
                                category: 'Mainan',
                                price: 150000,
                                stock: 10
                            },
                            // Kecantikan (Rose)
                            {
                                id: 16,
                                name: 'Lipstik Matte',
                                category: 'Kecantikan',
                                price: 35000,
                                stock: 40
                            },
                            // Perawatan Diri (Emerald)
                            {
                                id: 17,
                                name: 'Shampo Sunsilk',
                                category: 'Perawatan Diri',
                                price: 12000,
                                stock: 55
                            }
                        ];
                    }
                    // Tambahkan warna ke setiap produk
                    this.products = this.products.map(p => ({
                        ...p,
                        ...this.categoryColors[p.category]
                    }));
                    this.filteredProducts = [...this.products];
                },

                saveProducts() {
                    let productsToSave = this.products.map(p => ({
                        id: p.id,
                        name: p.name,
                        category: p.category,
                        price: p.price,
                        stock: p.stock
                    }));
                    localStorage.setItem('products_pos', JSON.stringify(productsToSave));
                    this.filterProduk();
                },

                openModal() {
                    this.isEdit = false;
                    this.modalTitle = 'Tambah Produk';
                    this.form = {
                        id: null,
                        name: '',
                        category: 'Makanan',
                        price: 0,
                        stock: 0
                    };
                    this.showModal = true;
                },

                editProduct(product) {
                    this.isEdit = true;
                    this.modalTitle = 'Edit Produk';
                    this.form = {
                        ...product
                    };
                    this.showModal = true;
                },

                saveProduct() {
                    if (!this.form.name) {
                        alert('Nama produk harus diisi!');
                        return;
                    }
                    if (this.form.price <= 0) {
                        alert('Harga harus lebih dari 0!');
                        return;
                    }
                    if (this.form.stock < 0) {
                        alert('Stok tidak boleh negatif!');
                        return;
                    }

                    if (this.isEdit) {
                        let index = this.products.findIndex(p => p.id === this.form.id);
                        if (index !== -1) {
                            this.products[index] = {
                                ...this.form,
                                ...this.categoryColors[this.form.category]
                            };
                        }
                    } else {
                        let newId = Math.max(...this.products.map(p => p.id), 0) + 1;
                        this.products.push({
                            id: newId,
                            ...this.form,
                            ...this.categoryColors[this.form.category]
                        });
                    }
                    this.saveProducts();
                    this.closeModal();
                    alert(this.isEdit ? '✅ Produk berhasil diupdate!' : '✅ Produk berhasil ditambahkan!');
                },

                confirmDelete(product) {
                    this.productToDelete = product;
                    this.showDeleteModal = true;
                },

                deleteProduct() {
                    let index = this.products.findIndex(p => p.id === this.productToDelete.id);
                    if (index !== -1) {
                        this.products.splice(index, 1);
                        this.saveProducts();
                    }
                    this.showDeleteModal = false;
                    this.productToDelete = null;
                    alert('🗑️ Produk berhasil dihapus!');
                },

                closeModal() {
                    this.showModal = false;
                    this.form = {
                        id: null,
                        name: '',
                        category: 'Makanan',
                        price: 0,
                        stock: 0
                    };
                }
            };
        }
    </script>
@endsection
