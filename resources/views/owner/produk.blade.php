@extends('layouts.owner')

@section('title', 'Satuan Barang - PROShop')
@section('header-title', 'Satuan Barang')
@section('header-subtitle', 'Monitor semua produk yang sudah disetujui')

@section('content')
    <div class="space-y-6">
        <!-- Filter & Search -->
        <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-4">
            <div class="flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-2" id="filterButtons">
                    <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white"
                        data-filter="all">Semua</button>
                    @foreach ($kategori as $kat)
                        <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100"
                            data-filter="{{ $kat->nama_kategori }}">{{ $kat->nama_kategori }}</button>
                    @endforeach
                </div>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari produk..."
                        class="pl-10 pr-4 py-2 border rounded-lg w-64">
                    <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Info Card Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Total Produk</p>
                        <p class="text-2xl font-bold text-slate-800" id="totalProduk">0</p>
                    </div>
                    <i class="fas fa-boxes text-3xl text-indigo-400"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Nilai Inventaris</p>
                        <p class="text-2xl font-bold text-slate-800" id="totalNilai">Rp 0</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-3xl text-green-400"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Stok Menipis (≤10)</p>
                        <p class="text-2xl font-bold text-slate-800" id="stokMenipis">0</p>
                    </div>
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-400"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Produk Kosong</p>
                        <p class="text-2xl font-bold text-slate-800" id="produkKosong">0</p>
                    </div>
                    <i class="fas fa-box-open text-3xl text-red-400"></i>
                </div>
            </div>
        </div>

        <!-- GRID PRODUK -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" id="productGrid"></div>
    </div>

    <!-- Modal Detail Produk -->
    <div id="modalDetailProduk" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div
                class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white rounded-t-2xl flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-info-circle text-indigo-600 mr-2"></i>Detail Produk</h3>
                <button onclick="closeModal('modalDetailProduk')" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-3" id="detailContent"></div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
                <button onclick="closeModal('modalDetailProduk')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Tutup</button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="fixed top-5 right-5 z-50 hidden transition-all duration-300">
        <div class="bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-l-4" id="toastContent">
            <div class="flex items-center gap-3">
                <div id="toastIcon"></div>
                <div class="flex-1">
                    <p id="toastMessage" class="text-sm font-medium"></p>
                </div>
                <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        let products = [];
        let categories = @json($kategori);

        // Ambil data dari database
        function loadProducts() {
            fetch('/owner/produk/data')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        products = data.data;
                        renderProducts();
                    } else {
                        showToast('Gagal memuat data produk', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        }

        function renderProducts() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const activeFilter = document.querySelector('.filter-btn.bg-indigo-600.text-white')?.dataset.filter || 'all';

            let filtered = products.filter(p => p.status === 'approved');

            filtered = filtered.filter(p =>
                (activeFilter === 'all' || p.kategori?.nama_kategori === activeFilter) &&
                p.nama_produk.toLowerCase().includes(search)
            );

            const grid = document.getElementById('productGrid');

            // Update statistik
            updateStats(filtered);

            if (filtered.length === 0) {
                grid.innerHTML =
                    '<div class="col-span-full text-center py-12"><i class="fas fa-box-open text-5xl text-slate-300 mb-3"></i><p class="text-slate-500">Tidak ada produk</p></div>';
                return;
            }

            grid.innerHTML = filtered.map(p => {
                const stok = p.stok_gudang || 0;
                const stockClass = stok < 10 ? 'text-red-600 font-bold' : (stok < 20 ? 'text-orange-600' :
                    'text-slate-700');
                const stockWarning = stok < 10 ?
                    '<span class="ml-1 text-red-500 text-xs animate-pulse">⚠️ Stok Kritis</span>' : (stok < 20 ?
                        '<span class="ml-1 text-orange-500 text-xs">⚠️ Stok Menipis</span>' : '');

                // Warna berdasarkan kategori
                let bgColor = 'indigo';
                let icon = 'fa-box';

                const categoryName = p.kategori?.nama_kategori || 'Lainnya';
                if (categoryName === 'Makanan') {
                    bgColor = 'red';
                    icon = 'fa-utensils';
                } else if (categoryName === 'Minuman') {
                    bgColor = 'blue';
                    icon = 'fa-mug-hot';
                } else if (categoryName === 'Snack') {
                    bgColor = 'yellow';
                    icon = 'fa-cookie-bite';
                } else if (categoryName === 'Makanan Siap Saji') {
                    bgColor = 'orange';
                    icon = 'fa-hamburger';
                } else if (categoryName === 'Rokok') {
                    bgColor = 'gray';
                    icon = 'fa-smoking';
                } else if (categoryName === 'Perawatan Tubuh') {
                    bgColor = 'green';
                    icon = 'fa-spa';
                } else if (categoryName === 'Kebersihan') {
                    bgColor = 'cyan';
                    icon = 'fa-pump-soap';
                } else if (categoryName === 'Kesehatan') {
                    bgColor = 'emerald';
                    icon = 'fa-capsules';
                }

                // Gambar URL
                let imageUrl = null;
                if (p.gambar_produk) {
                    if (p.gambar_produk.startsWith('produk/')) {
                        imageUrl = `/storage/${p.gambar_produk}`;
                    } else if (p.gambar_produk.startsWith('/storage/')) {
                        imageUrl = p.gambar_produk;
                    } else {
                        imageUrl = `/storage/produk/${p.gambar_produk}`;
                    }
                }

                return `
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border hover:shadow-xl transition group">
                    <div class="bg-${bgColor}-100 h-32 flex items-center justify-center relative">
                        ${imageUrl ? 
                            `<img src="${imageUrl}" class="w-full h-full object-cover">` : 
                            `<i class="fas ${icon} text-5xl text-${bgColor}-600"></i>`
                        }
                        <div class="absolute top-2 right-2">
                            <button onclick="lihatDetail(${p.produk_id})" class="bg-indigo-600/80 hover:bg-indigo-600 p-1.5 rounded-full transition">
                                <i class="fas fa-eye text-white text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-semibold text-lg">${escapeHtml(p.nama_produk)}</h4>
                        <p class="text-xs text-slate-400 mb-2">${escapeHtml(categoryName)}</p>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm">Harga</span>
                            <span class="font-bold text-green-600">Rp ${formatPrice(p.harga)}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Stok</span>
                            <div class="text-right">
                                <span class="font-semibold ${stockClass}">${stok}</span>
                                ${stockWarning}
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t">
                            <button onclick="lihatDetail(${p.produk_id})" class="w-full bg-indigo-50 text-indigo-600 py-1.5 rounded-lg text-sm hover:bg-indigo-100 transition">
                                <i class="fas fa-eye mr-1"></i> Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
                `;
            }).join('');
        }

        function updateStats(products) {
            const totalProduk = products.length;
            const totalNilai = products.reduce((sum, p) => sum + (p.harga * (p.stok_gudang || 0)), 0);
            const stokMenipis = products.filter(p => (p.stok_gudang || 0) < 20 && (p.stok_gudang || 0) > 0).length;
            const produkKosong = products.filter(p => (p.stok_gudang || 0) === 0).length;

            document.getElementById('totalProduk').innerText = totalProduk;
            document.getElementById('totalNilai').innerText = 'Rp ' + formatPrice(totalNilai);
            document.getElementById('stokMenipis').innerText = stokMenipis;
            document.getElementById('produkKosong').innerText = produkKosong;
        }

        function lihatDetail(id) {
            const product = products.find(p => p.produk_id === id);
            if (product) {
                const stok = product.stok_gudang || 0;
                const categoryName = product.kategori?.nama_kategori || 'Lainnya';

                let bgColor = 'indigo';
                let icon = 'fa-box';
                if (categoryName === 'Makanan') {
                    bgColor = 'red';
                    icon = 'fa-utensils';
                } else if (categoryName === 'Minuman') {
                    bgColor = 'blue';
                    icon = 'fa-mug-hot';
                } else if (categoryName === 'Snack') {
                    bgColor = 'yellow';
                    icon = 'fa-cookie-bite';
                }

                let imageUrl = null;
                if (product.gambar_produk) {
                    if (product.gambar_produk.startsWith('produk/')) {
                        imageUrl = `/storage/${product.gambar_produk}`;
                    } else {
                        imageUrl = `/storage/produk/${product.gambar_produk}`;
                    }
                }

                const detailHtml = `
                    <div class="space-y-3">
                        <div class="flex items-center gap-4 pb-3 border-b">
                            <div class="w-16 h-16 rounded-xl bg-${bgColor}-100 flex items-center justify-center overflow-hidden">
                                ${imageUrl ? 
                                    `<img src="${imageUrl}" class="w-full h-full object-cover">` : 
                                    `<i class="fas ${icon} text-3xl text-${bgColor}-600"></i>`
                                }
                            </div>
                            <div>
                                <h4 class="font-bold text-lg">${escapeHtml(product.nama_produk)}</h4>
                                <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-600">${escapeHtml(categoryName)}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500">Harga</p>
                                <p class="text-lg font-bold text-green-600">Rp ${formatPrice(product.harga)}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-xs text-gray-500">Stok Gudang</p>
                                <p class="text-lg font-bold ${stok < 10 ? 'text-red-600' : stok < 20 ? 'text-orange-600' : 'text-slate-700'}">${stok}</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Total Nilai</p>
                            <p class="text-md font-semibold">Rp ${formatPrice(product.harga * stok)}</p>
                        </div>
                        ${product.alasan_ditolak ? `
                            <div class="bg-red-50 rounded-lg p-3 border border-red-200">
                                <p class="text-xs text-red-600"><i class="fas fa-times-circle mr-1"></i> Alasan Ditolak</p>
                                <p class="text-sm text-red-500 mt-1">${escapeHtml(product.alasan_ditolak)}</p>
                            </div>
                            ` : ''}
                        ${stok < 10 ? `
                            <div class="bg-red-50 rounded-lg p-3 border border-red-200">
                                <p class="text-xs text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i> Peringatan Stok Kritis!</p>
                                <p class="text-sm text-red-500 mt-1">Stok tersisa ${stok}, segera lakukan penambahan stok.</p>
                            </div>
                            ` : stok < 20 ? `
                            <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                                <p class="text-xs text-yellow-600"><i class="fas fa-info-circle mr-1"></i> Stok Menipis</p>
                                <p class="text-sm text-yellow-500 mt-1">Stok tersisa ${stok}, perhatikan penjualan.</p>
                            </div>
                            ` : ''}
                    </div>
                `;
                document.getElementById('detailContent').innerHTML = detailHtml;
                showModal('modalDetailProduk');
            }
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(price);
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            const toastContent = document.getElementById('toastContent');
            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');

            if (type === 'success') {
                toastContent.className = 'bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-l-4 border-green-500';
                toastIcon.innerHTML = '<i class="fas fa-check-circle text-green-500 text-xl"></i>';
            } else if (type === 'error') {
                toastContent.className = 'bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-l-4 border-red-500';
                toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-red-500 text-xl"></i>';
            } else {
                toastContent.className = 'bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-l-4 border-blue-500';
                toastIcon.innerHTML = '<i class="fas fa-info-circle text-blue-500 text-xl"></i>';
            }

            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        function hideToast() {
            document.getElementById('toast').classList.add('hidden');
        }

        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Event Listeners
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.onclick = () => {
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('bg-indigo-600', 'text-white');
                    b.classList.add('text-slate-600');
                });
                btn.classList.add('bg-indigo-600', 'text-white');
                renderProducts();
            };
        });

        document.getElementById('searchInput').addEventListener('input', renderProducts);

        // Load data dari database
        loadProducts();
    </script>
@endsection
