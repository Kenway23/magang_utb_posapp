@extends('layouts.owner')

@section('title', 'Persetujuan Aktivitas Gudang - PROShop')
@section('header-title', 'Persetujuan Aktivitas Gudang')
@section('header-subtitle',
    'Setujui atau tolak pengajuan (Produk Baru, Tambah Stok, Pengiriman Stok, Penyesuaian
    Stok)')

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

        .btn-disabled {
            background-color: #d1d5db !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }

        .btn-disabled:hover {
            transform: none !important;
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

        <!-- TABS UTAMA -->
        <div class="bg-white rounded-xl shadow-sm mb-4">
            <div class="border-b border-slate-200 px-6">
                <div class="flex gap-6 overflow-x-auto">
                    <button onclick="showJenisTab('produk')" id="tabJenisProduk"
                        class="tab-jenis py-3 px-2 text-sm font-medium transition tab-active">
                        <i class="fas fa-box text-indigo-500 mr-2"></i> Produk Baru
                        <span id="badgeProduk"
                            class="ml-2 px-1.5 py-0.5 text-xs bg-amber-500 text-white rounded-full">0</span>
                    </button>
                    <button onclick="showJenisTab('tambah_stok')" id="tabJenisTambahStok"
                        class="tab-jenis py-3 px-2 text-sm font-medium transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-plus-circle text-teal-500 mr-2"></i> Tambah Stok
                        <span id="badgeTambahStok"
                            class="ml-2 px-1.5 py-0.5 text-xs bg-amber-500 text-white rounded-full">0</span>
                    </button>
                    <button onclick="showJenisTab('pengiriman')" id="tabJenisPengiriman"
                        class="tab-jenis py-3 px-2 text-sm font-medium transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-arrow-up text-orange-500 mr-2"></i> Pengiriman Stok
                        <span id="badgePengiriman"
                            class="ml-2 px-1.5 py-0.5 text-xs bg-amber-500 text-white rounded-full">0</span>
                    </button>
                    <button onclick="showJenisTab('penyesuaian')" id="tabJenisPenyesuaian"
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
                        class="tab-status py-2 px-3 text-sm font-medium transition border-b-2 border-emerald-500 text-emerald-600">Semua
                        Status</button>
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
            <div class="px-4 py-2 border-b border-slate-200 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800 text-sm"><i class="fas fa-box text-indigo-500 mr-2"></i> Daftar
                    Pengajuan Produk Baru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500 w-10">No</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Tgl</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500">Produk</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500 w-24">Kategori</th>
                            <th class="px-3 py-2 text-right font-semibold text-slate-500 w-28">Harga</th>
                            <th class="px-3 py-2 text-center font-semibold text-slate-500 w-16">Stok</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-500 w-24">Pengaju</th>
                            <th class="px-3 py-2 text-center font-semibold text-slate-500 w-24">Status</th>
                            <th class="px-3 py-2 text-center font-semibold text-slate-500 w-28">Aksi</th>
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
                </table>
            </div>
        </div>

        <!-- TABEL PENGIRIMAN STOK -->
        <div id="tablePengiriman"
            class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200 mb-6 hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <i class="fas fa-arrow-up text-orange-500 text-lg"></i>
                    <h3 class="font-semibold text-slate-800">Daftar Pengajuan Pengiriman Stok</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Kode</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Jumlah</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Stok Gudang
                            </th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Ketersediaan
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tujuan</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Pengaju</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPengiriman" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>

        <!-- TABEL PENYESUAIAN STOK -->
        <div id="tablePenyesuaian"
            class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200 mb-6 hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <i class="fas fa-sliders-h text-purple-500 text-lg"></i>
                    <h3 class="font-semibold text-slate-800">Daftar Pengajuan Penyesuaian Stok</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Stok Lama</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Stok Baru</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Perubahan</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Status Stok
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Alasan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Pengaju</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPenyesuaian" class="divide-y divide-slate-100"></tbody>
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
        // Data dari server
        let produkData = [];
        let tambahStokData = [];
        let pengirimanData = [];
        let penyesuaianData = [];
        let currentJenisTab = 'produk';
        let currentStatusTab = 'semua';
        let currentItemId = null;
        let currentItemType = null;
        let currentItemName = null;

        // Load semua data
        function loadData() {
            fetch('/owner/stok/approval/data')
                .then(res => res.json())
                .then(data => {
                    console.log('API Response:', data);
                    if (data.success) {
                        produkData = (data.data.produk_baru || []).map(p => ({
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

                        tambahStokData = (data.data.tambah_stok || []).map(item => ({
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

                        pengirimanData = (data.data.pengiriman || []).map(item => ({
                            id: item.id,
                            kode: item.kode,
                            produk_nama: item.nama,
                            jumlah: item.jumlah,
                            stok_gudang_sebelum: item.stok_gudang_sebelum || 0,
                            tujuan_toko: item.tujuan_toko,
                            requester_name: item.diajukan_oleh,
                            created_at: item.tanggal,
                            status: item.status,
                            alasan_ditolak: item.alasan_ditolak
                        }));

                        penyesuaianData = (data.data.penyesuaian || []).map(item => ({
                            id: item.id,
                            kode: item.kode,
                            produk_nama: item.nama,
                            stok_sebelum: item.stok_sebelum,
                            stok_sesudah: item.stok_sesudah,
                            perubahan: item.perubahan,
                            jenis: item.jenis,
                            alasan: item.alasan,
                            keterangan: item.keterangan,
                            requester_name: item.diajukan_oleh,
                            created_at: item.tanggal,
                            status: item.status,
                            alasan_ditolak: item.alasan_ditolak
                        }));

                        updateStatistics();
                        renderTabelProduk();
                    }
                })
                .catch(err => console.error('Error load data:', err));
        }

        function updateStatistics() {
            const semua = [...produkData, ...tambahStokData, ...pengirimanData, ...penyesuaianData];
            document.getElementById('totalMenunggu').innerText = semua.filter(p => p.status === 'pending').length;
            document.getElementById('totalDisetujui').innerText = semua.filter(p => p.status === 'approved').length;
            document.getElementById('totalDitolak').innerText = semua.filter(p => p.status === 'rejected').length;
            document.getElementById('totalSemua').innerText = semua.length;
            document.getElementById('badgeProduk').innerText = produkData.filter(p => p.status === 'pending').length;
            document.getElementById('badgeTambahStok').innerText = tambahStokData.filter(p => p.status === 'pending')
                .length;
            document.getElementById('badgePengiriman').innerText = pengirimanData.filter(p => p.status === 'pending' || p
                .status === 'waiting_owner').length;
            document.getElementById('badgePenyesuaian').innerText = penyesuaianData.filter(p => p.status === 'pending')
                .length;
        }

        function showJenisTab(jenis) {
            currentJenisTab = jenis;
            document.querySelectorAll('.tab-jenis').forEach(tab => {
                tab.classList.remove('tab-active');
                tab.classList.add('text-slate-500');
            });
            let tabId = {
                produk: 'tabJenisProduk',
                tambah_stok: 'tabJenisTambahStok',
                pengiriman: 'tabJenisPengiriman',
                penyesuaian: 'tabJenisPenyesuaian'
            } [jenis];
            if (tabId) document.getElementById(tabId).classList.add('tab-active');
            ['tableProduk', 'tableTambahStok', 'tablePengiriman', 'tablePenyesuaian'].forEach(t => {
                let el = document.getElementById(t);
                if (el) el.classList.add('hidden');
            });
            document.getElementById({
                produk: 'tableProduk',
                tambah_stok: 'tableTambahStok',
                pengiriman: 'tablePengiriman',
                penyesuaian: 'tablePenyesuaian'
            } [jenis]).classList.remove('hidden');
            if (jenis === 'produk') renderTabelProduk();
            else if (jenis === 'tambah_stok') renderTabelTambahStok();
            else if (jenis === 'pengiriman') renderTabelPengiriman();
            else if (jenis === 'penyesuaian') renderTabelPenyesuaian();
        }

        function showStatusTab(status) {
            currentStatusTab = status;
            document.querySelectorAll('.tab-status').forEach(tab => {
                tab.classList.remove('border-b-2', 'border-emerald-500', 'text-emerald-600');
                tab.classList.add('text-slate-500');
            });
            let tabId = {
                semua: 'tabStatusSemua',
                pending: 'tabStatusPending',
                approved: 'tabStatusApproved',
                rejected: 'tabStatusRejected'
            } [status];
            if (tabId) document.getElementById(tabId).classList.add('border-b-2', 'border-emerald-500', 'text-emerald-600');
            if (currentJenisTab === 'produk') renderTabelProduk();
            else if (currentJenisTab === 'tambah_stok') renderTabelTambahStok();
            else if (currentJenisTab === 'pengiriman') renderTabelPengiriman();
            else if (currentJenisTab === 'penyesuaian') renderTabelPenyesuaian();
        }

        function getFilteredData(data) {
            if (currentStatusTab === 'semua') return data;
            if (currentStatusTab === 'pending') return data.filter(item => item.status === 'pending' || item.status ===
                'waiting_owner');
            return data.filter(item => item.status === currentStatusTab);
        }

        function getStatusBadge(status) {
            if (status === 'pending')
                return '<span class="status-badge status-pending"><i class="fas fa-clock text-xs"></i> Menunggu</span>';
            if (status === 'waiting_owner')
                return '<span class="status-badge" style="background:#e0e7ff;color:#4f46e5;"><i class="fas fa-hourglass-half text-xs"></i> Menunggu Owner</span>';
            if (status === 'approved')
                return '<span class="status-badge status-approved"><i class="fas fa-check-circle text-xs"></i> Disetujui</span>';
            if (status === 'rejected')
                return '<span class="status-badge status-rejected"><i class="fas fa-times-circle text-xs"></i> Ditolak</span>';
            return '';
        }

        function renderTabelProduk() {
            let tbody = document.getElementById('tbodyProduk');
            let filtered = getFilteredData(produkData);

            if (!filtered.length) {
                tbody.innerHTML =
                    `<tr><td colspan="9" class="text-center py-8 text-slate-400 text-sm">Tidak ada data</td></tr>`;
                return;
            }

            tbody.innerHTML = filtered.map(p => `
        <tr class="hover:bg-slate-50">
            <td class="px-3 py-2 text-center text-slate-500">${filtered.indexOf(p) + 1}</td>
            <td class="px-3 py-2 text-slate-600 whitespace-nowrap text-[11px]">${formatTanggalPendek(p.tanggal)}</td>
            <td class="px-3 py-2 font-medium text-slate-700 text-sm">${p.nama.length > 25 ? p.nama.substring(0,25)+'...' : p.nama}</td>
            <td class="px-3 py-2"><span class="px-2 py-0.5 text-[10px] rounded-full bg-indigo-100 text-indigo-700">${p.kategori}</span></td>
            <td class="px-3 py-2 text-right font-semibold text-emerald-600 text-sm">Rp ${p.harga}</td>
            <td class="px-3 py-2 text-center">${p.stok}</td>
            <td class="px-3 py-2 text-slate-600 text-[11px]">${p.diajukan_oleh.length > 15 ? p.diajukan_oleh.substring(0,15)+'...' : p.diajukan_oleh}</td>
            <td class="px-3 py-2 text-center">${getStatusBadge(p.status)}</td>
            <td class="px-3 py-2 text-center">
                ${p.status === 'pending' ? `
                                <div class="flex gap-1 justify-center">
                                    <button onclick="showApproveModal('produk',${p.id},'${p.nama.replace(/'/g,"\\'")}')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-2 py-1 rounded text-[10px] font-medium">
                                        <i class="fas fa-check text-[9px]"></i>
                                    </button>
                                    <button onclick="showRejectModal('produk',${p.id},'${p.nama.replace(/'/g,"\\'")}')" class="bg-rose-500 hover:bg-rose-600 text-white px-2 py-1 rounded text-[10px] font-medium">
                                        <i class="fas fa-times text-[9px]"></i>
                                    </button>
                                </div>
                            ` : '<span class="text-slate-400 text-xs">-</span>'}
            </td>
        </tr>
    `).join('');
        }

        function formatTanggalPendek(dateString) {
            if (!dateString) return '-';
            try {
                let date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } catch (e) {
                return dateString;
            }
        }

        function renderTabelTambahStok() {
            let tbody = document.getElementById('tbodyTambahStok');
            let filtered = getFilteredData(tambahStokData);
            if (!filtered.length) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-8 text-slate-400">Tidak ada data</td></tr>';
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
                    <td class="px-5 py-3">${item.requester_name}</td>
                    <td class="px-5 py-3">${getStatusBadge(item.status)}</td>
                    <td class="px-5 py-3 text-center">${item.status === 'pending' ? `<div class="flex gap-2 justify-center"><button onclick="showApproveModal('tambah_stok',${item.id},'${item.produk_nama.replace(/'/g,"\\'")}')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs">Setuju</button><button onclick="showRejectModal('tambah_stok',${item.id},'${item.produk_nama.replace(/'/g,"\\'")}')" class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs">Tolak</button></div>` : '<span class="text-slate-400">-</span>'}</td>
                </tr>`).join('');
        }

        function renderTabelPengiriman() {
            let tbody = document.getElementById('tbodyPengiriman');
            let filtered = getFilteredData(pengirimanData);
            if (!filtered.length) {
                tbody.innerHTML =
                    '<tr><td colspan="10" class="text-center py-8 text-slate-400">Tidak ada data pengiriman</td></tr>';
                return;
            }
            tbody.innerHTML = filtered.map(item => {
                const stokCukup = (item.stok_gudang_sebelum || 0) >= item.jumlah && (item.stok_gudang_sebelum ||
                    0) > 0;
                const approveDisabled = !stokCukup;
                return `
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-3 text-xs">${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                        <td class="px-3 py-3 font-mono text-xs font-semibold text-indigo-600">${item.kode}</td>
                        <td class="px-3 py-3 font-medium text-sm">${item.produk_nama.length > 25 ? item.produk_nama.substring(0,25)+'...' : item.produk_nama}</td>
                        <td class="px-3 py-3 text-red-600 font-semibold text-center">-${item.jumlah}</td>
                        <td class="px-3 py-3 text-center ${stokCukup ? 'text-green-600' : 'text-red-600'}">${item.stok_gudang_sebelum || 0} pcs</td>
                        <td class="px-3 py-3 text-center">${stokCukup ? '<span class="text-green-600 text-xs">Tersedia</span>' : '<span class="text-red-600 text-xs">Stok Kurang</span>'}</td>
                        <td class="px-3 py-3 text-xs">${item.tujuan_toko || '-'}</td>
                        <td class="px-3 py-3 text-xs">${item.requester_name}</td>
                        <td class="px-3 py-3">${getStatusBadge(item.status)}</td>
                        <td class="px-3 py-3 text-center">
                            ${(item.status === 'pending' || item.status === 'waiting_owner') ? 
                                `<div class="flex gap-2 justify-center">
                                                        <button ${approveDisabled ? 'disabled' : `onclick="showApproveModal('pengiriman',${item.id},'${item.produk_nama.replace(/'/g,"\\'")}')"`} class="${approveDisabled ? 'bg-gray-300 cursor-not-allowed btn-disabled' : 'bg-emerald-500 hover:bg-emerald-600'} text-white px-3 py-1.5 rounded-lg text-xs">
                                                            <i class="fas fa-check text-xs mr-1"></i> Setuju
                                                        </button>
                                                        <button onclick="showRejectModal('pengiriman',${item.id},'${item.produk_nama.replace(/'/g,"\\'")}')" class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs">
                                                            <i class="fas fa-times text-xs mr-1"></i> Tolak
                                                        </button>
                                                    </div>` : '<span class="text-slate-400 text-xs">-</span>'
                            }
                        </td>
                    </tr>`;
            }).join('');
        }

        // 🔥 RENDER TABEL PENYESUAIAN DENGAN VALIDASI STOK
        function renderTabelPenyesuaian() {
            let tbody = document.getElementById('tbodyPenyesuaian');
            let filtered = getFilteredData(penyesuaianData);
            if (!filtered.length) {
                tbody.innerHTML =
                    '<td><td colspan="11" class="text-center py-8 text-slate-400">Tidak ada data penyesuaian stok</td></tr>';
                return;
            }
            tbody.innerHTML = filtered.map(item => {
                const isMinus = item.perubahan < 0;
                const stokCukup = !isMinus || (item.stok_sebelum >= Math.abs(item.perubahan));
                const approveDisabled = isMinus && !stokCukup;
                const stockStatusText = isMinus ? (stokCukup ? '✅ Stok Cukup' :
                    `❌ Stok Kurang (${item.stok_sebelum}/${Math.abs(item.perubahan)})`) : '➕ Penambahan Stok';
                const stockStatusColor = isMinus ? (stokCukup ? 'text-green-600' : 'text-red-600') :
                    'text-blue-600';

                return `
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-xs">${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">${item.kode}</td>
                        <td class="px-4 py-3 font-medium text-sm">${item.produk_nama.length > 25 ? item.produk_nama.substring(0,25)+'...' : item.produk_nama}</td>
                        <td class="px-4 py-3 text-center">${item.stok_sebelum}</td>
                        <td class="px-4 py-3 text-center font-semibold ${item.perubahan >= 0 ? 'text-green-600' : 'text-red-600'}">${item.stok_sesudah}</td>
                        <td class="px-4 py-3 text-center ${item.perubahan >= 0 ? 'text-green-600' : 'text-red-600'}">${item.perubahan >= 0 ? '+' : ''}${item.perubahan}</td>
                        <td class="px-4 py-3 text-center ${stockStatusColor} font-semibold">${stockStatusText}</td>
                        <td class="px-4 py-3 text-xs">${item.alasan}</td>
                        <td class="px-4 py-3 text-xs">${item.requester_name}</td>
                        <td class="px-4 py-3">${getStatusBadge(item.status)}</td>
                        <td class="px-4 py-3 text-center">
                            ${item.status === 'pending' ? 
                                `<div class="flex gap-2 justify-center">
                                                        <button ${approveDisabled ? 'disabled' : `onclick="showApproveModal('penyesuaian',${item.id},'${item.produk_nama.replace(/'/g,"\\'")}')"`} class="${approveDisabled ? 'bg-gray-300 cursor-not-allowed btn-disabled' : 'bg-emerald-500 hover:bg-emerald-600'} text-white px-3 py-1.5 rounded-lg text-xs">
                                                            <i class="fas fa-check text-xs mr-1"></i> Setuju
                                                        </button>
                                                        <button onclick="showRejectModal('penyesuaian',${item.id},'${item.produk_nama.replace(/'/g,"\\'")}')" class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs">
                                                            <i class="fas fa-times text-xs mr-1"></i> Tolak
                                                        </button>
                                                    </div>` : '<span class="text-slate-400 text-xs">-</span>'
                            }
                        </td>
                    </tr>`;
            }).join('');
        }

        function showToast(message, type = 'success') {
            let toast = document.getElementById('toastNotification');
            let toastIcon = document.getElementById('toastIcon');
            let toastMessage = document.getElementById('toastMessage');
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
            let url = '';
            if (currentItemType === 'produk') url = `/owner/stok/approval/produk/${currentItemId}/approve`;
            else if (currentItemType === 'tambah_stok') url = `/owner/stok/approval/tambah-stok/${currentItemId}/approve`;
            else if (currentItemType === 'pengiriman') url = `/owner/stok/approval/pengiriman/${currentItemId}/approve`;
            else if (currentItemType === 'penyesuaian') url = `/owner/stok/approval/penyesuaian/${currentItemId}/approve`;
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json()).then(data => {
                    closeApproveModal();
                    showToast(data.message, data.success ? 'success' : 'error');
                    if (data.success) setTimeout(() => location.reload(), 1500);
                })
                .catch(err => {
                    closeApproveModal();
                    showToast('Terjadi kesalahan', 'error');
                });
        }

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
            let alasan = document.getElementById('alasanTolak').value.trim();
            if (!alasan) {
                showToast('Alasan penolakan harus diisi!', 'warning');
                return;
            }
            let url = '';
            if (currentItemType === 'produk') url = `/owner/stok/approval/produk/${currentItemId}/reject`;
            else if (currentItemType === 'tambah_stok') url = `/owner/stok/approval/tambah-stok/${currentItemId}/reject`;
            else if (currentItemType === 'pengiriman') url = `/owner/stok/approval/pengiriman/${currentItemId}/reject`;
            else if (currentItemType === 'penyesuaian') url = `/owner/stok/approval/penyesuaian/${currentItemId}/reject`;
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
                .then(res => res.json()).then(data => {
                    closeRejectModal();
                    showToast(data.message, data.success ? 'success' : 'error');
                    if (data.success) setTimeout(() => location.reload(), 1500);
                })
                .catch(err => {
                    closeRejectModal();
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        document.getElementById('confirmApproveBtn').addEventListener('click', confirmApprove);
        document.getElementById('confirmRejectBtn').addEventListener('click', confirmReject);
        window.addEventListener('click', function(e) {
            if (e.target === document.getElementById('approveModal')) closeApproveModal();
            if (e.target === document.getElementById('rejectModal')) closeRejectModal();
        });
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });
    </script>
@endsection
