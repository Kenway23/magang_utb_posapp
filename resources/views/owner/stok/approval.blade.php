@extends('layouts.owner')

@section('title', 'Persetujuan Stok - PROShop')
@section('header-title', 'Persetujuan Stok')
@section('header-subtitle', 'Setujui atau tolak pengajuan (Produk Baru, Stok Masuk, Stok Keluar, Penyesuaian)')

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

        .toast-info {
            border-left: 4px solid #3b82f6;
        }

        /* Custom Scroll untuk tabel */
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
                    <button onclick="showJenisTab('produk')" id="tabJenisProduk"
                        class="tab-jenis py-3 px-2 text-sm font-medium transition tab-active">
                        <i class="fas fa-box text-indigo-500 mr-2"></i> Produk Baru
                        <span id="badgeProduk"
                            class="ml-2 px-1.5 py-0.5 text-xs bg-amber-500 text-white rounded-full">0</span>
                    </button>
                    <button onclick="showJenisTab('penerimaan')" id="tabJenisPenerimaan"
                        class="tab-jenis py-3 px-2 text-sm font-medium transition text-slate-500 hover:text-slate-700">
                        <i class="fas fa-arrow-down text-emerald-500 mr-2"></i> Penerimaan Stok
                        <span id="badgePenerimaan"
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

        <!-- SUB TABS (Filter Status: Semua/Pending/Approved/Rejected) -->
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

        <!-- TABEL PENERIMAAN STOK (placeholder) -->
        <div id="tablePenerimaan" class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200 mb-6 hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-arrow-down text-emerald-500 mr-2"></i> Daftar
                    Pengajuan Penerimaan Stok</h3>
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl Pengajuan
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Jumlah</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Supplier</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPenerimaan" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>

        <!-- TABEL PENGIRIMAN STOK (placeholder) -->
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
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl Pengajuan
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Jumlah</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tujuan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPengiriman" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>

        <!-- TABEL PENYESUAIAN STOK (placeholder) -->
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
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl Pengajuan
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Stok Lama</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Stok Baru</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Alasan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
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
                    <button onclick="closeApproveModal()"
                        class="flex-1 px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition">Batal</button>
                    <button id="confirmApproveBtn"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">Ya,
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
                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-rose-500"
                    placeholder="Masukkan alasan penolakan..."></textarea>
                <div class="flex gap-3 mt-6">
                    <button onclick="closeRejectModal()"
                        class="flex-1 px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition">Batal</button>
                    <button id="confirmRejectBtn"
                        class="flex-1 px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition">Kirim
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
        // 🔥 PERBAIKAN: Ambil data dari server dengan benar
        let produkData = [];

        // Data dari server (pastikan ini terisi)
        const serverData = @json($produkPending);

        console.log('Data dari server:', serverData); // Debug

        // Mapping data ke format yang digunakan
        if (serverData && serverData.length > 0) {
            produkData = serverData.map(p => {
                return {
                    id: p.produk_id,
                    nama: p.nama_produk,
                    kategori: p.kategori?.nama_kategori || 'Lainnya',
                    harga: new Intl.NumberFormat('id-ID').format(p.harga),
                    stok: p.stok_gudang,
                    diajukan_oleh: p.created_by?.name || 'Gudang',
                    tanggal: p.created_at,
                    status: p.status,
                    alasan_ditolak: p.alasan_ditolak || null
                };
            });
        }

        let currentJenisTab = 'produk';
        let currentStatusTab = 'semua';
        let currentItemId = null;
        let currentItemName = null;

        function updateStatistics() {
            const menunggu = produkData.filter(p => p.status === 'pending').length;
            const disetujui = produkData.filter(p => p.status === 'approved').length;
            const ditolak = produkData.filter(p => p.status === 'rejected').length;

            document.getElementById('totalMenunggu').innerText = menunggu;
            document.getElementById('totalDisetujui').innerText = disetujui;
            document.getElementById('totalDitolak').innerText = ditolak;
            document.getElementById('totalSemua').innerText = produkData.length;

            // Update badge
            document.getElementById('badgeProduk').innerText = menunggu;
        }

        function showJenisTab(jenis) {
            currentJenisTab = jenis;

            // Update tab style
            document.querySelectorAll('.tab-jenis').forEach(tab => {
                tab.classList.remove('tab-active');
                tab.classList.add('text-slate-500');
            });
            document.getElementById(`tabJenis${jenis.charAt(0).toUpperCase() + jenis.slice(1)}`).classList.add(
            'tab-active');
            document.getElementById(`tabJenis${jenis.charAt(0).toUpperCase() + jenis.slice(1)}`).classList.remove(
                'text-slate-500');

            // Show/hide tables
            document.getElementById('tableProduk').classList.add('hidden');
            document.getElementById('tablePenerimaan').classList.add('hidden');
            document.getElementById('tablePengiriman').classList.add('hidden');
            document.getElementById('tablePenyesuaian').classList.add('hidden');

            if (jenis === 'produk') {
                document.getElementById('tableProduk').classList.remove('hidden');
                renderTabelProduk();
            } else if (jenis === 'penerimaan') {
                document.getElementById('tablePenerimaan').classList.remove('hidden');
                renderTabelPenerimaan();
            } else if (jenis === 'pengiriman') {
                document.getElementById('tablePengiriman').classList.remove('hidden');
                renderTabelPengiriman();
            } else if (jenis === 'penyesuaian') {
                document.getElementById('tablePenyesuaian').classList.remove('hidden');
                renderTabelPenyesuaian();
            }
        }

        function showStatusTab(status) {
            currentStatusTab = status;

            // Update tab style
            document.querySelectorAll('.tab-status').forEach(tab => {
                tab.classList.remove('border-b-2', 'border-emerald-500', 'text-emerald-600');
                tab.classList.add('text-slate-500');
            });

            let tabId = 'tabStatus';
            if (status === 'semua') tabId += 'Semua';
            else if (status === 'pending') tabId += 'Pending';
            else if (status === 'approved') tabId += 'Approved';
            else if (status === 'rejected') tabId += 'Rejected';

            const activeTab = document.getElementById(tabId);
            if (activeTab) {
                activeTab.classList.add('border-b-2', 'border-emerald-500', 'text-emerald-600');
                activeTab.classList.remove('text-slate-500');
            }

            // Re-render tabel yang aktif
            if (currentJenisTab === 'produk') renderTabelProduk();
            else if (currentJenisTab === 'penerimaan') renderTabelPenerimaan();
            else if (currentJenisTab === 'pengiriman') renderTabelPengiriman();
            else if (currentJenisTab === 'penyesuaian') renderTabelPenyesuaian();
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

        function getActionButtons(item) {
            if (item.status !== 'pending') return '<span class="text-slate-400 text-xs">-</span>';
            return `
            <div class="flex items-center justify-center gap-2">
                <button onclick="showApproveModal(${item.id}, '${item.nama.replace(/'/g, "\\'")}')" class="action-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1">
                    <i class="fas fa-check text-xs"></i> Setuju
                </button>
                <button onclick="showRejectModal(${item.id}, '${item.nama.replace(/'/g, "\\'")}')" class="action-btn bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1">
                    <i class="fas fa-times text-xs"></i> Tolak
                </button>
            </div>
        `;
        }

        function renderTabelProduk() {
            const tbody = document.getElementById('tbodyProduk');
            if (!tbody) return;

            let filtered = getFilteredData(produkData);

            if (filtered.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="8" class="px-5 py-10 text-center text-slate-400"><i class="fas fa-inbox text-4xl mb-2 block"></i>Tidak ada data</td></tr>`;
                return;
            }

            tbody.innerHTML = filtered.map(p => {
                // Format tanggal
                let tanggal = '-';
                if (p.tanggal) {
                    const d = new Date(p.tanggal);
                    tanggal = d.toLocaleDateString('id-ID');
                }

                return `
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">${tanggal}</td>
                    <td class="px-5 py-3 font-medium">${p.nama}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">${p.kategori}</span></td>
                    <td class="px-5 py-3 font-semibold text-emerald-600">Rp ${p.harga}</td>
                    <td class="px-5 py-3">${p.stok} pcs</td>
                    <td class="px-5 py-3">${p.diajukan_oleh}</td>
                    <td class="px-5 py-3">${getStatusBadge(p.status)}</td>
                    <td class="px-5 py-3 text-center">${getActionButtons(p)}</td>
                </tr>
            `;
            }).join('');
        }

        function renderTabelPenerimaan() {
            const tbody = document.getElementById('tbodyPenerimaan');
            if (tbody) {
                tbody.innerHTML =
                    `<tr><td colspan="6" class="px-5 py-10 text-center text-slate-400"><i class="fas fa-inbox text-4xl mb-2 block"></i>Fitur penerimaan stok akan segera hadir</td></tr>`;
            }
        }

        function renderTabelPengiriman() {
            const tbody = document.getElementById('tbodyPengiriman');
            if (tbody) {
                tbody.innerHTML =
                    `<tr><td colspan="6" class="px-5 py-10 text-center text-slate-400"><i class="fas fa-inbox text-4xl mb-2 block"></i>Fitur pengiriman stok akan segera hadir</td></tr>`;
            }
        }

        function renderTabelPenyesuaian() {
            const tbody = document.getElementById('tbodyPenyesuaian');
            if (tbody) {
                tbody.innerHTML =
                    `<tr><td colspan="7" class="px-5 py-10 text-center text-slate-400"><i class="fas fa-inbox text-4xl mb-2 block"></i>Fitur penyesuaian stok akan segera hadir</td></tr>`;
            }
        }

        // Toast functions
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');
            if (!toast) return;

            toast.className = 'toast-notification';
            if (type === 'success') {
                toast.classList.add('toast-success');
                toastIcon.innerHTML = '<i class="fas fa-check-circle text-emerald-500 text-xl"></i>';
            } else if (type === 'error') {
                toast.classList.add('toast-error');
                toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-rose-500 text-xl"></i>';
            } else if (type === 'warning') {
                toast.classList.add('toast-warning');
                toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-amber-500 text-xl"></i>';
            }
            toastMessage.innerHTML = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        function hideToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) toast.classList.remove('show');
        }

        // Modal Approve
        function showApproveModal(id, name) {
            currentItemId = id;
            currentItemName = name;
            const approveItemName = document.getElementById('approveItemName');
            if (approveItemName) approveItemName.innerText = name;
            const modal = document.getElementById('approveModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeApproveModal() {
            const modal = document.getElementById('approveModal');
            if (modal) modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmApprove() {
            if (!currentItemId) return;
            fetch(`/owner/stok/approval/produk/${currentItemId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
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
        function showRejectModal(id, name) {
            currentItemId = id;
            currentItemName = name;
            const alasanTolak = document.getElementById('alasanTolak');
            if (alasanTolak) alasanTolak.value = '';
            const modal = document.getElementById('rejectModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            if (modal) modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmReject() {
            const alasan = document.getElementById('alasanTolak')?.value.trim();
            if (!alasan) {
                showToast('Alasan penolakan harus diisi!', 'warning');
                return;
            }
            fetch(`/owner/stok/approval/produk/${currentItemId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
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
        const confirmApproveBtn = document.getElementById('confirmApproveBtn');
        if (confirmApproveBtn) confirmApproveBtn.addEventListener('click', confirmApprove);

        const confirmRejectBtn = document.getElementById('confirmRejectBtn');
        if (confirmRejectBtn) confirmRejectBtn.addEventListener('click', confirmReject);

        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('approveModal')) closeApproveModal();
            if (event.target === document.getElementById('rejectModal')) closeRejectModal();
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Jumlah data produk:', produkData.length);
            console.log('Data produk:', produkData);
            updateStatistics();
            renderTabelProduk();
        });
    </script>
@endsection
