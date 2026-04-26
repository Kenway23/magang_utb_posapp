@extends('layouts.owner')

@section('title', 'Persetujuan Aktivitas Gudang - PROShop')
@section('header-title', 'Persetujuan Aktivitas Gudang')
@section('header-subtitle',
    'Setujui atau tolak pengajuan (Produk Baru, Tambah Stok, Stok Masuk, Stok Keluar,
    Penyesuaian)')

@section('content')
    <style>
        .action-btn {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .tab-active {
            border-bottom: 2px solid #4f46e5;
            color: #4f46e5;
            font-weight: 600;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #059669;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 320px;
            max-width: 400px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-success {
            border-left: 4px solid #10b981;
        }

        .toast-error {
            border-left: 4px solid #ef4444;
        }

        .toast-warning {
            border-left: 4px solid #f59e0b;
        }

        .table-container {
            max-height: 500px;
            overflow-y: auto;
            overflow-x: auto;
        }

        .table-container thead th {
            position: sticky;
            top: 0;
            background-color: #f8fafc;
            z-index: 10;
        }
    </style>

    <div>
        <!-- STATISTIK CARD -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-amber-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Menunggu Persetujuan</p>
                        <p class="text-3xl font-bold text-amber-600" id="totalMenunggu">0</p>
                    </div>
                    <div class="w-11 h-11 bg-amber-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-amber-600 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-emerald-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Disetujui</p>
                        <p class="text-3xl font-bold text-emerald-600" id="totalDisetujui">0</p>
                    </div>
                    <div class="w-11 h-11 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-rose-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Ditolak</p>
                        <p class="text-3xl font-bold text-rose-600" id="totalDitolak">0</p>
                    </div>
                    <div class="w-11 h-11 bg-rose-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-rose-600 text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Total Pengajuan</p>
                        <p class="text-3xl font-bold text-blue-600" id="totalSemua">0</p>
                    </div>
                    <div class="w-11 h-11 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-inbox text-blue-600 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABS UTAMA (Jenis Pengajuan) -->
        <div class="bg-white rounded-xl shadow-sm mb-4">
            <div class="border-b border-slate-200 px-6">
                <div class="flex gap-6 overflow-x-auto">
                    <button onclick="showJenisTab('produk')"
                        class="tab-jenis py-3 px-2 text-sm font-medium transition tab-active">
                        <i class="fas fa-box text-indigo-500 mr-2"></i> Produk Baru
                        <span id="badgeProduk"
                            class="ml-2 px-1.5 py-0.5 text-xs bg-amber-500 text-white rounded-full">0</span>
                    </button>
                    <button onclick="showJenisTab('tambah_stok')"
                        class="tab-jenis py-3 px-2 text-sm font-medium transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-plus-circle text-teal-500 mr-2"></i> Tambah Stok
                        <span id="badgeTambahStok"
                            class="ml-2 px-1.5 py-0.5 text-xs bg-amber-500 text-white rounded-full">0</span>
                    </button>
                    <button onclick="showJenisTab('penerimaan')"
                        class="tab-jenis py-3 px-2 text-sm font-medium transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-arrow-down text-emerald-500 mr-2"></i> Penerimaan Stok
                        <span id="badgePenerimaan"
                            class="ml-2 px-1.5 py-0.5 text-xs bg-amber-500 text-white rounded-full">0</span>
                    </button>
                    <button onclick="showJenisTab('pengiriman')"
                        class="tab-jenis py-3 px-2 text-sm font-medium transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-arrow-up text-orange-500 mr-2"></i> Pengiriman Stok
                        <span id="badgePengiriman"
                            class="ml-2 px-1.5 py-0.5 text-xs bg-amber-500 text-white rounded-full">0</span>
                    </button>
                    <button onclick="showJenisTab('penyesuaian')"
                        class="tab-jenis py-3 px-2 text-sm font-medium transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-sliders-h text-purple-500 mr-2"></i> Penyesuaian Stok
                        <span id="badgePenyesuaian"
                            class="ml-2 px-1.5 py-0.5 text-xs bg-amber-500 text-white rounded-full">0</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- SUB TABS (Filter Status) -->
        <div class="bg-white rounded-xl shadow-sm mb-6">
            <div class="border-b border-slate-200 px-6">
                <div class="flex gap-4 overflow-x-auto">
                    <button onclick="showStatusTab('semua')" id="tabStatusSemua"
                        class="tab-status py-2 px-3 text-sm font-medium transition border-b-2 border-emerald-500 text-emerald-600">
                        Semua Status
                    </button>
                    <button onclick="showStatusTab('pending')" id="tabStatusPending"
                        class="tab-status py-2 px-3 text-sm font-medium transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-clock mr-1"></i> Menunggu
                    </button>
                    <button onclick="showStatusTab('approved')" id="tabStatusApproved"
                        class="tab-status py-2 px-3 text-sm font-medium transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-check-circle mr-1"></i> Disetujui
                    </button>
                    <button onclick="showStatusTab('rejected')" id="tabStatusRejected"
                        class="tab-status py-2 px-3 text-sm font-medium transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                    </button>
                </div>
            </div>
        </div>

        <!-- TABEL PRODUK BARU -->
        <div id="tableProduk" class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200 mb-6">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-box text-indigo-500 mr-2"></i> Daftar Pengajuan
                    Produk Baru</h3>
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl Pengajuan
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Harga</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Stok</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Diajukan Oleh
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyProduk" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>

        <!-- TABEL TAMBAH STOK -->
        <div id="tableTambahStok" class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200 mb-6 hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-plus-circle text-teal-500 mr-2"></i> Daftar
                    Pengajuan Tambah Stok</h3>
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl Pengajuan
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Stok Saat Ini
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Jumlah Request
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Stok Sesudah
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Supplier</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Diajukan Oleh
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyTambahStok" class="divide-y divide-slate-100"></tbody>
                    </tr>
            </div>
        </div>

        <!-- TABEL LAINNYA (Placeholder) -->
        <div id="tablePenerimaan"
            class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200 mb-6 hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-arrow-down text-emerald-500 mr-2"></i> Daftar
                    Pengajuan Penerimaan Stok</h3>
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left">Tgl Pengajuan</th>
                            <th class="px-5 py-3 text-left">Produk</th>
                            <th class="px-5 py-3 text-left">Jumlah</th>
                            <th class="px-5 py-3 text-left">Supplier</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPenerimaan"></tbody>
                </table>
            </div>
        </div>

        <div id="tablePengiriman"
            class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200 mb-6 hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-arrow-up text-orange-500 mr-2"></i> Daftar
                    Pengajuan Pengiriman Stok</h3>
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left">Tgl Pengajuan</th>
                            <th class="px-5 py-3 text-left">Produk</th>
                            <th class="px-5 py-3 text-left">Jumlah</th>
                            <th class="px-5 py-3 text-left">Tujuan</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPengiriman"></tbody>
                </table>
            </div>
        </div>

        <div id="tablePenyesuaian"
            class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200 mb-6 hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-sliders-h text-purple-500 mr-2"></i> Daftar
                    Pengajuan Penyesuaian Stok</h3>
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left">Tgl Pengajuan</th>
                            <th class="px-5 py-3 text-left">Produk</th>
                            <th class="px-5 py-3 text-left">Stok Lama</th>
                            <th class="px-5 py-3 text-left">Stok Baru</th>
                            <th class="px-5 py-3 text-left">Alasan</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPenyesuaian"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI SETUJU -->
    <div id="approveModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-emerald-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Konfirmasi Persetujuan</h3>
                <p class="text-slate-500 mb-6">Apakah Anda yakin ingin menyetujui <span id="approveItemName"
                        class="font-semibold text-slate-700"></span>?</p>
                <div class="flex gap-3">
                    <button onclick="closeApproveModal()" class="flex-1 px-4 py-2 border rounded-lg">Batal</button>
                    <button id="confirmApproveBtn"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Ya,
                        Setujui</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PENOLAKAN -->
    <div id="rejectModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-times-circle text-rose-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Alasan Penolakan</h3>
                <p class="text-slate-500 text-sm mb-4">Silakan berikan alasan mengapa ini ditolak</p>
                <textarea id="alasanTolak" rows="4"
                    class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-rose-500"
                    placeholder="Masukkan alasan penolakan..."></textarea>
                <div class="flex gap-3 mt-6">
                    <button onclick="closeRejectModal()" class="flex-1 px-4 py-2 border rounded-lg">Batal</button>
                    <button id="confirmRejectBtn"
                        class="flex-1 px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">Kirim
                        Penolakan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toastNotification" class="toast-notification">
        <div class="flex items-center p-4">
            <div id="toastIcon" class="flex-shrink-0 mr-3"><i class="fas fa-check-circle text-xl"></i></div>
            <div class="flex-1">
                <p id="toastMessage" class="text-sm font-medium text-slate-800"></p>
            </div>
            <button onclick="hideToast()" class="flex-shrink-0 ml-3 text-slate-400 hover:text-slate-600"><i
                    class="fas fa-times"></i></button>
        </div>
    </div>

    <script>
        // Data dari server (satu endpoint)
        let produkData = [];
        let tambahStokData = [];
        let currentJenisTab = 'produk';
        let currentStatusTab = 'semua';
        let currentItemId = null;
        let currentItemType = null;
        let currentItemName = null;

        // Load semua data dari satu endpoint (owner.stok.approval.data)
        function loadData() {
            fetch('/owner/stok/approval/data')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        produkData = data.data.produk_baru.map(p => ({
                            id: p.id,
                            nama: p.nama,
                            kategori: p.kategori,
                            harga: p.harga,
                            stok: p.stok,
                            diajukan_oleh: p.diajukan_oleh,
                            tanggal: p.tanggal,
                            status: p.status,
                            alasan_ditolak: p.alasan_ditolak
                        }));

                        tambahStokData = data.data.tambah_stok.map(item => ({
                            id: item.id,
                            produk_nama: item.nama,
                            jumlah_request: item.jumlah_request,
                            stok_sebelum: item.stok_sebelum,
                            stok_sesudah: item.stok_sesudah,
                            supplier: item.supplier,
                            requester_name: item.diajukan_oleh,
                            created_at: item.tanggal,
                            status: item.status,
                            alasan_ditolak: item.alasan_ditolak
                        }));

                        updateStatistics();

                        if (currentJenisTab === 'produk') renderTabelProduk();
                        else if (currentJenisTab === 'tambah_stok') renderTabelTambahStok();
                    }
                })
                .catch(err => console.error('Error load data:', err));
        }

        function updateStatistics() {
            const semuaProduk = [...produkData, ...tambahStokData];
            const menunggu = semuaProduk.filter(p => p.status === 'pending').length;
            const disetujui = semuaProduk.filter(p => p.status === 'approved').length;
            const ditolak = semuaProduk.filter(p => p.status === 'rejected').length;

            document.getElementById('totalMenunggu').innerText = menunggu;
            document.getElementById('totalDisetujui').innerText = disetujui;
            document.getElementById('totalDitolak').innerText = ditolak;
            document.getElementById('totalSemua').innerText = semuaProduk.length;

            document.getElementById('badgeProduk').innerText = produkData.filter(p => p.status === 'pending').length;
            document.getElementById('badgeTambahStok').innerText = tambahStokData.filter(p => p.status === 'pending')
                .length;
        }

        function showJenisTab(jenis) {
            currentJenisTab = jenis;

            // Update tab style - gunakan class, bukan ID
            document.querySelectorAll('.tab-jenis').forEach(tab => {
                tab.classList.remove('tab-active');
                tab.classList.add('text-slate-500');
            });

            // Cari tab yang aktif berdasarkan event target
            const activeTabs = document.querySelectorAll('.tab-jenis');
            for (let i = 0; i < activeTabs.length; i++) {
                const tab = activeTabs[i];
                const tabText = tab.innerText.toLowerCase();
                if (tabText.includes(jenis.replace('_', ' '))) {
                    tab.classList.add('tab-active');
                    tab.classList.remove('text-slate-500');
                    break;
                }
            }

            // Hide all tables
            document.getElementById('tableProduk')?.classList.add('hidden');
            document.getElementById('tableTambahStok')?.classList.add('hidden');
            document.getElementById('tablePenerimaan')?.classList.add('hidden');
            document.getElementById('tablePengiriman')?.classList.add('hidden');
            document.getElementById('tablePenyesuaian')?.classList.add('hidden');

            // Show selected table
            if (jenis === 'produk') {
                const table = document.getElementById('tableProduk');
                if (table) table.classList.remove('hidden');
                renderTabelProduk();
            } else if (jenis === 'tambah_stok') {
                const table = document.getElementById('tableTambahStok');
                if (table) table.classList.remove('hidden');
                renderTabelTambahStok();
            } else if (jenis === 'penerimaan') {
                const table = document.getElementById('tablePenerimaan');
                if (table) table.classList.remove('hidden');
                renderTabelPenerimaan();
            } else if (jenis === 'pengiriman') {
                const table = document.getElementById('tablePengiriman');
                if (table) table.classList.remove('hidden');
                renderTabelPengiriman();
            } else if (jenis === 'penyesuaian') {
                const table = document.getElementById('tablePenyesuaian');
                if (table) table.classList.remove('hidden');
                renderTabelPenyesuaian();
            }
        }

        function showStatusTab(status) {
            currentStatusTab = status;
            document.querySelectorAll('.tab-status').forEach(tab => {
                tab.classList.remove('border-b-2', 'border-emerald-500', 'text-emerald-600');
                tab.classList.add('text-slate-500');
            });
            let tabId = status === 'semua' ? 'tabStatusSemua' : status === 'pending' ? 'tabStatusPending' : status ===
                'approved' ? 'tabStatusApproved' : 'tabStatusRejected';
            document.getElementById(tabId).classList.add('border-b-2', 'border-emerald-500', 'text-emerald-600');

            if (currentJenisTab === 'produk') renderTabelProduk();
            else if (currentJenisTab === 'tambah_stok') renderTabelTambahStok();
        }

        function getFilteredData(data) {
            if (currentStatusTab === 'semua') return data;
            return data.filter(item => item.status === currentStatusTab);
        }

        function getStatusBadge(status) {
            if (status === 'pending')
                return '<span class="status-badge status-pending"><i class="fas fa-clock text-xs"></i> Menunggu</span>';
            if (status === 'approved')
                return '<span class="status-badge status-approved"><i class="fas fa-check-circle text-xs"></i> Disetujui</span>';
            if (status === 'rejected')
                return '<span class="status-badge status-rejected"><i class="fas fa-times-circle text-xs"></i> Ditolak</span>';
            return '';
        }

        function getActionButtons(item, type, id, nama) {
            if (item.status !== 'pending') return '<span class="text-slate-400 text-xs">-</span>';
            return `
                <div class="flex items-center justify-center gap-2">
                    <button onclick="showApproveModal('${type}', ${id}, '${nama.replace(/'/g, "\\'")}')" class="action-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1">
                        <i class="fas fa-check text-xs"></i> Setuju
                    </button>
                    <button onclick="showRejectModal('${type}', ${id}, '${nama.replace(/'/g, "\\'")}')" class="action-btn bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1">
                        <i class="fas fa-times text-xs"></i> Tolak
                    </button>
                </div>
            `;
        }

        function renderTabelProduk() {
            const tbody = document.getElementById('tbodyProduk');
            let filtered = getFilteredData(produkData);
            if (filtered.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="8" class="text-center py-8 text-slate-400"><i class="fas fa-inbox text-4xl mb-2 block"></i>Tidak ada数据</td></tr>`;
                return;
            }
            tbody.innerHTML = filtered.map(p => `
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">${new Date(p.tanggal).toLocaleDateString('id-ID')}</td>
                    <td class="px-5 py-3 font-medium">${p.nama}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">${p.kategori}</span></td>
                    <td class="px-5 py-3 font-semibold text-emerald-600">Rp ${p.harga}</td>
                    <td class="px-5 py-3">${p.stok} pcs</td></td>
                    <td class="px-5 py-3">${p.diajukan_oleh}</td></td>
                    <td class="px-5 py-3">${getStatusBadge(p.status)}</td></td>
                    <td class="px-5 py-3 text-center">${getActionButtons(p, 'produk', p.id, p.nama)}</td></td>
                 </tr>
            `).join('');
        }

        function renderTabelTambahStok() {
            const tbody = document.getElementById('tbodyTambahStok');
            let filtered = getFilteredData(tambahStokData);
            if (filtered.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="9" class="text-center py-8 text-slate-400"><i class="fas fa-inbox text-4xl mb-2 block"></i>Tidak ada data</td></tr>`;
                return;
            }
            tbody.innerHTML = filtered.map(item => `
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                    <td class="px-5 py-3 font-medium">${item.produk_nama}</td>
                    <td class="px-5 py-3">${item.stok_sebelum}</td>
                    <td class="px-5 py-3 text-green-600 font-semibold">+${item.jumlah_request}</td>
                    <td class="px-5 py-3">${item.stok_sesudah}</td>
                    <td class="px-5 py-3">${item.supplier || '-'}</td>
                    <td class="px-5 py-3">${item.requester_name || 'Gudang'}</td>
                    <td class="px-5 py-3">${getStatusBadge(item.status)}</td></td>
                    <td class="px-5 py-3 text-center">${getActionButtons(item, 'tambah_stok', item.id, item.produk_nama)}</td></td>
                 </tr>
            `).join('');
        }

        function renderTabelPenerimaan() {
            document.getElementById('tbodyPenerimaan').innerHTML =
                `<tr><td colspan="6" class="text-center py-8 text-slate-400">Fitur penerimaan stok akan segera hadir</td></tr>`;
        }

        function renderTabelPengiriman() {
            document.getElementById('tbodyPengiriman').innerHTML =
                `<tr><td colspan="6" class="text-center py-8 text-slate-400">Fitur pengiriman stok akan segera hadir</td></tr>`;
        }

        function renderTabelPenyesuaian() {
            document.getElementById('tbodyPenyesuaian').innerHTML =
                `<tr><td colspan="7" class="text-center py-8 text-slate-400">Fitur penyesuaian stok akan segera hadir</td></tr>`;
        }

        // Toast functions
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');
            toast.className = 'toast-notification';
            if (type === 'success') {
                toast.classList.add('toast-success');
                toastIcon.innerHTML = '<i class="fas fa-check-circle text-emerald-500 text-xl"></i>';
            } else if (type === 'error') {
                toast.classList.add('toast-error');
                toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-rose-500 text-xl"></i>';
            } else {
                toast.classList.add('toast-warning');
                toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-amber-500 text-xl"></i>';
            }
            toastMessage.innerHTML = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        function hideToast() {
            document.getElementById('toastNotification').classList.remove('show');
        }

        // Modal Approve
        function showApproveModal(type, id, name) {
            currentItemType = type;
            currentItemId = id;
            currentItemName = name;
            document.getElementById('approveItemName').innerText = name;
            document.getElementById('approveModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmApprove() {
            if (!currentItemId) return;
            let url = currentItemType === 'produk' ? `/owner/stok/approval/produk/${currentItemId}/approve` :
                `/owner/stok/approval/tambah-stok/${currentItemId}/approve`;
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    closeApproveModal();
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Gagal menyetujui', 'error');
                    }
                })
                .catch(err => {
                    closeApproveModal();
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        }

        // Modal Reject
        function showRejectModal(type, id, name) {
            currentItemType = type;
            currentItemId = id;
            currentItemName = name;
            document.getElementById('alasanTolak').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmReject() {
            const alasan = document.getElementById('alasanTolak').value.trim();
            if (!alasan) {
                showToast('Alasan penolakan harus diisi!', 'warning');
                return;
            }
            let url = currentItemType === 'produk' ? `/owner/stok/approval/produk/${currentItemId}/reject` :
                `/owner/stok/approval/tambah-stok/${currentItemId}/reject`;
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        alasan: alasan
                    })
                })
                .then(res => res.json())
                .then(data => {
                    closeRejectModal();
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Gagal menolak', 'error');
                    }
                })
                .catch(err => {
                    closeRejectModal();
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        }

        // Event listeners
        document.getElementById('confirmApproveBtn').addEventListener('click', confirmApprove);
        document.getElementById('confirmRejectBtn').addEventListener('click', confirmReject);
        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('approveModal')) closeApproveModal();
            if (event.target === document.getElementById('rejectModal')) closeRejectModal();
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });
    </script>
@endsection
