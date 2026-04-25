@extends('layouts.owner')

@section('title', 'Riwayat Transaksi - PROShop')
@section('header-title', 'Riwayat Transaksi')
@section('header-subtitle', 'Lihat dan kelola semua transaksi yang terjadi')

@section('content')
    <div class="space-y-6">
        {{-- Filter dan Pencarian --}}
        <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">
                        <i class="fas fa-search mr-1 text-indigo-600"></i>Cari No. Transaksi
                    </label>
                    <input type="text" id="searchTransaksi" placeholder="Masukkan No. Transaksi..."
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">
                        <i class="fas fa-calendar mr-1 text-indigo-600"></i>Dari Tanggal
                    </label>
                    <input type="date" id="dariTanggal"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">
                        <i class="fas fa-calendar mr-1 text-indigo-600"></i>Sampai Tanggal
                    </label>
                    <input type="date" id="sampaiTanggal"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex flex-wrap gap-3 mt-4">
                <button onclick="filterTransactions()"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-search mr-1"></i>Cari
                </button>
                <button onclick="resetSearch()"
                    class="px-6 py-2 border rounded-lg text-slate-700 hover:bg-slate-50 transition">
                    <i class="fas fa-undo-alt mr-1"></i>Reset
                </button>
                <button onclick="refreshData()"
                    class="px-6 py-2 border rounded-lg text-slate-700 hover:bg-slate-50 transition">
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
                <button onclick="exportToExcel()"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition ml-auto">
                    <i class="fas fa-file-excel mr-1"></i>Export Excel
                </button>
                <button onclick="exportToPDF()"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-file-pdf mr-1"></i>Export PDF
                </button>
            </div>
        </div>

        {{-- Statistik Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl shadow-md p-4 border border-slate-100">
                <p class="text-slate-500 text-sm">Total Transaksi</p>
                <p class="text-2xl font-bold text-indigo-600" id="totalTransaksiCount">0</p>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-4 border border-slate-100">
                <p class="text-slate-500 text-sm">Total Item Terjual</p>
                <p class="text-2xl font-bold text-blue-600" id="totalItemTerjual">0</p>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-4 border border-slate-100">
                <p class="text-slate-500 text-sm">Total Pendapatan</p>
                <p class="text-2xl font-bold text-green-600" id="totalPendapatan">Rp 0</p>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-4 border border-slate-100">
                <p class="text-slate-500 text-sm">Rata-rata Transaksi</p>
                <p class="text-2xl font-bold text-purple-600" id="rataRataTransaksi">Rp 0</p>
            </div>
        </div>

        {{-- Tabel Riwayat Transaksi --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div
                class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-history text-indigo-600 mr-2"></i>Semua Riwayat Transaksi</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Menampilkan:</span>
                    <select id="pageSize" onchange="changePageSize()" class="px-2 py-1 border rounded-lg text-sm">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">No. Transaksi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Kasir</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Total Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Total Belanja
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-between items-center">
                <p class="text-sm text-slate-500" id="paginationInfo">Menampilkan 0 - 0 dari 0 data</p>
                <div class="flex gap-2" id="paginationButtons"></div>
            </div>
        </div>
    </div>

    {{-- Modal Detail Transaksi --}}
    <div id="modalDetailTransaksi" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 animate-modal">
            <div
                class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white rounded-t-2xl flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-receipt text-indigo-600 mr-2"></i>Detail Transaksi</h3>
                <button onclick="closeModal('modalDetailTransaksi')" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 max-h-[70vh] overflow-y-auto" id="detailContent"></div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-between items-center">
                <button onclick="printTransaction()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-print mr-1"></i>Cetak Struk
                </button>
                <button onclick="closeModal('modalDetailTransaksi')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <style>
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

    <script>
        // Data transaksi lengkap
        let allTransactions = [{
                id: "TRX-24052024-0015",
                date: "2024-05-20",
                dateDisplay: "20 Mei 2024 10:30",
                cashier: "Kasir 1",
                totalItem: 4,
                totalAmount: 15000,
                status: "Lunas",
                items: [{
                    name: "Indomie Goreng",
                    qty: 2,
                    price: 3500
                }, {
                    name: "Telur",
                    qty: 2,
                    price: 4000
                }]
            },
            {
                id: "TRX-24052024-0016",
                date: "2024-05-20",
                dateDisplay: "20 Mei 2024 11:15",
                cashier: "Kasir 2",
                totalItem: 3,
                totalAmount: 27500,
                status: "Lunas",
                items: [{
                    name: "Pocari Sweat",
                    qty: 2,
                    price: 8000
                }, {
                    name: "Roti",
                    qty: 1,
                    price: 11500
                }]
            },
            {
                id: "TRX-24052024-0017",
                date: "2024-05-20",
                dateDisplay: "20 Mei 2024 13:45",
                cashier: "Kasir 1",
                totalItem: 7,
                totalAmount: 89500,
                status: "Lunas",
                items: [{
                    name: "Sunlight",
                    qty: 2,
                    price: 15000
                }, {
                    name: "Rinso",
                    qty: 1,
                    price: 12500
                }, {
                    name: "Pocky",
                    qty: 4,
                    price: 11750
                }]
            },
            {
                id: "TRX-24052024-0018",
                date: "2024-05-21",
                dateDisplay: "21 Mei 2024 09:20",
                cashier: "Kasir 3",
                totalItem: 2,
                totalAmount: 12500,
                status: "Lunas",
                items: [{
                    name: "Aqua",
                    qty: 2,
                    price: 6250
                }]
            },
            {
                id: "TRX-24052024-0019",
                date: "2024-05-21",
                dateDisplay: "21 Mei 2024 14:00",
                cashier: "Kasir 2",
                totalItem: 5,
                totalAmount: 43200,
                status: "Lunas",
                items: [{
                    name: "Goodday",
                    qty: 3,
                    price: 5000
                }, {
                    name: "Indomie",
                    qty: 2,
                    price: 14100
                }]
            },
            {
                id: "TRX-24052024-0020",
                date: "2024-05-22",
                dateDisplay: "22 Mei 2024 10:00",
                cashier: "Kasir 1",
                totalItem: 6,
                totalAmount: 67800,
                status: "Pending",
                items: []
            },
            {
                id: "TRX-24052024-0021",
                date: "2024-05-22",
                dateDisplay: "22 Mei 2024 15:30",
                cashier: "Kasir 3",
                totalItem: 3,
                totalAmount: 23400,
                status: "Lunas",
                items: []
            },
            {
                id: "TRX-24052024-0022",
                date: "2024-05-23",
                dateDisplay: "23 Mei 2024 11:00",
                cashier: "Kasir 2",
                totalItem: 8,
                totalAmount: 124500,
                status: "Lunas",
                items: []
            },
            {
                id: "TRX-24052024-0023",
                date: "2024-05-23",
                dateDisplay: "23 Mei 2024 16:45",
                cashier: "Kasir 1",
                totalItem: 4,
                totalAmount: 35600,
                status: "Batal",
                items: []
            },
            {
                id: "TRX-24052024-0024",
                date: "2024-05-24",
                dateDisplay: "24 Mei 2024 09:30",
                cashier: "Kasir 3",
                totalItem: 2,
                totalAmount: 18700,
                status: "Lunas",
                items: []
            },
            {
                id: "TRX-24052024-0025",
                date: "2024-05-24",
                dateDisplay: "24 Mei 2024 14:20",
                cashier: "Kasir 1",
                totalItem: 3,
                totalAmount: 54200,
                status: "Lunas",
                items: []
            },
            {
                id: "TRX-24052024-0026",
                date: "2024-05-25",
                dateDisplay: "25 Mei 2024 08:45",
                cashier: "Kasir 2",
                totalItem: 5,
                totalAmount: 78300,
                status: "Lunas",
                items: []
            },
        ];

        let filteredTransactions = [...allTransactions];
        let currentPage = 1;
        let pageSize = 25;

        // Format Rupiah
        function formatRp(angka) {
            return 'Rp ' + angka.toLocaleString('id-ID');
        }

        // Format Tanggal untuk tampilan
        function formatDateDisplay(dateStr) {
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                'Oktober', 'November', 'Desember'
            ];
            const [year, month, day] = dateStr.split('-');
            return `${parseInt(day)} ${months[parseInt(month)-1]} ${year}`;
        }

        // Hitung statistik
        function updateStatistics(data) {
            const totalTransaksi = data.length;
            const totalItem = data.reduce((sum, t) => sum + t.totalItem, 0);
            const totalPendapatan = data.reduce((sum, t) => sum + (t.status === 'Lunas' ? t.totalAmount : 0), 0);
            const rataRata = totalTransaksi > 0 ? totalPendapatan / totalTransaksi : 0;

            document.getElementById('totalTransaksiCount').innerText = totalTransaksi;
            document.getElementById('totalItemTerjual').innerText = totalItem;
            document.getElementById('totalPendapatan').innerHTML = formatRp(totalPendapatan);
            document.getElementById('rataRataTransaksi').innerHTML = formatRp(Math.round(rataRata));
        }

        // Render tabel dengan pagination
        function renderTable() {
            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            const paginatedData = filteredTransactions.slice(start, end);
            const tbody = document.getElementById('tableBody');
            const startIndex = start + 1;

            // Update statistik
            updateStatistics(filteredTransactions);

            if (paginatedData.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="8" class="px-6 py-12 text-center text-slate-500"><i class="fas fa-inbox text-4xl mb-3 block"></i>Tidak ada transaksi</td></tr>`;
                document.getElementById('paginationInfo').innerText = `Menampilkan 0 - 0 dari 0 data`;
                document.getElementById('paginationButtons').innerHTML = '';
                return;
            }

            // Render baris tabel
            tbody.innerHTML = paginatedData.map((t, index) => {
                let statusColor = '';
                let statusIcon = '';
                if (t.status === 'Lunas') {
                    statusColor = 'bg-green-100 text-green-700';
                    statusIcon = '<i class="fas fa-check-circle mr-1"></i>';
                } else if (t.status === 'Pending') {
                    statusColor = 'bg-yellow-100 text-yellow-700';
                    statusIcon = '<i class="fas fa-clock mr-1"></i>';
                } else {
                    statusColor = 'bg-red-100 text-red-700';
                    statusIcon = '<i class="fas fa-times-circle mr-1"></i>';
                }

                return `<tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-3 text-sm text-slate-500">${startIndex + index}</td>
                    <td class="px-6 py-3 text-sm font-medium text-indigo-600">${t.id}</td>
                    <td class="px-6 py-3 text-sm text-slate-600">${t.dateDisplay}</td>
                    <td class="px-6 py-3"><span class="px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs">${t.cashier}</span></td>
                    <td class="px-6 py-3 text-sm text-slate-600">${t.totalItem} item</td>
                    <td class="px-6 py-3 text-sm font-semibold text-green-600">${formatRp(t.totalAmount)}</td>
                    <td class="px-6 py-3"><span class="px-2 py-1 rounded-full text-xs ${statusColor}">${statusIcon} ${t.status}</span></td>
                    <td class="px-6 py-3">
                        <button onclick="showDetail('${t.id}')" class="text-indigo-600 hover:text-indigo-800 transition" title="Detail">
                            <i class="fas fa-file-alt mr-1"></i>Detail
                        </button>
                    </td>
                </tr>`;
            }).join('');

            // Update pagination info
            const totalData = filteredTransactions.length;
            const totalPages = Math.ceil(totalData / pageSize);
            const showingEnd = Math.min(end, totalData);
            document.getElementById('paginationInfo').innerText =
                `Menampilkan ${start + 1} - ${showingEnd} dari ${totalData} data`;

            // Render pagination buttons
            renderPaginationButtons(totalPages);
        }

        // Render tombol pagination
        function renderPaginationButtons(totalPages) {
            const container = document.getElementById('paginationButtons');
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let buttons = '';
            // Previous button
            buttons += `<button onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} 
                class="px-3 py-1 border rounded-md text-sm ${currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100'}">
                <i class="fas fa-chevron-left"></i>
            </button>`;

            // Page numbers
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) {
                buttons +=
                    `<button onclick="goToPage(1)" class="px-3 py-1 border rounded-md text-sm text-slate-600 hover:bg-slate-100">1</button>`;
                if (startPage > 2) buttons += `<span class="px-2 text-slate-400">...</span>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                buttons += `<button onclick="goToPage(${i})" 
                    class="px-3 py-1 border rounded-md text-sm ${currentPage === i ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'}">
                    ${i}
                </button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) buttons += `<span class="px-2 text-slate-400">...</span>`;
                buttons +=
                    `<button onclick="goToPage(${totalPages})" class="px-3 py-1 border rounded-md text-sm text-slate-600 hover:bg-slate-100">${totalPages}</button>`;
            }

            // Next button
            buttons += `<button onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}
                class="px-3 py-1 border rounded-md text-sm ${currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100'}">
                <i class="fas fa-chevron-right"></i>
            </button>`;

            container.innerHTML = buttons;
        }

        // Pindah halaman
        function goToPage(page) {
            const totalPages = Math.ceil(filteredTransactions.length / pageSize);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderTable();
        }

        // Ubah jumlah data per halaman
        function changePageSize() {
            pageSize = parseInt(document.getElementById('pageSize').value);
            currentPage = 1;
            renderTable();
        }

        // Filter transaksi berdasarkan pencarian dan tanggal
        function filterTransactions() {
            const searchValue = document.getElementById('searchTransaksi').value.toLowerCase();
            const dariTanggal = document.getElementById('dariTanggal').value;
            const sampaiTanggal = document.getElementById('sampaiTanggal').value;

            filteredTransactions = allTransactions.filter(t => {
                let match = true;
                if (searchValue && !t.id.toLowerCase().includes(searchValue)) match = false;
                if (dariTanggal && t.date < dariTanggal) match = false;
                if (sampaiTanggal && t.date > sampaiTanggal) match = false;
                return match;
            });

            currentPage = 1;
            renderTable();
        }

        // Reset semua filter
        function resetSearch() {
            document.getElementById('searchTransaksi').value = '';
            document.getElementById('dariTanggal').value = '';
            document.getElementById('sampaiTanggal').value = '';
            filteredTransactions = [...allTransactions];
            currentPage = 1;
            renderTable();
        }

        // Refresh data (simulasi ambil data baru)
        function refreshData() {
            showLoading('Mengambil data terbaru...');
            setTimeout(() => {
                resetSearch();
                hideLoading();
                showSuccess('Data berhasil diperbarui!');
            }, 500);
        }

        // Show modal detail
        function showDetail(id) {
            const transaction = allTransactions.find(x => x.id === id);
            if (!transaction) return;

            let itemsHtml = '';
            if (transaction.items && transaction.items.length > 0) {
                itemsHtml = `
                    <div class="border-t pt-4 mt-4">
                        <p class="font-medium text-slate-700 mb-3">📋 Daftar Item:</p>
                        <div class="space-y-2">
                            ${transaction.items.map(item => `
                                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                        <div>
                                            <p class="font-medium text-slate-700">${item.name}</p>
                                            <p class="text-xs text-slate-400">${item.qty} x ${formatRp(item.price)}</p>
                                        </div>
                                        <p class="font-semibold text-green-600">${formatRp(item.qty * item.price)}</p>
                                    </div>
                                `).join('')}
                        </div>
                    </div>
                `;
            } else {
                itemsHtml = `
                    <div class="border-t pt-4 mt-4">
                        <p class="text-sm text-slate-500 text-center py-4">Tidak ada detail item</p>
                    </div>
                `;
            }

            document.getElementById('detailContent').innerHTML = `
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-3 rounded-lg">
                            <p class="text-xs text-slate-400">No. Transaksi</p>
                            <p class="font-semibold text-slate-800">${transaction.id}</p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg">
                            <p class="text-xs text-slate-400">Tanggal</p>
                            <p class="font-semibold text-slate-800">${transaction.dateDisplay}</p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg">
                            <p class="text-xs text-slate-400">Kasir</p>
                            <p class="font-semibold text-slate-800">${transaction.cashier}</p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg">
                            <p class="text-xs text-slate-400">Total Item</p>
                            <p class="font-semibold text-slate-800">${transaction.totalItem} item</p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg">
                            <p class="text-xs text-slate-400">Status</p>
                            <p class="font-semibold ${transaction.status === 'Lunas' ? 'text-green-600' : transaction.status === 'Pending' ? 'text-yellow-600' : 'text-red-600'}">
                                ${transaction.status}
                            </p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg">
                            <p class="text-xs text-slate-400">Total Belanja</p>
                            <p class="font-bold text-green-600 text-xl">${formatRp(transaction.totalAmount)}</p>
                        </div>
                    </div>
                    ${itemsHtml}
                </div>
            `;

            // Simpan ID transaksi untuk print
            window.currentTransactionId = id;
            showModal('modalDetailTransaksi');
        }

        // Print transaction
        function printTransaction() {
            const transaction = allTransactions.find(x => x.id === window.currentTransactionId);
            if (transaction) {
                showSuccess(`Mencetak struk untuk transaksi ${transaction.id}`);
                // Buka window print atau generate PDF
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                    <head><title>Struk Transaksi ${transaction.id}</title>
                    <style>
                        body { font-family: monospace; padding: 20px; }
                        .header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; }
                        .content { margin: 20px 0; }
                        .footer { text-align: center; border-top: 1px dashed #000; padding-top: 10px; }
                        table { width: 100%; }
                        td { padding: 5px 0; }
                    </style>
                    </head>
                    <body>
                        <div class="header">
                            <h2>PROShop</h2>
                            <p>Jl. Raya No. 123, Indonesia</p>
                            <p>Telp: (021) 1234567</p>
                        </div>
                        <div class="content">
                            <p><strong>No. Transaksi:</strong> ${transaction.id}</p>
                            <p><strong>Tanggal:</strong> ${transaction.dateDisplay}</p>
                            <p><strong>Kasir:</strong> ${transaction.cashier}</p>
                            <hr>
                            <table>
                                ${transaction.items ? transaction.items.map(item => `
                                        <tr><td>${item.name}</td><td align="right">${item.qty} x ${formatRp(item.price)}</td><td align="right">${formatRp(item.qty * item.price)}</td></tr>
                                    `).join('') : '<tr><td colspan="3" align="center">Tidak ada detail</td></tr>'}
                                <tr><td colspan="2" align="right"><strong>Total:</strong></td><td align="right"><strong>${formatRp(transaction.totalAmount)}</strong></td></tr>
                            </table>
                            <hr>
                            <p align="center">Terima kasih telah berbelanja!</p>
                        </div>
                        <div class="footer">
                            <p>*** Simpan struk ini sebagai bukti pembayaran ***</p>
                        </div>
                        <script>window.print();window.close();<\/script>
                    </body>
                    </html>
                `);
                printWindow.document.close();
            }
        }

        // Export to Excel (CSV)
        function exportToExcel() {
            let csvContent = "No,No. Transaksi,Tanggal,Kasir,Total Item,Total Belanja,Status\n";
            filteredTransactions.forEach((t, index) => {
                csvContent +=
                    `${index + 1},${t.id},${t.dateDisplay},${t.cashir},${t.totalItem},${t.totalAmount},${t.status}\n`;
            });

            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', `riwayat_transaksi_${new Date().toISOString().slice(0,19)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            showSuccess('File Excel berhasil diunduh!');
        }

        // Export to PDF (simulasi)
        function exportToPDF() {
            showLoading('Menyiapkan file PDF...');
            setTimeout(() => {
                hideLoading();
                showSuccess('Laporan PDF berhasil diunduh!');
            }, 1000);
        }

        // Show modal
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        // Close modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // Show loading
        function showLoading(message) {
            let loadingModal = document.getElementById('modalLoading');
            if (!loadingModal) {
                loadingModal = document.createElement('div');
                loadingModal.id = 'modalLoading';
                loadingModal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden';
                loadingModal.innerHTML = `
                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xs mx-4 p-6 text-center">
                        <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-3"></div>
                        <p class="text-slate-600" id="loadingMessage">Memproses...</p>
                    </div>
                `;
                document.body.appendChild(loadingModal);
            }
            document.getElementById('loadingMessage').innerText = message;
            loadingModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideLoading() {
            const modal = document.getElementById('modalLoading');
            if (modal) modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showSuccess(message) {
            const modal = document.getElementById('modalSuccess');
            if (modal) {
                document.getElementById('successTitle').innerText = 'Berhasil!';
                document.getElementById('successMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => closeModal('modalSuccess'), 2500);
            } else {
                alert(message);
            }
        }

        // Event listener
        document.getElementById('searchTransaksi').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') filterTransactions();
        });

        // Render awal
        renderTable();
    </script>
@endsection
