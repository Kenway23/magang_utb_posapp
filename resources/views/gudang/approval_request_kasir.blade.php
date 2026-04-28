@extends('layouts.gudang')

@section('title', 'Approval Request Stok - PROShop Gudang')
@section('page-title', 'Approval Request Stok dari Kasir')
@section('page-subtitle', 'Setujui atau tolak permintaan stok dari kasir')

@section('content')
    <style>
        /* ==================== ANIMASI & TRANSISI ==================== */
        .action-btn {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .action-btn.disabled,
        .action-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* ==================== STATUS BADGE ==================== */
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

        .status-waiting_owner {
            background-color: #e0e7ff;
            color: #4f46e5;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* ==================== TOAST NOTIFICATION ==================== */
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

        /* ==================== STOK STATUS ==================== */
        .stock-available {
            color: #059669;
            font-weight: 600;
        }

        .stock-insufficient {
            color: #dc2626;
            font-weight: 600;
        }

        .stock-low {
            color: #d97706;
            font-weight: 600;
        }

        /* ==================== TAMBAH STOK BUTTON ==================== */
        .btn-add-stock {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            color: #4f46e5;
            text-decoration: none;
            margin-top: 4px;
        }

        .btn-add-stock:hover {
            text-decoration: underline;
        }

        /* ==================== MODAL ANIMATION ==================== */
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

        /* ==================== REFRESH BUTTON ==================== */
        .refresh-btn {
            transition: all 0.2s ease;
        }

        .refresh-btn:hover {
            transform: rotate(180deg);
        }
    </style>

    <div class="space-y-6">
        <!-- ==================== STATISTIK CARD ==================== -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-amber-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-xs font-medium">Menunggu</p>
                        <p class="text-2xl font-bold text-amber-600" id="statMenunggu">0</p>
                    </div>
                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-amber-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-xs font-medium">Menunggu Owner</p>
                        <p class="text-2xl font-bold text-indigo-600" id="statWaitingOwner">0</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-emerald-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-xs font-medium">Disetujui</p>
                        <p class="text-2xl font-bold text-emerald-600" id="statApproved">0</p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-rose-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 text-xs font-medium">Ditolak</p>
                        <p class="text-2xl font-bold text-rose-600" id="statRejected">0</p>
                    </div>
                    <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-rose-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TABEL REQUEST ==================== -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-indigo-500 text-lg"></i>
                        <h3 class="font-semibold text-slate-800">Daftar Request Stok dari Kasir</h3>
                    </div>
                    <button onclick="loadData()" class="refresh-btn text-indigo-600 hover:text-indigo-800 transition"
                        title="Refresh Data">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 uppercase w-10">#</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase">Kode</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 uppercase">Stok Toko
                            </th>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 uppercase">Request</th>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 uppercase">Stok Gudang
                            </th>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 uppercase">Ketersediaan
                            </th>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="requestTableBody">
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-400">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i>
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL KONFIRMASI SETUJU ==================== -->
    <div id="approveModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-emerald-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Konfirmasi Persetujuan</h3>
                <p class="text-slate-500 mb-6">
                    Apakah Anda yakin ingin menyetujui request stok untuk produk
                    <span id="approveProductName" class="font-semibold text-slate-700"></span>?
                </p>
                <div class="flex gap-3">
                    <button onclick="closeApproveModal()" class="flex-1 px-4 py-2 border rounded-lg hover:bg-slate-50">
                        Batal
                    </button>
                    <button id="confirmApproveBtn"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Ya, Setujui
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL PENOLAKAN ==================== -->
    <div id="rejectModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-times-circle text-rose-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Alasan Penolakan</h3>
                <p class="text-slate-500 text-sm mb-4">Silakan berikan alasan mengapa request ini ditolak</p>
                <textarea id="alasanTolak" rows="4"
                    class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-rose-500"
                    placeholder="Masukkan alasan penolakan..."></textarea>
                <div class="flex gap-3 mt-6">
                    <button onclick="closeRejectModal()" class="flex-1 px-4 py-2 border rounded-lg hover:bg-slate-50">
                        Batal
                    </button>
                    <button id="confirmRejectBtn"
                        class="flex-1 px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">
                        Kirim Penolakan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL DETAIL ==================== -->
    <div id="detailModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">
                    <i class="fas fa-info-circle mr-2"></i>Detail Request
                </h3>
                <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="detailContent" class="space-y-3"></div>
            <div class="flex justify-end mt-6">
                <button onclick="closeDetailModal()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL STOK TIDAK CUKUP ==================== -->
    <div id="stokKurangModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Stok Gudang Tidak Cukup</h3>
                <p class="text-slate-500 mb-4" id="stokKurangMessage"></p>
                <div class="flex gap-3">
                    <button onclick="closeStokKurangModal()" class="flex-1 px-4 py-2 border rounded-lg hover:bg-slate-50">
                        Kembali
                    </button>
                    <a href="/gudang/tambah-stok"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Stok
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== TOAST NOTIFICATION ==================== -->
    <div id="toastNotification" class="toast-notification">
        <div class="flex items-center p-4">
            <div id="toastIcon" class="flex-shrink-0 mr-3">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div class="flex-1">
                <p id="toastMessage" class="text-sm font-medium text-slate-800"></p>
            </div>
            <button onclick="hideToast()" class="flex-shrink-0 ml-3 text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- ==================== JAVASCRIPT ==================== -->
    <script>
        // Data dari server
        let requests = [];
        let currentRequestId = null;
        let autoRefreshInterval = null;

        // ==================== LOAD DATA DARI SERVER ====================
        function loadData() {
            showToast('Mengambil data terbaru...', 'warning');

            fetch('/gudang/approval-request-kasir/data')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        requests = data.data;
                        renderTable();
                        updateStatistics();
                        showToast('Data berhasil diperbarui', 'success');
                        console.log('Data refreshed - Stok terbaru:', requests.map(r => ({
                            produk: r.produk?.nama_produk,
                            stok_gudang: r.stok_gudang_sebelum,
                            jumlah_request: r.jumlah
                        })));
                    } else {
                        showToast(data.message || 'Gagal memuat data', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error load data:', err);
                    showToast('Terjadi kesalahan saat memuat data', 'error');
                });
        }

        // ==================== RENDER TABEL ====================
        function renderTable() {
            const tbody = document.getElementById('requestTableBody');
            if (!tbody) return;

            if (requests.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-8 text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-2 block"></i>
                    Tidak ada request stok dari kasir
                </td></tr>`;
                return;
            }

            tbody.innerHTML = requests.map((item, idx) => {
                // Status Stok
                const stokCukup = item.stok_gudang_sebelum >= item.jumlah;
                const stokHabis = item.stok_gudang_sebelum <= 0;
                const stokKurang = !stokCukup && item.stok_gudang_sebelum > 0;

                const stokClass = stokCukup ? 'stock-available' : (stokHabis ? 'stock-insufficient' : 'stock-low');
                const stockStatusText = stokCukup ? 'Tersedia' : (stokHabis ? 'Stok Habis!' : 'Stok Kurang');
                const stockStatusIcon = stokCukup ? 'fa-check-circle' : (stokHabis ? 'fa-times-circle' :
                    'fa-exclamation-triangle');
                const stockStatusColor = stokCukup ? 'bg-green-100 text-green-700' : (stokHabis ?
                    'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700');

                // Status Badge
                let statusBadge = '';
                if (item.status === 'pending') {
                    statusBadge =
                        '<span class="status-badge status-pending text-[10px] px-2 py-0.5"><i class="fas fa-clock text-[8px]"></i> Menunggu</span>';
                } else if (item.status === 'waiting_owner') {
                    statusBadge =
                        '<span class="status-waiting_owner"><i class="fas fa-hourglass-half text-[8px]"></i> Menunggu Owner</span>';
                } else if (item.status === 'approved') {
                    statusBadge =
                        '<span class="status-badge status-approved text-[10px] px-2 py-0.5"><i class="fas fa-check-circle text-[8px]"></i> Disetujui</span>';
                } else {
                    statusBadge =
                        '<span class="status-badge status-rejected text-[10px] px-2 py-0.5"><i class="fas fa-times-circle text-[8px]"></i> Ditolak</span>';
                }

                // Aksi Buttons
                let actionButtons = '';
                if (item.status === 'pending') {
                    actionButtons = `
                        <div class="flex flex-col items-center gap-1">
                            <div class="flex items-center justify-center gap-1">
                                ${stokCukup ? 
                                    `<button onclick="showApproveModal(${item.id}, '${(item.produk?.nama_produk || '').replace(/'/g, "\\'")}')" 
                                            class="action-btn bg-emerald-500 hover:bg-emerald-600 text-white px-2 py-1 rounded text-[10px]" title="Setujui">
                                            <i class="fas fa-check text-[8px]"></i>
                                        </button>` :
                                    `<button disabled 
                                            class="action-btn disabled bg-gray-300 text-white px-2 py-1 rounded text-[10px]" 
                                            title="Stok gudang tidak mencukupi! Silakan tambah stok terlebih dahulu.">
                                            <i class="fas fa-check text-[8px]"></i>
                                        </button>`
                                }
                                <button onclick="showRejectModal(${item.id}, '${(item.produk?.nama_produk || '').replace(/'/g, "\\'")}')" 
                                    class="action-btn bg-rose-500 hover:bg-rose-600 text-white px-2 py-1 rounded text-[10px]" title="Tolak">
                                    <i class="fas fa-times text-[8px]"></i>
                                </button>
                                <button onclick="viewDetail(${item.id})" 
                                    class="action-btn bg-indigo-500 hover:bg-indigo-600 text-white px-2 py-1 rounded text-[10px]" title="Detail">
                                    <i class="fas fa-eye text-[8px]"></i>
                                </button>
                            </div>
                            ${!stokCukup ? `<a href="/gudang/tambah-stok" class="btn-add-stock"><i class="fas fa-plus-circle text-[10px]"></i> Tambah Stok Sekarang</a>` : ''}
                        </div>
                    `;
                } else {
                    actionButtons =
                        `<button onclick="viewDetail(${item.id})" class="action-btn bg-indigo-500 hover:bg-indigo-600 text-white px-2 py-1 rounded text-[10px]" title="Detail"><i class="fas fa-eye text-[8px]"></i> Detail</button>`;
                }

                return `
                    <tr class="border-b hover:bg-slate-50">
                        <td class="px-3 py-2.5 text-center">${idx + 1}</td>
                        <td class="px-3 py-2.5 font-mono text-xs font-semibold text-indigo-600">${item.kode_pengiriman || '-'}</td>
                        <td class="px-3 py-2.5 font-medium text-sm truncate max-w-[150px]">${item.produk?.nama_produk || '-'}</td>
                        <td class="px-3 py-2.5 text-center text-sm ${item.stok_toko_sebelum <= 10 ? 'text-red-600 font-bold' : ''}">${item.stok_toko_sebelum || 0}</td>
                        <td class="px-3 py-2.5 text-center text-sm text-emerald-600 font-semibold">+${item.jumlah || 0}</td>
                        <td class="px-3 py-2.5 text-center text-sm ${stokClass}">${item.stok_gudang_sebelum || 0}</td>
                        <td class="px-3 py-2.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs ${stockStatusColor}">
                                <i class="fas ${stockStatusIcon} text-[10px]"></i> ${stockStatusText}
                            </span>
                        </td>
                        <td class="px-3 py-2.5 text-center">${statusBadge}</td>
                        <td class="px-3 py-2.5 text-center">${actionButtons}</td>
                    </tr>
                `;
            }).join('');
        }

        // ==================== UPDATE STATISTIK ====================
        function updateStatistics() {
            const menunggu = requests.filter(r => r.status === 'pending').length;
            const waitingOwner = requests.filter(r => r.status === 'waiting_owner').length;
            const approved = requests.filter(r => r.status === 'approved').length;
            const rejected = requests.filter(r => r.status === 'rejected').length;

            document.getElementById('statMenunggu').innerText = menunggu;
            document.getElementById('statWaitingOwner').innerText = waitingOwner;
            document.getElementById('statApproved').innerText = approved;
            document.getElementById('statRejected').innerText = rejected;
        }

        // ==================== TOAST FUNCTIONS ====================
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
            const toast = document.getElementById('toastNotification');
            if (toast) toast.classList.remove('show');
        }

        // ==================== MODAL STOK KURANG ====================
        function showStokKurangModal(productName, stokGudang, jumlahRequest) {
            const message =
                `Stok gudang untuk produk <strong>${productName}</strong> hanya <strong>${stokGudang} pcs</strong>, sedangkan request sebanyak <strong>${jumlahRequest} pcs</strong>.<br><br>Silakan tambah stok terlebih dahulu melalui menu "Request Tambah Stok".`;
            document.getElementById('stokKurangMessage').innerHTML = message;
            document.getElementById('stokKurangModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeStokKurangModal() {
            document.getElementById('stokKurangModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // ==================== MODAL SETUJU ====================
        function showApproveModal(id, name) {
            const request = requests.find(r => r.id === id);

            if (request) {
                const stokGudang = request.stok_gudang_sebelum;
                const jumlahRequest = request.jumlah;

                if (stokGudang < jumlahRequest) {
                    showStokKurangModal(name, stokGudang, jumlahRequest);
                    return;
                }
            }

            currentRequestId = id;
            document.getElementById('approveProductName').innerText = name;
            document.getElementById('approveModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmApprove() {
            if (!currentRequestId) return;

            fetch(`/gudang/approval-request-kasir/${currentRequestId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    closeApproveModal();
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => loadData(), 1500);
                    } else {
                        showToast(data.message || 'Gagal menyetujui request', 'error');
                    }
                })
                .catch(err => {
                    closeApproveModal();
                    console.error('Error:', err);
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        }

        // ==================== MODAL TOLAK ====================
        function showRejectModal(id, name) {
            currentRequestId = id;
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

            fetch(`/gudang/approval-request-kasir/${currentRequestId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                        setTimeout(() => loadData(), 1500);
                    } else {
                        showToast(data.message || 'Gagal menolak request', 'error');
                    }
                })
                .catch(err => {
                    closeRejectModal();
                    console.error('Error:', err);
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        }

        // ==================== MODAL DETAIL ====================
        function viewDetail(id) {
            fetch(`/gudang/approval-request-kasir/${id}/detail`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const item = data.data;
                        let statusText = '',
                            statusColor = '';

                        switch (item.status) {
                            case 'pending':
                                statusText = 'Menunggu';
                                statusColor = 'text-yellow-600';
                                break;
                            case 'waiting_owner':
                                statusText = 'Menunggu Owner';
                                statusColor = 'text-purple-600';
                                break;
                            case 'approved':
                                statusText = 'Disetujui';
                                statusColor = 'text-green-600';
                                break;
                            case 'rejected':
                                statusText = 'Ditolak';
                                statusColor = 'text-red-600';
                                break;
                            default:
                                statusText = item.status;
                                statusColor = 'text-gray-600';
                        }

                        const stokCukup = item.stok_gudang_sebelum >= item.jumlah;
                        const stokWarning = !stokCukup ?
                            '<p class="text-orange-600"><strong>⚠️ Peringatan:</strong> Stok gudang tidak mencukupi untuk request ini!</p>' :
                            '';

                        document.getElementById('detailContent').innerHTML = `
                            <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                                <p><strong>Kode Request:</strong> <span class="font-mono text-indigo-600">${item.kode_pengiriman}</span></p>
                                <p><strong>Produk:</strong> ${item.produk?.nama_produk || '-'}</p>
                                <p><strong>Stok Toko Saat Ini:</strong> ${item.stok_toko_sebelum} pcs</p>
                                <p><strong>Jumlah Request:</strong> <span class="text-emerald-600 font-semibold">+${item.jumlah} pcs</span></p>
                                <p><strong>Stok Gudang Saat Ini:</strong> <span class="${!stokCukup ? 'text-red-600 font-bold' : ''}">${item.stok_gudang_sebelum} pcs</span></p>
                                <p><strong>Keterangan:</strong> ${item.keterangan || '-'}</p>
                                <p><strong>Diajukan Oleh:</strong> ${item.requester?.name || 'Kasir'}</p>
                                <p><strong>Tanggal Request:</strong> ${new Date(item.created_at).toLocaleString('id-ID')}</p>
                                <p><strong>Status:</strong> <span class="${statusColor} font-semibold">${statusText}</span></p>
                                ${stokWarning}
                                ${item.alasan_ditolak ? `<p class="text-red-600"><strong><i class="fas fa-times-circle mr-1"></i> Alasan Ditolak:</strong> ${escapeHtml(item.alasan_ditolak)}</p>` : ''}
                                ${item.approved_at ? `<p class="text-gray-500 text-xs pt-2"><strong>Diproses Pada:</strong> ${new Date(item.approved_at).toLocaleString('id-ID')}</p>` : ''}
                            </div>
                        `;
                        document.getElementById('detailModal').classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    } else {
                        showToast('Gagal memuat detail request', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    showToast('Terjadi kesalahan saat memuat detail', 'error');
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ==================== AUTO REFRESH ====================
        function startAutoRefresh() {
            if (autoRefreshInterval) clearInterval(autoRefreshInterval);
            autoRefreshInterval = setInterval(() => {
                if (!document.hidden) {
                    loadData();
                }
            }, 30000);
        }

        // ==================== EVENT LISTENERS ====================
        document.getElementById('confirmApproveBtn').addEventListener('click', confirmApprove);
        document.getElementById('confirmRejectBtn').addEventListener('click', confirmReject);

        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('approveModal')) closeApproveModal();
            if (event.target === document.getElementById('rejectModal')) closeRejectModal();
            if (event.target === document.getElementById('detailModal')) closeDetailModal();
            if (event.target === document.getElementById('stokKurangModal')) closeStokKurangModal();
        });

        // ==================== INITIALIZE ====================
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            startAutoRefresh();
        });
    </script>
@endsection
