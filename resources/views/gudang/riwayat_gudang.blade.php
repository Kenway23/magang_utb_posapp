@extends('layouts.gudang')

@section('title', 'PROShop - Riwayat Gudang')
@section('page-title', 'Riwayat Transaksi')
@section('page-subtitle', 'Riwayat semua transaksi gudang (penerimaan, pengeluaran, penyesuaian)')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-slate-800">
                <i class="fas fa-history text-indigo-600 mr-2"></i>
                Riwayat Transaksi Gudang
            </h3>
            <button onclick="refreshData()" class="text-indigo-600 hover:text-indigo-800 transition">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
        </div>

        <!-- Filter -->
        <div class="mb-6 flex flex-wrap gap-3">
            <select id="typeFilter" onchange="filterRiwayatTable()"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                <option value="all">Semua Tipe</option>
                <option value="Penerimaan">📥 Penerimaan</option>
                <option value="Pengeluaran">📤 Pengeluaran</option>
                <option value="Penyesuaian">⚙️ Penyesuaian</option>
            </select>
            <select id="statusFilterRiwayat" onchange="filterRiwayatTable()"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                <option value="all">Semua Status</option>
                <option value="approved">✓ Disetujui</option>
                <option value="pending">⏳ Pending</option>
                <option value="rejected">✗ Ditolak</option>
                <option value="draft">📝 Draft</option>
            </select>
            <input type="date" id="dateFilter" onchange="filterRiwayatTable()"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
            <input type="text" id="searchRiwayat" onkeyup="filterRiwayatTable()" placeholder="Cari produk atau kode..."
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm w-64">
            <button onclick="resetFilters()"
                class="px-3 py-2 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="riwayatTable">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">ID Transaksi</th>
                        <th class="p-3 text-left">Tipe</th>
                        <th class="p-3 text-left">Produk</th>
                        <th class="p-3 text-left">Jumlah</th>
                        <th class="p-3 text-left">Tanggal</th>
                        <th class="p-3 text-left">User</th>
                        <th class="p-3 text-left">Keterangan</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody id="riwayatTableBody">
                    <!-- Data akan diisi oleh JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-slate-500">
                Menampilkan <span id="startCount">0</span> - <span id="endCount">0</span> dari <span
                    id="totalCount">0</span> transaksi
            </div>
            <div class="flex gap-2">
                <button onclick="prevPage()" id="prevBtn"
                    class="px-3 py-1 border rounded-lg hover:bg-slate-50 disabled:opacity-50" disabled>
                    <i class="fas fa-chevron-left"></i> Sebelumnya
                </button>
                <button onclick="nextPage()" id="nextBtn"
                    class="px-3 py-1 border rounded-lg hover:bg-slate-50 disabled:opacity-50" disabled>
                    Selanjutnya <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div id="modalDetailTransaksi" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-slate-800">Detail Transaksi</h3>
                    <button onclick="closeModal('modalDetailTransaksi')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="space-y-3" id="detailTransaksiContent">
                    <!-- Konten detail akan diisi oleh JavaScript -->
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeModal('modalDetailTransaksi')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data riwayat transaksi
        let historyTransactions = [{
                id: "TRX001",
                type: "Penerimaan",
                product: "Rocky Rasa Coklat",
                qty: "+50",
                date: "2026-04-25 10:30",
                user: "Admin Gudang",
                status: "approved",
                note: "Pembelian dari supplier PT Maju Jaya"
            },
            {
                id: "TRX101",
                type: "Pengeluaran",
                product: "Indomie Goreng",
                qty: "-46",
                date: "2026-04-25 10:32",
                user: "Kasir",
                status: "approved",
                note: "Pengiriman ke Toko A"
            },
            {
                id: "TRX002",
                type: "Penerimaan",
                product: "Indomie Goreng",
                qty: "+30",
                date: "2026-04-24 09:45",
                user: "Admin Gudang",
                status: "pending",
                note: "Menunggu verifikasi"
            },
            {
                id: "TRX102",
                type: "Pengeluaran",
                product: "Rocky Coklat",
                qty: "-5",
                date: "2026-04-24 10:10",
                user: "Kasir",
                status: "pending",
                note: "Menunggu persetujuan"
            },
            {
                id: "ADJ001",
                type: "Penyesuaian",
                product: "Pocky Coklat",
                qty: "-3",
                date: "2026-04-23 15:45",
                user: "Owner",
                status: "approved",
                note: "Produk kadaluarsa"
            },
            {
                id: "TRX003",
                type: "Penerimaan",
                product: "Teh Botol Sosro",
                qty: "+20",
                date: "2026-04-23 08:30",
                user: "Admin Gudang",
                status: "approved",
                note: "Pembelian rutin"
            },
            {
                id: "TRX103",
                type: "Pengeluaran",
                product: "Teh Botol",
                qty: "-10",
                date: "2026-04-22 07:00",
                user: "Kasir",
                status: "approved",
                note: "Pengiriman ke Toko C"
            },
            {
                id: "ADJ002",
                type: "Penyesuaian",
                product: "Indomie Goreng",
                qty: "+5",
                date: "2026-04-22 09:00",
                user: "Admin Gudang",
                status: "draft",
                note: "Retur customer"
            },
            {
                id: "TRX004",
                type: "Penerimaan",
                product: "Lays Original",
                qty: "+40",
                date: "2026-04-21 14:20",
                user: "Admin Gudang",
                status: "approved",
                note: "Pembelian dari distributor"
            },
            {
                id: "TRX104",
                type: "Pengeluaran",
                product: "Lays Original",
                qty: "-15",
                date: "2026-04-21 16:30",
                user: "Kasir",
                status: "rejected",
                note: "Ditolak karena stok tidak cukup"
            },
            {
                id: "TRX005",
                type: "Penerimaan",
                product: "Coca Cola",
                qty: "+60",
                date: "2026-04-20 11:15",
                user: "Admin Gudang",
                status: "rejected",
                note: "Ditolak karena produk tidak sesuai"
            },
            {
                id: "TRX105",
                type: "Pengeluaran",
                product: "Coca Cola",
                qty: "-25",
                date: "2026-04-20 13:00",
                user: "Kasir",
                status: "approved",
                note: "Pengiriman ke Toko B"
            }
        ];

        let currentPage = 1;
        let rowsPerPage = 10;
        let filteredData = [];

        // Render tabel riwayat
        function renderRiwayatTable() {
            const tbody = document.getElementById('riwayatTableBody');
            if (!tbody) return;

            // Filter data
            const type = document.getElementById('typeFilter')?.value || 'all';
            const status = document.getElementById('statusFilterRiwayat')?.value || 'all';
            const search = document.getElementById('searchRiwayat')?.value.toLowerCase() || '';
            const date = document.getElementById('dateFilter')?.value || '';

            filteredData = historyTransactions.filter(item => {
                let typeMatch = type === 'all' || item.type === type;
                let statusMatch = status === 'all' || item.status === status;
                let searchMatch = search === '' || item.product.toLowerCase().includes(search) || item.id
                    .toLowerCase().includes(search);
                let dateMatch = date === '' || item.date.split(' ')[0] === date;
                return typeMatch && statusMatch && searchMatch && dateMatch;
            });

            // Pagination
            const totalPages = Math.ceil(filteredData.length / rowsPerPage);
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const paginatedData = filteredData.slice(start, end);

            // Update pagination info
            document.getElementById('startCount').innerText = filteredData.length === 0 ? 0 : start + 1;
            document.getElementById('endCount').innerText = Math.min(end, filteredData.length);
            document.getElementById('totalCount').innerText = filteredData.length;

            document.getElementById('prevBtn').disabled = currentPage === 1;
            document.getElementById('nextBtn').disabled = currentPage === totalPages || totalPages === 0;

            // Render rows
            tbody.innerHTML = paginatedData.map((item, idx) => {
                let typeColor = '';
                let typeIcon = '';
                if (item.type === 'Penerimaan') {
                    typeColor = 'bg-green-100 text-green-700';
                    typeIcon = '📥';
                } else if (item.type === 'Pengeluaran') {
                    typeColor = 'bg-red-100 text-red-700';
                    typeIcon = '📤';
                } else {
                    typeColor = 'bg-blue-100 text-blue-700';
                    typeIcon = '⚙️';
                }

                let statusColor = '';
                if (item.status === 'approved') statusColor = 'status-approved';
                else if (item.status === 'pending') statusColor = 'status-pending';
                else if (item.status === 'rejected') statusColor = 'status-rejected';
                else statusColor = 'status-draft';

                let statusText = '';
                if (item.status === 'approved') statusText = '✓ Disetujui';
                else if (item.status === 'pending') statusText = '⏳ Pending';
                else if (item.status === 'rejected') statusText = '✗ Ditolak';
                else statusText = '📝 Draft';

                let qtyColor = item.qty.startsWith('+') ? 'text-green-600' : 'text-red-600';

                return `
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="p-3">${start + idx + 1}】+
                    <td class="p-3 font-mono text-xs font-bold">${item.id}】+
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-xs ${typeColor}">
                            ${typeIcon} ${item.type}
                        </span>
                    </td>
                    <td class="p-3 font-medium">${item.product}】+
                    <td class="p-3 ${qtyColor} font-semibold">${item.qty} pcs】+
                    <td class="p-3">${item.date}】+
                    <td class="p-3">
                        <div class="flex items-center gap-1">
                            <i class="fas fa-user-circle text-slate-400 text-sm"></i>
                            ${item.user}
                        </div>
                    </td>
                    <td class="p-3 max-w-xs truncate" title="${item.note}">${item.note.substring(0, 30)}${item.note.length > 30 ? '...' : ''}</td>
                    <td class="p-3">
                        <span class="status-badge ${statusColor}">
                            ${statusText}
                        </span>
                    </td>
                    <td class="p-3">
                        <button onclick="viewDetail('${item.id}')" class="text-indigo-600 hover:text-indigo-800" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
            }).join('');

            if (paginatedData.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="p-8 text-center text-slate-400">
                        <i class="fas fa-inbox text-4xl mb-2 block"></i>
                        <p>Tidak ada data transaksi</p>
                        <p class="text-sm mt-1">Coba ubah filter pencarian</p>
                    </td>
                </tr>
            `;
            }
        }

        // Filter functions
        function filterRiwayatTable() {
            currentPage = 1;
            renderRiwayatTable();
        }

        function resetFilters() {
            document.getElementById('typeFilter').value = 'all';
            document.getElementById('statusFilterRiwayat').value = 'all';
            document.getElementById('dateFilter').value = '';
            document.getElementById('searchRiwayat').value = '';
            currentPage = 1;
            renderRiwayatTable();
        }

        function refreshData() {
            // Simulasi refresh data
            showLoading('Menyegarkan data...');
            setTimeout(() => {
                renderRiwayatTable();
                hideLoading();
                showSuccess('Data berhasil disegarkan!');
            }, 500);
        }

        // Pagination
        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                renderRiwayatTable();
            }
        }

        function nextPage() {
            const totalPages = Math.ceil(filteredData.length / rowsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderRiwayatTable();
            }
        }

        // View detail transaksi
        function viewDetail(id) {
            const item = historyTransactions.find(t => t.id === id);
            if (item) {
                const detailHtml = `
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-slate-600">ID Transaksi:</span>
                        <span class="font-mono font-bold">${item.id}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-slate-600">Tipe:</span>
                        <span>${item.type}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-slate-600">Produk:</span>
                        <span class="font-medium">${item.product}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-slate-600">Jumlah:</span>
                        <span class="${item.qty.startsWith('+') ? 'text-green-600' : 'text-red-600'} font-bold">${item.qty} pcs</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-slate-600">Tanggal:</span>
                        <span>${item.date}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-slate-600">User:</span>
                        <span>${item.user}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="font-medium text-slate-600">Status:</span>
                        <span class="status-badge status-${item.status}">${item.status === 'approved' ? 'Disetujui' : (item.status === 'pending' ? 'Pending' : (item.status === 'rejected' ? 'Ditolak' : 'Draft'))}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-600">Keterangan:</span>
                        <span class="text-sm">${item.note || '-'}</span>
                    </div>
                </div>
            `;
                document.getElementById('detailTransaksiContent').innerHTML = detailHtml;
                showModal('modalDetailTransaksi');
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

        function showLoading(message) {
            const modal = document.getElementById('modalLoading');
            if (modal) {
                document.getElementById('loadingMessage').innerHTML = message || 'Memproses...';
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function hideLoading() {
            const modal = document.getElementById('modalLoading');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        function showSuccess(message) {
            alert('✓ ' + message);
        }

        // Inisialisasi halaman
        document.addEventListener('DOMContentLoaded', function() {
            renderRiwayatTable();
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
