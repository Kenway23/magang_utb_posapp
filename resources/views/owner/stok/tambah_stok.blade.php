@extends('layouts.owner')

@section('title', 'Approval Tambah Stok - PROShop')

@section('header-title', 'Approval Tambah Stok')
@section('header-subtitle', 'Setujui atau tolak pengajuan Tambah stok dari gudang')

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-indigo-600"></i>
                    Daftar Pengajuan Tambah Stok
                </h3>
                <p class="text-sm text-slate-500 mt-1">Dari Staff Gudang</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl Pengajuan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl Terima</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableApprovalPenerimaan">
                        <tr class="text-center text-slate-500">
                            <td colspan="7" class="px-6 py-8">
                                <i class="fas fa-inbox text-4xl mb-2 block"></i>
                                Tidak ada pengajuan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
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

        function showTambahRequestModal() {
            document.getElementById('produk_id').value = '';
            document.getElementById('stok_saat_ini').value = '';
            document.getElementById('stok_saat_ini').removeAttribute('data-value');
            document.getElementById('jumlah_request').value = '';
            document.getElementById('stok_sesudah').value = '';
            document.getElementById('supplier').value = '';
            document.getElementById('keterangan').value = '';
            showModal('modalTambahRequest');
        }

        function simpanRequest(event) {
            event.preventDefault();

            const produkId = document.getElementById('produk_id').value;
            const jumlah = document.getElementById('jumlah_request').value;
            const supplier = document.getElementById('supplier').value;
            const keterangan = document.getElementById('keterangan').value;

            if (!produkId) {
                alert('Pilih produk terlebih dahulu!');
                return;
            }

            if (!jumlah || jumlah < 1) {
                alert('Jumlah request minimal 1!');
                return;
            }

            fetch('{{ route('gudang.stok.tambah_stok.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        produk_id: produkId,
                        jumlah_request: jumlah,
                        supplier: supplier,
                        keterangan: keterangan
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        closeModal('modalTambahRequest');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan: ' + err);
                });
        }

        function viewDetail(id) {
            const item = requests.find(r => r.id === id);
            if (item) {
                let statusText = '';
                let statusColor = '';
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

        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        // Ambil data dari localStorage (yg dikirim gudang)
        let pengajuanPenerimaan = JSON.parse(localStorage.getItem('pengajuanPenerimaan') || '[]');

        function renderApprovalPenerimaan() {
            const tbody = document.getElementById('tableApprovalPenerimaan');
            const pendingItems = pengajuanPenerimaan.filter(item => item.status === 'pending');

            if (pendingItems.length === 0) {
                tbody.innerHTML = `<tr class="text-center text-slate-500">
                <td colspan="7" class="px-6 py-8">
                    <i class="fas fa-check-circle text-4xl mb-2 block text-green-500"></i>
                    Semua pengajuan sudah diproses
                </td>
            </tr>`;
                return;
            }

            tbody.innerHTML = pendingItems.map(item => `
            <tr class="hover:bg-slate-50 transition">
                <td class="px-6 py-3 text-sm text-slate-600">${item.tanggalPengajuan}</td>
                <td class="px-6 py-3 text-sm font-medium text-slate-700">${item.produk}</td>
                <td class="px-6 py-3 text-sm text-green-600 font-semibold">+${item.jumlah}</td>
                <td class="px-6 py-3 text-sm text-slate-600">${item.supplier}</td>
                <td class="px-6 py-3 text-sm text-slate-600">${item.tanggal}</td>
                <td class="px-6 py-3">
                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                        <i class="fas fa-clock mr-1"></i>Menunggu
                    </span>
                </td>
                <td class="px-6 py-3">
                    <div class="flex gap-2">
                        <button onclick="approvePenerimaan(${item.id})" 
                            class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm flex items-center gap-1">
                            <i class="fas fa-check"></i> Setuju
                        </button>
                        <button onclick="rejectPenerimaan(${item.id})" 
                            class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm flex items-center gap-1">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
        }

        function approvePenerimaan(id) {
            if (confirm('Setujui pengajuan penerimaan ini?')) {
                const index = pengajuanPenerimaan.findIndex(i => i.id === id);
                if (index !== -1) {
                    pengajuanPenerimaan[index].status = 'approved';
                    pengajuanPenerimaan[index].disetujuiPada = new Date().toLocaleString('id-ID');
                    localStorage.setItem('pengajuanPenerimaan', JSON.stringify(pengajuanPenerimaan));
                    renderApprovalPenerimaan();
                    alert('Pengajuan DISETUJUI! Stok akan ditambahkan.');
                }
            }
        }

        function rejectPenerimaan(id) {
            const alasan = prompt('Masukkan alasan penolakan:');
            if (alasan !== null) {
                const index = pengajuanPenerimaan.findIndex(i => i.id === id);
                if (index !== -1) {
                    pengajuanPenerimaan[index].status = 'rejected';
                    pengajuanPenerimaan[index].alasanTolak = alasan;
                    pengajuanPenerimaan[index].ditolakPada = new Date().toLocaleString('id-ID');
                    localStorage.setItem('pengajuanPenerimaan', JSON.stringify(pengajuanPenerimaan));
                    renderApprovalPenerimaan();
                    alert(`Pengajuan DITOLAK dengan alasan: ${alasan}`);
                }
            }
        }

        // Refresh data setiap 2 detik (simulasi realtime)
        setInterval(() => {
            pengajuanPenerimaan = JSON.parse(localStorage.getItem('pengajuanPenerimaan') || '[]');
            renderApprovalPenerimaan();
        }, 2000);

        renderApprovalPenerimaan();
    </script>
@endsection
