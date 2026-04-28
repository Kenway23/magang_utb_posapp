@extends('layouts.gudang')

@section('title', 'PROShop - Penyesuaian Stok')
@section('page-title', 'Penyesuaian Stok')
@section('page-subtitle', 'Lakukan penyesuaian stok (plus/minus) karena rusak, kadaluarsa, atau retur')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-slate-800">
                <i class="fas fa-sliders-h text-indigo-600 mr-2"></i>Penyesuaian Stok
            </h3>
            <button onclick="showTambahPenyesuaianModal()"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Buat
            </button>
        </div>

        <!-- Info Box - lebih compact -->
        <div class="bg-amber-50 border-l-4 border-amber-500 p-3 mb-4 rounded-r-xl text-xs">
            <div class="flex items-start gap-2">
                <i class="fas fa-info-circle text-amber-500 text-sm mt-0.5"></i>
                <div>
                    <p class="text-xs text-amber-800 font-medium">Penyesuaian Stok:</p>
                    <ul class="text-xs text-amber-700 mt-0.5 flex flex-wrap gap-x-4">
                        <li>• Retur customer</li>
                        <li>• Barang rusak/kadaluarsa</li>
                        <li>• Koreksi fisik</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Filter - lebih compact -->
        <div class="mb-4 flex flex-wrap gap-2">
            <select id="statusFilterAdj" onchange="filterPenyesuaianTable()"
                class="px-2 py-1.5 border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="all">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="pending">Menunggu</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
            <input type="text" id="searchInputAdj" onkeyup="filterPenyesuaianTable()" placeholder="Cari produk..."
                class="px-2 py-1.5 border border-slate-300 rounded-lg text-xs w-48 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <!-- Tabel Compact - Tidak Perlu Di Scroll -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-2 text-center w-8">No</th>
                        <th class="p-2 text-left">Kode</th>
                        <th class="p-2 text-left">Tanggal</th>
                        <th class="p-2 text-left">Produk</th>
                        <th class="p-2 text-right w-16">Stok Lama</th>
                        <th class="p-2 text-right w-16">Stok Baru</th>
                        <th class="p-2 text-right w-16">Perubahan</th>
                        <th class="p-2 text-left">Alasan</th>
                        <th class="p-2 text-center w-20">Status</th>
                        <th class="p-2 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody id="penyesuaianTableBody">
                    @forelse($adjustments ?? [] as $index => $adj)
                        @php
                            $statusConfig = [
                                'draft' => ['bg' => 'bg-gray-100 text-gray-700', 'text' => 'Draft'],
                                'pending' => ['bg' => 'bg-yellow-100 text-yellow-700', 'text' => 'Menunggu'],
                                'approved' => ['bg' => 'bg-green-100 text-green-700', 'text' => 'Disetujui'],
                                'rejected' => ['bg' => 'bg-red-100 text-red-700', 'text' => 'Ditolak'],
                            ][$adj->status] ?? ['bg' => 'bg-gray-100 text-gray-700', 'text' => ucfirst($adj->status)];
                        @endphp
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-2 text-center">{{ $index + 1 }}</td>
                            <td class="p-2 font-mono text-xs font-semibold text-indigo-600 whitespace-nowrap">
                                {{ $adj->kode_adjustment }}</td>
                            <td class="p-2 text-slate-600 whitespace-nowrap">{{ $adj->created_at->format('d/m/y H:i') }}
                            </td>
                            <td class="p-2 font-medium text-slate-800 max-w-[150px] truncate">
                                {{ $adj->produk->nama_produk }}</td>
                            <td class="p-2 text-right">{{ number_format($adj->stok_sebelum) }}</td>
                            <td
                                class="p-2 text-right font-semibold {{ $adj->perubahan >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($adj->stok_sesudah) }}
                            </td>
                            <td class="p-2 text-right {{ $adj->perubahan >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $adj->perubahan >= 0 ? '+' : '' }}{{ number_format($adj->perubahan) }}
                            </td>
                            <td class="p-2 max-w-[120px] truncate">{{ $adj->alasan }}</td>
                            <td class="p-2 text-center">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] {{ $statusConfig['bg'] }} whitespace-nowrap">
                                    {{ $statusConfig['text'] }}
                                </span>
                            </td>
                            <td class="p-2">
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="viewDetail({{ $adj->id }})"
                                        class="text-indigo-600 hover:text-indigo-800 transition p-1" title="Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                    @if (in_array($adj->status, ['draft', 'pending']))
                                        <button onclick="editAdjustment({{ $adj->id }})"
                                            class="text-blue-600 hover:text-blue-800 transition p-1" title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                    @endif
                                    @if ($adj->status === 'draft')
                                        <button onclick="showSubmitConfirm({{ $adj->id }})"
                                            class="text-green-600 hover:text-green-800 transition p-1" title="Kirim">
                                            <i class="fas fa-paper-plane text-xs"></i>
                                        </button>
                                    @endif
                                    @if (in_array($adj->status, ['draft', 'pending']))
                                        <button onclick="showDeleteConfirm({{ $adj->id }})"
                                            class="text-red-600 hover:text-red-800 transition p-1" title="Hapus">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-6 text-slate-400 text-xs">
                                <i class="fas fa-inbox text-3xl mb-1 block"></i>
                                Tidak ada data penyesuaian stok
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah/Edit Penyesuaian (tetap sama) -->
    <div id="modalTambahPenyesuaian" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-slate-800" id="modalTitle">
                        <i class="fas fa-plus-circle text-indigo-600 mr-2"></i>Penyesuaian Stok
                    </h3>
                    <button onclick="closeModal('modalTambahPenyesuaian')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <form onsubmit="simpanPenyesuaian(event)">
                    <input type="hidden" id="edit_id" value="">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Pilih Produk <span
                                    class="text-red-500">*</span></label>
                            <select id="productSelectAdj" required onchange="loadCurrentStock()"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($produk ?? [] as $p)
                                    <option value="{{ $p->produk_id }}" data-stok="{{ $p->stok_gudang }}"
                                        data-nama="{{ $p->nama_produk }}">
                                        {{ $p->nama_produk }} ({{ number_format($p->stok_gudang) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Stok Saat Ini</label>
                            <input type="text" id="currentStock" readonly
                                class="w-full px-3 py-2 text-sm bg-slate-100 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Stok Baru <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="newStock" required min="0" oninput="calculateChange()"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Perubahan</label>
                            <input type="text" id="changeAmount" readonly
                                class="w-full px-3 py-2 text-sm bg-slate-100 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Alasan <span
                                    class="text-red-500">*</span></label>
                            <select id="reasonSelect" required
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Pilih Alasan --</option>
                                <option value="Barang Rusak">Barang Rusak</option>
                                <option value="Barang Kadaluarsa">Kadaluarsa</option>
                                <option value="Retur Customer">Retur Customer</option>
                                <option value="Koreksi Stok Fisik">Koreksi Fisik</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Keterangan</label>
                            <textarea id="keterangan" rows="2"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Opsional..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-5">
                        <button type="button" onclick="closeModal('modalTambahPenyesuaian')"
                            class="px-3 py-1.5 text-sm border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition">Batal</button>
                        <button type="submit"
                            class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-save mr-1 text-xs"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Penyesuaian -->
    <div id="modalDetailPenyesuaian" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-slate-800">Detail Penyesuaian</h3>
                    <button onclick="closeModal('modalDetailPenyesuaian')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div id="detailContent" class="space-y-2"></div>
                <div class="flex justify-end mt-5">
                    <button onclick="closeModal('modalDetailPenyesuaian')"
                        class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Submit -->
    <div id="modalSubmitConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-sm w-full mx-4 p-5">
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-paper-plane text-green-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Kirim Penyesuaian</h3>
                <p class="text-xs text-slate-500 mb-3">Yakin ingin mengirim penyesuaian ini untuk disetujui Owner?</p>
                <p class="text-[11px] text-amber-600 mb-4">Setelah dikirim, Anda masih bisa mengedit sampai Owner
                    memproses.</p>
                <div class="flex gap-2">
                    <button onclick="closeModal('modalSubmitConfirm')"
                        class="flex-1 px-3 py-1.5 text-sm border border-slate-300 rounded-lg hover:bg-slate-50 transition">Batal</button>
                    <button id="confirmSubmitBtn"
                        class="flex-1 px-3 py-1.5 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Kirim</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="modalDeleteConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl max-w-sm w-full mx-4 p-5">
            <div class="text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-trash-alt text-red-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Hapus Penyesuaian</h3>
                <p class="text-xs text-slate-500 mb-4" id="deleteConfirmMessage">Yakin ingin menghapus penyesuaian ini?
                    Data akan dihapus permanen.</p>
                <div class="flex gap-2">
                    <button onclick="closeModal('modalDeleteConfirm')"
                        class="flex-1 px-3 py-1.5 text-sm border border-slate-300 rounded-lg hover:bg-slate-50 transition">Batal</button>
                    <button id="confirmDeleteBtn"
                        class="flex-1 px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Loading -->
    <div id="modalLoading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-5 text-center min-w-[180px]">
            <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-2">
            </div>
            <p class="text-slate-600 text-xs" id="loadingMessage">Memproses...</p>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-5 right-5 z-50 space-y-2"></div>

    <style>
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

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-slide {
            animation: slideIn 0.3s ease-out;
        }

        /* Tabel Compact */
        .overflow-x-auto {
            overflow-x: auto;
        }

        /* Responsive: Untuk layar kecil, izinkan scroll horizontal */
        @media (max-width: 1024px) {
            .overflow-x-auto {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }
        }

        /* Untuk layar besar, tidak perlu scroll */
        @media (min-width: 1024px) {
            .overflow-x-auto {
                overflow-x: visible;
            }

            table {
                min-width: 100%;
            }
        }
    </style>

    <script>
        let csrfToken = '{{ csrf_token() }}';
        let currentEditId = null;
        let currentActionId = null;

        // Toast Notification
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');

            let bgColor, icon;
            if (type === 'success') {
                bgColor = 'border-green-500 bg-green-50';
                icon = '<i class="fas fa-check-circle text-green-500 text-base"></i>';
            } else if (type === 'error') {
                bgColor = 'border-red-500 bg-red-50';
                icon = '<i class="fas fa-exclamation-circle text-red-500 text-base"></i>';
            } else {
                bgColor = 'border-yellow-500 bg-yellow-50';
                icon = '<i class="fas fa-exclamation-triangle text-yellow-500 text-base"></i>';
            }

            toast.className = `bg-white rounded-lg shadow-lg p-3 min-w-[280px] border-l-4 ${bgColor} toast-slide`;
            toast.innerHTML = `
                <div class="flex items-center gap-2">
                    ${icon}
                    <p class="text-xs font-medium text-slate-700 flex-1">${message}</p>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            `;

            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        function showLoading(message = 'Memproses...') {
            const modal = document.getElementById('modalLoading');
            document.getElementById('loadingMessage').innerHTML = message;
            if (modal) modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function hideLoading() {
            const modal = document.getElementById('modalLoading');
            if (modal) modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showSubmitConfirm(id) {
            currentActionId = id;
            showModal('modalSubmitConfirm');
        }

        function showDeleteConfirm(id) {
            currentActionId = id;
            showModal('modalDeleteConfirm');
        }

        document.getElementById('confirmSubmitBtn')?.addEventListener('click', function() {
            closeModal('modalSubmitConfirm');
            if (currentActionId) executeSubmit(currentActionId);
        });

        document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
            closeModal('modalDeleteConfirm');
            if (currentActionId) executeDelete(currentActionId);
        });

        function loadCurrentStock() {
            const select = document.getElementById('productSelectAdj');
            const option = select.options[select.selectedIndex];
            const stok = option.getAttribute('data-stok') || 0;
            document.getElementById('currentStock').value = stok;
            document.getElementById('currentStock').setAttribute('data-value', stok);
            if (!currentEditId) document.getElementById('newStock').value = stok;
            calculateChange();
        }

        function calculateChange() {
            const currentStock = parseInt(document.getElementById('currentStock')?.getAttribute('data-value') || 0);
            const newStock = parseInt(document.getElementById('newStock')?.value) || 0;
            const change = newStock - currentStock;
            const changeText = (change >= 0 ? '+' : '') + change;
            const changeInput = document.getElementById('changeAmount');
            if (changeInput) {
                changeInput.value = changeText;
                changeInput.className = 'w-full px-3 py-2 text-sm bg-slate-100 border border-slate-300 rounded-lg ' +
                    (change > 0 ? 'text-green-600' : (change < 0 ? 'text-red-600' : ''));
            }
        }

        function showTambahPenyesuaianModal() {
            currentEditId = null;
            document.getElementById('edit_id').value = '';
            document.getElementById('modalTitle').innerHTML =
                '<i class="fas fa-plus-circle text-indigo-600 mr-2"></i>Penyesuaian Stok';
            document.getElementById('productSelectAdj').value = '';
            document.getElementById('currentStock').value = '';
            document.getElementById('currentStock').removeAttribute('data-value');
            document.getElementById('newStock').value = '';
            document.getElementById('changeAmount').value = '';
            document.getElementById('reasonSelect').value = '';
            document.getElementById('keterangan').value = '';
            showModal('modalTambahPenyesuaian');
        }

        function editAdjustment(id) {
            showLoading('Memuat data...');
            fetch(`/gudang/penyesuaian-stok/${id}/detail`)
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        const item = data.data;
                        currentEditId = item.id;
                        document.getElementById('edit_id').value = item.id;
                        document.getElementById('modalTitle').innerHTML =
                            '<i class="fas fa-edit text-indigo-600 mr-2"></i>Edit Penyesuaian Stok';
                        const select = document.getElementById('productSelectAdj');
                        select.value = item.produk_id;
                        loadCurrentStock();
                        document.getElementById('newStock').value = item.stok_sesudah;
                        document.getElementById('reasonSelect').value = item.alasan;
                        document.getElementById('keterangan').value = item.keterangan || '';
                        calculateChange();
                        showModal('modalTambahPenyesuaian');
                    } else {
                        showToast(data.message || 'Gagal memuat data', 'error');
                    }
                })
                .catch(err => {
                    hideLoading();
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        function simpanPenyesuaian(event) {
            event.preventDefault();
            const produkId = document.getElementById('productSelectAdj').value;
            const stokBaru = parseInt(document.getElementById('newStock').value);
            const alasan = document.getElementById('reasonSelect').value;
            const keterangan = document.getElementById('keterangan').value;

            if (!produkId) {
                showToast('Pilih produk!', 'warning');
                return;
            }
            if (isNaN(stokBaru) || stokBaru < 0) {
                showToast('Stok baru tidak valid!', 'warning');
                return;
            }
            if (!alasan) {
                showToast('Pilih alasan!', 'warning');
                return;
            }

            showLoading('Menyimpan...');
            const isEdit = currentEditId !== null;
            const url = isEdit ? `/gudang/penyesuaian-stok/${currentEditId}` :
                '{{ route('gudang.penyesuaian_stok.store') }}';
            const method = isEdit ? 'PUT' : 'POST';

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        _method: method,
                        produk_id: produkId,
                        stok_baru: stokBaru,
                        alasan: alasan,
                        keterangan: keterangan
                    })
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showToast(data.message, 'success');
                        closeModal('modalTambahPenyesuaian');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(err => {
                    hideLoading();
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        function executeSubmit(id) {
            showLoading('Mengirim ke Owner...');
            fetch(`/gudang/penyesuaian-stok/${id}/submit`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
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
                        showToast(data.message, 'error');
                    }
                })
                .catch(err => {
                    hideLoading();
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        function executeDelete(id) {
            showLoading('Menghapus...');
            fetch(`/gudang/penyesuaian-stok/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
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
                        showToast(data.message, 'error');
                    }
                })
                .catch(err => {
                    hideLoading();
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        function viewDetail(id) {
            showLoading('Memuat detail...');
            fetch(`/gudang/penyesuaian-stok/${id}/detail`)
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        const item = data.data;
                        const statusText = {
                            'draft': 'Draft',
                            'pending': 'Menunggu',
                            'approved': 'Disetujui',
                            'rejected': 'Ditolak'
                        } [item.status] || item.status;
                        const statusColor = {
                            'draft': 'text-gray-600',
                            'pending': 'text-yellow-600',
                            'approved': 'text-green-600',
                            'rejected': 'text-red-600'
                        } [item.status] || 'text-gray-600';
                        document.getElementById('detailContent').innerHTML = `
                            <div class="bg-gray-50 rounded-lg p-3 space-y-1 text-xs">
                                <div class="grid grid-cols-2 gap-2">
                                    <div><p class="text-gray-400">Kode</p><p class="font-mono font-semibold text-indigo-600">${escapeHtml(item.kode_adjustment)}</p></div>
                                    <div><p class="text-gray-400">Tanggal</p><p>${new Date(item.created_at).toLocaleString('id-ID')}</p></div>
                                    <div><p class="text-gray-400">Produk</p><p class="font-semibold">${escapeHtml(item.produk?.nama_produk) || '-'}</p></div>
                                    <div><p class="text-gray-400">Stok Lama</p><p>${item.stok_sebelum} pcs</p></div>
                                    <div><p class="text-gray-400">Stok Baru</p><p class="font-semibold ${item.perubahan >= 0 ? 'text-green-600' : 'text-red-600'}">${item.stok_sesudah} pcs</p></div>
                                    <div><p class="text-gray-400">Perubahan</p><p class="${item.perubahan >= 0 ? 'text-green-600' : 'text-red-600'}">${item.perubahan >= 0 ? '+' : ''}${item.perubahan} pcs</p></div>
                                    <div><p class="text-gray-400">Alasan</p><p>${escapeHtml(item.alasan)}</p></div>
                                    <div><p class="text-gray-400">Keterangan</p><p>${escapeHtml(item.keterangan) || '-'}</p></div>
                                </div>
                                <div class="border-t pt-2"><p class="text-gray-400">Status</p><p class="font-semibold ${statusColor}">${statusText}</p></div>
                                ${item.alasan_ditolak ? `<div class="bg-red-50 rounded p-2"><p class="text-red-600 text-xs">Alasan Ditolak: ${escapeHtml(item.alasan_ditolak)}</p></div>` : ''}
                                ${item.approved_by ? `<div class="text-gray-400 text-[10px] pt-1"><p>Diproses: ${new Date(item.approved_at).toLocaleString('id-ID')}</p><p>Oleh: ${escapeHtml(item.approver?.name) || '-'}</p></div>` : ''}
                            </div>
                        `;
                        showModal('modalDetailPenyesuaian');
                    } else {
                        showToast('Gagal memuat detail', 'error');
                    }
                })
                .catch(err => {
                    hideLoading();
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function filterPenyesuaianTable() {
            const status = document.getElementById('statusFilterAdj')?.value || 'all';
            const search = document.getElementById('searchInputAdj')?.value.toLowerCase() || '';
            const rows = document.querySelectorAll('#penyesuaianTableBody tr');
            rows.forEach(row => {
                if (row.cells.length < 9) return;
                const statusCell = row.cells[8]?.innerText.toLowerCase() || '';
                const productCell = row.cells[3]?.innerText.toLowerCase() || '';
                const statusMatch = status === 'all' || statusCell.includes(status);
                const searchMatch = search === '' || productCell.includes(search);
                row.style.display = statusMatch && searchMatch ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {});
    </script>
@endsection
