@extends('layouts.owner')

@section('title', 'Laporan Penjualan - PROShop')
@section('header-title', 'Laporan Penjualan Owner')
@section('header-subtitle', 'Lihat dan kelola laporan penjualan toko secara lengkap')

@section('content')
    <div class="space-y-6">
        {{-- Filter Laporan --}}
        <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">
                        <i class="fas fa-calendar text-indigo-600 mr-1"></i>Dari Tanggal
                    </label>
                    <input type="date" id="dariTanggal"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">
                        <i class="fas fa-calendar text-indigo-600 mr-1"></i>Sampai Tanggal
                    </label>
                    <input type="date" id="sampaiTanggal"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">
                        <i class="fas fa-user text-indigo-600 mr-1"></i>Kasir
                    </label>
                    <select id="filterKasir" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="all">Semua Kasir</option>
                        <option value="Kasir 1">Kasir 1</option>
                        <option value="Kasir 2">Kasir 2</option>
                        <option value="Kasir 3">Kasir 3</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">
                        <i class="fas fa-chart-line text-indigo-600 mr-1"></i>Jenis Laporan
                    </label>
                    <select id="jenisLaporan" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="all">Semua</option>
                        <option value="produk">Per Produk</option>
                        <option value="transaksi">Per Transaksi</option>
                        <option value="kategori">Per Kategori</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button onclick="filterLaporan()"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-search mr-1"></i>Tampilkan
                    </button>
                    <button onclick="resetFilter()"
                        class="px-6 py-2 border rounded-lg text-slate-700 hover:bg-slate-50 transition">
                        <i class="fas fa-undo-alt mr-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- Statistik Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-md p-6 card-hover border border-slate-100">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-sm">Total Transaksi</p>
                        <p class="text-3xl font-bold text-indigo-600" id="totalTransaksi">0</p>
                        <p class="text-xs text-green-600 mt-2" id="trendTransaksi">+0%</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-receipt text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-6 card-hover border border-slate-100">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-sm">Total Pendapatan</p>
                        <p class="text-3xl font-bold text-green-600" id="totalPendapatan">Rp 0</p>
                        <p class="text-xs text-green-600 mt-2" id="trendPendapatan">+0%</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-rupiah-sign text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-md p-6 card-hover border border-slate-100">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-sm">Total Item Terjual</p>
                        <p class="text-3xl font-bold text-blue-600" id="totalItemTerjual">0</p>
                        <p class="text-xs text-green-600 mt-2" id="trendItem">+0%</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-boxes text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik Penjualan (Chart.js) --}}
        {{-- <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
                <h3 class="font-semibold"><i class="fas fa-chart-bar text-indigo-600 mr-2"></i>Grafik Penjualan</h3>
            </div>
            <div class="p-6">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div> --}}

        {{-- Tombol Export Laporan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="exportLaporan('pdf')"
                class="bg-red-600 text-white px-5 py-3 rounded-xl hover:bg-red-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-file-pdf text-xl"></i>
                <span>Export PDF</span>
            </button>
            <button onclick="exportLaporan('excel')"
                class="bg-green-600 text-white px-5 py-3 rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-file-excel text-xl"></i>
                <span>Export Excel</span>
            </button>
            <button onclick="printLaporan()"
                class="bg-blue-600 text-white px-5 py-3 rounded-xl hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-print text-xl"></i>
                <span>Cetak Laporan</span>
            </button>
        </div>

        {{-- Ringkasan Penjualan Per Produk --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div
                class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-boxes text-indigo-600 mr-2"></i>Ringkasan Penjualan Per Produk
                </h3>
                <div class="flex gap-2">
                    <input type="text" id="searchProduk" placeholder="Cari produk..."
                        class="px-3 py-1 border rounded-lg text-sm w-48">
                    <button onclick="filterProduk()"
                        class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm">Cari</button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Qty Terjual</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Total Pendapatan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">% Kontribusi
                            </th>
                        </tr>
                    </thead>
                    <tbody id="productSalesBody"></tbody>
                    <tfoot class="bg-slate-50">
                        <tr class="font-semibold">
                            <td colspan="4" class="px-6 py-3 text-right">Total Keseluruhan:</td>
                            <td id="totalProductRevenue" class="px-6 py-3 font-bold text-green-600">Rp 0</td>
                            <td class="px-6 py-3">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Ringkasan Penjualan Per Kategori --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
                <h3 class="font-semibold"><i class="fas fa-tags text-indigo-600 mr-2"></i>Ringkasan Penjualan Per Kategori
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Total Item Terjual</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Total Pendapatan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">% Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody id="categorySalesBody"></tbody>
                    <tfoot class="bg-slate-50">
                        <tr class="font-semibold">
                            <td colspan="3" class="px-6 py-3 text-right">Total Keseluruhan:</td>
                            <td id="totalCategoryRevenue" class="px-6 py-3 font-bold text-green-600">Rp 0</td>
                            <td>100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Ringkasan Penjualan Per Transaksi --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div
                class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-receipt text-indigo-600 mr-2"></i>Ringkasan Penjualan Per
                    Transaksi</h3>
                <div class="flex gap-2">
                    <select id="transactionPageSize" onchange="changeTransactionPage()"
                        class="px-2 py-1 border rounded-lg text-sm">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold">No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">No. Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Kasir</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Total Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Total Belanja</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody id="transactionSalesBody"></tbody>
                    <tfoot class="bg-slate-50">
                        <tr class="font-semibold">
                            <td colspan="5" class="px-6 py-3 text-right">Total Keseluruhan:</td>
                            <td id="totalTransactionRevenue" class="px-6 py-3 font-bold text-green-600">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-slate-100 flex justify-between items-center">
                <p class="text-sm text-slate-500" id="transactionPaginationInfo"></p>
                <div class="flex gap-2" id="transactionPaginationButtons"></div>
            </div>
        </div>

        {{-- Top 5 Produk Terlaris --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
                <h3 class="font-semibold"><i class="fas fa-trophy text-yellow-500 mr-2"></i>Top 5 Produk Terlaris</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4" id="topProductsList"></div>
            </div>
        </div>
    </div>

    <style>
        .card-hover:hover {
            transform: translateY(-4px);
            transition: all 0.3s ease;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5, #818cf8);
            border-radius: 4px;
            transition: width 0.5s ease;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data Laporan (lengkap)
        let salesData = {
            products: [{
                    name: "Indomie Goreng",
                    category: "Makanan",
                    qty: 125,
                    revenue: 437500
                },
                {
                    name: "Pocky Coklat",
                    category: "Makanan",
                    qty: 89,
                    revenue: 703100
                },
                {
                    name: "Tolak Angin",
                    category: "Kesehatan",
                    qty: 76,
                    revenue: 357200
                },
                {
                    name: "Sunlight 690ml",
                    category: "Kebersihan",
                    qty: 68,
                    revenue: 1020000
                },
                {
                    name: "Pocari Sweat",
                    category: "Minuman",
                    qty: 54,
                    revenue: 432000
                },
                {
                    name: "Rinso Bubuk",
                    category: "Kebersihan",
                    qty: 47,
                    revenue: 587500
                },
                {
                    name: "Kayu Putih",
                    category: "Kesehatan",
                    qty: 42,
                    revenue: 373800
                },
                {
                    name: "Goodday",
                    category: "Minuman",
                    qty: 38,
                    revenue: 190000
                },
                {
                    name: "Japota Honey",
                    category: "Makanan",
                    qty: 35,
                    revenue: 381500
                },
                {
                    name: "Aqua Mineral",
                    category: "Minuman",
                    qty: 32,
                    revenue: 160000
                }
            ],
            categories: [{
                    name: "Makanan",
                    qty: 249,
                    revenue: 1522100
                },
                {
                    name: "Kebersihan",
                    qty: 115,
                    revenue: 1607500
                },
                {
                    name: "Kesehatan",
                    qty: 118,
                    revenue: 731000
                },
                {
                    name: "Minuman",
                    qty: 124,
                    revenue: 782000
                }
            ],
            transactions: [{
                    id: "TRX-001",
                    date: "20/05/2024",
                    cashier: "Kasir 1",
                    totalItem: 4,
                    totalAmount: 150000,
                    status: "Lunas"
                },
                {
                    id: "TRX-002",
                    date: "20/05/2024",
                    cashier: "Kasir 2",
                    totalItem: 3,
                    totalAmount: 27500,
                    status: "Lunas"
                },
                {
                    id: "TRX-003",
                    date: "21/05/2024",
                    cashier: "Kasir 1",
                    totalItem: 7,
                    totalAmount: 89500,
                    status: "Lunas"
                },
                {
                    id: "TRX-004",
                    date: "21/05/2024",
                    cashier: "Kasir 3",
                    totalItem: 2,
                    totalAmount: 12500,
                    status: "Lunas"
                },
                {
                    id: "TRX-005",
                    date: "22/05/2024",
                    cashier: "Kasir 2",
                    totalItem: 5,
                    totalAmount: 43200,
                    status: "Lunas"
                },
                {
                    id: "TRX-006",
                    date: "22/05/2024",
                    cashier: "Kasir 1",
                    totalItem: 6,
                    totalAmount: 67800,
                    status: "Pending"
                }
            ]
        };

        let filteredProducts = [...salesData.products];
        let filteredTransactions = [...salesData.transactions];
        let currentTransactionPage = 1;
        let transactionPageSize = 10;
        let salesChart = null;

        // Format Rupiah
        function formatRp(angka) {
            return 'Rp ' + angka.toLocaleString('id-ID');
        }

        // Update statistik
        function updateStatistics() {
            const totalTransaksi = filteredTransactions.length;
            const totalPendapatan = filteredTransactions.filter(t => t.status === 'Lunas').reduce((s, t) => s + t
                .totalAmount, 0);
            const totalItem = filteredProducts.reduce((s, p) => s + p.qty, 0);
            const rataTransaksi = totalTransaksi > 0 ? totalPendapatan / totalTransaksi : 0;

            document.getElementById('totalTransaksi').innerText = totalTransaksi;
            document.getElementById('totalPendapatan').innerHTML = formatRp(totalPendapatan);
            document.getElementById('totalItemTerjual').innerText = totalItem;
            document.getElementById('rataTransaksi').innerHTML = formatRp(Math.round(rataTransaksi));
        }

        // Render produk dengan filter dan pagination
        function renderProductSales() {
            const searchTerm = document.getElementById('searchProduk')?.value.toLowerCase() || '';
            const filtered = filteredProducts.filter(p => p.name.toLowerCase().includes(searchTerm));
            const totalRevenue = filtered.reduce((s, p) => s + p.revenue, 0);

            document.getElementById('totalProductRevenue').innerHTML = formatRp(totalRevenue);

            if (filtered.length === 0) {
                document.getElementById('productSalesBody').innerHTML =
                    '<tr><td colspan="6" class="px-6 py-12 text-center">Tidak ada data</td></tr>';
                return;
            }

            document.getElementById('productSalesBody').innerHTML = filtered.map((p, idx) => {
                const percentage = totalRevenue > 0 ? ((p.revenue / totalRevenue) * 100).toFixed(1) : 0;
                return `<tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 text-sm">${idx + 1}</td>
                    <td class="px-6 py-3 font-medium text-slate-700">${p.name}</td>
                    <td class="px-6 py-3 text-sm"><span class="px-2 py-1 rounded-full text-xs bg-gray-100">${p.category}</span></td>
                    <td class="px-6 py-3 text-sm">${p.qty} item</td>
                    <td class="px-6 py-3 font-semibold text-green-600">${formatRp(p.revenue)}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <div class="progress-bar flex-1"><div class="progress-fill" style="width: ${percentage}%"></div></div>
                            <span class="text-xs text-slate-500 w-12">${percentage}%</span>
                        </div>
                    </td>
                </tr>`;
            }).join('');
        }

        // Render kategori
        function renderCategorySales() {
            const totalRevenue = salesData.categories.reduce((s, c) => s + c.revenue, 0);
            document.getElementById('totalCategoryRevenue').innerHTML = formatRp(totalRevenue);

            document.getElementById('categorySalesBody').innerHTML = salesData.categories.map((c, idx) => {
                const percentage = totalRevenue > 0 ? ((c.revenue / totalRevenue) * 100).toFixed(1) : 0;
                return `<tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 text-sm">${idx + 1}</td>
                    <td class="px-6 py-3 font-medium text-slate-700">${c.name}</td>
                    <td class="px-6 py-3 text-sm">${c.qty} item</td>
                    <td class="px-6 py-3 font-semibold text-green-600">${formatRp(c.revenue)}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <div class="progress-bar flex-1"><div class="progress-fill" style="width: ${percentage}%"></div></div>
                            <span class="text-xs text-slate-500">${percentage}%</span>
                        </div>
                    </td>
                </tr>`;
            }).join('');
        }

        // Render transaksi dengan pagination
        function renderTransactionSales() {
            const start = (currentTransactionPage - 1) * transactionPageSize;
            const end = start + transactionPageSize;
            const paginatedData = filteredTransactions.slice(start, end);
            const totalRevenue = filteredTransactions.reduce((s, t) => s + t.totalAmount, 0);

            document.getElementById('totalTransactionRevenue').innerHTML = formatRp(totalRevenue);

            if (paginatedData.length === 0) {
                document.getElementById('transactionSalesBody').innerHTML =
                    '<tr><td colspan="7" class="px-6 py-12 text-center">Tidak ada data</td></tr>';
                document.getElementById('transactionPaginationInfo').innerHTML = '';
                document.getElementById('transactionPaginationButtons').innerHTML = '';
                return;
            }

            document.getElementById('transactionSalesBody').innerHTML = paginatedData.map((t, idx) => {
                let statusColor = t.status === 'Lunas' ? 'bg-green-100 text-green-700' :
                    'bg-yellow-100 text-yellow-700';
                return `<tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 text-sm text-slate-500">${start + idx + 1}</td>
                    <td class="px-6 py-3 text-sm font-medium text-indigo-600">${t.id}</td>
                    <td class="px-6 py-3 text-sm">${t.date}</td>
                    <td class="px-6 py-3"><span class="px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs">${t.cashier}</span></td>
                    <td class="px-6 py-3 text-sm">${t.totalItem}</td>
                    <td class="px-6 py-3 font-semibold text-green-600">${formatRp(t.totalAmount)}</td>
                    <td class="px-6 py-3"><span class="px-2 py-1 rounded-full text-xs ${statusColor}">${t.status}</span></td>
                </tr>`;
            }).join('');

            // Update pagination info
            const totalData = filteredTransactions.length;
            const totalPages = Math.ceil(totalData / transactionPageSize);
            const showingEnd = Math.min(end, totalData);
            document.getElementById('transactionPaginationInfo').innerHTML =
                `Menampilkan ${start + 1} - ${showingEnd} dari ${totalData} transaksi`;

            // Render pagination buttons
            let buttons = '';
            buttons +=
                `<button onclick="changeTransactionPageNum(${currentTransactionPage - 1})" ${currentTransactionPage === 1 ? 'disabled' : ''} class="px-3 py-1 border rounded-md text-sm ${currentTransactionPage === 1 ? 'text-slate-300' : 'hover:bg-slate-100'}"><i class="fas fa-chevron-left"></i></button>`;

            let startPage = Math.max(1, currentTransactionPage - 2);
            let endPage = Math.min(totalPages, currentTransactionPage + 2);
            for (let i = startPage; i <= endPage; i++) {
                buttons +=
                    `<button onclick="changeTransactionPageNum(${i})" class="px-3 py-1 border rounded-md text-sm ${currentTransactionPage === i ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100'}">${i}</button>`;
            }

            buttons +=
                `<button onclick="changeTransactionPageNum(${currentTransactionPage + 1})" ${currentTransactionPage === totalPages ? 'disabled' : ''} class="px-3 py-1 border rounded-md text-sm ${currentTransactionPage === totalPages ? 'text-slate-300' : 'hover:bg-slate-100'}"><i class="fas fa-chevron-right"></i></button>`;

            document.getElementById('transactionPaginationButtons').innerHTML = buttons;
        }

        // Top 5 produk terlaris
        function renderTopProducts() {
            const top5 = [...salesData.products].sort((a, b) => b.revenue - a.revenue).slice(0, 5);
            const maxRevenue = top5[0]?.revenue || 1;

            document.getElementById('topProductsList').innerHTML = top5.map((p, idx) => {
                const percentage = (p.revenue / maxRevenue) * 100;
                const medals = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];
                return `
                    <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-xl">
                        <div class="w-10 text-center text-2xl">${medals[idx]}</div>
                        <div class="flex-1">
                            <div class="flex justify-between mb-1">
                                <span class="font-medium text-slate-700">${p.name}</span>
                                <span class="font-semibold text-green-600">${formatRp(p.revenue)}</span>
                            </div>
                            <div class="progress-bar"><div class="progress-fill" style="width: ${percentage}%"></div></div>
                            <p class="text-xs text-slate-400 mt-1">Terjual: ${p.qty} item</p>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Grafik penjualan
        function initChart() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const categories = salesData.categories.map(c => c.name);
            const revenues = salesData.categories.map(c => c.revenue);

            if (salesChart) salesChart.destroy();

            salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: revenues,
                        backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `Rp ${ctx.raw.toLocaleString()}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (v) => `Rp ${v.toLocaleString()}`
                            }
                        }
                    }
                }
            });
        }

        // Filter laporan
        function filterLaporan() {
            const dari = document.getElementById('dariTanggal')?.value;
            const sampai = document.getElementById('sampaiTanggal')?.value;
            const kasir = document.getElementById('filterKasir')?.value;

            // Filter transaksi
            filteredTransactions = salesData.transactions.filter(t => {
                if (kasir !== 'all' && t.cashier !== kasir) return false;
                return true;
            });

            updateStatistics();
            renderTransactionSales();
            showSuccess('Laporan berhasil diperbarui!');
        }

        function resetFilter() {
            document.getElementById('dariTanggal').value = '';
            document.getElementById('sampaiTanggal').value = '';
            document.getElementById('filterKasir').value = 'all';
            document.getElementById('searchProduk').value = '';
            filteredProducts = [...salesData.products];
            filteredTransactions = [...salesData.transactions];
            currentTransactionPage = 1;
            updateStatistics();
            renderProductSales();
            renderTransactionSales();
            showSuccess('Filter berhasil direset!');
        }

        function filterProduk() {
            renderProductSales();
        }

        function changeTransactionPage() {
            transactionPageSize = parseInt(document.getElementById('transactionPageSize').value);
            currentTransactionPage = 1;
            renderTransactionSales();
        }

        function changeTransactionPageNum(page) {
            const totalPages = Math.ceil(filteredTransactions.length / transactionPageSize);
            if (page < 1 || page > totalPages) return;
            currentTransactionPage = page;
            renderTransactionSales();
        }

        // Export functions
        function exportLaporan(type) {
            showLoading(`Menyiapkan file ${type.toUpperCase()}...`);
            setTimeout(() => {
                hideLoading();
                showSuccess(`Laporan berhasil diexport ke ${type.toUpperCase()}!`);
            }, 1000);
        }

        function printLaporan() {
            window.print();
        }

        // Loading and notification functions
        function showLoading(message) {
            let loadingModal = document.getElementById('modalLoading');
            if (!loadingModal) {
                loadingModal = document.createElement('div');
                loadingModal.id = 'modalLoading';
                loadingModal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden';
                loadingModal.innerHTML =
                    `<div class="bg-white rounded-2xl shadow-xl w-full max-w-xs mx-4 p-6 text-center"><div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-3"></div><p class="text-slate-600" id="loadingMessage">Memproses...</p></div>`;
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
            let successModal = document.getElementById('modalSuccess');
            if (!successModal) {
                successModal = document.createElement('div');
                successModal.id = 'modalSuccess';
                successModal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden';
                successModal.innerHTML =
                    `<div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center"><div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-check-circle text-green-600 text-3xl"></i></div><h3 class="text-xl font-semibold text-slate-800 mb-2">Berhasil!</h3><p class="text-slate-500" id="successMessage"></p><button onclick="this.closest('.fixed').classList.add('hidden'); document.body.style.overflow='auto'" class="mt-6 px-6 py-2 bg-green-600 text-white rounded-lg">Tutup</button></div>`;
                document.body.appendChild(successModal);
            }
            document.getElementById('successMessage').innerHTML = message;
            successModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                successModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 2500);
        }

        // Initialize
        function init() {
            // Set default tanggal (bulan ini)
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            document.getElementById('dariTanggal').value = firstDay.toISOString().split('T')[0];
            document.getElementById('sampaiTanggal').value = today.toISOString().split('T')[0];

            renderProductSales();
            renderCategorySales();
            renderTransactionSales();
            renderTopProducts();
            updateStatistics();
            initChart();
        }

        init();
    </script>
@endsection
