@extends('layouts.gudang')

@section('title', 'PROShop - Pengeluaran Stok')
@section('page-title', 'Pengeluaran Stok')
@section('page-subtitle', 'Kelola barang keluar dari gudang')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-slate-800">
                <i class="fas fa-arrow-up text-red-600 mr-2"></i>Daftar Pengeluaran Stok
            </h3>
        </div>

        <!-- Filter -->
        <div class="mb-6 flex flex-wrap gap-3">
            <select id="statusFilterOut" onchange="filterPengeluaranTable()"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                <option value="all">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
            <input type="text" id="searchInputOut" onkeyup="filterPengeluaranTable()" placeholder="Cari produk..."
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm w-64">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="pengeluaranTable">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">Kode</th>
                        <th class="p-3 text-left">Produk</th>
                        <th class="p-3 text-left">Jumlah</th>
                        <th class="p-3 text-left">Tujuan</th>
                        <th class="p-3 text-left">Tanggal</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pengeluaranTableBody">
                    <!-- Data akan diisi oleh JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Pengeluaran -->
    <div id="modalTambahPengeluaran" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-slate-800">Tambah Pengeluaran Stok</h3>
                    <button onclick="closeModal('modalTambahPengeluaran')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data dummy untuk pengeluaran stok
        let outgoingTransactions = [{
                id: 1,
                product: "Indomie Goreng",
                qty: 46,
                time: "8 menit yang lalu",
                status: "approved",
                destination: "Toko A",
                code: "TRX101",
                date: "25/04/2026 10:32"
            },
            {
                id: 2,
                product: "Rocky Coklat",
                qty: 5,
                time: "30 menit yang lalu",
                status: "pending",
                destination: "Toko B",
                code: "TRX102",
                date: "25/04/2026 10:10"
            },
            {
                id: 3,
                product: "Teh Botol",
                qty: 10,
                time: "3 jam yang lalu",
                status: "approved",
                destination: "Toko C",
                code: "TRX103",
                date: "25/04/2026 07:00"
            },
            {
                id: 4,
                product: "Pocky Coklat",
                qty: 20,
                time: "5 jam yang lalu",
                status: "pending",
                destination: "Toko A",
                code: "TRX104",
                date: "25/04/2026 05:30"
            },
            {
                id: 5,
                product: "Lays Original",
                qty: 15,
                time: "kemarin",
                status: "approved",
                destination: "Toko B",
                code: "TRX105",
                date: "24/04/2026 16:00"
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

        // Render tabel pengeluaran
        function renderPengeluaranTable() {
            const tbody = document.getElementById('pengeluaranTableBody');
            if (!tbody) return;

            tbody.innerHTML = outgoingTransactions.map((item, idx) => `
            <tr class="border-b border-slate-100 hover:bg-slate-50">
                <td class="p-3">${idx+1}】+
                <td class="p-3 font-mono text-xs">${item.code}】+
                <td class="p-3 font-medium">${item.product}】+
                <td class="p-3 text-red-600 font-medium">-${item.qty} pcs</td>
                <td class="p-3">${item.destination}</td>
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
                                <button onclick="approveItem(${item.id})" class="text-green-600 hover:text-green-800 mr-2">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="rejectItem(${item.id})" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            ` : ''}
                </td>
            </tr>
        `).join('');
        }

        // Load produk ke select option
        function loadProductsToSelect() {
            const select = document.getElementById('productSelectOut');
            if (!select) return;

            select.innerHTML = '<option value="">-- Pilih Produk --</option>' +
                stocks.map(p =>
                    `<option value="${p.id}" data-stock="${p.stock}" data-name="${p.name}">${p.name} (Stok: ${p.stock} ${p.unit})</option>`
                ).join('');

            // Tambahkan event listener untuk cek stok
            select.addEventListener('change', function() {
                const selectedOption = select.options[select.selectedIndex];
                const stockAvailable = parseInt(selectedOption.getAttribute('data-stock') || 0);
                document.getElementById('productSelectOut').setAttribute('data-current-stock', stockAvailable);
            });
        }

        // Filter tabel
        function filterPengeluaranTable() {
            const status = document.getElementById('statusFilterOut')?.value || 'all';
            const search = document.getElementById('searchInputOut')?.value.toLowerCase() || '';
            const rows = document.querySelectorAll('#pengeluaranTable tbody tr');

            rows.forEach(row => {
                const statusCell = row.cells[6]?.innerText.toLowerCase() || '';
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

        function showTambahPengeluaranModal() {
            // Reset form
            document.getElementById('productSelectOut').value = '';
            document.getElementById('qtyInputOut').value = '';
            document.getElementById('destinationInput').value = '';
            document.getElementById('notesInputOut').value = '';
            document.getElementById('stockWarning').classList.add('hidden');
            showModal('modalTambahPengeluaran');
        }

        // Cek ketersediaan stok
        function checkStockAvailability() {
            const select = document.getElementById('productSelectOut');
            const selectedOption = select.options[select.selectedIndex];
            const stockAvailable = parseInt(selectedOption.getAttribute('data-stock') || 0);
            const qty = parseInt(document.getElementById('qtyInputOut').value) || 0;
            const warningEl = document.getElementById('stockWarning');

            if (qty > stockAvailable) {
                warningEl.classList.remove('hidden');
                return false;
            } else {
                warningEl.classList.add('hidden');
                return true;
            }
        }

        // Simpan pengeluaran
        function simpanPengeluaran(event) {
            event.preventDefault();

            const select = document.getElementById('productSelectOut');
            const selectedOption = select.options[select.selectedIndex];
            const productName = selectedOption.getAttribute('data-name') || selectedOption.text.split(' (')[0];
            const stockAvailable = parseInt(selectedOption.getAttribute('data-stock') || 0);
            const qty = parseInt(document.getElementById('qtyInputOut').value);
            const destination = document.getElementById('destinationInput').value || '-';
            const notes = document.getElementById('notesInputOut').value;

            // Validasi stok
            if (qty > stockAvailable) {
                alert('Stok tidak mencukupi! Stok tersedia: ' + stockAvailable);
                return;
            }

            // Buat ID baru
            const newId = outgoingTransactions.length + 1;
            const newCode = `TRX10${newId + 100}`;

            // Tambah data baru
            const newTransaction = {
                id: newId,
                product: productName,
                qty: qty,
                time: "baru saja",
                status: "pending",
                destination: destination,
                code: newCode,
                date: new Date().toLocaleString('id-ID')
            };

            outgoingTransactions.unshift(newTransaction);

            // Update stok produk
            const productIndex = stocks.findIndex(p => p.name === productName);
            if (productIndex !== -1) {
                stocks[productIndex].stock -= qty;
                // Update data-stock attribute pada option
                const option = select.querySelector(`option[value="${stocks[productIndex].id}"]`);
                if (option) {
                    option.setAttribute('data-stock', stocks[productIndex].stock);
                    option.text =
                        `${stocks[productIndex].name} (Stok: ${stocks[productIndex].stock} ${stocks[productIndex].unit})`;
                }
            }

            // Refresh tabel
            renderPengeluaranTable();

            // Reset form & tutup modal
            closeModal('modalTambahPengeluaran');

            // Tampilkan notifikasi sukses
            alert('✓ Pengeluaran stok berhasil ditambahkan dan menunggu persetujuan');
        }

        // Action functions
        function viewDetail(id) {
            const item = outgoingTransactions.find(t => t.id === id);
            if (item) {
                alert(
                    `Detail Pengeluaran\n\nKode: ${item.code}\nProduk: ${item.product}\nJumlah: ${item.qty} pcs\nTujuan: ${item.destination}\nTanggal: ${item.date}\nStatus: ${item.status === 'approved' ? 'Disetujui' : (item.status === 'pending' ? 'Pending' : 'Ditolak')}`
                    );
            }
        }

        function approveItem(id) {
            if (confirm('Setujui pengeluaran ini?')) {
                const index = outgoingTransactions.findIndex(t => t.id === id);
                if (index !== -1) {
                    outgoingTransactions[index].status = 'approved';
                    renderPengeluaranTable();
                    alert('✓ Pengeluaran stok berhasil disetujui');
                }
            }
        }

        function rejectItem(id) {
            if (confirm('Tolak pengeluaran ini?')) {
                const index = outgoingTransactions.findIndex(t => t.id === id);
                if (index !== -1) {
                    outgoingTransactions[index].status = 'rejected';
                    renderPengeluaranTable();
                    alert('⚠ Pengeluaran stok ditolak');
                }
            }
        }

        // Event listener untuk cek stok saat jumlah berubah
        document.addEventListener('DOMContentLoaded', function() {
            renderPengeluaranTable();
            loadProductsToSelect();

            const qtyInput = document.getElementById('qtyInputOut');
            if (qtyInput) {
                qtyInput.addEventListener('input', checkStockAvailability);
            }
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
