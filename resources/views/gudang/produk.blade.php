@extends('layouts.gudang')

@section('title', 'Manajemen Produk - PROShop')
@section('page-title', 'Manajemen Produk')
@section('page-subtitle', 'Kelola data produk (Tambah, Edit, Hapus)')

@section('content')
    <div>
        <div class="space-y-6">
            <!-- Tombol Tambah & Pencarian -->
            <div class="bg-white rounded-xl shadow-sm p-4 flex justify-between items-center flex-wrap gap-3">
                <button onclick="showTambahProdukModal()"
                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Produk
                </button>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchProduk" onkeyup="filterProdukTable()" placeholder="Cari produk..."
                        class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <!-- Filter Status -->
            <div class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3 items-center">
                <span class="text-sm font-medium text-gray-700">Filter Status:</span>
                <select id="filterStatus" onchange="filterProdukTable()" class="px-3 py-1.5 border rounded-lg text-sm">
                    <option value="all">Semua</option>
                    <option value="pending">Menunggu Approve</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
                <div class="ml-auto text-sm text-gray-500">
                    <i class="fas fa-info-circle"></i> Produk yang sudah disetujui tidak dapat diedit/dihapus
                </div>
            </div>

            <!-- GRID PRODUK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5" id="produkGrid">
            </div>

            <!-- Statistik -->
            <div class="bg-white rounded-xl shadow-sm p-4 flex justify-between items-center flex-wrap gap-3">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-chart-line mr-1"></i> Total <span id="totalProduk">0</span> produk
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-money-bill-wave mr-1"></i> Total nilai inventaris: Rp <span
                        id="totalInventaris">0</span>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-clock mr-1 text-yellow-500"></i> Menunggu Approve: <span id="pendingCount">0</span>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-exclamation-triangle mr-1 text-amber-500"></i> Stok menipis: <span
                        id="lowStockCount">0</span>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH/EDIT PRODUK -->
        <div id="modalProduk" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl max-w-md w-full mx-4">
                <div class="p-5 border-b flex justify-between items-center">
                    <h3 class="font-bold text-lg" id="modalTitle">Tambah Produk</h3>
                    <button onclick="closeModal('modalProduk')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form onsubmit="saveProduct(event)">
                    <div class="p-5 space-y-4">
                        <input type="hidden" id="produkId">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Produk <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="namaProduk" required
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Kategori</label>
                            <select id="kategoriId" required
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategori as $kat)
                                    <option value="{{ $kat->kategori_id }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Harga (Rp)</label>
                            <input type="number" id="harga" required min="0" step="100"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Stok Gudang <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="stokGudang" required min="10" value="10"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <p class="text-xs text-gray-500 mt-1">Stok awal di gudang (minimal 10 untuk produk baru)</p>
                        </div>

                        <!-- Preview Gambar -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-2">Upload Gambar (Opsional)</p>
                            <input type="file" id="gambarProduk" accept="image/*" class="w-full text-sm"
                                onchange="previewImage(this)">
                            <div id="previewGambar" class="mt-2 hidden">
                                <img id="previewImg" class="w-20 h-20 object-cover rounded-lg">
                            </div>
                        </div>
                    </div>
                    <div class="p-5 border-t flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-save mr-1"></i> Simpan & Kirim Approve
                        </button>
                        <button type="button" onclick="closeModal('modalProduk')"
                            class="flex-1 bg-gray-200 py-2 rounded-lg hover:bg-gray-300 transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL DETAIL PENOLAKAN -->
        <div id="modalRejectReason" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-red-600"><i class="fas fa-times-circle mr-2"></i>Alasan Penolakan</h3>
                    <button onclick="closeModal('modalRejectReason')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p id="rejectReasonText" class="text-gray-600 p-3 bg-red-50 rounded-lg"></p>
                <div class="flex justify-end mt-4">
                    <button onclick="closeModal('modalRejectReason')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Tutup</button>
                </div>
            </div>
        </div>

        <!-- MODAL KONFIRMASI HAPUS -->
        <div id="modalHapus" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl max-w-sm w-full mx-4 p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Hapus Produk?</h3>
                <p class="text-gray-500 mb-4">Yakin ingin menghapus "<span id="deleteProductName"></span>"?</p>
                <div class="flex gap-3">
                    <button onclick="confirmDeleteProduct()"
                        class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">Hapus</button>
                    <button onclick="closeModal('modalHapus')"
                        class="flex-1 bg-gray-200 py-2 rounded-lg hover:bg-gray-300">Batal</button>
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
    </div>

    <script>
        let products = [];
        let deleteId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
        });

        function loadProducts() {
            fetch('/gudang/produk/data')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        products = data.data;
                        renderProdukGrid();
                    } else {
                        console.error('Gagal load produk');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    products = @json($produk);
                    renderProdukGrid();
                });
        }

        function renderProdukGrid() {
            const grid = document.getElementById('produkGrid');
            const search = document.getElementById('searchProduk')?.value.toLowerCase() || '';
            const statusFilter = document.getElementById('filterStatus')?.value || 'all';

            let filtered = products.filter(p => {
                const matchSearch = p.nama_produk && p.nama_produk.toLowerCase().includes(search);
                const matchStatus = statusFilter === 'all' || p.status === statusFilter;
                return matchSearch && matchStatus;
            });

            if (filtered.length === 0) {
                grid.innerHTML =
                    '<div class="col-span-full text-center py-16 text-gray-400"><i class="fas fa-box-open text-6xl mb-3 block"></i><p class="text-lg">Belum ada produk</p><p class="text-sm mt-1">Klik tombol "Tambah Produk" untuk menambahkan</p></div>';
                updateStats([]);
                return;
            }

            grid.innerHTML = filtered.map(p => {
                const totalStok = (p.stok_gudang || 0);
                const categoryName = p.kategori?.nama_kategori || 'Lainnya';

                // Warna berdasarkan kategori untuk badge di gambar
                let badgeColor = 'bg-indigo-600';
                let textColor = 'text-indigo-600';
                let statusColor = '';
                let statusText = '';
                let statusIcon = '';

                if (categoryName === 'Makanan') {
                    badgeColor = 'bg-red-600';
                    textColor = 'text-red-600';
                } else if (categoryName === 'Minuman') {
                    badgeColor = 'bg-blue-600';
                    textColor = 'text-blue-600';
                } else if (categoryName === 'Snack') {
                    badgeColor = 'bg-yellow-600';
                    textColor = 'text-yellow-600';
                } else if (categoryName === 'Makanan Siap Saji') {
                    badgeColor = 'bg-orange-600';
                    textColor = 'text-orange-600';
                } else if (categoryName === 'Rokok') {
                    badgeColor = 'bg-gray-600';
                    textColor = 'text-gray-600';
                } else if (categoryName === 'Perawatan Tubuh') {
                    badgeColor = 'bg-green-600';
                    textColor = 'text-green-600';
                }

                // Status untuk ditampilkan (tanpa kategori)
                if (p.status === 'pending') {
                    statusColor = 'text-yellow-600 bg-yellow-50';
                    statusIcon = 'fa-clock';
                    statusText = 'Menunggu Approve';
                } else if (p.status === 'approved') {
                    statusColor = 'text-green-600 bg-green-50';
                    statusIcon = 'fa-check-circle';
                    statusText = 'Disetujui';
                } else if (p.status === 'rejected') {
                    statusColor = 'text-red-600 bg-red-50';
                    statusIcon = 'fa-times-circle';
                    statusText = 'Ditolak';
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

                const isEditable = p.status !== 'approved';

                return `
    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300 group">
        <!-- Gambar Full di Atas -->
        <div class="relative h-48 overflow-hidden bg-gray-200">
            ${imageUrl ? 
                `<img src="${imageUrl}" alt="${escapeHtml(p.nama_produk)}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-100 to-purple-100\'><i class=\'fas fa-box-open text-5xl text-indigo-300\'></i></div>'">` : 
                `<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-100 to-purple-100">
                                                                                            <i class="fas fa-box-open text-5xl text-indigo-300"></i>
                                                                                        </div>`
            }
            <!-- Category Badge di pojok kanan atas GAMBAR -->
            <span class="absolute top-3 right-3 text-xs px-3 py-1 rounded-full text-white font-medium shadow-md ${badgeColor} z-10">
                <i class="fas fa-tag mr-1 text-xs"></i> ${escapeHtml(categoryName)}
            </span>
            <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-black/30 to-transparent"></div>
        </div>
        
        <!-- Info Produk -->
        <div class="p-4">
          <!-- Hanya STATUS di sebelah kiri -->
            <div class="flex justify-start mb-2">
                <span class="text-xs px-2 py-1 rounded-full ${statusColor}">
                    <i class="fas ${statusIcon} mr-1 text-xs"></i> ${statusText}
                </span>
            </div>
            
            <!-- Nama Produk -->
            <h3 class="font-bold text-lg text-gray-800 mb-1 line-clamp-1">${escapeHtml(p.nama_produk)}</h3>
            
            <!-- Harga -->
            <div class="text-2xl font-bold ${textColor} mb-3">
                    Rp ${formatPrice(p.harga)} 
                </div>
            
            <!-- Stok dan Aksi -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-warehouse text-gray-400 text-sm"></i>
                    <span class="text-sm ${totalStok <= 5 ? 'text-red-600 font-bold' : 'text-gray-600'}">
                        Stok: ${totalStok}
                    </span>
                    ${totalStok <= 5 ? '<span class="text-red-500 text-xs animate-pulse">⚠️</span>' : ''}
                </div>
                <div class="flex gap-2">
                    ${isEditable ? `
                                                                                                <button onclick="editProduct(${p.produk_id})" class="text-blue-600 hover:text-blue-800 transition p-1.5 hover:bg-blue-50 rounded-lg" title="Edit">
                                                                                                    <i class="fas fa-edit"></i>
                                                                                                </button>
                                                                                                <button onclick="showDeleteModal(${p.produk_id}, '${escapeHtml(p.nama_produk)}')" class="text-red-600 hover:text-red-800 transition p-1.5 hover:bg-red-50 rounded-lg" title="Hapus">
                                                                                                    <i class="fas fa-trash-alt"></i>
                                                                                                </button>
                                                                                            ` : `
                                                                                                <span class="text-gray-400 text-sm" title="Produk sudah disetujui tidak dapat diedit">
                                                                                                    <i class="fas fa-lock"></i>
                                                                                                </span>
                                                                                            `}
                    ${p.status === 'rejected' && p.alasan_ditolak ? `
                                                                                                <button onclick="showRejectReason('${escapeHtml(p.alasan_ditolak)}')" class="text-red-500 hover:text-red-700 transition p-1.5" title="Lihat Alasan Ditolak">
                                                                                                    <i class="fas fa-info-circle"></i>
                                                                                                </button>
                                                                                            ` : ''}
                </div>
            </div>
        </div>
    </div>
    `;
            }).join('');

            updateStats(filtered);
        }

        function updateStats(products) {
            const totalNilai = products.reduce((sum, p) => sum + (p.harga * (p.stok_gudang || 0)), 0);
            const lowStock = products.filter(p => (p.stok_gudang || 0) <= 5).length;
            const pendingCount = products.filter(p => p.status === 'pending').length;

            document.getElementById('totalProduk').innerText = products.length;
            document.getElementById('totalInventaris').innerText = formatPrice(totalNilai);
            document.getElementById('lowStockCount').innerText = lowStock;
            document.getElementById('pendingCount').innerText = pendingCount;
        }

        function filterProdukTable() {
            renderProdukGrid();
        }

        function previewImage(input) {
            const preview = document.getElementById('previewGambar');
            const previewImg = document.getElementById('previewImg');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function showRejectReason(alasan) {
            document.getElementById('rejectReasonText').innerText = alasan;
            showModal('modalRejectReason');
        }

        function showTambahProdukModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Produk';
            document.getElementById('produkId').value = '';
            document.getElementById('namaProduk').value = '';
            document.getElementById('kategoriId').value = '';
            document.getElementById('harga').value = '';
            document.getElementById('stokGudang').value = '0';
            document.getElementById('gambarProduk').value = '';
            document.getElementById('previewGambar').classList.add('hidden');
            showModal('modalProduk');
        }

        function editProduct(id) {
            const product = products.find(p => p.produk_id === id);
            if (product) {
                if (product.status === 'approved') {
                    showToast('Produk yang sudah disetujui tidak dapat diedit!', 'error');
                    return;
                }
                document.getElementById('modalTitle').innerText = 'Edit Produk';
                document.getElementById('produkId').value = product.produk_id;
                document.getElementById('namaProduk').value = product.nama_produk;
                document.getElementById('kategoriId').value = product.kategori_id;
                document.getElementById('harga').value = product.harga;
                document.getElementById('stokGudang').value = product.stok_gudang || 0;
                document.getElementById('gambarProduk').value = '';
                document.getElementById('previewGambar').classList.add('hidden');
                showModal('modalProduk');
            }
        }

        function saveProduct(event) {
            event.preventDefault();

            const id = document.getElementById('produkId').value;
            const stokGudang = parseInt(document.getElementById('stokGudang').value) || 0;

            // 🔥 VALIDASI STOK MINIMAL 10 UNTUK PRODUK BARU
            if (!id && stokGudang < 10) {
                showToast('Stok minimal untuk produk baru adalah 10!', 'error');
                return;
            }

            const url = id ? `/gudang/produk/${id}` : '/gudang/produk';

            const formData = new FormData();
            formData.append('nama_produk', document.getElementById('namaProduk').value);
            formData.append('kategori_id', document.getElementById('kategoriId').value);
            formData.append('harga', document.getElementById('harga').value);
            formData.append('stok_gudang', stokGudang);

            const gambarFile = document.getElementById('gambarProduk').files[0];
            if (gambarFile) {
                formData.append('gambar', gambarFile);
            }

            if (id) {
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        closeModal('modalProduk');
                        loadProducts();
                    } else {
                        showToast(data.message || 'Gagal menyimpan produk', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        }

        function showDeleteModal(id, name) {
            const product = products.find(p => p.produk_id === id);
            if (product && product.status === 'approved') {
                showToast('Produk yang sudah disetujui tidak dapat dihapus!', 'error');
                return;
            }
            deleteId = id;
            document.getElementById('deleteProductName').innerText = name;
            showModal('modalHapus');
        }

        function confirmDeleteProduct() {
            if (!deleteId) return;

            fetch(`/gudang/produk/${deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        closeModal('modalHapus');
                        deleteId = null;
                        loadProducts();
                    } else {
                        showToast(data.message || 'Gagal menghapus produk', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        }

        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function formatPrice(price) {
            const angka = parseFloat(price);
            if (isNaN(angka)) return '0';
            return angka.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastContent = document.getElementById('toastContent');
            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');

            if (type === 'success') {
                toastContent.className = 'bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-l-4 border-green-500';
                toastIcon.innerHTML = '<i class="fas fa-check-circle text-green-500 text-xl"></i>';
            } else {
                toastContent.className = 'bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-l-4 border-red-500';
                toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-red-500 text-xl"></i>';
            }

            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        function hideToast() {
            document.getElementById('toast').classList.add('hidden');
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

    <style>
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
