@extends('layouts.gudang')

@section('title', 'PROShop - Laporan Stok')
@section('page-title', 'Laporan Stok')
@section('page-subtitle', 'Laporan pergerakan stok produk')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-slate-800">
                <i class="fas fa-file-alt text-indigo-600 mr-2"></i>Laporan Pergerakan Stok
            </h3>
            <div class="flex gap-2">
                <button onclick="exportToExcel()"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
                <button onclick="printReport()"
                    class="bg-slate-600 text-white px-4 py-2 rounded-lg hover:bg-slate-700 transition">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>

        <!-- Filter Periode -->
        <div class="mb-6 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" id="dateFrom" onchange="filterLaporan()"
                    class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" id="dateTo" onchange="filterLaporan()"
                    class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                <select id="categoryFilter" onchange="filterLaporan()"
                    class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    <option value="all">Semua Kategori</option>
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                    <option value="Makanan Ringan">Makanan Ringan</option>
                    <option value="Produk Kesehatan">Produk Kesehatan</option>
                    <option value="Produk Kebersihan">Produk Kebersihan</option>
                    <option value="Kebutuhan Harian">Kebutuhan Harian</option>
                </select>
            </div>
            <button onclick="resetFilter()"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-undo mr-1"></i> Reset
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="laporanTable">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">Nama Produk</th>
                        <th class="p-3 text-left">Kategori</th>
                        <th class="p-3 text-left">Stok Awal</th>
                        <th class="p-3 text-left">Penerimaan</th>
                        <th class="p-3 text-left">Pengeluaran</th>
                        <th class="p-3 text-left">Penyesuaian</th>
                        <th class="p-3 text-left">Stok Akhir</th>
                        <th class="p-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody id="laporanTableBody">
                    <!-- Data akan diisi oleh JavaScript -->
                </tbody>
                <tfoot class="bg-slate-100 font-semibold" id="laporanTableFooter">
                    <!-- Footer akan diisi oleh JavaScript -->
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6" id="statistikCards">
        <!-- Statistik akan diisi oleh JavaScript -->
    </div>

    <script>
        // Data laporan stok
        let stockReport = [{
                id: 1,
                product: "Rocky Rasa Coklat",
                category: "Makanan",
                awal: 10,
                masuk: 50,
                keluar: 45,
                penyesuaian: 0,
                akhir: 15,
                minStock: 40,
                unit: "pcs",
                price: 5000
            },
            {
                id: 2,
                product: "Indomie Goreng",
                category: "Makanan",
                awal: 120,
                masuk: 30,
                keluar: 136,
                penyesuaian: 0,
                akhir: 14,
                minStock: 50,
                unit: "pcs",
                price: 3800
            },
            {
                id: 3,
                product: "Teh Botol Sosro",
                category: "Minuman",
                awal: 45,
                masuk: 20,
                keluar: 20,
                penyesuaian: -5,
                akhir: 40,
                minStock: 30,
                unit: "botol",
                price: 5000
            },
            {
                id: 4,
                product: "Pocky Coklat",
                category: "Makanan Ringan",
                awal: 8,
                masuk: 15,
                keluar: 18,
                penyesuaian: 0,
                akhir: 5,
                minStock: 15,
                unit: "box",
                price: 7900
            },
            {
                id: 5,
                product: "Lays Original",
                category: "Makanan Ringan",
                awal: 25,
                masuk: 10,
                keluar: 5,
                penyesuaian: 0,
                akhir: 30,
                minStock: 20,
                unit: "pcs",
                price: 12000
            },
            {
                id: 6,
                product: "Coca Cola",
                category: "Minuman",
                awal: 60,
                masuk: 0,
                keluar: 15,
                penyesuaian: 0,
                akhir: 45,
                minStock: 40,
                unit: "botol",
                price: 6000
            },
            {
                id: 7,
                product: "Paracetamol",
                category: "Produk Kesehatan",
                awal: 50,
                masuk: 20,
                keluar: 25,
                penyesuaian: 0,
                akhir: 45,
                minStock: 30,
                unit: "strip",
                price: 5000
            },
            {
                id: 8,
                product: "Sabun Lifebuoy",
                category: "Produk Kebersihan",
                awal: 45,
                masuk: 30,
                keluar: 20,
                penyesuaian: 0,
                akhir: 55,
                minStock: 25,
                unit: "pcs",
                price: 3500
            },
            {
                id: 9,
                product: "Rinso Bubuk",
                category: "Kebutuhan Harian",
                awal: 100,
                masuk: 0,
                keluar: 30,
                penyesuaian: 0,
                akhir: 70,
                minStock: 40,
                unit: "pcs",
                price: 20700
            }
        ];

        // Data transaksi untuk perhitungan
        let incomingTransactions = [{
                product: "Rocky Rasa Coklat",
                qty: 50,
                date: "2026-04-25"
            },
            {
                product: "Indomie Goreng",
                qty: 30,
                date: "2026-04-24"
            },
            {
                product: "Teh Botol Sosro",
                qty: 20,
                date: "2026-04-23"
            },
            {
                product: "Pocky Coklat",
                qty: 15,
                date: "2026-04-22"
            },
            {
                product: "Paracetamol",
                qty: 20,
                date: "2026-04-21"
            }
        ];

        let outgoingTransactions = [{
                product: "Indomie Goreng",
                qty: 46,
                date: "2026-04-25"
            },
            {
                product: "Rocky Rasa Coklat",
                qty: 5,
                date: "2026-04-24"
            },
            {
                product: "Teh Botol Sosro",
                qty: 10,
                date: "2026-04-23"
            },
            {
                product: "Pocky Coklat",
                qty: 8,
                date: "2026-04-22"
            },
            {
                product: "Coca Cola",
                qty: 15,
                date: "2026-04-21"
            },
            {
                product: "Indomie Goreng",
                qty: 90,
                date: "2026-04-20"
            },
            {
                product: "Rocky Rasa Coklat",
                qty: 40,
                date: "2026-04-19"
            }
        ];

        // Render laporan
        function renderLaporan() {
            const tbody = document.getElementById('laporanTableBody');
            const tfoot = document.getElementById('laporanTableFooter');
            if (!tbody) return;

            // Filter berdasarkan kategori
            const categoryFilter = document.getElementById('categoryFilter')?.value || 'all';
            let filteredData = [...stockReport];

            if (categoryFilter !== 'all') {
                filteredData = filteredData.filter(item => item.category === categoryFilter);
            }

            // Filter berdasarkan tanggal
            const dateFrom = document.getElementById('dateFrom')?.value;
            const dateTo = document.getElementById('dateTo')?.value;

            if (dateFrom || dateTo) {
                filteredData = filteredData.map(item => {
                    // Hitung ulang berdasarkan tanggal
                    let masuk = 0,
                        keluar = 0;

                    incomingTransactions.forEach(trx => {
                        if (trx.product === item.product) {
                            if ((!dateFrom || trx.date >= dateFrom) && (!dateTo || trx.date <= dateTo)) {
                                masuk += trx.qty;
                            }
                        }
                    });

                    outgoingTransactions.forEach(trx => {
                        if (trx.product === item.product) {
                            if ((!dateFrom || trx.date >= dateFrom) && (!dateTo || trx.date <= dateTo)) {
                                keluar += trx.qty;
                            }
                        }
                    });

                    const akhir = item.awal + masuk - keluar + item.penyesuaian;
                    return {
                        ...item,
                        masuk,
                        keluar,
                        akhir
                    };
                });
            }

            // Hitung total
            let totalAwal = 0,
                totalMasuk = 0,
                totalKeluar = 0,
                totalPenyesuaian = 0,
                totalAkhir = 0,
                totalNilai = 0;

            tbody.innerHTML = filteredData.map((item, idx) => {
                totalAwal += item.awal;
                totalMasuk += item.masuk;
                totalKeluar += item.keluar;
                totalPenyesuaian += item.penyesuaian;
                totalAkhir += item.akhir;
                totalNilai += item.akhir * item.price;

                return `
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="p-3">${idx+1}】+
                    <td class="p-3 font-medium">${item.product}】+
                    <td class="p-3">${item.category}】+
                    <td class="p-3">${item.awal} ${item.unit}】+
                    <td class="p-3 text-green-600">+${item.masuk} ${item.unit}】+
                    <td class="p-3 text-red-600">-${item.keluar} ${item.unit}】+
                    <td class="p-3 ${item.penyesuaian > 0 ? 'text-green-600' : (item.penyesuaian < 0 ? 'text-red-600' : '')}">
                        ${item.penyesuaian > 0 ? '+' : ''}${item.penyesuaian} ${item.unit}
                    】+
                    <td class="p-3 font-bold">${item.akhir} ${item.unit}】+
                    <td class="p-3">
                        ${item.akhir < item.minStock ? 
                            '<span class="status-badge status-pending">⚠️ Stok Menipis</span>' : 
                            '<span class="status-badge status-approved">✓ Normal</span>'}
                    】+
                </tr>
            `;
            }).join('');

            // Render footer
            tfoot.innerHTML = `
            <tr>
                <td colspan="3" class="p-3 text-right font-bold">Total:</td>
                <td class="p-3 font-bold">${totalAwal} pcs</td>
                <td class="p-3 text-green-600 font-bold">+${totalMasuk} pcs</td>
                <td class="p-3 text-red-600 font-bold">-${totalKeluar} pcs</td>
                <td class="p-3 font-bold">${totalPenyesuaian > 0 ? '+' : ''}${totalPenyesuaian} pcs</td>
                <td class="p-3 font-bold">${totalAkhir} pcs</td>
                <td class="p-3"></td>
            </tr>
        `;

            // Render statistik cards
            renderStatistik(totalMasuk, totalKeluar, totalAkhir, totalNilai, filteredData);
        }

        // Render statistik cards
        function renderStatistik(totalMasuk, totalKeluar, totalAkhir, totalNilai, filteredData) {
            const stokMenipis = filteredData.filter(item => item.akhir < item.minStock).length;
            const statContainer = document.getElementById('statistikCards');

            if (statContainer) {
                statContainer.innerHTML = `
                <div class="bg-white rounded-xl shadow-sm p-6 card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-arrow-down text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Penerimaan</p>
                            <p class="text-2xl font-bold text-slate-800">${totalMasuk}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-arrow-up text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Pengeluaran</p>
                            <p class="text-2xl font-bold text-slate-800">${totalKeluar}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-boxes text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Stok Akhir</p>
                            <p class="text-2xl font-bold text-slate-800">${totalAkhir}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Stok Menipis</p>
                            <p class="text-2xl font-bold text-slate-800">${stokMenipis}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 card md:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Nilai Inventaris</p>
                            <p class="text-2xl font-bold text-slate-800">Rp ${formatPrice(totalNilai)}</p>
                        </div>
                    </div>
                </div>
            `;
            }
        }

        // Format price
        function formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(price);
        }

        // Filter laporan
        function filterLaporan() {
            renderLaporan();
        }

        // Reset filter
        function resetFilter() {
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            document.getElementById('categoryFilter').value = 'all';
            renderLaporan();
        }

        // Export to Excel
        function exportToExcel() {
            const table = document.getElementById('laporanTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];

            // Header
            let headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.innerText);
            });
            csv.push(headers.join(','));

            // Data rows
            rows.forEach(row => {
                if (row.parentElement.tagName !== 'THEAD') {
                    let rowData = [];
                    row.querySelectorAll('td').forEach(td => {
                        rowData.push('"' + td.innerText.replace(/"/g, '""') + '"');
                    });
                    if (rowData.length > 0) csv.push(rowData.join(','));
                }
            });

            // Download
            const blob = new Blob([csv.join('\n')], {
                type: 'text/csv'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `laporan_stok_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            URL.revokeObjectURL(url);

            alert('✓ Laporan berhasil diekspor!');
        }

        // Print report
        function printReport() {
            const printContent = document.querySelector('.bg-white.rounded-xl.shadow-sm.p-6').cloneNode(true);
            const originalTitle = document.title;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
            <html>
                <head>
                    <title>${originalTitle}</title>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f3f4f6; }
                        .status-badge { padding: 2px 8px; border-radius: 12px; font-size: 12px; }
                        .status-pending { background: #fef3c7; color: #d97706; }
                        .status-approved { background: #d1fae5; color: #059669; }
                        @media print {
                            button { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <h2>Laporan Pergerakan Stok</h2>
                    <p>Tanggal Cetak: ${new Date().toLocaleString('id-ID')}</p>
                    ${printContent.innerHTML}
                </body>
            </html>
        `);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        }

        // Inisialisasi halaman
        document.addEventListener('DOMContentLoaded', function() {
            renderLaporan();
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

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        @media print {

            .btn-primary,
            .bg-green-600,
            .bg-slate-600,
            .bg-gray-500 {
                display: none;
            }

            .no-print {
                display: none;
            }
        }
    </style>
@endsection
