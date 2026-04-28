@extends('layouts.owner')

@section('title', 'Laporan Penjualan - PROShop')
@section('header-title', 'Laporan Penjualan Owner')
@section('header-subtitle', 'Lihat dan kelola laporan penjualan toko secara lengkap')

@section('content')
    <div class="space-y-6">
        {{-- Filter Laporan --}}
        <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-5">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i>Dari Tanggal
                    </label>
                    <input type="date" id="dariTanggal"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i>Sampai Tanggal
                    </label>
                    <input type="date" id="sampaiTanggal"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">
                        <i class="fas fa-user-circle text-indigo-500 mr-1"></i>Filter Kasir
                    </label>
                    <select id="filterKasir"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="all">📋 Semua Kasir</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <div class="flex gap-2">
                        <button onclick="loadData()"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                            <i class="fas fa-search text-xs"></i>
                            <span>Tampilkan</span>
                        </button>
                        <button onclick="resetFilter()"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                            <i class="fas fa-undo-alt text-xs"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Export --}}
        <div id="exportContainer" class="hidden">
            <div class="flex gap-3 justify-end">
                <button onclick="exportToPDF()"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-pdf text-lg"></i>
                    <span>Export PDF</span>
                </button>
                <button onclick="exportToExcel()"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-excel text-lg"></i>
                    <span>Export Excel</span>
                </button>
                <button onclick="printLaporan()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-print text-lg"></i>
                    <span>Cetak</span>
                </button>
            </div>
        </div>

        {{-- Loading --}}
        <div id="loading" class="text-center py-12">
            <div class="inline-flex flex-col items-center">
                <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                <p class="mt-3 text-sm text-slate-500">Memuat data laporan...</p>
            </div>
        </div>

        {{-- Statistik Ringkasan --}}
        <div id="statistikContainer" class="grid grid-cols-1 md:grid-cols-3 gap-5 hidden">
            <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Total Transaksi</p>
                        <p class="text-2xl font-bold text-indigo-600 mt-1" id="totalTransaksi">0</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-receipt text-indigo-600 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-green-600 mt-1" id="totalPendapatan">Rp 0</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-rupiah-sign text-green-600 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Total Item Terjual</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1" id="totalItemTerjual">0</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-boxes text-blue-600 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Penjualan Per Produk --}}
        <div id="produkContainer" class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-100 hidden">
            <div
                class="px-5 py-3 border-b border-slate-100 bg-slate-50/50 flex flex-wrap items-center justify-between gap-3">
                <h3 class="font-semibold text-slate-800 text-sm">
                    <i class="fas fa-boxes text-indigo-500 mr-2"></i>Ringkasan Penjualan Per Produk
                </h3>
                <div class="flex gap-2">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="searchProduk" placeholder="Cari produk..."
                            class="pl-8 pr-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-48">
                    </div>
                    <button onclick="filterProduk()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                        <i class="fas fa-search mr-1"></i> Cari
                    </button>
                    <button onclick="refreshProduk()"
                        class="bg-gray-100 hover:bg-gray-200 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="productTable">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">No</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-slate-500 uppercase">Qty Terjual
                            </th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-slate-500 uppercase">Total Pendapatan
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase w-32">% Kontribusi
                            </th>
                        </tr>
                    </thead>
                    <tbody id="productSalesBody" class="divide-y divide-slate-100"></tbody>
                    <tfoot class="bg-slate-50">
                        <tr class="font-semibold">
                            <td colspan="4" class="px-4 py-2 text-right text-xs">Total Keseluruhan:</td>
                            <td id="totalProductRevenue" class="px-4 py-2 text-right text-sm font-bold text-green-600">Rp
                                0</td>
                            <td class="px-4 py-2 text-xs">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Ringkasan Penjualan Per Kategori --}}
        <div id="kategoriContainer" class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-100 hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800 text-sm">
                    <i class="fas fa-tags text-indigo-500 mr-2"></i>Ringkasan Penjualan Per Kategori
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="categoryTable">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">No</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-slate-500 uppercase">Total Item
                            </th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-slate-500 uppercase">Total
                                Pendapatan</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase w-32">%
                                Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody id="categorySalesBody" class="divide-y divide-slate-100"></tbody>
                    <tfoot class="bg-slate-50">
                        <tr class="font-semibold">
                            <td colspan="3" class="px-4 py-2 text-right text-xs">Total Keseluruhan:</td>
                            <td id="totalCategoryRevenue" class="px-4 py-2 text-right text-sm font-bold text-green-600">Rp
                                0</td>
                            <td class="px-4 py-2 text-xs">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Ringkasan Penjualan Per Transaksi --}}
        <div id="transaksiContainer" class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-100 hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800 text-sm">
                    <i class="fas fa-receipt text-indigo-500 mr-2"></i>Ringkasan Penjualan Per Transaksi
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="transactionTable">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">No</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">No. Transaksi
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Kasir</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-slate-500 uppercase">Item</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-slate-500 uppercase">Total Belanja
                            </th>
                        </tr>
                    </thead>
                    <tbody id="transactionSalesBody" class="divide-y divide-slate-100"></tbody>
                    <tfoot class="bg-slate-50">
                        <tr class="font-semibold">
                            <td colspan="5" class="px-4 py-2 text-right text-xs">Total Keseluruhan:</td>
                            <td id="totalTransactionRevenue"
                                class="px-4 py-2 text-right text-sm font-bold text-green-600">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- PDF Export Template (Hidden) -->
    <div id="pdfTemplate" style="display: none;">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">PROShop</h2>
                <p style="margin: 5px 0;">Laporan Penjualan</p>
                <p style="margin: 5px 0; font-size: 12px; color: #666;" id="pdfDateRange"></p>
            </div>
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; background: #f0fdf4; border: 1px solid #ddd;"><strong>Total
                                Transaksi</strong></td>
                        <td style="padding: 8px; background: #f0fdf4; border: 1px solid #ddd;" id="pdfTotalTransaksi">0
                        </td>
                        <td style="padding: 8px; background: #f0fdf4; border: 1px solid #ddd;"><strong>Total
                                Pendapatan</strong></td>
                        <td style="padding: 8px; background: #f0fdf4; border: 1px solid #ddd;" id="pdfTotalPendapatan">Rp
                            0</td>
                        <td style="padding: 8px; background: #f0fdf4; border: 1px solid #ddd;"><strong>Total Item</strong>
                        </td>
                        <td style="padding: 8px; background: #f0fdf4; border: 1px solid #ddd;" id="pdfTotalItem">0</td>
                    </tr>
                </table>
            </div>
            <div style="margin-bottom: 20px;">
                <h3 style="margin: 0 0 10px 0; font-size: 14px;">📦 Penjualan Per Produk</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">No</th>
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">Produk</th>
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">Kategori</th>
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: center;">Qty</th>
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: right;">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody id="pdfProductBody"></tbody>
                </table>
            </div>
            <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #999;">
                Dicetak pada: <span id="pdfPrintDate"></span>
            </div>
        </div>
    </div>

    <style>
        /* Stabilkan posisi tombol export */
        #exportContainer {
            display: flex !important;
            justify-content: flex-end;
            margin-bottom: 1rem;
        }

        #exportContainer .flex {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }

        /* Pastikan tombol tidak bergeser saat proses export */
        .exporting #exportContainer {
            opacity: 0.5;
            pointer-events: none;
        }

        /* Stabilkan posisi tombol export - JANGAN PERNAH DIHIDE */
        #exportContainer {
            display: block !important;
        }

        #exportContainer .flex {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        /* Tombol export tidak akan bergeser */
        #exportContainer button {
            transition: all 0.2s ease;
            min-width: 120px;
        }

        /* Saat proses export, tombol tetap di tempat */
        body.exporting #exportContainer {
            opacity: 0.6;
        }

        body.exporting #exportContainer button {
            pointer-events: none;
        }

        .progress-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5, #818cf8);
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        let productsData = [];
        let categoriesData = [];
        let transactionsData = [];

        function formatRp(angka) {
            return 'Rp ' + (angka || 0).toLocaleString('id-ID');
        }

        async function loadData() {
            const loading = document.getElementById('loading');
            const containers = ['statistikContainer', 'produkContainer', 'kategoriContainer', 'transaksiContainer',
                'exportContainer'
            ];
            containers.forEach(c => {
                const el = document.getElementById(c);
                if (el) el.classList.add('hidden');
            });
            if (loading) loading.classList.remove('hidden');

            const dariTanggal = document.getElementById('dariTanggal')?.value || '';
            const sampaiTanggal = document.getElementById('sampaiTanggal')?.value || '';
            const kasirId = document.getElementById('filterKasir')?.value || 'all';

            let url = `/owner/laporan-penjualan/data?`;
            if (dariTanggal) url += `dari_tanggal=${dariTanggal}&`;
            if (sampaiTanggal) url += `sampai_tanggal=${sampaiTanggal}&`;
            if (kasirId && kasirId !== 'all') url += `kasir_id=${kasirId}`;

            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    productsData = result.data.products || [];
                    categoriesData = result.data.categories || [];
                    transactionsData = result.data.transactions || [];
                    const summary = result.data.summary || {};

                    document.getElementById('totalTransaksi').innerText = summary.total_transaksi || 0;
                    document.getElementById('totalPendapatan').innerHTML = formatRp(summary.total_pendapatan);
                    document.getElementById('totalItemTerjual').innerText = summary.total_item || 0;

                    renderProductSales();
                    renderCategorySales();
                    renderTransactionSales();

                    containers.forEach(c => {
                        const el = document.getElementById(c);
                        if (el) el.classList.remove('hidden');
                    });
                } else {
                    showNotification(result.message || 'Gagal memuat data', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat memuat data', 'error');
            } finally {
                if (loading) loading.classList.add('hidden');
            }
        }

        function renderProductSales() {
            const tbody = document.getElementById('productSalesBody');
            const searchTerm = document.getElementById('searchProduk')?.value.toLowerCase() || '';
            const filtered = productsData.filter(p => p.name.toLowerCase().includes(searchTerm));
            const totalRevenue = filtered.reduce((s, p) => s + p.revenue, 0);
            document.getElementById('totalProductRevenue').innerHTML = formatRp(totalRevenue);

            if (!tbody) return;

            if (filtered.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Tidak ada data produk</td></tr>`;
                return;
            }

            tbody.innerHTML = filtered.map((p, idx) => {
                const percentage = totalRevenue > 0 ? ((p.revenue / totalRevenue) * 100).toFixed(1) : 0;
                return `<tr class="hover:bg-slate-50">
                    <td class="px-4 py-2">${idx + 1}</td>
                    <td class="px-4 py-2 font-medium">${p.name}</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded-full text-xs bg-gray-100">${p.category}</span></td>
                    <td class="px-4 py-2 text-center">${p.qty}</td>
                    <td class="px-4 py-2 text-right font-semibold text-green-600">${formatRp(p.revenue)}</td>
                    <td class="px-4 py-2"><div class="flex items-center gap-2"><div class="progress-bar flex-1"><div class="progress-fill" style="width: ${percentage}%"></div></div><span class="text-xs text-slate-500">${percentage}%</span></div></td>
                </tr>`;
            }).join('');
        }

        function renderCategorySales() {
            const tbody = document.getElementById('categorySalesBody');
            const totalRevenue = categoriesData.reduce((s, c) => s + c.revenue, 0);
            document.getElementById('totalCategoryRevenue').innerHTML = formatRp(totalRevenue);

            if (!tbody) return;

            if (categoriesData.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Tidak ada data kategori</td></tr>`;
                return;
            }

            tbody.innerHTML = categoriesData.map((c, idx) => {
                const percentage = totalRevenue > 0 ? ((c.revenue / totalRevenue) * 100).toFixed(1) : 0;
                return `<tr class="hover:bg-slate-50">
                    <td class="px-4 py-2">${idx + 1}</td>
                    <td class="px-4 py-2 font-medium">${c.name}</td>
                    <td class="px-4 py-2 text-center">${c.qty}</td>
                    <td class="px-4 py-2 text-right font-semibold text-green-600">${formatRp(c.revenue)}</td>
                    <td class="px-4 py-2"><div class="flex items-center gap-2"><div class="progress-bar flex-1"><div class="progress-fill" style="width: ${percentage}%"></div></div><span class="text-xs text-slate-500">${percentage}%</span></div></td>
                </tr>`;
            }).join('');
        }

        function renderTransactionSales() {
            const tbody = document.getElementById('transactionSalesBody');
            const totalRevenue = transactionsData.reduce((s, t) => s + t.totalAmount, 0);
            document.getElementById('totalTransactionRevenue').innerHTML = formatRp(totalRevenue);

            if (!tbody) return;

            if (transactionsData.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Tidak ada data transaksi</td></tr>`;
                return;
            }

            tbody.innerHTML = transactionsData.map((t, idx) => {
                return `<tr class="hover:bg-slate-50">
                    <td class="px-4 py-2">${idx + 1}</td>
                    <td class="px-4 py-2 font-mono text-indigo-600">${t.id}</td>
                    <td class="px-4 py-2">${t.date}</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-700">${t.cashier}</span></td>
                    <td class="px-4 py-2 text-center">${t.totalItem}</td>
                    <td class="px-4 py-2 text-right font-semibold text-green-600">${formatRp(t.totalAmount)}</td>
                </tr>`;
            }).join('');
        }

        // ==================== EXPORT PDF LANGSUNG DOWNLOAD ====================
        // ==================== EXPORT PDF LANGSUNG DOWNLOAD ====================
        async function exportToPDF() {
            // Tampilkan loading dengan backdrop agar tidak mengganggu layout
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
            loadingDiv.style.backdropFilter = 'blur(2px)';
            loadingDiv.innerHTML = `
        <div class="bg-white rounded-xl p-6 text-center min-w-[250px] shadow-xl">
            <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-3"></div>
            <p class="text-slate-600 text-sm font-medium">Membuat PDF...</p>
            <p class="text-slate-400 text-xs mt-1">Mohon tunggu</p>
        </div>
    `;
            document.body.appendChild(loadingDiv);

            // Tambah class ke body untuk indikator (tanpa merubah layout)
            document.body.classList.add('exporting');

            try {
                // JANGAN sembunyikan export container, cukup disabled visual
                const exportContainer = document.getElementById('exportContainer');
                const exportButtons = document.querySelectorAll('#exportContainer button');

                // Disable tombol export sementara (tanpa menyembunyikan)
                exportButtons.forEach(btn => {
                    btn.style.opacity = '0.5';
                    btn.style.pointerEvents = 'none';
                    btn.disabled = true;
                });

                // Siapkan data
                const dariTanggal = document.getElementById('dariTanggal').value || 'Semua';
                const sampaiTanggal = document.getElementById('sampaiTanggal').value || 'Semua';
                const kasirSelect = document.getElementById('filterKasir');
                const kasirNama = kasirSelect?.options[kasirSelect.selectedIndex]?.text || 'Semua Kasir';
                const totalTransaksi = document.getElementById('totalTransaksi').innerText || '0';
                const totalPendapatan = document.getElementById('totalPendapatan').innerHTML || 'Rp 0';
                const totalItem = document.getElementById('totalItemTerjual').innerText || '0';
                const now = new Date();

                // Buat container untuk PDF (di luar layar)
                const pdfContainer = document.createElement('div');
                pdfContainer.style.padding = '20px';
                pdfContainer.style.fontFamily = 'Arial, sans-serif';
                pdfContainer.style.backgroundColor = 'white';
                pdfContainer.style.width = '800px';
                pdfContainer.style.position = 'fixed';
                pdfContainer.style.left = '-9999px';
                pdfContainer.style.top = '0';
                pdfContainer.style.zIndex = '-1';

                // Buat HTML untuk PDF (sama seperti sebelumnya)
                pdfContainer.innerHTML = `
            <div style="padding: 20px;">
                <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 15px;">
                    <h2 style="margin: 0; color: #1e293b;">PROShop</h2>
                    <p style="margin: 5px 0; color: #64748b;">Laporan Penjualan</p>
                    <p style="margin: 5px 0; font-size: 11px; color: #94a3b8;">Periode: ${dariTanggal} s/d ${sampaiTanggal} | Kasir: ${kasirNama}</p>
                </div>
                <div style="margin-bottom: 20px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 10px; background: #f0fdf4; border: 1px solid #dcfce7; text-align: center;">
                                <strong style="color: #166534;">Total Transaksi</strong><br>
                                <span style="font-size: 18px; font-weight: bold; color: #166534;">${totalTransaksi}</span>
                            </td>
                            <td style="padding: 10px; background: #ecfdf5; border: 1px solid #d1fae5; text-align: center;">
                                <strong style="color: #065f46;">Total Pendapatan</strong><br>
                                <span style="font-size: 18px; font-weight: bold; color: #065f46;">${totalPendapatan}</span>
                            </td>
                            <td style="padding: 10px; background: #eff6ff; border: 1px solid #dbeafe; text-align: center;">
                                <strong style="color: #1e40af;">Total Item</strong><br>
                                <span style="font-size: 18px; font-weight: bold; color: #1e40af;">${totalItem}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="margin-bottom: 20px;">
                    <h3 style="margin: 0 0 10px 0; font-size: 13px; color: #334155;">📦 Penjualan Per Produk</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                        <thead>
                            <tr style="background-color: #f1f5f9;">
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">No</th>
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Produk</th>
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: left;">Kategori</th>
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">Qty</th>
                                <th style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${productsData.length > 0 ? productsData.map((p, idx) => `
                                    <tr>
                                        <td style="border: 1px solid #cbd5e1; padding: 4px;">${idx + 1}</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 4px;">${p.name}</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 4px;">${p.category}</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 4px; text-align: center;">${p.qty}</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 4px; text-align: right;">${formatRp(p.revenue)}</td>
                                    </tr>
                                `).join('') : `
                                    <tr><td colspan="5" style="border: 1px solid #cbd5e1; padding: 20px; text-align: center;">Tidak ada data produk</td><td style="display:none;"></td><td style="display:none;"></td><td style="display:none;"></td><td style="display:none;"></td></tr>
                                `}
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f8fafc;">
                                <td colspan="4" style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold;">Total:
                                <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold; color: #059669;">${formatRp(productsData.reduce((s, p) => s + p.revenue, 0))}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div style="margin-top: 20px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px;">
                    Dicetak pada: ${now.toLocaleString('id-ID')}
                </div>
            </div>
        `;

                document.body.appendChild(pdfContainer);

                // Gunakan html2canvas
                const canvas = await html2canvas(pdfContainer, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    logging: false,
                    useCORS: true
                });

                const imgData = canvas.toDataURL('image/png');
                const {
                    jsPDF
                } = window.jspdf;
                const imgWidth = 210;
                const pageHeight = 297;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                const pdf = new jsPDF('p', 'mm', 'a4');
                pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);

                let heightLeft = imgHeight - pageHeight;
                let position = -pageHeight;
                while (heightLeft > 0) {
                    position = position - pageHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                const fileName =
                    `laporan_penjualan_${now.getFullYear()}-${now.getMonth()+1}-${now.getDate()}_${now.getHours()}-${now.getMinutes()}.pdf`;
                pdf.save(fileName);

                showNotification('PDF berhasil diunduh!', 'success');

            } catch (error) {
                console.error('PDF Error:', error);
                showNotification('Gagal membuat PDF: ' + error.message, 'error');
            } finally {
                // Cleanup
                const pdfContainerElem = document.body.querySelector(
                    'div[style*="position: fixed"][style*="left: -9999px"]');
                if (pdfContainerElem) pdfContainerElem.remove();
                loadingDiv.remove();

                // Kembalikan tombol ke keadaan semula
                const exportButtons = document.querySelectorAll('#exportContainer button');
                exportButtons.forEach(btn => {
                    btn.style.opacity = '';
                    btn.style.pointerEvents = '';
                    btn.disabled = false;
                });

                document.body.classList.remove('exporting');
            }
        }


        // ==================== EXPORT EXCEL ====================
        function exportToExcel() {
            let csvContent = "No,Produk,Kategori,Qty Terjual,Total Pendapatan\n";
            productsData.forEach((p, idx) => {
                csvContent += `${idx + 1},${p.name},${p.category},${p.qty},${p.revenue}\n`;
            });

            csvContent += "\n\nNo,Kategori,Total Item,Total Pendapatan\n";
            categoriesData.forEach((c, idx) => {
                csvContent += `${idx + 1},${c.name},${c.qty},${c.revenue}\n`;
            });

            csvContent += "\n\nNo,No. Transaksi,Tanggal,Kasir,Total Item,Total Belanja\n";
            transactionsData.forEach((t, idx) => {
                csvContent += `${idx + 1},${t.id},${t.date},${t.cashier},${t.totalItem},${t.totalAmount}\n`;
            });

            const blob = new Blob(["\uFEFF" + csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', `laporan_penjualan_${new Date().toISOString().slice(0,19)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            showNotification('File Excel berhasil diunduh!', 'success');
        }

        // ==================== PRINT ====================
        function printLaporan() {
            window.print();
        }

        // ==================== UTILITY FUNCTIONS ====================
        async function loadKasirList() {
            try {
                const response = await fetch('/owner/laporan-penjualan/kasir-list');
                const result = await response.json();
                if (result.success) {
                    const select = document.getElementById('filterKasir');
                    select.innerHTML = '<option value="all">📋 Semua Kasir</option>';
                    result.data.forEach(kasir => {
                        select.innerHTML += `<option value="${kasir.id}">${kasir.name}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading kasir list:', error);
            }
        }

        function filterProduk() {
            renderProductSales();
        }

        function refreshProduk() {
            document.getElementById('searchProduk').value = '';
            renderProductSales();
        }

        function resetFilter() {
            document.getElementById('dariTanggal').value = '';
            document.getElementById('sampaiTanggal').value = '';
            document.getElementById('filterKasir').value = 'all';
            document.getElementById('searchProduk').value = '';
            loadData();
        }

        function showNotification(message, type = 'success') {
            // Buat toast notification sederhana
            const toast = document.createElement('div');
            toast.className = `fixed bottom-5 right-5 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            toast.innerHTML =
                `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function setDefaultDate() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            document.getElementById('dariTanggal').value = firstDay.toISOString().split('T')[0];
            document.getElementById('sampaiTanggal').value = today.toISOString().split('T')[0];
        }

        document.addEventListener('DOMContentLoaded', () => {
            setDefaultDate();
            loadKasirList();
            loadData();
        });
    </script>
@endsection
