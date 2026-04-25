@extends('layouts.owner')

@section('title', 'Approval Penerimaan Stok - PROShop')

@section('header-title', 'Approval Penerimaan Stok')
@section('header-subtitle', 'Setujui atau tolak pengajuan penerimaan stok dari gudang')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
            <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-indigo-600"></i>
                Daftar Pengajuan Penerimaan Stok
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