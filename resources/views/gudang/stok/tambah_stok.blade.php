@extends('layouts.gudang')

@section('title', 'PROShop - Request Tambah Stok')
@section('page-title', 'Request Tambah Stok')
@section('page-subtitle', 'Ajukan permintaan tambah stok ke Owner untuk persetujuan')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Tombol Request Baru -->
        <div class="flex justify-end mb-6">
            <button onclick="showTambahRequestModal()"
                class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Request Tambah Stok
            </button>
        </div>

        <!-- Filter -->
        <div class="mb-6 flex flex-wrap gap-3">
            <select id="statusFilter" onchange="filterTable()" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                <option value="all">Semua Status</option>
                <option value="pending">Menunggu</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari produk..."
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm w-64">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="requestTable">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">Tanggal Request</th>
                        <th class="p-3 text-left">Produk</th>
                        <th class="p-3 text-left">Stok Saat Ini</th>
                        <th class="p-3 text-left">Jumlah Request</th>
                        <th class="p-3 text-left">Stok Sesudah</th>
                        <th class="p-3 text-left">Supplier</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody id="requestTableBody">
                    <tr>
                        <td colspan="9" class="text-center py-8 text-slate-400">
                            <i class="fas fa-inbox text-4xl mb-2 block"></i>
                            Tidak ada data request
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH/EDIT REQUEST -->
    <div id="modalTambahRequest" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-slate-800" id="modalTitle">
                        <i class="fas fa-plus-circle text-indigo-600 mr-2"></i>Request Tambah Stok
                    </h3>
                    <button onclick="closeModal('modalTambahRequest')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form onsubmit="simpanRequest(event)">
                    <input type="hidden" id="edit_id" value="">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Produk <span
                                    class="text-red-500">*</span></label>
                            <select id="produk_id" required onchange="loadCurrentStock()"
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
                            <label class="block text-sm font-medium text-slate-700 mb-1">Stok Saat Ini</label>
                            <input type="text" id="stok_saat_ini" readonly
                                class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Tambah <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="jumlah_request" required min="1"
                                oninput="calculateStokSesudah()"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Stok Sesudah</label>
                            <input type="text" id="stok_sesudah" readonly
                                class="w-full px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Supplier</label>
                            <input type="text" id="supplier" class="w-full px-3 py-2 border border-slate-300 rounded-lg"
                                placeholder="Nama supplier (opsional)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                            <textarea id="keterangan" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg"
                                placeholder="Alasan request tambah stok..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeModal('modalTambahRequest')"
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
                <div class="flex justify-end gap-3 mt-6">
                    <button onclick="closeModal('modalDetail')"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Tutup</button>
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
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let requests = @json($requests);
        let produkList = @json($produk);
        let currentEditId = null;

        // ==================== RENDER TABEL ====================
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
                        <td colspan="9" class="text-center py-8 text-slate-400">
                            <i class="fas fa-inbox text-4xl mb-2 block"></i>
                            Tidak ada data request
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filtered.map((item, idx) => {
                let statusBadge = '',
                    statusText = '';
                if (item.status === 'pending') {
                    statusBadge = 'bg-yellow-100 text-yellow-700';
                    statusText = 'Menunggu';
                } else if (item.status === 'approved') {
                    statusBadge = 'bg-green-100 text-green-700';
                    statusText = 'Disetujui';
                } else {
                    statusBadge = 'bg-red-100 text-red-700';
                    statusText = 'Ditolak';
                }

                // Tentukan tombol aksi berdasarkan status
                let actionButtons = '';
                if (item.status === 'pending') {
                    actionButtons = `
                        <div class="flex gap-2">
                            <button onclick="editRequest(${item.id})" class="text-blue-600 hover:text-blue-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteRequest(${item.id})" class="text-red-600 hover:text-red-800" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <button onclick="viewDetail(${item.id})" class="text-indigo-600 hover:text-indigo-800" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    `;
                } else if (item.status === 'approved') {
                    actionButtons = `
                        <div class="flex gap-2">
                            <span class="text-gray-400" title="Tidak dapat diedit (sudah disetujui)">
                                <i class="fas fa-lock"></i>
                            </span>
                            <button onclick="viewDetail(${item.id})" class="text-indigo-600 hover:text-indigo-800" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    `;
                } else if (item.status === 'rejected') {
                    actionButtons = `
                        <div class="flex gap-2">
                            <button onclick="editRequest(${item.id})" class="text-blue-600 hover:text-blue-800" title="Edit (akan dikirim ulang)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteRequest(${item.id})" class="text-red-600 hover:text-red-800" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <button onclick="viewDetail(${item.id})" class="text-indigo-600 hover:text-indigo-800" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="lihatAlasanDitolak('${escapeHtml(item.alasan_ditolak)}')" class="text-red-500 hover:text-red-700" title="Lihat Alasan Ditolak">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    `;
                }

                return `
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="p-3">${idx + 1}</td>
                        <td class="p-3">${formatDate(item.created_at)}</td>
                        <td class="p-3 font-medium">${item.produk?.nama_produk || '-'}</td>
                        <td class="p-3">${item.stok_sebelum}</td>
                        <td class="p-3 text-green-600 font-semibold">+${item.jumlah_request}</td>
                        <td class="p-3">${item.stok_sesudah}</td>
                        <td class="p-3">${item.supplier || '-'}</td>
                        <td class="p-3"><span class="px-2 py-1 text-xs rounded-full ${statusBadge}">${statusText}</span></td>
                        <td class="p-3">${actionButtons}</td>
                    </tr>
                `;
            }).join('');
        }

        function formatDate(date) {
            if (!date) return '-';
            const d = new Date(date);
            return d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID');
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function filterTable() {
            renderTable();
        }

        // ==================== STOK FUNCTIONS ====================
        function loadCurrentStock() {
            const select = document.getElementById('produk_id');
            const selectedOption = select.options[select.selectedIndex];
            const stok = selectedOption.getAttribute('data-stok') || 0;
            document.getElementById('stok_saat_ini').value = stok;
            document.getElementById('stok_saat_ini').setAttribute('data-value', stok);
            calculateStokSesudah();
        }

        function calculateStokSesudah() {
            const stokSaatIni = parseInt(document.getElementById('stok_saat_ini')?.getAttribute('data-value') || 0);
            const jumlah = parseInt(document.getElementById('jumlah_request')?.value) || 0;
            document.getElementById('stok_sesudah').value = stokSaatIni + jumlah;
        }

        // ==================== CRUD REQUEST ====================
        function showTambahRequestModal() {
            document.getElementById('modalTitle').innerHTML =
                '<i class="fas fa-plus-circle text-indigo-600 mr-2"></i>Request Tambah Stok';
            document.getElementById('edit_id').value = '';
            currentEditId = null;
            document.getElementById('produk_id').value = '';
            document.getElementById('stok_saat_ini').value = '';
            document.getElementById('stok_saat_ini').removeAttribute('data-value');
            document.getElementById('jumlah_request').value = '';
            document.getElementById('stok_sesudah').value = '';
            document.getElementById('supplier').value = '';
            document.getElementById('keterangan').value = '';
            showModal('modalTambahRequest');
        }

        function editRequest(id) {
            const item = requests.find(r => r.id === id);
            if (item) {
                document.getElementById('modalTitle').innerHTML =
                    '<i class="fas fa-edit text-indigo-600 mr-2"></i>Edit Request Tambah Stok';
                document.getElementById('edit_id').value = item.id;
                currentEditId = item.id;

                // Isi form dengan data yang ada
                document.getElementById('produk_id').value = item.produk_id;
                // Trigger change untuk load stok
                const select = document.getElementById('produk_id');
                const selectedOption = select.options[select.selectedIndex];
                const stok = selectedOption.getAttribute('data-stok') || 0;
                document.getElementById('stok_saat_ini').value = stok;
                document.getElementById('stok_saat_ini').setAttribute('data-value', stok);
                document.getElementById('jumlah_request').value = item.jumlah_request;
                document.getElementById('stok_sesudah').value = item.stok_sesudah;
                document.getElementById('supplier').value = item.supplier || '';
                document.getElementById('keterangan').value = item.keterangan || '';

                showModal('modalTambahRequest');
            }
        }

        function simpanRequest(event) {
            event.preventDefault();

            const id = document.getElementById('edit_id').value;
            const produkId = document.getElementById('produk_id').value;
            const jumlah = document.getElementById('jumlah_request').value;
            const supplier = document.getElementById('supplier').value;
            const keterangan = document.getElementById('keterangan').value;

            if (!produkId) {
                showWarning('Pilih produk terlebih dahulu!');
                return;
            }

            if (!jumlah || jumlah < 1) {
                showWarning('Jumlah request minimal 1!');
                return;
            }

            showLoading('Menyimpan request...');

            const url = id ? `/gudang/tambah-stok/${id}` : '{{ route('gudang.tambah_stok.store') }}';
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
                        jumlah_request: jumlah,
                        supplier: supplier,
                        keterangan: keterangan
                    })
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showSuccess(data.message);
                        closeModal('modalTambahRequest');
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showError(data.message || 'Gagal menyimpan request');
                    }
                })
                .catch(err => {
                    hideLoading();
                    console.error('Error:', err);
                    showError('Terjadi kesalahan pada server');
                });
        }

        function deleteRequest(id) {
            const item = requests.find(r => r.id === id);
            showConfirmDelete(`Yakin ingin menghapus request tambah stok untuk produk "${item?.produk?.nama_produk}"?`,
                () => {
                    showLoading('Menghapus request...');

                    fetch(`/gudang/tambah-stok/${id}`, {
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
                                showSuccess(data.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                showError(data.message || 'Gagal menghapus request');
                            }
                        })
                        .catch(err => {
                            hideLoading();
                            console.error('Error:', err);
                            showError('Terjadi kesalahan pada server');
                        });
                });
        }

        function viewDetail(id) {
            const item = requests.find(r => r.id === id);
            if (item) {
                let statusText = '',
                    statusColor = '';
                if (item.status === 'pending') {
                    statusText = 'Menunggu';
                    statusColor = 'text-yellow-600';
                } else if (item.status === 'approved') {
                    statusText = 'Disetujui';
                    statusColor = 'text-green-600';
                } else {
                    statusText = 'Ditolak';
                    statusColor = 'text-red-600';
                }

                const detailHtml = `
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-sm"><strong>ID Request:</strong> #${item.id}</p>
                        <p class="text-sm mt-2"><strong>Produk:</strong> ${item.produk?.nama_produk || '-'}</p>
                        <p class="text-sm mt-2"><strong>Tanggal Request:</strong> ${formatDate(item.created_at)}</p>
                        <p class="text-sm mt-2"><strong>Stok Sebelum:</strong> ${item.stok_sebelum}</p>
                        <p class="text-sm mt-2"><strong>Jumlah Request:</strong> +${item.jumlah_request}</p>
                        <p class="text-sm mt-2"><strong>Stok Sesudah:</strong> ${item.stok_sesudah}</p>
                        <p class="text-sm mt-2"><strong>Supplier:</strong> ${item.supplier || '-'}</p>
                        <p class="text-sm mt-2"><strong>Keterangan:</strong> ${item.keterangan || '-'}</p>
                        <p class="text-sm mt-2"><strong>Status:</strong> <span class="${statusColor}">${statusText}</span></p>
                        ${item.alasan_ditolak ? `<p class="text-sm mt-2 text-red-600"><strong>Alasan Ditolak:</strong> ${item.alasan_ditolak}</p>` : ''}
                    </div>
                `;
                document.getElementById('detailContent').innerHTML = detailHtml;
                showModal('modalDetail');
            }
        }

        function lihatAlasanDitolak(alasan) {
            document.getElementById('alasanText').innerHTML = alasan || 'Tidak ada alasan';
            showModal('modalAlasanDitolak');
        }

        // ==================== NOTIFICATION FUNCTIONS ====================
        function showSuccess(message) {
            const modal = document.getElementById('modalSuccess');
            if (modal) {
                document.getElementById('successMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }, 2500);
            }
        }

        function showError(message) {
            const modal = document.getElementById('modalError');
            if (modal) {
                document.getElementById('errorMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function showWarning(message) {
            const modal = document.getElementById('modalWarning');
            if (modal) {
                document.getElementById('warningMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }, 2000);
            }
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
        window.onclick = function(event) {
            if (event.target.classList && event.target.classList.contains('fixed')) {
                event.target.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // ==================== INIT ====================
        document.addEventListener('DOMContentLoaded', function() {
            renderTable();
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
