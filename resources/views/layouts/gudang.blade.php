<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PROShop - Gudang')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: #f1f5f9;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 10px;
        }

        .sidebar-item:hover {
            background: linear-gradient(90deg, #eef2ff 0%, #ffffff 100%);
            transform: translateX(4px);
        }

        .sidebar-item-active {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 500;
        }

        .submenu-item:hover {
            background: #f1f5f9;
            transform: translateX(4px);
        }

        .submenu-active {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 500;
        }

        .rotate-icon {
            transition: transform 0.3s ease;
        }

        .rotate-icon.rotate {
            transform: rotate(90deg);
        }

        .submenu-collapse {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .submenu-collapse.open {
            max-height: 500px;
            transition: max-height 0.3s ease-in;
        }

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

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
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

        .btn-secondary {
            background-color: #64748b;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background-color: #475569;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>

    @stack('styles')
</head>

<body class="bg-slate-100">

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="w-72 bg-white shadow-xl flex flex-col h-full overflow-y-auto custom-scroll">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">PROShop</h1>
                        <p class="text-xs text-slate-400">Gudang</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <a href="#" onclick="showPage('beranda')"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-slate-600 hover:bg-slate-50"
                    id="nav-beranda">
                    <i class="fas fa-home w-5 h-5"></i> Beranda
                </a>

                <!-- Produk -->
                <a href="{{ route('gudang.produk') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('kasir.produk') ? 'sidebar-item-active' : 'text-slate-600' }}">
                    <i class="fas fa-box w-5 h-5"></i>
                    <span>Produk</span>
                </a>

                <!-- Stok Dropdown -->
                <div>
                    <button onclick="toggleSubmenu('stokSubmenu', 'stokIcon')"
                        class="sidebar-item flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200 text-slate-600">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-warehouse w-5 h-5"></i>
                            <span>Stok</span>
                        </div>
                        <i id="stokIcon" class="fas fa-chevron-right text-xs rotate-icon"></i>
                    </button>
                    <div id="stokSubmenu" class="submenu-collapse ml-6 mt-1 space-y-1">
                        <a href="#" onclick="showPage('penerimaan')"
                            class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 text-slate-600 hover:bg-slate-50">
                            <i class="fas fa-arrow-down w-4 h-4"></i> Stok Masuk
                        </a>
                        <a href="#" onclick="showPage('pengeluaran')"
                            class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 text-slate-600 hover:bg-slate-50">
                            <i class="fas fa-arrow-up w-4 h-4"></i> Stok Keluar
                        </a>
                        <a href="#" onclick="showPage('penyesuaian')"
                            class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 text-slate-600 hover:bg-slate-50">
                            <i class="fas fa-sliders-h w-4 h-4"></i> Penyesuaian Stok
                        </a>
                        <a href="#" onclick="showPage('laporan_stok')"
                            class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 text-slate-600 hover:bg-slate-50">
                            <i class="fas fa-file-alt w-4 h-4"></i> Laporan
                        </a>
                    </div>
                </div>

                <a href="#" onclick="showPage('riwayat')"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-slate-600 hover:bg-slate-50">
                    <i class="fas fa-history w-5 h-5"></i> Riwayat Transaksi
                </a>
            </nav>

            <div class="p-4 border-t border-slate-200">
                <button onclick="showLogoutConfirm()"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-all duration-200 w-full">
                    <i class="fas fa-sign-out-alt w-5 h-5"></i>
                    <span>Keluar</span>
                </button>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-auto custom-scroll">
            <div class="bg-white shadow-sm px-8 py-5 border-b border-slate-200 sticky top-0 z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-800" id="page-title">Beranda Gudang</h2>
                        <p class="text-sm text-slate-500 mt-0.5" id="page-subtitle">Kelola stok dan aktivitas gudang</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 pl-3 border-l border-slate-200">
                            <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="hidden md:block">
                                <p class="font-semibold text-slate-800 text-sm">{{ Auth::user()->name ?? 'Gudang' }}</p>
                                <p class="text-xs text-slate-400">Online</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6" id="page-content"></div>
        </main>
    </div>

    <script>
        // ==================== DATA DUMMY ====================
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
                name: "Rocky Rasa Coklat",
                category: "Makanan",
                stock: 10,
                minStock: 40,
                unit: "pcs"
            },
            {
                id: 3,
                name: "Rocky Rasa Coklat",
                category: "Makanan",
                stock: 10,
                minStock: 40,
                unit: "pcs"
            },
            {
                id: 4,
                name: "Rocky Rasa Coklat",
                category: "Makanan",
                stock: 10,
                minStock: 40,
                unit: "pcs"
            },
            {
                id: 5,
                name: "Rocky Rasa Coklat",
                category: "Makanan",
                stock: 10,
                minStock: 40,
                unit: "pcs"
            },
            {
                id: 6,
                name: "Rocky Rasa Coklat",
                category: "Makanan",
                stock: 10,
                minStock: 40,
                unit: "pcs"
            },
            {
                id: 7,
                name: "Rocky Rasa Coklat",
                category: "Makanan",
                stock: 10,
                minStock: 40,
                unit: "pcs"
            },
            {
                id: 8,
                name: "Indomie Goreng",
                category: "Makanan",
                stock: 120,
                minStock: 50,
                unit: "pcs"
            },
            {
                id: 9,
                name: "Teh Botol Sosro",
                category: "Minuman",
                stock: 45,
                minStock: 30,
                unit: "botol"
            },
            {
                id: 10,
                name: "Pocky Coklat",
                category: "Makanan",
                stock: 8,
                minStock: 15,
                unit: "box"
            }
        ];

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
                product: "Rocky Rasa Coklat",
                qty: 50,
                time: "15 menit yang lalu",
                status: "pending",
                code: "TRX002",
                date: "25/04/2026 10:28"
            },
            {
                id: 3,
                product: "Rocky Rasa Coklat",
                qty: 50,
                time: "15 menit yang lalu",
                status: "approved",
                code: "TRX003",
                date: "25/04/2026 10:25"
            },
            {
                id: 4,
                product: "Teh Botol Sosro",
                qty: 30,
                time: "2 jam yang lalu",
                status: "pending",
                code: "TRX004",
                date: "25/04/2026 08:30"
            },
            {
                id: 5,
                product: "Pocky Coklat",
                qty: 20,
                time: "1 hari yang lalu",
                status: "approved",
                code: "TRX005",
                date: "24/04/2026 14:00"
            }
        ];

        let outgoingTransactions = [{
                id: 1,
                product: "Indomie goreng",
                qty: 46,
                time: "8 menit yang lalu",
                status: "approved",
                destination: "Toko A",
                code: "TRX101",
                date: "25/04/2026 10:32"
            },
            {
                id: 2,
                product: "Indomie goreng",
                qty: 46,
                time: "9 menit yang lalu",
                status: "pending",
                destination: "Toko B",
                code: "TRX102",
                date: "25/04/2026 10:31"
            },
            {
                id: 3,
                product: "Indomie goreng",
                qty: 48,
                time: "8 menit yang lalu",
                status: "approved",
                destination: "Toko C",
                code: "TRX103",
                date: "25/04/2026 10:30"
            },
            {
                id: 4,
                product: "Rocky Coklat",
                qty: 25,
                time: "3 jam yang lalu",
                status: "approved",
                destination: "Toko A",
                code: "TRX104",
                date: "25/04/2026 07:15"
            },
            {
                id: 5,
                product: "Teh Botol",
                qty: 10,
                time: "5 jam yang lalu",
                status: "pending",
                destination: "Toko B",
                code: "TRX105",
                date: "25/04/2026 05:00"
            }
        ];

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
            }
        ];

        let stockReport = [{
                product: "Rocky Rasa Coklat",
                category: "Makanan",
                awal: 10,
                masuk: 20,
                keluar: 15,
                akhir: 15,
                minStock: 40
            },
            {
                product: "Indomie Goreng",
                category: "Makanan",
                awal: 50,
                masuk: 10,
                keluar: 46,
                akhir: 14,
                minStock: 50
            },
            {
                product: "Teh Botol Sosro",
                category: "Minuman",
                awal: 25,
                masuk: 30,
                keluar: 10,
                akhir: 45,
                minStock: 30
            },
            {
                product: "Pocky Coklat",
                category: "Makanan",
                awal: 10,
                masuk: 5,
                keluar: 7,
                akhir: 8,
                minStock: 15
            }
        ];

        let historyTransactions = [{
                id: "TRX001",
                type: "Penerimaan",
                product: "Rocky Coklat",
                qty: "+50",
                date: "25/04/2026 10:30",
                user: "Admin Gudang",
                status: "approved"
            },
            {
                id: "TRX101",
                type: "Pengeluaran",
                product: "Indomie Goreng",
                qty: "-46",
                date: "25/04/2026 10:32",
                user: "Kasir",
                status: "approved"
            },
            {
                id: "TRX002",
                type: "Penerimaan",
                product: "Rocky Coklat",
                qty: "+50",
                date: "25/04/2026 10:28",
                user: "Admin Gudang",
                status: "pending"
            },
            {
                id: "TRX003",
                type: "Penerimaan",
                product: "Rocky Coklat",
                qty: "+50",
                date: "25/04/2026 10:25",
                user: "Admin Gudang",
                status: "approved"
            },
            {
                id: "ADJ001",
                type: "Penyesuaian",
                product: "Pocky Coklat",
                qty: "-3",
                date: "24/04/2026 15:45",
                user: "Owner",
                status: "approved"
            },
            {
                id: "TRX102",
                type: "Pengeluaran",
                product: "Indomie Goreng",
                qty: "-46",
                date: "25/04/2026 10:31",
                user: "Kasir",
                status: "pending"
            },
            {
                id: "TRX103",
                type: "Pengeluaran",
                product: "Indomie Goreng",
                qty: "-48",
                date: "25/04/2026 10:30",
                user: "Kasir",
                status: "approved"
            }
        ];

        // ==================== RENDER FUNCTIONS ====================
        function showPage(page) {
            document.getElementById('page-title').innerText = getPageTitle(page);
            document.getElementById('page-subtitle').innerText = getPageSubtitle(page);
            document.getElementById('page-content').innerHTML = getPageContent(page);

            // Update active menu
            document.querySelectorAll('.sidebar-item, .submenu-item').forEach(el => {
                el.classList.remove('sidebar-item-active', 'submenu-active');
            });
        }

        function getPageTitle(page) {
            const titles = {
                beranda: "Beranda Gudang",
                penerimaan: "Penerimaan Stok",
                pengeluaran: "Pengeluaran Stok",
                penyesuaian: "Penyesuaian Stok",
                laporan_stok: "Laporan Stok",
                riwayat: "Riwayat Transaksi"
            };
            return titles[page] || "Dashboard";
        }

        function getPageSubtitle(page) {
            const subtitles = {
                beranda: "Ringkasan aktivitas dan stok gudang",
                penerimaan: "Kelola barang masuk ke gudang",
                pengeluaran: "Kelola barang keluar dari gudang",
                penyesuaian: "Lakukan penyesuaian stok (plus/minus) karena opname, rusak, atau kadaluarsa",
                laporan_stok: "Laporan pergerakan stok produk",
                riwayat: "Riwayat semua transaksi gudang"
            };
            return subtitles[page] || "Kelola stok produk";
        }

        function getPageContent(page) {
            switch (page) {
                case 'beranda':
                    return renderBeranda();
                case 'penerimaan':
                    return renderPenerimaan();
                case 'pengeluaran':
                    return renderPengeluaran();
                case 'penyesuaian':
                    return renderPenyesuaian();
                case 'laporan_stok':
                    return renderLaporanStok();
                case 'riwayat':
                    return renderRiwayat();
                default:
                    return renderBeranda();
            }
        }

        function renderBeranda() {
            return `
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Tabel Stok -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 card">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-slate-800">
                                <i class="fas fa-boxes text-indigo-600 mr-2"></i>Stok Produk
                            </h3>
                            <button onclick="showPage('laporan_stok')" class="text-indigo-600 text-sm hover:underline">
                                Lihat Semua Stok →
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="p-3 text-left">No</th>
                                        <th class="p-3 text-left">Nama Produk</th>
                                        <th class="p-3 text-left">Kategori</th>
                                        <th class="p-3 text-left">Stok</th>
                                        <th class="p-3 text-left">Stok Minimal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${stocks.slice(0,7).map((item, idx) => `
                                            <tr class="border-b border-slate-100">
                                                <td class="p-3">${idx+1}</td>
                                                <td class="p-3 font-medium">${item.name}</td>
                                                <td class="p-3">${item.category}</td>
                                                <td class="p-3 ${item.stock < item.minStock ? 'text-red-600 font-bold' : ''}">
                                                    ${item.stock} ${item.unit}
                                                </td>
                                                <td class="p-3">${item.minStock} ${item.unit}</td>
                                            </tr>
                                        `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Aktivitas Terbaru -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-6 card">
                            <h3 class="text-lg font-semibold mb-4">
                                <i class="fas fa-arrow-down text-green-600 mr-2"></i>Penerimaan Terbaru
                            </h3>
                            <div class="space-y-3">
                                ${incomingTransactions.slice(0,3).map(t => `
                                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                                            <div>
                                                <span class="font-medium">${t.product}</span>
                                                <span class="text-green-600 ml-2 font-semibold">+${t.qty} pcs</span>
                                            </div>
                                            <span class="text-xs text-slate-500">${t.time}</span>
                                        </div>
                                    `).join('')}
                            </div>
                            <button onclick="showPage('penerimaan')" class="mt-4 text-indigo-600 text-sm hover:underline">
                                Lihat Semua Penerimaan →
                            </button>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm p-6 card">
                            <h3 class="text-lg font-semibold mb-4">
                                <i class="fas fa-arrow-up text-red-600 mr-2"></i>Pengeluaran Terbaru
                            </h3>
                            <div class="space-y-3">
                                ${outgoingTransactions.slice(0,3).map(t => `
                                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                                            <div>
                                                <span class="font-medium">${t.product}</span>
                                                <span class="text-red-600 ml-2 font-semibold">-${t.qty} pcs</span>
                                            </div>
                                            <span class="text-xs text-slate-500">${t.time}</span>
                                        </div>
                                    `).join('')}
                            </div>
                            <button onclick="showPage('pengeluaran')" class="mt-4 text-indigo-600 text-sm hover:underline">
                                Lihat Semua Pengeluaran →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Peringatan Stok Menipis -->
                <div class="mt-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-500 text-xl"></i>
                        <div>
                            <h4 class="font-semibold text-amber-800">Peringatan Stok Menipis</h4>
                            <p class="text-sm text-amber-700 mt-1">
                                Terdapat beberapa produk dengan stok di bawah batas minimal. 
                                Segera lakukan penyesuaian atau pemesanan.
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderPenerimaan() {
            return `
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-slate-800">
                            <i class="fas fa-arrow-down text-green-600 mr-2"></i>Daftar Penerimaan Stok
                        </h3>
                        <button onclick="showTambahPenerimaanModal()" class="btn-primary flex items-center gap-2">
                            <i class="fas fa-plus"></i> Tambah Penerimaan
                        </button>
                    </div>

                    <!-- Filter -->
                    <div class="mb-6 flex flex-wrap gap-3">
                        <select id="statusFilter" onchange="filterPenerimaanTable()" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
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
                            <tbody>
                                ${incomingTransactions.map((item, idx) => `
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
                                                <button onclick="viewDetail('penerimaan', ${item.id})" class="text-indigo-600 hover:text-indigo-800 mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                ${item.status === 'pending' ? `
                                                <button onclick="approveItem('penerimaan', ${item.id})" class="text-green-600 hover:text-green-800 mr-2">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                                <button onclick="rejectItem('penerimaan', ${item.id})" class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            ` : ''}
                                            </td>
                                        </tr>
                                    `).join('')}
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
                            <form onsubmit="simpanPenerimaan(event)">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Produk</label>
                                        <select id="productSelect" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                            <option value="">-- Pilih Produk --</option>
                                            ${stocks.map(p => `<option value="${p.id}">${p.name} (Stok: ${p.stock} ${p.unit})</option>`).join('')}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                                        <input type="number" id="qtyInput" required min="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                                        <textarea id="notesInput" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Contoh: Pembelian dari supplier..."></textarea>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button" onclick="closeModal('modalTambahPenerimaan')" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Batal</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderPengeluaran() {
            return `
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-slate-800">
                            <i class="fas fa-arrow-up text-red-600 mr-2"></i>Daftar Pengeluaran Stok
                        </h3>
                        <button onclick="showTambahPengeluaranModal()" class="btn-primary flex items-center gap-2">
                            <i class="fas fa-plus"></i> Tambah Pengeluaran
                        </button>
                    </div>

                    <!-- Filter -->
                    <div class="mb-6 flex flex-wrap gap-3">
                        <select id="statusFilterOut" onchange="filterPengeluaranTable()" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
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
                            <tbody>
                                ${outgoingTransactions.map((item, idx) => `
                                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                                            <td class="p-3">${idx+1}</td>
                                            <td class="p-3 font-mono text-xs">${item.code}</td>
                                            <td class="p-3 font-medium">${item.product}</td>
                                            <td class="p-3 text-red-600 font-medium">-${item.qty} pcs</td>
                                            <td class="p-3">${item.destination}</td>
                                            <td class="p-3">${item.date}</td>
                                            <td class="p-3">
                                                <span class="status-badge status-${item.status}">
                                                    ${item.status === 'approved' ? 'Disetujui' : (item.status === 'pending' ? 'Pending' : 'Ditolak')}
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <button onclick="viewDetail('pengeluaran', ${item.id})" class="text-indigo-600 hover:text-indigo-800 mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                ${item.status === 'pending' ? `
                                                <button onclick="approveItem('pengeluaran', ${item.id})" class="text-green-600 hover:text-green-800 mr-2">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                                <button onclick="rejectItem('pengeluaran', ${item.id})" class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            ` : ''}
                                            </td>
                                        </tr>
                                    `).join('')}
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
                            <form onsubmit="simpanPengeluaran(event)">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Produk</label>
                                        <select id="productSelectOut" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                            <option value="">-- Pilih Produk --</option>
                                            ${stocks.map(p => `<option value="${p.id}" data-stock="${p.stock}">${p.name} (Stok: ${p.stock} ${p.unit})</option>`).join('')}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                                        <input type="number" id="qtyInputOut" required min="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                        <p id="stockWarning" class="text-xs text-red-500 mt-1 hidden">Stok tidak mencukupi!</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Tujuan</label>
                                        <input type="text" id="destinationInput" class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Toko / Customer">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                                        <textarea id="notesInputOut" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Catatan pengiriman..."></textarea>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button" onclick="closeModal('modalTambahPengeluaran')" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Batal</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderPenyesuaian() {
            return `
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
                                    <li>Koreksi stok fisik setelah opname</li>
                                    <li>Penambahan stok karena retur dari customer</li>
                                    <li>Pengurangan stok karena barang rusak atau kadaluarsa</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
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
                            <tbody>
                                ${adjustments.map((item, idx) => {
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
                                                    <button onclick="viewDetail('penyesuaian', ${item.id})" class="text-indigo-600 hover:text-indigo-800 mr-2">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    ${item.status === 'draft' ? `
                                                    <button onclick="submitAdjustment(${item.id})" class="text-green-600 hover:text-green-800 mr-2">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                    <button onclick="deleteAdjustment(${item.id})" class="text-red-600 hover:text-red-800">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                ` : ''}
                                                </td>
                                            </tr>
                                        `;
                                }).join('')}
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
                                        <select id="productSelectAdj" required onchange="loadCurrentStock()" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                            <option value="">-- Pilih Produk --</option>
                                            ${stocks.map(p => `<option value="${p.id}" data-stock="${p.stock}" data-unit="${p.unit}" data-name="${p.name}">${p.name} (Stok: ${p.stock} ${p.unit})</option>`).join('')}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Stok Saat Ini</label>
                                        <input type="text" id="currentStock" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Stok Baru</label>
                                        <input type="number" id="newStock" required oninput="calculateChange()" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Perubahan</label>
                                        <input type="text" id="changeAmount" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg">
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
                                    <button type="button" onclick="closeModal('modalTambahPenyesuaian')" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Batal</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan Sebagai Draft</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderLaporanStok() {
            return `
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-slate-800">
                            <i class="fas fa-file-alt text-indigo-600 mr-2"></i>Laporan Pergerakan Stok
                        </h3>
                        <div class="flex gap-2">
                            <button onclick="exportToExcel()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                            <button onclick="printReport()" class="bg-slate-600 text-white px-4 py-2 rounded-lg hover:bg-slate-700 transition">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                        </div>
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
                            <tbody>
                                ${stockReport.map((item, idx) => `
                                        <tr class="border-b border-slate-100">
                                            <td class="p-3">${idx+1}</td>
                                            <td class="p-3 font-medium">${item.product}</td>
                                            <td class="p-3">${item.category}</td>
                                            <td class="p-3">${item.awal} pcs</td>
                                            <td class="p-3 text-green-600">+${item.masuk} pcs</td>
                                            <td class="p-3 text-red-600">-${item.keluar} pcs</td>
                                            <td class="p-3">0 pcs</td>
                                            <td class="p-3 font-bold">${item.akhir} pcs</td>
                                            <td class="p-3">
                                                ${item.akhir < item.minStock ? 
                                                    '<span class="status-badge status-pending">Stok Menipis</span>' : 
                                                    '<span class="status-badge status-approved">Normal</span>'}
                                            </td>
                                        </tr>
                                    `).join('')}
                            </tbody>
                            <tfoot class="bg-slate-100 font-semibold">
                                <tr>
                                    <td colspan="3" class="p-3 text-right">Total:</td>
                                    <td class="p-3">${stockReport.reduce((sum, item) => sum + item.awal, 0)} pcs</td>
                                    <td class="p-3 text-green-600">+${stockReport.reduce((sum, item) => sum + item.masuk, 0)} pcs</td>
                                    <td class="p-3 text-red-600">-${stockReport.reduce((sum, item) => sum + item.keluar, 0)} pcs</td>
                                    <td class="p-3">0 pcs</td>
                                    <td class="p-3">${stockReport.reduce((sum, item) => sum + item.akhir, 0)} pcs</td>
                                    <td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Statistik -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                    <div class="bg-white rounded-xl shadow-sm p-6 card">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-arrow-down text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500">Total Penerimaan</p>
                                <p class="text-2xl font-bold text-slate-800">${stockReport.reduce((sum, item) => sum + item.masuk, 0)}</p>
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
                                <p class="text-2xl font-bold text-slate-800">${stockReport.reduce((sum, item) => sum + item.keluar, 0)}</p>
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
                                <p class="text-2xl font-bold text-slate-800">${stockReport.reduce((sum, item) => sum + item.akhir, 0)}</p>
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
                                <p class="text-2xl font-bold text-slate-800">${stockReport.filter(item => item.akhir < item.minStock).length}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderRiwayat() {
            return `
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-xl font-semibold mb-4">
                        <i class="fas fa-history text-indigo-600 mr-2"></i>Riwayat Transaksi
                    </h3>

                    <!-- Filter -->
                    <div class="mb-6 flex flex-wrap gap-3">
                        <select id="typeFilter" onchange="filterRiwayatTable()" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="all">Semua Tipe</option>
                            <option value="Penerimaan">Penerimaan</option>
                            <option value="Pengeluaran">Pengeluaran</option>
                            <option value="Penyesuaian">Penyesuaian</option>
                        </select>
                        <select id="statusFilterRiwayat" onchange="filterRiwayatTable()" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="all">Semua Status</option>
                            <option value="approved">Disetujui</option>
                            <option value="pending">Pending</option>
                        </select>
                        <input type="text" id="searchRiwayat" onkeyup="filterRiwayatTable()" placeholder="Cari produk..." 
                            class="px-3 py-2 border border-slate-300 rounded-lg text-sm w-64">
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
                                    <th class="p-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${historyTransactions.map((item, idx) => `
                                        <tr class="border-b border-slate-100">
                                            <td class="p-3">${idx+1}</td>
                                            <td class="p-3 font-mono text-xs">${item.id}</td>
                                            <td class="p-3">
                                                <span class="px-2 py-1 rounded text-xs ${item.type === 'Penerimaan' ? 'bg-green-100 text-green-700' : (item.type === 'Pengeluaran' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')}">
                                                    ${item.type}
                                                </span>
                                            </td>
                                            <td class="p-3 font-medium">${item.product}</td>
                                            <td class="p-3 ${item.qty.startsWith('+') ? 'text-green-600' : 'text-red-600'}">${item.qty} pcs</td>
                                            <td class="p-3">${item.date}</td>
                                            <td class="p-3">${item.user}</td>
                                            <td class="p-3">
                                                <span class="status-badge status-${item.status}">
                                                    ${item.status === 'approved' ? 'Disetujui' : 'Pending'}
                                                </span>
                                            </td>
                                        </tr>
                                    `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        }

        // ==================== HELPER FUNCTIONS ====================
        function toggleSubmenu(submenuId, iconId) {
            const submenu = document.getElementById(submenuId);
            const icon = document.getElementById(iconId);
            if (submenu && icon) {
                submenu.classList.toggle('open');
                icon.classList.toggle('rotate');
            }
        }

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

        function showSuccess(message, title = 'Berhasil!') {
            const modal = document.getElementById('modalSuccess');
            if (modal) {
                document.getElementById('successTitle').innerText = title;
                document.getElementById('successMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => closeModal('modalSuccess'), 2500);
            }
        }

        function showError(message, title = 'Gagal!') {
            const modal = document.getElementById('modalError');
            if (modal) {
                document.getElementById('errorTitle').innerText = title;
                document.getElementById('errorMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function showWarning(message, title = 'Peringatan!') {
            const modal = document.getElementById('modalWarning');
            if (modal) {
                document.getElementById('warningTitle').innerText = title;
                document.getElementById('warningMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function showInfo(message, title = 'Informasi') {
            const modal = document.getElementById('modalInfo');
            if (modal) {
                document.getElementById('infoTitle').innerText = title;
                document.getElementById('infoMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => closeModal('modalInfo'), 2000);
            }
        }

        function showConfirmDelete(message, onConfirm) {
            const modal = document.getElementById('modalConfirmDelete');
            if (modal) {
                document.getElementById('confirmDeleteMessage').innerHTML = message;
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                const newBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
                newBtn.onclick = () => {
                    closeModal('modalConfirmDelete');
                    if (onConfirm) onConfirm();
                };
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function showLogoutConfirm() {
            const modal = document.getElementById('modalLogoutConfirm');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function confirmLogout() {
            closeModal('modalLogoutConfirm');
            showInfo('Logging out...', 'Proses');
            setTimeout(() => {
                window.location.href = '#';
            }, 1000);
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

        // Filter functions
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

        function filterRiwayatTable() {
            const type = document.getElementById('typeFilter')?.value || 'all';
            const status = document.getElementById('statusFilterRiwayat')?.value || 'all';
            const search = document.getElementById('searchRiwayat')?.value.toLowerCase() || '';
            const rows = document.querySelectorAll('#riwayatTable tbody tr');

            rows.forEach(row => {
                const typeCell = row.cells[2]?.innerText || '';
                const statusCell = row.cells[7]?.innerText.toLowerCase() || '';
                const productCell = row.cells[3]?.innerText.toLowerCase() || '';

                let typeMatch = type === 'all' || typeCell.includes(type);
                let statusMatch = status === 'all' || (status === 'approved' ? statusCell.includes('disetujui') :
                    statusCell.includes('pending'));
                let searchMatch = search === '' || productCell.includes(search);

                row.style.display = typeMatch && statusMatch && searchMatch ? '' : 'none';
            });
        }

        // Action functions
        function showTambahPenerimaanModal() {
            showModal('modalTambahPenerimaan');
        }

        function showTambahPengeluaranModal() {
            showModal('modalTambahPengeluaran');
        }

        function showTambahPenyesuaianModal() {
            showModal('modalTambahPenyesuaian');
        }

        function simpanPenerimaan(event) {
            event.preventDefault();
            closeModal('modalTambahPenerimaan');
            showSuccess('Penerimaan stok berhasil ditambahkan dan menunggu persetujuan', 'Berhasil!');
        }

        function simpanPengeluaran(event) {
            event.preventDefault();
            closeModal('modalTambahPengeluaran');
            showSuccess('Pengeluaran stok berhasil ditambahkan dan menunggu persetujuan', 'Berhasil!');
        }

        function simpanPenyesuaian(event) {
            event.preventDefault();
            closeModal('modalTambahPenyesuaian');
            showSuccess('Penyesuaian stok berhasil disimpan sebagai draft', 'Berhasil!');
        }

        function loadCurrentStock() {
            const select = document.getElementById('productSelectAdj');
            const selectedOption = select.options[select.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock') || 0;
            const unit = selectedOption.getAttribute('data-unit') || 'pcs';

            document.getElementById('currentStock').value = stock + ' ' + unit;
            document.getElementById('currentStock').setAttribute('data-value', stock);
            document.getElementById('currentStock').setAttribute('data-unit', unit);
            document.getElementById('newStock').value = stock;
            document.getElementById('changeAmount').value = '0 ' + unit;
        }

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

        function viewDetail(type, id) {
            showInfo(`Detail ${type} dengan ID: ${id}`, 'Detail Transaksi');
        }

        function approveItem(type, id) {
            showConfirmDelete(`Setujui ${type} ini?`, () => {
                showSuccess(`${type} berhasil disetujui`, 'Disetujui!');
            });
        }

        function rejectItem(type, id) {
            showConfirmDelete(`Tolak ${type} ini?`, () => {
                showWarning(`${type} ditolak`, 'Ditolak');
            });
        }

        function submitAdjustment(id) {
            showConfirmDelete('Kirim penyesuaian ini untuk disetujui?', () => {
                showSuccess('Penyesuaian telah dikirim', 'Berhasil!');
            });
        }

        function deleteAdjustment(id) {
            showConfirmDelete('Hapus draft penyesuaian ini?', () => {
                showWarning('Penyesuaian dihapus', 'Dihapus');
            });
        }

        function exportToExcel() {
            showInfo('Fitur export Excel akan segera hadir', 'Informasi');
        }

        function printReport() {
            window.print();
        }

        // Initial load
        showPage('beranda');
    </script>

    @stack('scripts')

    <!-- MODALS -->
    <div id="modalSuccess" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center animate-modal">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2" id="successTitle">Berhasil!</h3>
            <p class="text-slate-500" id="successMessage">Data berhasil disimpan</p>
            <button onclick="closeModal('modalSuccess')"
                class="mt-6 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Tutup</button>
        </div>
    </div>

    <div id="modalError" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center animate-modal">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times-circle text-red-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2" id="errorTitle">Gagal!</h3>
            <p class="text-slate-500" id="errorMessage">Terjadi kesalahan</p>
            <button onclick="closeModal('modalError')"
                class="mt-6 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Tutup</button>
        </div>
    </div>

    <div id="modalWarning" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center animate-modal">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2" id="warningTitle">Peringatan!</h3>
            <p class="text-slate-500" id="warningMessage"></p>
            <button onclick="closeModal('modalWarning')"
                class="mt-6 px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">Tutup</button>
        </div>
    </div>

    <div id="modalInfo" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center animate-modal">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-info-circle text-blue-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2" id="infoTitle">Informasi</h3>
            <p class="text-slate-500" id="infoMessage"></p>
            <button onclick="closeModal('modalInfo')"
                class="mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
        </div>
    </div>

    <div id="modalConfirmDelete" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-trash-alt text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-800">Konfirmasi Hapus</h3>
                </div>
                <p class="text-slate-500 mb-6" id="confirmDeleteMessage">Apakah Anda yakin ingin menghapus data ini?
                </p>
                <div class="flex justify-end gap-3">
                    <button onclick="closeModal('modalConfirmDelete')"
                        class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition">Batal</button>
                    <button id="confirmDeleteBtn"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalLoading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xs mx-4 p-6 text-center animate-modal">
            <div
                class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-3">
            </div>
            <p class="text-slate-600" id="loadingMessage">Memproses...</p>
        </div>
    </div>

    <div id="modalLogoutConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-red-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800">Konfirmasi Keluar</h3>
                        <p class="text-sm text-slate-500">Apakah Anda yakin ingin keluar?</p>
                    </div>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-amber-800 font-medium">Perhatian!</p>
                            <p class="text-xs text-amber-700 mt-1">Anda akan keluar dari sistem. Pastikan semua data
                                telah tersimpan sebelum keluar.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button onclick="closeModal('modalLogoutConfirm')"
                        class="px-5 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition font-medium">
                        <i class="fas fa-times mr-2"></i> Batal
                    </button>
                    <button onclick="confirmLogout()"
                        class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
