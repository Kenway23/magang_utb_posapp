@extends('layouts.gudang')

@section('title', 'PROShop - Penyesuaian Stok')
@section('page-title', 'Penyesuaian Stok')
@section('page-subtitle', 'Lakukan penyesuaian stok (plus/minus) karena rusak, atau kadaluarsa')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-slate-800">
                <i class="fas fa-sliders-h text-indigo-600 mr-2"></i>Penyesuaian Stok
            </h3>
            <button onclick="showTambahPenyesuaianModal()" class="btn-primary flex items-center gap-2">
                <i class="fas fa-plus"></i> Buat Penyesuaian
            </button>
        </div>

        <!-- Info Box -->
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-xl">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-amber-500 text-lg mt-0.5"></i>
                <div>
                    <p class="text-sm text-amber-800 font-medium">Penyesuaian Stok digunakan untuk:</p>
                    <ul class="text-sm text-amber-700 mt-1 list-disc list-inside">
                        <li>Penambahan stok karena retur dari customer</li>
                        <li>Pengurangan stok karena barang rusak atau kadaluarsa</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="mb-6 flex flex-wrap gap-3">
            <select id="statusFilterAdj" onchange="filterPenyesuaianTable()"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                <option value="all">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
            <input type="text" id="searchInputAdj" onkeyup="filterPenyesuaianTable()" placeholder="Cari produk..."
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm w-64">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="penyesuaianTable">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">Tanggal</th>
                        <th class="p-3 text-left">Produk</th>
                        <th class="p-3 text-left">Stok Lama</th>
                        <th class="p-3 text-left">Stok Baru</th>
                        <th class="p-3 text-left">Perubahan</th>
                        <th class="p-3 text-left">Alasan</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody id="penyesuaianTableBody">
                    <!-- Data akan diisi oleh JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Penyesuaian -->
    <div id="modalTambahPenyesuaian" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-slate-800">Penyesuaian Stok</h3>
                    <button onclick="closeModal('modalTambahPenyesuaian')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form onsubmit="simpanPenyesuaian(event)">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Produk</label>
                            <select id="productSelectAdj" required onchange="loadCurrentStock()"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                <option value="">-- Pilih Produk --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Stok Saat Ini</label>
                            <input type="text" id="currentStock" readonly
                                class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Stok Baru</label>
                            <input type="number" id="newStock" required oninput="calculateChange()"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Perubahan</label>
                            <input type="text" id="changeAmount" readonly
                                class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Alasan Penyesuaian</label>
                            <select id="reasonSelect" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                <option value="">-- Pilih Alasan --</option>
                                <option value="Barang Rusak">Barang Rusak</option>
                                <option value="Barang Kadaluarsa">Barang Kadaluarsa</option>
                                <option value="Retur Customer">Retur Customer</option>
                                <option value="Koreksi Manual">Koreksi Manual</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeModal('modalTambahPenyesuaian')"
                            class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan Sebagai
                            Draft</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Penyesuaian -->
    <div id="modalDetailPenyesuaian" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-slate-800">Detail Penyesuaian</h3>
                    <button onclick="closeModal('modalDetailPenyesuaian')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="space-y-3" id="detailContent">
                    <!-- Konten detail akan diisi oleh JavaScript -->
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeModal('modalDetailPenyesuaian')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data dummy untuk penyesuaian stok
        let adjustments = [{
                id: 1,
                product: "Rocky Rasa Coklat",
                oldStock: 10,
                newStock: 15,
                reason: "Koreksi stok fisik",
                date: "25/04/2026 10:30",
                status: "approved",
                unit: "pcs"
            },
            {
                id: 2,
                product: "Pocky Coklat",
                oldStock: 8,
                newStock: 5,
                reason: "Produk kadaluarsa",
                date: "24/04/2026 15:45",
                status: "approved",
                unit: "box"
            },
            {
                id: 3,
                product: "Indomie Goreng",
                oldStock: 120,
                newStock: 125,
                reason: "Retur customer",
                date: "24/04/2026 09:00",
                status: "draft",
                unit: "pcs"
            },
            {
                id: 4,
                product: "Teh Botol Sosro",
                oldStock: 45,
                newStock: 40,
                reason: "Opname stok",
                date: "23/04/2026 14:20",
                status: "draft",
                unit: "botol"
            },
            {
                id: 5,
                product: "Lays Original",
                oldStock: 25,
                newStock: 30,
                reason: "Retur supplier",
                date: "23/04/2026 11:00",
                status: "rejected",
                unit: "pcs"
            }
        ];

        let stocks = [{
                id: 1,
                name: "Rocky Rasa Coklat",
                category: "Makanan",
                stock: 10,
                minStock: 40,
                unit: "pcs"
            },
            {
                id: 2,
                name: "Indomie Goreng",
                category: "Makanan",
                stock: 120,
                minStock: 50,
                unit: "pcs"
            },
            {
                id: 3,
                name: "Teh Botol Sosro",
                category: "Minuman",
                stock: 45,
                minStock: 30,
                unit: "botol"
            },
            {
                id: 4,
                name: "Pocky Coklat",
                category: "Makanan",
                stock: 8,
                minStock: 15,
                unit: "box"
            },
            {
                id: 5,
                name: "Lays Original",
                category: "Makanan",
                stock: 25,
                minStock: 20,
                unit: "pcs"
            },
            {
                id: 6,
                name: "Coca Cola",
                category: "Minuman",
                stock: 60,
                minStock: 40,
                unit: "botol"
            }
        ];

        // Render tabel penyesuaian
        function renderPenyesuaianTable() {
            const tbody = document.getElementById('penyesuaianTableBody');
            if (!tbody) return;

            tbody.innerHTML = adjustments.map((item, idx) => {
                const change = item.newStock - item.oldStock;
                return `
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="p-3">${idx+1}</td>
                    <td class="p-3">${item.date}</td>
                    <td class="p-3 font-medium">${item.product}</td>
                    <td class="p-3">${item.oldStock} ${item.unit}</td>
                    <td class="p-3 font-bold ${change > 0 ? 'text-green-600' : 'text-red-600'}">
                        ${item.newStock} ${item.unit}
                    </td>
                    <td class="p-3 ${change > 0 ? 'text-green-600' : 'text-red-600'}">
                        ${change > 0 ? '+' : ''}${change} ${item.unit}
                    </td>
                    <td class="p-3">${item.reason}</td>
                    <td class="p-3">
                        <span class="status-badge status-${item.status}">
                            ${item.status === 'approved' ? 'Disetujui' : (item.status === 'draft' ? 'Draft' : 'Ditolak')}
                        </span>
                    </td>
                    <td class="p-3">
                        <button onclick="viewDetail(${item.id})" class="text-indigo-600 hover:text-indigo-800 mr-2" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${item.status === 'draft' ? `
                                        <button onclick="submitAdjustment(${item.id})" class="text-green-600 hover:text-green-800 mr-2" title="Kirim untuk Disetujui">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                        <button onclick="deleteAdjustment(${item.id})" class="text-red-600 hover:text-red-800" title="Hapus Draft">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    ` : ''}
                    </td>
                </tr>
            `;
            }).join('');
        }

        // Filter tabel
        function filterPenyesuaianTable() {
            const status = document.getElementById('statusFilterAdj')?.value || 'all';
            const search = document.getElementById('searchInputAdj')?.value.toLowerCase() || '';
            const rows = document.querySelectorAll('#penyesuaianTable tbody tr');

            rows.forEach(row => {
                const statusCell = row.cells[7]?.innerText.toLowerCase() || '';
                const productCell = row.cells[2]?.innerText.toLowerCase() || '';

                let statusMatch = status === 'all' || statusCell.includes(status);
                let searchMatch = search === '' || productCell.includes(search);

                row.style.display = statusMatch && searchMatch ? '' : 'none';
            });
        }

        // Load produk ke select option
        function loadProductsToSelect() {
            const select = document.getElementById('productSelectAdj');
            if (!select) return;

            select.innerHTML = '<option value="">-- Pilih Produk --</option>' +
                stocks.map(p =>
                    `<option value="${p.id}" data-stock="${p.stock}" data-unit="${p.unit}" data-name="${p.name}">${p.name} (Stok: ${p.stock} ${p.unit})</option>`
                ).join('');
        }

        // Load current stock
        function loadCurrentStock() {
            const select = document.getElementById('productSelectAdj');
            const selectedOption = select.options[select.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock') || 0;
            const unit = selectedOption.getAttribute('data-unit') || 'pcs';
            const productName = selectedOption.getAttribute('data-name') || '';

            document.getElementById('currentStock').value = stock + ' ' + unit;
            document.getElementById('currentStock').setAttribute('data-value', stock);
            document.getElementById('currentStock').setAttribute('data-unit', unit);
            document.getElementById('currentStock').setAttribute('data-product', productName);
            document.getElementById('newStock').value = stock;
            document.getElementById('changeAmount').value = '0 ' + unit;
        }

        // Calculate change
        function calculateChange() {
            const currentStock = parseInt(document.getElementById('currentStock')?.getAttribute('data-value') || 0);
            const newStock = parseInt(document.getElementById('newStock')?.value) || 0;
            const unit = document.getElementById('currentStock')?.getAttribute('data-unit') || 'pcs';
            const change = newStock - currentStock;
            const changeText = (change >= 0 ? '+' : '') + change + ' ' + unit;
            const changeInput = document.getElementById('changeAmount');
            if (changeInput) {
                changeInput.value = changeText;
                changeInput.className = 'w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg ' +
                    (change > 0 ? 'text-green-600' : (change < 0 ? 'text-red-600' : ''));
            }
        }

        // Modal functions
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        function showTambahPenyesuaianModal() {
            // Reset form
            document.getElementById('productSelectAdj').value = '';
            document.getElementById('currentStock').value = '';
            document.getElementById('newStock').value = '';
            document.getElementById('changeAmount').value = '';
            document.getElementById('reasonSelect').value = '';
            showModal('modalTambahPenyesuaian');
        }

        // Simpan penyesuaian
        function simpanPenyesuaian(event) {
            event.preventDefault();

            const select = document.getElementById('productSelectAdj');
            const selectedOption = select.options[select.selectedIndex];
            const productName = selectedOption.getAttribute('data-name') || '';
            const currentStock = parseInt(document.getElementById('currentStock')?.getAttribute('data-value') || 0);
            const unit = document.getElementById('currentStock')?.getAttribute('data-unit') || 'pcs';
            const newStock = parseInt(document.getElementById('newStock').value);
            const reason = document.getElementById('reasonSelect').value;

            if (!productName) {
                alert('Silakan pilih produk terlebih dahulu!');
                return;
            }

            if (!reason) {
                alert('Silakan pilih alasan penyesuaian!');
                return;
            }

            // Buat ID baru
            const newId = adjustments.length + 1;

            // Tambah data baru
            const newAdjustment = {
                id: newId,
                product: productName,
                oldStock: currentStock,
                newStock: newStock,
                reason: reason,
                date: new Date().toLocaleString('id-ID'),
                status: "draft",
                unit: unit
            };

            adjustments.unshift(newAdjustment);

            // Refresh tabel
            renderPenyesuaianTable();

            // Reset form & tutup modal
            closeModal('modalTambahPenyesuaian');

            // Tampilkan notifikasi sukses
            alert('✓ Penyesuaian stok berhasil disimpan sebagai draft');
        }

        // View detail
        function viewDetail(id) {
            const item = adjustments.find(a => a.id === id);
            if (item) {
                const change = item.newStock - item.oldStock;
                const detailHtml = `
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm"><strong>ID:</strong> ${item.id}</p>
                    <p class="text-sm mt-2"><strong>Produk:</strong> ${item.product}</p>
                    <p class="text-sm mt-2"><strong>Tanggal:</strong> ${item.date}</p>
                    <p class="text-sm mt-2"><strong>Stok Lama:</strong> ${item.oldStock} ${item.unit}</p>
                    <p class="text-sm mt-2"><strong>Stok Baru:</strong> ${item.newStock} ${item.unit}</p>
                    <p class="text-sm mt-2"><strong>Perubahan:</strong> ${change > 0 ? '+' : ''}${change} ${item.unit}</p>
                    <p class="text-sm mt-2"><strong>Alasan:</strong> ${item.reason}</p>
                    <p class="text-sm mt-2"><strong>Status:</strong> ${item.status === 'approved' ? 'Disetujui' : (item.status === 'draft' ? 'Draft' : 'Ditolak')}</p>
                </div>
            `;
                document.getElementById('detailContent').innerHTML = detailHtml;
                showModal('modalDetailPenyesuaian');
            }
        }

        // Submit adjustment for approval
        function submitAdjustment(id) {
            if (confirm('Kirim penyesuaian ini untuk disetujui?')) {
                const index = adjustments.findIndex(a => a.id === id);
                if (index !== -1) {
                    // Update status stok produk jika disetujui (simulasi)
                    const productIndex = stocks.findIndex(p => p.name === adjustments[index].product);
                    if (productIndex !== -1) {
                        stocks[productIndex].stock = adjustments[index].newStock;
                    }
                    adjustments[index].status = 'approved';
                    renderPenyesuaianTable();
                    alert('✓ Penyesuaian telah dikirim dan disetujui');
                }
            }
        }

        // Delete adjustment
        function deleteAdjustment(id) {
            if (confirm('Hapus draft penyesuaian ini?')) {
                const index = adjustments.findIndex(a => a.id === id);
                if (index !== -1) {
                    adjustments.splice(index, 1);
                    renderPenyesuaianTable();
                    alert('⚠ Penyesuaian dihapus');
                }
            }
        }

        // Inisialisasi halaman
        document.addEventListener('DOMContentLoaded', function() {
            renderPenyesuaianTable();
            loadProductsToSelect();
        });
    </script>

    <style>
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-approved {
            background: #d1fae5;
            color: #059669;
        }

        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-draft {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
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
@endsection
