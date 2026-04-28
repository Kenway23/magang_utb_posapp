@extends('layouts.gudang')

@section('title', 'PROShop - Riwayat Gudang')
@section('page-title', 'Riwayat Transaksi')
@section('page-subtitle', 'Riwayat semua transaksi gudang (penerimaan, pengeluaran, penyesuaian)')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-semibold text-slate-800">
                <i class="fas fa-history text-indigo-600 mr-2"></i>
                Riwayat Transaksi Gudang
            </h3>
            <button onclick="loadData()" class="text-indigo-600 hover:text-indigo-800 transition text-sm">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
        </div>

        <!-- Filter -->
        <div class="mb-5 flex flex-wrap gap-2">
            <select id="typeFilter" onchange="filterData()" class="px-2 py-1.5 border border-slate-300 rounded-lg text-xs">
                <option value="all">Semua Tipe</option>
                <option value="Penerimaan">📥 Penerimaan</option>
                <option value="Pengeluaran">📤 Pengeluaran</option>
                <option value="Penyesuaian">⚙️ Penyesuaian</option>
                <option value="Request Kasir">🏪 Request Kasir</option>
            </select>
            <select id="statusFilter" onchange="filterData()"
                class="px-2 py-1.5 border border-slate-300 rounded-lg text-xs">
                <option value="all">Semua Status</option>
                <option value="approved">✓ Disetujui</option>
                <option value="pending">⏳ Pending</option>
                <option value="rejected">✗ Ditolak</option>
                <option value="draft">📝 Draft</option>
                <option value="waiting_owner">👑 Menunggu Owner</option>
            </select>
            <input type="date" id="dateFilter" onchange="filterData()"
                class="px-2 py-1.5 border border-slate-300 rounded-lg text-xs w-36">
            <input type="text" id="searchInput" onkeyup="filterData()" placeholder="Cari produk/kode..."
                class="px-2 py-1.5 border border-slate-300 rounded-lg text-xs w-48">
            <button onclick="resetFilters()"
                class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-xs hover:bg-gray-600 transition">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>

        <!-- Loading -->
        <div id="loading" class="text-center py-8 hidden">
            <div class="inline-flex flex-col items-center">
                <div class="w-5 h-5 border-3 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                <p class="mt-1 text-xs text-slate-500">Memuat data...</p>
            </div>
        </div>

        <!-- Tabel Compact -->
        <div id="tableContainer" class="hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 border-b">
                        <tr>
                            <th class="px-2 py-2 text-center font-semibold text-slate-500 w-10">No</th>
                            <th class="px-2 py-2 text-left font-semibold text-slate-500">Kode</th>
                            <th class="px-2 py-2 text-center font-semibold text-slate-500 w-24">Tipe</th>
                            <th class="px-2 py-2 text-left font-semibold text-slate-500">Produk</th>
                            <th class="px-2 py-2 text-center font-semibold text-slate-500 w-16">Jumlah</th>
                            <th class="px-2 py-2 text-left font-semibold text-slate-500 w-28">Tanggal</th>
                            <th class="px-2 py-2 text-left font-semibold text-slate-500 w-20">Pengaju</th>
                            <th class="px-2 py-2 text-center font-semibold text-slate-500 w-20">Status</th>
                            <th class="px-2 py-2 text-center font-semibold text-slate-500 w-10">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="riwayatTableBody" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="mt-4 flex justify-between items-center hidden">
            <div class="text-xs text-slate-500">
                <span id="startCount">0</span> - <span id="endCount">0</span> dari <span id="totalCount">0</span>
            </div>
            <div class="flex gap-1">
                <button onclick="prevPage()" id="prevBtn"
                    class="px-2 py-1 border rounded-md text-xs hover:bg-slate-50 disabled:opacity-50 transition" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span id="pageInfo" class="px-2 py-1 text-xs text-slate-600"></span>
                <button onclick="nextPage()" id="nextBtn"
                    class="px-2 py-1 border rounded-md text-xs hover:bg-slate-50 disabled:opacity-50 transition" disabled>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="modalDetail" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 max-h-[80vh] overflow-auto">
            <div class="p-3 border-b sticky top-0 bg-white">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800">
                        <i class="fas fa-info-circle text-indigo-600 mr-2"></i>Detail Transaksi
                    </h3>
                    <button onclick="closeModal('modalDetail')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div id="detailContent" class="p-3 space-y-2 text-xs"></div>
            <div class="p-3 border-t flex justify-end">
                <button onclick="closeModal('modalDetail')"
                    class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs hover:bg-indigo-700 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 0.8s linear infinite;
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 500;
            display: inline-block;
            white-space: nowrap;
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

        .status-waiting_owner {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .type-badge {
            padding: 2px 6px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        .table-row-hover:hover {
            background-color: #f8fafc;
        }

        .truncate-text {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-text {
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>

    <script>
        let allData = [];
        let filteredData = [];
        let currentPage = 1;
        let rowsPerPage = 12;

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        async function loadData() {
            const loading = document.getElementById('loading');
            const tableContainer = document.getElementById('tableContainer');
            const paginationContainer = document.getElementById('paginationContainer');

            loading.classList.remove('hidden');
            tableContainer.classList.add('hidden');
            paginationContainer.classList.add('hidden');

            const type = document.getElementById('typeFilter').value;
            const status = document.getElementById('statusFilter').value;
            const date = document.getElementById('dateFilter').value;
            const search = document.getElementById('searchInput').value;

            let url = `/gudang/riwayat/data?`;
            if (type !== 'all') url += `type=${type}&`;
            if (status !== 'all') url += `status=${status}&`;
            if (date) url += `date=${date}&`;
            if (search) url += `search=${search}`;

            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    allData = result.data;
                    filteredData = [...allData];
                    currentPage = 1;
                    renderTable();
                    tableContainer.classList.remove('hidden');
                    paginationContainer.classList.remove('hidden');
                } else {
                    showNotification(result.message || 'Gagal memuat data', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan', 'error');
            } finally {
                loading.classList.add('hidden');
            }
        }

        function renderTable() {
            const tbody = document.getElementById('riwayatTableBody');
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const paginatedData = filteredData.slice(start, end);
            const totalData = filteredData.length;
            const totalPages = Math.ceil(totalData / rowsPerPage);

            if (paginatedData.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="py-8 text-center text-slate-400">
                    <i class="fas fa-inbox text-2xl mb-1 block"></i>
                    <p class="text-xs">Tidak ada data transaksi</p>
                </td></tr>`;
                document.getElementById('startCount').innerText = '0';
                document.getElementById('endCount').innerText = '0';
                document.getElementById('totalCount').innerText = '0';
                document.getElementById('pageInfo').innerText = '';
                document.getElementById('prevBtn').disabled = true;
                document.getElementById('nextBtn').disabled = true;
                return;
            }

            tbody.innerHTML = paginatedData.map((item, idx) => {
                let statusClass = '';
                let statusText = '';

                switch (item.status) {
                    case 'approved':
                        statusClass = 'status-approved';
                        statusText = '✓ Disetujui';
                        break;
                    case 'pending':
                        statusClass = 'status-pending';
                        statusText = '⏳ Pending';
                        break;
                    case 'rejected':
                        statusClass = 'status-rejected';
                        statusText = '✗ Ditolak';
                        break;
                    case 'draft':
                        statusClass = 'status-draft';
                        statusText = '📝 Draft';
                        break;
                    case 'waiting_owner':
                        statusClass = 'status-waiting_owner';
                        statusText = '👑 Menunggu Owner';
                        break;
                    default:
                        statusClass = 'status-pending';
                        statusText = item.status;
                }

                let qtyClass = item.qty_display.startsWith('+') ? 'text-green-600' : 'text-red-600';
                let qtyIcon = item.qty_display.startsWith('+') ? '↑' : '↓';

                return `
                    <tr class="table-row-hover transition">
                        <td class="px-2 py-2 text-center text-slate-500">${start + idx + 1}</td>
                        <td class="px-2 py-2 font-mono text-[10px] font-semibold text-indigo-600 whitespace-nowrap">${escapeHtml(item.kode).substring(0,15)}${escapeHtml(item.kode).length > 15 ? '...' : ''}</td>
                        <td class="px-2 py-2 text-center">
                            <span class="type-badge ${item.type_color}">
                                ${item.type_icon} ${item.type == 'Request Kasir' ? 'Req Kasir' : (item.type == 'Penyesuaian' ? 'Adjust' : item.type)}
                            </span>
                        </td>
                        <td class="px-2 py-2 font-medium text-slate-700 product-text" title="${escapeHtml(item.produk)}">${item.produk.length > 20 ? item.produk.substring(0,20)+'...' : item.produk}</td>
                        <td class="px-2 py-2 text-center ${qtyClass} font-semibold text-xs">${qtyIcon} ${Math.abs(parseInt(item.qty_display))}</td>
                        <td class="px-2 py-2 text-slate-500 whitespace-nowrap text-[10px]">${item.tanggal}</td>
                        <td class="px-2 py-2 text-slate-600 text-[10px] truncate-text" title="${escapeHtml(item.user)}">${item.user.length > 12 ? item.user.substring(0,12)+'...' : item.user}</td>
                        <td class="px-2 py-2 text-center"><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td class="px-2 py-2 text-center">
                            <button onclick="viewDetail('${item.kode}', '${item.type}')" class="text-indigo-500 hover:text-indigo-700 transition" title="Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            document.getElementById('startCount').innerText = filteredData.length === 0 ? 0 : start + 1;
            document.getElementById('endCount').innerText = Math.min(end, totalData);
            document.getElementById('totalCount').innerText = totalData;
            document.getElementById('pageInfo').innerText = `${currentPage}/${totalPages}`;

            document.getElementById('prevBtn').disabled = currentPage === 1;
            document.getElementById('nextBtn').disabled = currentPage === totalPages || totalPages === 0;
        }

        function viewDetail(kode, type) {
            const item = allData.find(i => i.kode === kode && i.type === type);
            if (!item) return;

            let detailHtml = `
                <div class="bg-slate-50 rounded-lg p-3 space-y-1">
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div><p class="text-slate-400">Kode</p><p class="font-mono font-semibold text-indigo-600 text-[11px]">${escapeHtml(item.kode)}</p></div>
                        <div><p class="text-slate-400">Tipe</p><p>${item.type}</p></div>
                        <div><p class="text-slate-400">Produk</p><p class="font-medium">${escapeHtml(item.produk)}</p></div>
                        <div><p class="text-slate-400">Jumlah</p><p class="${item.qty_display.startsWith('+') ? 'text-green-600' : 'text-red-600'} font-semibold">${item.qty_display}</p></div>
                        <div><p class="text-slate-400">Tanggal</p><p>${item.tanggal}</p></div>
                        <div><p class="text-slate-400">Pengaju</p><p>${escapeHtml(item.user)}</p></div>
            `;

            if (item.detail) {
                if (item.detail.supplier) detailHtml +=
                    `<div><p class="text-slate-400">Supplier</p><p>${escapeHtml(item.detail.supplier)}</p></div>`;
                if (item.detail.tujuan_toko) detailHtml +=
                    `<div><p class="text-slate-400">Tujuan Toko</p><p>${escapeHtml(item.detail.tujuan_toko)}</p></div>`;
                if (item.detail.stok_sebelum !== undefined) detailHtml +=
                    `<div><p class="text-slate-400">Stok Sebelum</p><p>${item.detail.stok_sebelum}</p></div>`;
                if (item.detail.stok_sesudah !== undefined) detailHtml +=
                    `<div><p class="text-slate-400">Stok Sesudah</p><p>${item.detail.stok_sesudah}</p></div>`;
                if (item.detail.alasan) detailHtml +=
                    `<div><p class="text-slate-400">Alasan</p><p>${escapeHtml(item.detail.alasan)}</p></div>`;
                if (item.detail.alasan_ditolak) detailHtml +=
                    `<div><p class="text-slate-400">Alasan Ditolak</p><p class="text-red-600">${escapeHtml(item.detail.alasan_ditolak)}</p></div>`;
            }

            detailHtml +=
                `</div><div class="border-t pt-2 mt-2"><p class="text-slate-400">Keterangan</p><p class="text-xs">${escapeHtml(item.keterangan)}</p></div>`;
            detailHtml +=
                `<div class="border-t pt-2 mt-1"><p class="text-slate-400">Status</p><span class="status-badge status-${item.status} mt-1 inline-block">${item.status}</span></div></div>`;

            document.getElementById('detailContent').innerHTML = detailHtml;
            document.getElementById('modalDetail').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function filterData() {
            currentPage = 1;
            loadData();
        }

        function resetFilters() {
            document.getElementById('typeFilter').value = 'all';
            document.getElementById('statusFilter').value = 'all';
            document.getElementById('dateFilter').value = '';
            document.getElementById('searchInput').value = '';
            loadData();
        }

        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        }

        function nextPage() {
            if (currentPage < Math.ceil(filteredData.length / rowsPerPage)) {
                currentPage++;
                renderTable();
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showNotification(message, type) {
            const toast = document.createElement('div');
            toast.className =
                `fixed bottom-5 right-5 z-50 px-2 py-1.5 rounded-lg shadow-lg text-[10px] font-medium ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
            toast.innerHTML =
                `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-1 text-[10px]"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadData();
        });
    </script>
@endsection
