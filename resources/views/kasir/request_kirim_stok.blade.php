@extends('layouts.kasir')

@section('title', 'Request Stok - PROShop Kasir')
@section('header-title', 'Request Stok ke Gudang')
@section('header-subtitle', 'Ajukan permintaan stok ke gudang jika stok toko menipis')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Tombol Request Baru -->
        <div class="flex justify-end mb-6">
            <button onclick="showTambahRequestModal()"
                class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Request Stok
            </button>
        </div>

        <!-- Filter -->
        <div class="mb-6 flex flex-wrap gap-3">
            <select id="statusFilter" onchange="renderTable()"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="all">Semua Status</option>
                <option value="pending">Menunggu Gudang</option>
                <option value="waiting_owner">Menunggu Owner</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
                <option value="completed">Selesai</option>
            </select>
            <input type="text" id="searchInput" onkeyup="renderTable()" placeholder="🔍 Cari produk..."
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">No</th>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">Kode Request</th>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">Stok Saat Ini</th>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">Jumlah Request</th>
                        <th class="p-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="p-3 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="requestTableBody" class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH/EDIT REQUEST -->
    <div id="modalRequest" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-slate-800" id="modalTitle">
                        <i class="fas fa-plus-circle text-indigo-600 mr-2"></i>Request Stok
                    </h3>
                    <button onclick="closeModal('modalRequest')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form onsubmit="simpanRequest(event)">
                    <input type="hidden" id="edit_id" value="">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Produk <span
                                    class="text-red-500">*</span></label>
                            <select id="produk_id" required onchange="loadStokProduk()"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($produk as $p)
                                    <option value="{{ $p->produk_id }}" data-stok="{{ $p->stok_toko }}"
                                        data-nama="{{ $p->nama_produk }}">
                                        {{ $p->nama_produk }} (Stok: {{ $p->stok_toko }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Stok Saat Ini</label>
                            <input type="text" id="stok_saat_ini" readonly
                                class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Request <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="jumlah" required min="1" oninput="validateStok()"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <div id="stokWarning" class="hidden mt-1 text-xs text-red-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Stok masih tersedia! Request hanya jika
                                stok menipis (≤10).
                            </div>
                            <div id="stokInfo" class="hidden mt-1 text-xs text-green-600">
                                <i class="fas fa-info-circle mr-1"></i> Stok menipis, silakan request.
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                            <textarea id="keterangan" rows="3"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Alasan request (opsional)..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeModal('modalRequest')"
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

    <!-- MODAL DETAIL REQUEST -->
    <div id="modalDetail" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-slate-800">
                        <i class="fas fa-info-circle text-indigo-600 mr-2"></i>Detail Request
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

    <!-- MODAL LIHAT ALASAN DITOLAK -->
    <div id="modalAlasanDitolak" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-red-600">
                        <i class="fas fa-times-circle mr-2"></i>Alasan Penolakan
                    </h3>
                    <button onclick="closeModal('modalAlasanDitolak')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="alasanDitolakContent" class="bg-red-50 rounded-lg p-4">
                    <p id="alasanText" class="text-red-700"></p>
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeModal('modalAlasanDitolak')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Tutup</button>
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
            <button onclick="hideToast()" class="flex-shrink-0 ml-3 text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <style>
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
    </style>

    <script>
        let requests = @json($requests);
        let currentEditId = null;

        function renderTable() {
            const tbody = document.getElementById('requestTableBody');
            const statusFilter = document.getElementById('statusFilter')?.value || 'all';
            const search = document.getElementById('searchInput')?.value.toLowerCase() || '';

            let filtered = requests.filter(item => {
                const matchStatus = statusFilter === 'all' || item.status === statusFilter;
                const matchSearch = item.produk?.nama_produk?.toLowerCase().includes(search) || false;
                return matchStatus && matchSearch;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-8 text-slate-400">
                        <i class="fas fa-inbox text-4xl mb-2 block"></i>
                        <p class="text-sm">Tidak ada request stok</p>
                    </td>
                </tr>
            `;
                return;
            }

            tbody.innerHTML = filtered.map((item, idx) => {
                // Status Badge Configuration
                let statusConfig = {
                    badge: '',
                    icon: '',
                    text: ''
                };

                switch (item.status) {
                    case 'pending':
                        statusConfig = {
                            badge: 'bg-yellow-100 text-yellow-700',
                            icon: 'fa-clock',
                            text: 'Menunggu Gudang'
                        };
                        break;
                    case 'waiting_owner':
                        statusConfig = {
                            badge: 'bg-purple-100 text-purple-700',
                            icon: 'fa-crown',
                            text: 'Menunggu Owner'
                        };
                        break;
                    case 'approved':
                        statusConfig = {
                            badge: 'bg-green-100 text-green-700',
                            icon: 'fa-check-circle',
                            text: 'Disetujui'
                        };
                        break;
                    case 'completed':
                        statusConfig = {
                            badge: 'bg-blue-100 text-blue-700',
                            icon: 'fa-boxes',
                            text: 'Selesai'
                        };
                        break;
                    case 'rejected':
                        statusConfig = {
                            badge: 'bg-red-100 text-red-700',
                            icon: 'fa-times-circle',
                            text: 'Ditolak'
                        };
                        break;
                    default:
                        statusConfig = {
                            badge: 'bg-gray-100 text-gray-700',
                            icon: 'fa-question',
                            text: 'Unknown'
                        };
                }

                const isStokMenipis = item.stok_toko_sebelum <= 10 && item.stok_toko_sebelum > 0;
                const isStokHabis = item.stok_toko_sebelum === 0;
                let stokClass = '';
                let stokAlert = '';

                if (isStokHabis) {
                    stokClass = 'text-red-600 font-bold';
                    stokAlert = '<span class="ml-1 text-red-500 text-xs">(Habis)</span>';
                } else if (isStokMenipis) {
                    stokClass = 'text-orange-600 font-bold';
                    stokAlert = '<span class="ml-1 text-orange-500 text-xs">(Menipis)</span>';
                }

                let actionButtons = '';
                if (item.status === 'pending') {
                    actionButtons = `
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="editRequest(${item.id})" class="text-blue-600 hover:text-blue-800 transition p-1" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="hapusRequest(${item.id})" class="text-red-600 hover:text-red-800 transition p-1" title="Hapus">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <button onclick="viewDetail(${item.id})" class="text-indigo-600 hover:text-indigo-800 transition p-1" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                `;
                } else {
                    actionButtons = `
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="viewDetail(${item.id})" class="text-indigo-600 hover:text-indigo-800 transition p-1" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${item.alasan_ditolak ? `
                                                                    <button onclick="lihatAlasan('${escapeHtml(item.alasan_ditolak)}')" class="text-red-500 hover:text-red-700 transition p-1" title="Alasan Ditolak">
                                                                        <i class="fas fa-info-circle"></i>
                                                                    </button>
                                                                    ` : ''}
                    </div>
                `;
                }

                return `
                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                    <td class="p-3 text-slate-600">${idx + 1}</td>
                    <td class="p-3 font-mono text-xs font-semibold text-indigo-600">${item.kode_pengiriman}</td>
                    <td class="p-3 text-slate-600">${formatDate(item.created_at)}</td>
                    <td class="p-3 font-medium text-slate-800">${item.produk?.nama_produk || '-'}</td>
                    <td class="p-3 ${stokClass}">${item.stok_toko_sebelum} pcs ${stokAlert}</td>
                    <td class="p-3 text-emerald-600 font-semibold">+${item.jumlah} pcs</td>
                    <td class="p-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full ${statusConfig.badge}">
                            <i class="fas ${statusConfig.icon} text-xs"></i> ${statusConfig.text}
                        </span>
                    </td>
                    <td class="p-3 text-center">${actionButtons}</td>
                </tr>
            `;
            }).join('');
        }

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

        function loadStokProduk() {
            const select = document.getElementById('produk_id');
            const selectedOption = select.options[select.selectedIndex];
            const stok = parseInt(selectedOption.getAttribute('data-stok') || 0);
            document.getElementById('stok_saat_ini').value = stok + ' pcs';
            document.getElementById('stok_saat_ini').setAttribute('data-value', stok);

            const warning = document.getElementById('stokWarning');
            const info = document.getElementById('stokInfo');

            if (stok > 10) {
                warning.classList.remove('hidden');
                info.classList.add('hidden');
            } else {
                warning.classList.add('hidden');
                info.classList.remove('hidden');
            }
        }

        function validateStok() {
            // Validation handled by loadStokProduk
        }

        function showTambahRequestModal() {
            document.getElementById('modalTitle').innerHTML =
                '<i class="fas fa-plus-circle text-indigo-600 mr-2"></i>Request Stok';
            document.getElementById('edit_id').value = '';
            currentEditId = null;
            document.getElementById('produk_id').value = '';
            document.getElementById('stok_saat_ini').value = '';
            document.getElementById('stok_saat_ini').removeAttribute('data-value');
            document.getElementById('jumlah').value = '';
            document.getElementById('keterangan').value = '';
            document.getElementById('stokWarning').classList.add('hidden');
            document.getElementById('stokInfo').classList.add('hidden');
            showModal('modalRequest');
        }

        function editRequest(id) {
            const item = requests.find(r => r.id === id);
            if (item) {
                // Cek apakah status masih pending
                if (item.status !== 'pending') {
                    showToast('Request sudah diproses, tidak dapat diedit!', 'warning');
                    return;
                }

                document.getElementById('modalTitle').innerHTML =
                    '<i class="fas fa-edit text-indigo-600 mr-2"></i>Edit Request Stok';
                document.getElementById('edit_id').value = item.id;
                currentEditId = item.id;

                // Set produk_id
                const produkSelect = document.getElementById('produk_id');
                produkSelect.value = item.produk_id;

                // Load stok produk
                loadStokProduk();

                // Set jumlah dan keterangan
                document.getElementById('jumlah').value = item.jumlah;
                document.getElementById('keterangan').value = item.keterangan || '';

                // Pastikan input jumlah dan tombol submit TIDAK disabled
                document.getElementById('jumlah').disabled = false;
                const submitBtn = document.querySelector('#modalRequest button[type="submit"]');
                if (submitBtn) submitBtn.disabled = false;

                showModal('modalRequest');
            }
        }

        function simpanRequest(event) {
            event.preventDefault();

            const id = document.getElementById('edit_id').value;
            const produkId = document.getElementById('produk_id').value;
            const jumlah = document.getElementById('jumlah').value;
            const keterangan = document.getElementById('keterangan').value;

            if (!produkId) {
                showToast('Pilih produk terlebih dahulu!', 'warning');
                return;
            }
            if (!jumlah || jumlah < 1) {
                showToast('Jumlah request minimal 1!', 'warning');
                return;
            }

            // HAPUS VALIDASI STOK GUDANG - Kasir tetap bisa request meski stok gudang 0

            // Optional: Tampilkan warning jika stok toko masih banyak (>10)
            const stokTokoValue = document.getElementById('stok_saat_ini')?.getAttribute('data-value');
            if (stokTokoValue && parseInt(stokTokoValue) > 10 && !id) {
                const confirmRequest = confirm('⚠️ Stok toko masih tersedia ' + stokTokoValue +
                    ' pcs (>10). Yakin tetap ingin request?');
                if (!confirmRequest) return;
            }

            showLoading('Menyimpan request...');

            const url = id ? `/kasir/request-kirim-stok/${id}` : '/kasir/request-kirim-stok';
            const method = id ? 'PUT' : 'POST';

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        _method: method,
                        produk_id: produkId,
                        jumlah: parseInt(jumlah),
                        keterangan: keterangan
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().catch(() => {
                            throw new Error(`HTTP ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showToast(data.message, 'success');
                        closeModal('modalRequest');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast(data.message || 'Gagal menyimpan request', 'error');
                    }
                })
                .catch(err => {
                    hideLoading();
                    console.error('Error:', err);
                    showToast('Terjadi kesalahan: ' + err.message, 'error');
                });
        }

        function hapusRequest(id) {
            if (confirm('Yakin ingin menghapus request ini?')) {
                showLoading('Menghapus request...');
                fetch(`/kasir/request-kirim-stok/${id}`, {
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
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast(data.message || 'Gagal hapus request', 'error');
                        }
                    })
                    .catch(err => {
                        hideLoading();
                        console.error('Error:', err);
                        showToast('Terjadi kesalahan pada server', 'error');
                    });
            }
        }

        function viewDetail(id) {
            const item = requests.find(r => r.id === id);
            if (item) {
                let statusText = '',
                    statusColor = '',
                    statusIcon = '';

                switch (item.status) {
                    case 'pending':
                        statusText = 'Menunggu Gudang';
                        statusColor = 'text-yellow-600';
                        statusIcon = 'fa-clock';
                        break;
                    case 'waiting_owner':
                        statusText = 'Menunggu Owner';
                        statusColor = 'text-purple-600';
                        statusIcon = 'fa-crown';
                        break;
                    case 'approved':
                        statusText = 'Disetujui';
                        statusColor = 'text-green-600';
                        statusIcon = 'fa-check-circle';
                        break;
                    case 'completed':
                        statusText = 'Selesai';
                        statusColor = 'text-blue-600';
                        statusIcon = 'fa-boxes';
                        break;
                    case 'rejected':
                        statusText = 'Ditolak';
                        statusColor = 'text-red-600';
                        statusIcon = 'fa-times-circle';
                        break;
                    default:
                        statusText = 'Unknown';
                        statusColor = 'text-gray-600';
                        statusIcon = 'fa-question';
                }

                document.getElementById('detailContent').innerHTML = `
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-400">Kode Request</p>
                            <p class="font-mono text-sm font-semibold text-indigo-600">${item.kode_pengiriman}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Tanggal Request</p>
                            <p class="text-sm">${formatDate(item.created_at)}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Produk</p>
                            <p class="text-sm font-semibold">${item.produk?.nama_produk || '-'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Stok Saat Ini</p>
                            <p class="text-sm ${item.stok_toko_sebelum <= 10 ? 'text-red-600 font-bold' : ''}">${item.stok_toko_sebelum} pcs</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Jumlah Request</p>
                            <p class="text-sm text-emerald-600 font-semibold">+${item.jumlah} pcs</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Stok Sesudah</p>
                            <p class="text-sm">${item.stok_toko_sesudah} pcs</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Keterangan</p>
                            <p class="text-sm">${item.keterangan || '-'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Diajukan Oleh</p>
                            <p class="text-sm">${item.requester?.name || 'Kasir'}</p>
                        </div>
                    </div>
                    <div class="pt-2 border-t">
                        <p class="text-xs text-gray-400">Status</p>
                        <p class="text-sm font-semibold ${statusColor}">
                            <i class="fas ${statusIcon} mr-1"></i> ${statusText}
                        </p>
                    </div>
                    ${item.alasan_ditolak ? `
                                                                <div class="bg-red-50 rounded-lg p-3 border border-red-200">
                                                                    <p class="text-xs text-red-600 font-semibold"><i class="fas fa-times-circle mr-1"></i> Alasan Ditolak</p>
                                                                    <p class="text-sm text-red-700 mt-1">${escapeHtml(item.alasan_ditolak)}</p>
                                                                </div>
                                                                ` : ''}
                    ${item.approved_at ? `
                                                                <div class="text-xs text-gray-400 pt-2">
                                                                    <p><strong>Diproses Pada:</strong> ${formatDate(item.approved_at)}</p>
                                                                    ${item.approver?.name ? `<p><strong>Diproses Oleh:</strong> ${item.approver.name}</p>` : ''}
                                                                </div>
                                                                ` : ''}
                </div>
            `;
                showModal('modalDetail');
            }
        }

        function lihatAlasan(alasan) {
            document.getElementById('alasanText').innerHTML = alasan || 'Tidak ada alasan';
            showModal('modalAlasanDitolak');
        }

        function hapusRequest(id) {
            if (confirm('Yakin ingin menghapus request ini?')) {
                showLoading('Menghapus request...');

                fetch(`/kasir/request-kirim-stok/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(`HTTP ${response.status}: ${text.substring(0, 100)}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        hideLoading();
                        if (data.success) {
                            showToast(data.message, 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showToast(data.message || 'Gagal menghapus request', 'error');
                        }
                    })
                    .catch(err => {
                        hideLoading();
                        console.error('Error:', err);
                        showToast('Terjadi kesalahan: ' + err.message, 'error');
                    });
            }
        }
        // Toast functions
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            if (!toast) {
                console.warn('Toast element not found');
                alert(message);
                return;
            }

            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');

            toast.className = 'toast-notification';

            if (type === 'success') {
                toast.classList.add('toast-success');
                if (toastIcon) toastIcon.innerHTML = '<i class="fas fa-check-circle text-emerald-500 text-xl"></i>';
            } else if (type === 'error') {
                toast.classList.add('toast-error');
                if (toastIcon) toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-rose-500 text-xl"></i>';
            } else if (type === 'warning') {
                toast.classList.add('toast-warning');
                if (toastIcon) toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-amber-500 text-xl"></i>';
            }

            if (toastMessage) toastMessage.innerHTML = message;
            toast.classList.add('show');

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        function hideToast() {
            const toast = document.getElementById('toastNotification');
            if (toast) toast.classList.remove('show');
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


        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList && event.target.classList.contains('fixed')) {
                event.target.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });

        document.addEventListener('DOMContentLoaded', () => renderTable());
    </script>

    <style>
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
@endsection
