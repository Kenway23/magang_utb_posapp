@extends('layouts.gudang')

@section('title', 'PROShop - Penerimaan Stok')
@section('page-title', 'Penerimaan Stok')
@section('page-subtitle', 'Kelola barang masuk ke gudang')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-slate-800">
                <i class="fas fa-arrow-down text-green-600 mr-2"></i>Daftar Penerimaan Stok
            </h3>
        </div>

        <!-- Filter -->
        <div class="mb-6 flex flex-wrap gap-3">
            <select id="statusFilter" onchange="filterPenerimaanTable()"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                <option value="all">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
            <input type="text" id="searchInput" onkeyup="filterPenerimaanTable()" placeholder="Cari produk..."
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm w-64">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="penerimaanTable">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">Kode</th>
                        <th class="p-3 text-left">Produk</th>
                        <th class="p-3 text-left">Jumlah</th>
                        <th class="p-3 text-left">Tanggal</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody id="penerimaanTableBody">
                    <!-- Data akan diisi oleh JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Penerimaan -->
    <div id="modalTambahPenerimaan" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-slate-800">Tambah Penerimaan Stok</h3>
                    <button onclick="closeModal('modalTambahPenerimaan')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data dummy untuk penerimaan stok
        let incomingTransactions = [{
                id: 1,
                product: "Rocky Rasa Coklat",
                qty: 50,
                time: "15 menit yang lalu",
                status: "approved",
                code: "TRX001",
                date: "25/04/2026 10:30"
            },
            {
                id: 2,
                product: "Indomie Goreng",
                qty: 30,
                time: "1 jam yang lalu",
                status: "pending",
                code: "TRX002",
                date: "25/04/2026 09:45"
            },
            {
                id: 3,
                product: "Teh Botol Sosro",
                qty: 20,
                time: "2 jam yang lalu",
                status: "approved",
                code: "TRX003",
                date: "25/04/2026 08:30"
            },
            {
                id: 4,
                product: "Pocky Coklat",
                qty: 15,
                time: "5 jam yang lalu",
                status: "pending",
                code: "TRX004",
                date: "25/04/2026 05:00"
            },
            {
                id: 5,
                product: "Lays Original",
                qty: 40,
                time: "kemarin",
                status: "approved",
                code: "TRX005",
                date: "24/04/2026 14:20"
            },
            {
                id: 6,
                product: "Coca Cola",
                qty: 60,
                time: "kemarin",
                status: "rejected",
                code: "TRX006",
                date: "24/04/2026 11:15"
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

        // Render tabel penerimaan
        function renderPenerimaanTable() {
            const tbody = document.getElementById('penerimaanTableBody');
            if (!tbody) return;

            tbody.innerHTML = incomingTransactions.map((item, idx) => `
            <tr class="border-b border-slate-100 hover:bg-slate-50">
                <td class="p-3">${idx+1}</td>
                <td class="p-3 font-mono text-xs">${item.code}</td>
                <td class="p-3 font-medium">${item.product}</td>
                <td class="p-3 text-green-600 font-medium">+${item.qty} pcs</td>
                <td class="p-3">${item.date}</td>
                <td class="p-3">
                    <span class="status-badge status-${item.status}">
                        ${item.status === 'approved' ? 'Disetujui' : (item.status === 'pending' ? 'Pending' : 'Ditolak')}
                    </span>
                </td>
                <td class="p-3">
                    <button onclick="viewDetail(${item.id})" class="text-indigo-600 hover:text-indigo-800 mr-2">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${item.status === 'pending' ? `
                                    ` : ''}
                </td>
            </tr>
        `).join('');
        }

        // Load produk ke select option
        function loadProductsToSelect() {
            const select = document.getElementById('productSelect');
            if (!select) return;

            select.innerHTML = '<option value="">-- Pilih Produk --</option>' +
                stocks.map(p =>
                    `<option value="${p.id}" data-stock="${p.stock}" data-unit="${p.unit}">${p.name} (Stok: ${p.stock} ${p.unit})</option>`
                ).join('');
        }

        // Filter tabel
        function filterPenerimaanTable() {
            const status = document.getElementById('statusFilter')?.value || 'all';
            const search = document.getElementById('searchInput')?.value.toLowerCase() || '';
            const rows = document.querySelectorAll('#penerimaanTable tbody tr');

            rows.forEach(row => {
                const statusCell = row.cells[5]?.innerText.toLowerCase() || '';
                const productCell = row.cells[2]?.innerText.toLowerCase() || '';

                let statusMatch = status === 'all' || statusCell.includes(status);
                let searchMatch = search === '' || productCell.includes(search);

                row.style.display = statusMatch && searchMatch ? '' : 'none';
            });
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

        function showTambahPenerimaanModal() {
            showModal('modalTambahPenerimaan');
        }

        // Simpan penerimaan
        function simpanPenerimaan(event) {
            event.preventDefault();

            const productSelect = document.getElementById('productSelect');
            const selectedProduct = productSelect.options[productSelect.selectedIndex];
            const productName = selectedProduct.text.split(' (')[0];
            const qty = document.getElementById('qtyInput').value;
            const notes = document.getElementById('notesInput').value;

            // Buat ID baru
            const newId = incomingTransactions.length + 1;
            const newCode = `TRX00${newId}`;

            // Tambah data baru
            const newTransaction = {
                id: newId,
                product: productName,
                qty: parseInt(qty),
                time: "baru saja",
                status: "pending",
                code: newCode,
                date: new Date().toLocaleString('id-ID')
            };

            incomingTransactions.unshift(newTransaction);

            // Refresh tabel
            renderPenerimaanTable();

            // Reset form & tutup modal
            document.getElementById('productSelect').value = '';
            document.getElementById('qtyInput').value = '';
            document.getElementById('notesInput').value = '';
            closeModal('modalTambahPenerimaan');

            // Tampilkan notifikasi sukses
            showSuccess('Penerimaan stok berhasil ditambahkan dan menunggu persetujuan');
        }

        // Action functions
        function viewDetail(id) {
            const item = incomingTransactions.find(t => t.id === id);
            if (item) {
                alert(
                    `Detail Penerimaan\n\nKode: ${item.code}\nProduk: ${item.product}\nJumlah: ${item.qty} pcs\nTanggal: ${item.date}\nStatus: ${item.status === 'approved' ? 'Disetujui' : (item.status === 'pending' ? 'Pending' : 'Ditolak')}`
                );
            }
        }

        function approveItem(id) {
            if (confirm('Setujui penerimaan ini?')) {
                const index = incomingTransactions.findIndex(t => t.id === id);
                if (index !== -1) {
                    incomingTransactions[index].status = 'approved';
                    renderPenerimaanTable();
                    showSuccess('Penerimaan stok berhasil disetujui');
                }
            }
        }

        function rejectItem(id) {
            if (confirm('Tolak penerimaan ini?')) {
                const index = incomingTransactions.findIndex(t => t.id === id);
                if (index !== -1) {
                    incomingTransactions[index].status = 'rejected';
                    renderPenerimaanTable();
                    showWarning('Penerimaan stok ditolak');
                }
            }
        }

        // Notifikasi
        function showSuccess(message) {
            alert('✓ ' + message);
        }

        function showWarning(message) {
            alert('⚠ ' + message);
        }

        // Inisialisasi halaman
        document.addEventListener('DOMContentLoaded', function() {
            renderPenerimaanTable();
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

        .btn-primary {
            background-color: #4f46e5;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
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
