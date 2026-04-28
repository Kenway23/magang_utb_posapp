@extends('layouts.gudang')

@section('title', 'PROShop - Request Kirim Stok')
@section('page-title', 'Request Kirim Stok')
@section('page-subtitle', 'Kelola pengiriman stok dari gudang ke toko')

@section('content')
    <style>
        /* ==================== BUTTON STYLES ==================== */
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
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
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

        .status-waiting_owner {
            background: #e0e7ff;
            color: #4f46e5;
        }

        /* ==================== BADGE REQUEST KASIR ==================== */
        .badge-request-from-kasir {
            background: #f3e8ff;
            color: #9333ea;
            font-size: 9px;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
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

        /* ==================== TAB STYLES ==================== */
        .tab-active {
            background-color: #4f46e5;
            color: white;
        }

        .tab-inactive {
            background-color: #f1f5f9;
            color: #475569;
        }
    </style>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- ==================== TABS ==================== -->
        <div class="flex mb-6 border-b">
            <button onclick="setActiveTab('gudang')" id="tabGudangBtn"
                class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition tab-active">
                <i class="fas fa-warehouse mr-2"></i> Request dari Gudang
            </button>
            <button onclick="setActiveTab('kasir')" id="tabKasirBtn"
                class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition tab-inactive">
                <i class="fas fa-store mr-2"></i> Request dari Kasir
                <span class="ml-2 px-2 py-0.5 text-xs bg-purple-200 text-purple-700 rounded-full">
                    {{ $pendingKasirRequests ?? 0 }}
                </span>
            </button>
        </div>

        <!-- ==================== TOMBOL REQUEST BARU ==================== -->
        <div id="btnRequestContainer" class="flex justify-end mb-6">
            <button onclick="showTambahPengirimanModal()"
                class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Request Kirim Stok
            </button>
        </div>

        <!-- ==================== FILTER ==================== -->
        <div class="mb-6 flex flex-wrap gap-3">
            <select id="statusFilter" onchange="renderTable()"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="all">📋 Semua Status</option>
                <option value="pending">⏳ Menunggu</option>
                <option value="waiting_owner">👑 Menunggu Owner</option>
                <option value="approved">✅ Disetujui</option>
                <option value="rejected">❌ Ditolak</option>
            </select>
            <input type="text" id="searchInput" onkeyup="renderTable()" placeholder="🔍 Cari produk..."
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <!-- ==================== TABEL ==================== -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">No</th>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">Kode</th>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                        <th class="p-3 text-center text-xs font-semibold text-slate-500 uppercase">Jumlah</th>
                        <th class="p-3 text-center text-xs font-semibold text-slate-500 uppercase">Stok Gudang</th>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">Tujuan</th>
                        <th class="p-3 text-center text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="p-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pengirimanTableBody"></tbody>
            </table>
        </div>
    </div>

    <!-- ==================== MODAL TAMBAH/EDIT ==================== -->
    <div id="modalPengiriman" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-slate-800" id="modalTitle">
                        <i class="fas fa-paper-plane text-indigo-600 mr-2"></i>Request Kirim Stok
                    </h3>
                    <button onclick="closeModal('modalPengiriman')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form onsubmit="simpanPengiriman(event)">
                    <input type="hidden" id="edit_id" value="">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Produk <span
                                    class="text-red-500">*</span></label>
                            <select id="produk_id" required onchange="loadStokProduk()"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($produk as $p)
                                    <option value="{{ $p->produk_id }}" data-stok="{{ $p->stok_gudang }}"
                                        data-nama="{{ $p->nama_produk }}">
                                        {{ $p->nama_produk }} (Stok: {{ $p->stok_gudang }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Stok Gudang Saat Ini</label>
                            <input type="text" id="stok_saat_ini" readonly
                                class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Kirim <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="jumlah" required min="1" oninput="updateStokSesudah()"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <div id="stokWarning" class="hidden mt-1 text-xs text-red-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Jumlah melebihi stok yang tersedia!
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Stok Gudang Sesudah</label>
                            <input type="text" id="stok_sesudah" readonly
                                class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tujuan Toko</label>
                            <input type="text" id="tujuan_toko"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Contoh: Toko Pusat, Toko Cabang">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                            <textarea id="keterangan" rows="3"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Alasan pengiriman..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeModal('modalPengiriman')"
                            class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL DETAIL ==================== -->
    <div id="modalDetail" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-slate-800">
                        <i class="fas fa-info-circle text-indigo-600 mr-2"></i>Detail Pengiriman
                    </h3>
                    <button onclick="closeModal('modalDetail')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="detailContent" class="space-y-3"></div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeModal('modalDetail')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL KONFIRMASI HAPUS ==================== -->
    <div id="modalConfirmDelete" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Konfirmasi Hapus</h3>
                <p id="confirmDeleteMessage" class="text-slate-500 mb-6"></p>
                <div class="flex gap-3">
                    <button onclick="closeModal('modalConfirmDelete')"
                        class="flex-1 px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 transition">Batal</button>
                    <button id="confirmDeleteBtn"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL ALASAN DITOLAK ==================== -->
    <div id="modalAlasanDitolak" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Alasan Penolakan</h3>
                <p id="alasanDitolakText" class="text-slate-600 mb-4"></p>
                <div class="flex justify-end">
                    <button onclick="closeModal('modalAlasanDitolak')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== LOADING MODAL ==================== -->
    <div id="modalLoading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-xs w-full mx-4 p-6 text-center">
            <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-3">
            </div>
            <p id="loadingMessage" class="text-slate-600">Memproses...</p>
        </div>
    </div>

    <!-- ==================== TOAST NOTIFICATION ==================== -->
    <div id="toastNotification" class="toast-notification">
        <div class="flex items-center p-4">
            <div id="toastIcon" class="flex-shrink-0 mr-3"><i class="fas fa-check-circle text-xl"></i></div>
            <div class="flex-1">
                <p id="toastMessage" class="text-sm font-medium text-slate-800"></p>
            </div>
            <button onclick="hideToast()" class="flex-shrink-0 ml-3 text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        // ==================== DATA ====================
        let pengirimanDariGudang = @json($pengirimanDariGudang);
        let pengirimanDariKasir = @json($pengirimanDariKasir);
        let currentEditId = null;
        let deleteId = null;
        let activeTab = 'gudang';

        // ==================== TOAST ====================
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
            document.getElementById('toastNotification')?.classList.remove('show');
        }

        // ==================== LOADING ====================
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

        // ==================== TAB MANAGEMENT ====================
        function setActiveTab(tab) {
            activeTab = tab;

            const tabGudangBtn = document.getElementById('tabGudangBtn');
            const tabKasirBtn = document.getElementById('tabKasirBtn');
            const btnRequestContainer = document.getElementById('btnRequestContainer');

            if (tab === 'gudang') {
                tabGudangBtn.classList.add('tab-active');
                tabGudangBtn.classList.remove('tab-inactive');
                tabKasirBtn.classList.add('tab-inactive');
                tabKasirBtn.classList.remove('tab-active');
                btnRequestContainer.classList.remove('hidden');
            } else {
                tabKasirBtn.classList.add('tab-active');
                tabKasirBtn.classList.remove('tab-inactive');
                tabGudangBtn.classList.add('tab-inactive');
                tabGudangBtn.classList.remove('tab-active');
                btnRequestContainer.classList.add('hidden');
            }

            renderTable();
        }

        function getCurrentData() {
            return activeTab === 'gudang' ? pengirimanDariGudang : pengirimanDariKasir;
        }

        // ==================== RENDER TABLE ====================
        function renderTable() {
            const tbody = document.getElementById('pengirimanTableBody');
            const statusFilter = document.getElementById('statusFilter')?.value || 'all';
            const search = document.getElementById('searchInput')?.value.toLowerCase() || '';

            let data = getCurrentData();

            let filtered = data.filter(item => {
                const matchStatus = statusFilter === 'all' || item.status === statusFilter;
                const matchSearch = item.produk?.nama_produk?.toLowerCase().includes(search) || false;
                return matchStatus && matchSearch;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-8 text-slate-400">
                            <i class="fas fa-inbox text-4xl mb-2 block"></i>
                            Tidak ada data pengiriman
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filtered.map((item, idx) => {
                // ==================== STATUS BADGE ====================
                let statusBadge = '',
                    statusText = '';

                if (item.status === 'pending') {
                    statusBadge = 'status-pending';
                    statusText = '<i class="fas fa-clock mr-1"></i> Menunggu';
                } else if (item.status === 'waiting_owner') {
                    statusBadge = 'status-waiting_owner';
                    statusText = '<i class="fas fa-crown mr-1"></i> Menunggu Owner';
                } else if (item.status === 'approved') {
                    statusBadge = 'status-approved';
                    statusText = '<i class="fas fa-check-circle mr-1"></i> Disetujui';
                } else {
                    statusBadge = 'status-rejected';
                    statusText = '<i class="fas fa-times-circle mr-1"></i> Ditolak';
                }

                // ==================== IDENTIFIKASI ASAL REQUEST ====================
                // Request dari kasir: requested_by ADA dan TIDAK SAMA dengan ID gudang
                const isFromKasir = (item.requested_by && item.requested_by !== {{ Auth::id() }});

                // ==================== JUMLAH (POSITIF/NEGATIF) ====================
                const jumlahDisplay = isFromKasir ?
                    `<span class="text-emerald-600 font-semibold">+${item.jumlah} pcs</span>` :
                    `<span class="text-red-600 font-semibold">-${item.jumlah} pcs</span>`;

                // ==================== AKSI BUTTONS ====================
                let actionButtons = '';

                if (item.status === 'pending' && !isFromKasir) {
                    actionButtons = `
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editPengiriman(${item.id})" class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="showDeleteConfirm(${item.id}, '${escapeHtml(item.produk?.nama_produk || 'Produk')}')" class="text-red-600 hover:text-red-800 transition" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <button onclick="viewDetail(${item.id})" class="text-indigo-600 hover:text-indigo-800 transition" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    `;
                } else if (item.status === 'rejected' && !isFromKasir) {
                    actionButtons = `
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editPengiriman(${item.id})" class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="showDeleteConfirm(${item.id}, '${escapeHtml(item.produk?.nama_produk || 'Produk')}')" class="text-red-600 hover:text-red-800 transition" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <button onclick="viewDetail(${item.id})" class="text-indigo-600 hover:text-indigo-800 transition" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${item.alasan_ditolak ? `
                                    <button onclick="lihatAlasan('${escapeHtml(item.alasan_ditolak)}')" class="text-red-500 hover:text-red-700 transition" title="Alasan Ditolak">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                ` : ''}
                        </div>
                    `;
                } else {
                    actionButtons = `
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="viewDetail(${item.id})" class="text-indigo-600 hover:text-indigo-800 transition" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${item.alasan_ditolak ? `
                                    <button onclick="lihatAlasan('${escapeHtml(item.alasan_ditolak)}')" class="text-red-500 hover:text-red-700 transition" title="Alasan Ditolak">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                ` : ''}
                        </div>
                    `;
                }

                return `
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                        <td class="p-3">
                            <div class="flex items-center gap-2">
                                <span>${idx + 1}</span>
                                ${isFromKasir ? `
                                        <span class="badge-request-from-kasir">
                                            <i class="fas fa-store mr-1"></i>Request Kasir
                                        </span>
                                    ` : ''}
                            </div>
                        </td>
                        <td class="p-3 font-mono text-xs font-semibold text-indigo-600">${item.kode_pengiriman}</td>
                        <td class="p-3 text-slate-600 whitespace-nowrap">${formatDate(item.created_at)}</td>
                        <td class="p-3 font-medium text-slate-800">${item.produk?.nama_produk || '-'}</td>
                        <td class="p-3 text-center">${jumlahDisplay}</td>
                        <td class="p-3 text-center">${item.stok_gudang_sesudah ?? item.stok_gudang_sebelum}</td>
                        <td class="p-3 text-slate-600">${item.tujuan_toko || (isFromKasir ? 'Permintaan Kasir' : '-')}</td>
                        <td class="p-3 text-center">
                            <span class="status-badge ${statusBadge}">${statusText}</span>
                        </td>
                        <td class="p-3 text-center">${actionButtons}</td>
                    </tr>
                `;
            }).join('');
        }

        // ==================== HELPER FUNCTIONS ====================
        function formatDate(date) {
            if (!date) return '-';
            const d = new Date(date);
            return d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function lihatAlasan(alasan) {
            document.getElementById('alasanDitolakText').innerHTML = alasan || 'Tidak ada alasan';
            showModal('modalAlasanDitolak');
        }

        // ==================== STOK FUNCTIONS ====================
        function loadStokProduk() {
            const select = document.getElementById('produk_id');
            const selectedOption = select.options[select.selectedIndex];
            const stok = selectedOption.getAttribute('data-stok') || 0;
            document.getElementById('stok_saat_ini').value = stok;
            document.getElementById('stok_saat_ini').setAttribute('data-value', stok);
            updateStokSesudah();
        }

        function updateStokSesudah() {
            const stokSaatIni = parseInt(document.getElementById('stok_saat_ini')?.getAttribute('data-value') || 0);
            const jumlah = parseInt(document.getElementById('jumlah')?.value) || 0;
            const stokSesudah = stokSaatIni - jumlah;
            document.getElementById('stok_sesudah').value = stokSesudah < 0 ? 0 : stokSesudah;

            const warning = document.getElementById('stokWarning');
            if (jumlah > stokSaatIni) warning.classList.remove('hidden');
            else warning.classList.add('hidden');
        }

        // ==================== CRUD ====================
        function showTambahPengirimanModal() {
            document.getElementById('modalTitle').innerHTML =
                '<i class="fas fa-paper-plane text-indigo-600 mr-2"></i>Request Kirim Stok';
            document.getElementById('edit_id').value = '';
            currentEditId = null;
            document.getElementById('produk_id').value = '';
            document.getElementById('stok_saat_ini').value = '';
            document.getElementById('stok_saat_ini').removeAttribute('data-value');
            document.getElementById('jumlah').value = '';
            document.getElementById('stok_sesudah').value = '';
            document.getElementById('tujuan_toko').value = '';
            document.getElementById('keterangan').value = '';
            document.getElementById('stokWarning').classList.add('hidden');
            showModal('modalPengiriman');
        }

        function editPengiriman(id) {
            const data = getCurrentData();
            const item = data.find(p => p.id === id);
            if (item) {
                document.getElementById('modalTitle').innerHTML =
                    '<i class="fas fa-edit text-indigo-600 mr-2"></i>Edit Request Kirim Stok';
                document.getElementById('edit_id').value = item.id;
                currentEditId = item.id;
                document.getElementById('produk_id').value = item.produk_id;
                loadStokProduk();
                document.getElementById('jumlah').value = item.jumlah;
                document.getElementById('tujuan_toko').value = item.tujuan_toko || '';
                document.getElementById('keterangan').value = item.keterangan || '';
                updateStokSesudah();
                showModal('modalPengiriman');
            }
        }

        function simpanPengiriman(event) {
            event.preventDefault();

            const id = document.getElementById('edit_id').value;
            const produkId = document.getElementById('produk_id').value;
            const jumlah = document.getElementById('jumlah').value;
            const tujuanToko = document.getElementById('tujuan_toko').value;
            const keterangan = document.getElementById('keterangan').value;

            if (!produkId) {
                showToast('Pilih produk terlebih dahulu!', 'warning');
                return;
            }
            if (!jumlah || jumlah < 1) {
                showToast('Jumlah kirim minimal 1!', 'warning');
                return;
            }

            const stokTersedia = parseInt(document.getElementById('stok_saat_ini')?.getAttribute('data-value') || 0);
            if (parseInt(jumlah) > stokTersedia) {
                showToast('Stok tidak mencukupi!', 'error');
                return;
            }

            showLoading('Menyimpan request...');
            const url = id ? `/gudang/pengiriman/${id}` : '/gudang/pengiriman';
            const method = id ? 'PUT' : 'POST';

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        _method: method,
                        produk_id: produkId,
                        jumlah: jumlah,
                        tujuan_toko: tujuanToko,
                        keterangan: keterangan
                    })
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showToast(data.message, 'success');
                        closeModal('modalPengiriman');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Gagal menyimpan request', 'error');
                    }
                })
                .catch(err => {
                    hideLoading();
                    console.error('Error:', err);
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        }

        function showDeleteConfirm(id, name) {
            deleteId = id;
            document.getElementById('confirmDeleteMessage').innerHTML =
                `Yakin ingin menghapus request pengiriman untuk produk "<strong>${name}</strong>"?`;
            showModal('modalConfirmDelete');
        }

        function hapusPengiriman() {
            if (!deleteId) return;
            showLoading('Menghapus request...');
            fetch(`/gudang/pengiriman/${deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showToast(data.message, 'success');
                        closeModal('modalConfirmDelete');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Gagal menghapus request', 'error');
                    }
                })
                .catch(err => {
                    hideLoading();
                    console.error('Error:', err);
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        }

        function viewDetail(id) {
            const data = getCurrentData();
            const item = data.find(p => p.id === id);
            if (item) {
                const isFromKasir = (item.requested_by && item.requested_by !== {{ Auth::id() }});

                let statusColor = item.status === 'pending' ? 'text-yellow-600' :
                    (item.status === 'waiting_owner' ? 'text-purple-600' :
                        (item.status === 'approved' ? 'text-green-600' : 'text-red-600'));
                let statusText = item.status === 'pending' ? 'Menunggu' :
                    (item.status === 'waiting_owner' ? 'Menunggu Owner' :
                        (item.status === 'approved' ? 'Disetujui' : 'Ditolak'));

                const requestFromInfo = isFromKasir ?
                    `<p class="border-t pt-2 mt-2"><strong><i class="fas fa-store mr-1 text-purple-600"></i> Request Dari:</strong> Kasir (${item.requester?.name || 'Kasir'})</p>` :
                    '';

                document.getElementById('detailContent').innerHTML = `
                    <div class="bg-slate-50 rounded-lg p-4 space-y-2 text-sm">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs text-slate-400">Kode Request</p>
                                <p class="font-mono text-sm font-semibold text-indigo-600">${item.kode_pengiriman}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Tanggal Request</p>
                                <p class="text-sm">${formatDate(item.created_at)}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Produk</p>
                                <p class="text-sm font-semibold">${item.produk?.nama_produk || '-'}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Jumlah</p>
                                <p class="text-sm ${isFromKasir ? 'text-emerald-600' : 'text-red-600'} font-semibold">
                                    ${isFromKasir ? '+' + item.jumlah : '-' + item.jumlah} pcs
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Stok Gudang Sebelum</p>
                                <p class="text-sm">${item.stok_gudang_sebelum} pcs</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Stok Gudang Sesudah</p>
                                <p class="text-sm">${item.stok_gudang_sesudah} pcs</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Stok Toko Sebelum</p>
                                <p class="text-sm">${item.stok_toko_sebelum} pcs</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Stok Toko Sesudah</p>
                                <p class="text-sm">${item.stok_toko_sesudah} pcs</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Tujuan Toko</p>
                                <p class="text-sm">${item.tujuan_toko || (isFromKasir ? 'Permintaan Kasir' : '-')}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Keterangan</p>
                                <p class="text-sm">${item.keterangan || '-'}</p>
                            </div>
                        </div>
                        ${requestFromInfo}
                        <div class="border-t pt-2">
                            <p class="text-xs text-slate-400">Status</p>
                            <p class="text-sm font-semibold ${statusColor}">${statusText}</p>
                        </div>
                        ${item.alasan_ditolak ? `
                                <div class="bg-red-50 rounded-lg p-3 border border-red-200">
                                    <p class="text-xs text-red-600 font-semibold"><i class="fas fa-times-circle mr-1"></i> Alasan Ditolak</p>
                                    <p class="text-sm text-red-700 mt-1">${escapeHtml(item.alasan_ditolak)}</p>
                                </div>
                            ` : ''}
                        ${item.approved_at ? `<p class="text-xs text-slate-400 pt-2"><strong>Diproses Pada:</strong> ${formatDate(item.approved_at)}</p>` : ''}
                        ${item.approver?.name ? `<p class="text-xs text-slate-400"><strong>Diproses Oleh:</strong> ${item.approver.name}</p>` : ''}
                    </div>
                `;
                showModal('modalDetail');
            }
        }

        // ==================== MODAL ====================
        function showModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // ==================== EVENT LISTENERS ====================
        document.getElementById('confirmDeleteBtn')?.addEventListener('click', hapusPengiriman);

        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('fixed')) {
                event.target.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
        });
    </script>
@endsection
