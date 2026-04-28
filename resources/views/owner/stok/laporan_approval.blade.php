@extends('layouts.owner')

@section('title', 'Laporan Persetujuan - PROShop')
@section('header-title', 'Laporan Persetujuan Owner')
@section('header-subtitle', 'Lihat histori approve/reject aktivitas gudang')

@section('content')
    <div class="space-y-6">
        {{-- Filter Laporan --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i>Dari Tgl
                    </label>
                    <input type="date" id="dariTanggal"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i>Sampai Tgl
                    </label>
                    <input type="date" id="sampaiTanggal"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        <i class="fas fa-tag text-indigo-500 mr-1"></i>Jenis
                    </label>
                    <select id="filterJenis"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="all">Semua</option>
                        <option value="produk">📦 Produk</option>
                        <option value="tambah_stok">➕ Tambah Stok</option>
                        <option value="pengiriman">📤 Pengiriman</option>
                        <option value="penyesuaian">⚙️ Penyesuaian</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        <i class="fas fa-check-circle text-indigo-500 mr-1"></i>Status
                    </label>
                    <select id="filterStatus"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="all">Semua</option>
                        <option value="approved">✅ Disetujui</option>
                        <option value="rejected">❌ Ditolak</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button onclick="loadData()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-search text-xs"></i> Tampilkan
                </button>
                <button onclick="resetFilter()"
                    class="bg-gray-100 hover:bg-gray-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-undo-alt text-xs"></i> Reset
                </button>
            </div>
        </div>

        {{-- Statistik Ringkasan --}}
        <div id="statistikContainer" class="grid grid-cols-3 md:grid-cols-6 gap-3 hidden">
            <div class="bg-white rounded-lg shadow-sm p-3 text-center border-l-4 border-indigo-500">
                <p class="text-xs text-slate-500">Total</p>
                <p class="text-xl font-bold text-indigo-600" id="statTotal">0</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-3 text-center border-l-4 border-green-500">
                <p class="text-xs text-slate-500">Disetujui</p>
                <p class="text-xl font-bold text-green-600" id="statDisetujui">0</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-3 text-center border-l-4 border-red-500">
                <p class="text-xs text-slate-500">Ditolak</p>
                <p class="text-xl font-bold text-red-600" id="statDitolak">0</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-3 text-center border-l-4 border-indigo-300">
                <p class="text-xs text-slate-500">Produk</p>
                <p class="text-lg font-semibold text-indigo-600" id="statProduk">0</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-3 text-center border-l-4 border-teal-500">
                <p class="text-xs text-slate-500">Tambah Stok</p>
                <p class="text-lg font-semibold text-teal-600" id="statTambahStok">0</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-3 text-center border-l-4 border-orange-500">
                <p class="text-xs text-slate-500">Pengiriman</p>
                <p class="text-lg font-semibold text-orange-600" id="statPengiriman">0</p>
            </div>
        </div>

        {{-- Loading --}}
        <div id="loading" class="text-center py-8 hidden">
            <div class="inline-flex flex-col items-center">
                <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                <p class="mt-2 text-xs text-slate-500">Memuat data...</p>
            </div>
        </div>

        {{-- Tabel Histori --}}
        <div id="tableContainer" class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-100 hidden">
            <div class="px-4 py-2.5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800 text-sm">
                    <i class="fas fa-history text-indigo-500 mr-2"></i>Histori Persetujuan
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500 w-12">No</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Tgl</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Jenis</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Nama/Produk</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Detail</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Pengaju</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
            <div class="px-4 py-2.5 border-t border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <p class="text-xs text-slate-500" id="paginationInfo"></p>
                <div class="flex gap-1" id="paginationButtons"></div>
            </div>
        </div>
    </div>

    <!-- Modal Alasan Ditolak -->
    <div id="modalAlasan" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-sm w-full mx-4 p-5">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-base font-bold text-red-600">
                    <i class="fas fa-times-circle mr-2"></i>Alasan Penolakan
                </h3>
                <button onclick="closeModal('modalAlasan')" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="bg-red-50 rounded-lg p-3">
                <p id="alasanText" class="text-sm text-red-700"></p>
            </div>
            <div class="flex justify-end mt-4">
                <button onclick="closeModal('modalAlasan')"
                    class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
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
            animation: spin 1s linear infinite;
        }

        .status-approved {
            background: #d1fae5;
            color: #059669;
        }

        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 500;
        }

        .progress-bar {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5, #818cf8);
            border-radius: 2px;
        }

        .truncate-text {
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>

    <script>
        let allData = [];
        let filteredData = [];
        let currentPage = 1;
        let pageSize = 15;

        function formatRp(angka) {
            return 'Rp ' + (angka || 0).toLocaleString('id-ID');
        }

        function getStatusBadge(status) {
            if (status === 'approved') {
                return '<span class="status-approved px-2 py-0.5 rounded-full text-xs font-medium"><i class="fas fa-check-circle mr-1 text-xs"></i> Disetujui</span>';
            }
            return '<span class="status-rejected px-2 py-0.5 rounded-full text-xs font-medium"><i class="fas fa-times-circle mr-1 text-xs"></i> Ditolak</span>';
        }

        function getTypeBadge(item) {
            const colors = {
                'produk': 'bg-indigo-100 text-indigo-700',
                'tambah_stok': 'bg-teal-100 text-teal-700',
                'pengiriman': 'bg-orange-100 text-orange-700',
                'penyesuaian': 'bg-purple-100 text-purple-700'
            };
            const icons = {
                'produk': 'fa-box',
                'tambah_stok': 'fa-plus-circle',
                'pengiriman': 'fa-arrow-up',
                'penyesuaian': 'fa-sliders-h'
            };
            return `<span class="type-badge ${colors[item.type]}"><i class="fas ${icons[item.type]} text-xs mr-1"></i>${item.type_label}</span>`;
        }

        function getDetailText(item) {
            if (item.type === 'produk') return `Rp ${item.detail.split('|')[0]?.replace('Kategori: ', '')}`;
            if (item.type === 'tambah_stok') return `+${item.jumlah} ${item.satuan}`;
            if (item.type === 'pengiriman') return `-${item.jumlah} ${item.satuan}`;
            if (item.type === 'penyesuaian') return `${item.perubahan >= 0 ? '+' : ''}${item.perubahan} ${item.satuan}`;
            return item.detail;
        }

        async function loadData() {
            const loading = document.getElementById('loading');
            const tableContainer = document.getElementById('tableContainer');
            const statistikContainer = document.getElementById('statistikContainer');

            loading.classList.remove('hidden');
            tableContainer.classList.add('hidden');
            statistikContainer.classList.add('hidden');

            const dariTanggal = document.getElementById('dariTanggal').value;
            const sampaiTanggal = document.getElementById('sampaiTanggal').value;
            const jenis = document.getElementById('filterJenis').value;
            const status = document.getElementById('filterStatus').value;

            let url = `/owner/stok/laporan-approval/data?`;
            if (dariTanggal) url += `dari_tanggal=${dariTanggal}&`;
            if (sampaiTanggal) url += `sampai_tanggal=${sampaiTanggal}&`;
            if (jenis && jenis !== 'all') url += `jenis=${jenis}&`;
            if (status && status !== 'all') url += `status=${status}`;

            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    allData = result.data;
                    filteredData = [...allData];

                    document.getElementById('statTotal').innerText = result.statistik.total;
                    document.getElementById('statDisetujui').innerText = result.statistik.disetujui;
                    document.getElementById('statDitolak').innerText = result.statistik.ditolak;
                    document.getElementById('statProduk').innerText = result.statistik.produk;
                    document.getElementById('statTambahStok').innerText = result.statistik.tambah_stok;
                    document.getElementById('statPengiriman').innerText = result.statistik.pengiriman;

                    currentPage = 1;
                    renderTable();

                    statistikContainer.classList.remove('hidden');
                    tableContainer.classList.remove('hidden');
                } else {
                    showNotification(result.message || 'Gagal memuat data', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat memuat data', 'error');
            } finally {
                loading.classList.add('hidden');
            }
        }

        function renderTable() {
            const tbody = document.getElementById('tableBody');
            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            const paginatedData = filteredData.slice(start, end);
            const totalData = filteredData.length;
            const totalPages = Math.ceil(totalData / pageSize);

            if (paginatedData.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="8" class="px-3 py-6 text-center text-slate-400">Tidak ada data</td></tr>`;
                document.getElementById('paginationInfo').innerHTML = '';
                document.getElementById('paginationButtons').innerHTML = '';
                return;
            }

            tbody.innerHTML = paginatedData.map((item, idx) => {
                const isRejected = item.status === 'rejected';
                return `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-3 py-2 text-slate-500">${start + idx + 1}</td>
                        <td class="px-3 py-2 text-slate-600 whitespace-nowrap">${item.tanggal_diproses}</td>
                        <td class="px-3 py-2">${getTypeBadge(item)}</td>
                        <td class="px-3 py-2">
                            <div class="truncate-text" title="${item.nama}">
                                <span class="font-medium text-slate-800">${item.nama.length > 25 ? item.nama.substring(0, 25) + '...' : item.nama}</span>
                                <span class="text-slate-400 text-[10px] font-mono ml-1">${item.kode}</span>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-slate-500">${getDetailText(item)}</td>
                        <td class="px-3 py-2 text-slate-500">${item.diajukan_oleh}</td>
                        <td class="px-3 py-2">${getStatusBadge(item.status)}</td>
                        <td class="px-3 py-2 text-center">
                            ${isRejected ? `<button onclick="showAlasan('${escapeHtml(item.alasan_ditolak || 'Tidak ada alasan')}')" class="text-red-500 hover:text-red-700 transition"><i class="fas fa-info-circle"></i></button>` : '<span class="text-slate-300">-</span>'}
                        </td>
                    </tr>
                `;
            }).join('');

            document.getElementById('paginationInfo').innerHTML =
                `${start + 1} - ${Math.min(end, totalData)} dari ${totalData}`;

            let buttons = '';
            buttons +=
                `<button onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="px-2 py-1 border rounded text-xs ${currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'hover:bg-slate-100'}"><i class="fas fa-chevron-left"></i></button>`;

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) {
                buttons +=
                    `<button onclick="goToPage(1)" class="px-2 py-1 border rounded text-xs hover:bg-slate-100">1</button>`;
                if (startPage > 2) buttons += `<span class="px-1 text-slate-400">...</span>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                buttons +=
                    `<button onclick="goToPage(${i})" class="px-2 py-1 border rounded text-xs ${currentPage === i ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100'}">${i}</button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) buttons += `<span class="px-1 text-slate-400">...</span>`;
                buttons +=
                    `<button onclick="goToPage(${totalPages})" class="px-2 py-1 border rounded text-xs hover:bg-slate-100">${totalPages}</button>`;
            }

            buttons +=
                `<button onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="px-2 py-1 border rounded text-xs ${currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'hover:bg-slate-100'}"><i class="fas fa-chevron-right"></i></button>`;

            document.getElementById('paginationButtons').innerHTML = buttons;
        }

        function goToPage(page) {
            const totalPages = Math.ceil(filteredData.length / pageSize);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderTable();
        }

        function resetFilter() {
            document.getElementById('dariTanggal').value = '';
            document.getElementById('sampaiTanggal').value = '';
            document.getElementById('filterJenis').value = 'all';
            document.getElementById('filterStatus').value = 'all';
            loadData();
        }

        function showAlasan(alasan) {
            document.getElementById('alasanText').innerHTML = alasan;
            document.getElementById('modalAlasan').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showNotification(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-5 right-5 z-50 px-3 py-2 rounded-lg shadow-lg text-xs font-medium transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
            }`;
            toast.innerHTML =
                `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-1"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        document.getElementById('dariTanggal').valueAsDate = new Date(new Date().setDate(1));
        document.getElementById('sampaiTanggal').valueAsDate = new Date();

        document.addEventListener('DOMContentLoaded', () => {
            loadData();
        });
    </script>
@endsection
