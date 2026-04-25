@extends('layouts.owner')

@section('title', 'Approval Pengiriman Stok - PROShop')

@section('header-title', 'Approval Pengiriman Stok')
@section('header-subtitle', 'Setujui atau tolak pengajuan pengiriman stok dari gudang')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
            <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                <i class="fas fa-truck text-orange-600"></i>
                Daftar Pengajuan Pengiriman Stok
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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl Kirim</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableApprovalPengiriman">
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
    let pengajuanPengiriman = JSON.parse(localStorage.getItem('pengajuanPengiriman') || '[]');
    
    function renderApprovalPengiriman() {
        const tbody = document.getElementById('tableApprovalPengiriman');
        const pendingItems = pengajuanPengiriman.filter(item => item.status === 'pending');
        
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
                <td class="px-6 py-3 text-sm text-red-600 font-semibold">-${item.jumlah}</td>
                <td class="px-6 py-3 text-sm text-slate-600">${item.tujuan}</td>
                <td class="px-6 py-3 text-sm text-slate-600">${item.tanggal}</td>
                <td class="px-6 py-3">
                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                        <i class="fas fa-clock mr-1"></i>Menunggu
                    </span>
                </td>
                <td class="px-6 py-3">
                    <div class="flex gap-2">
                        <button onclick="approvePengiriman(${item.id})" 
                            class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm flex items-center gap-1">
                            <i class="fas fa-check"></i> Setuju
                        </button>
                        <button onclick="rejectPengiriman(${item.id})" 
                            class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm flex items-center gap-1">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
    
    function approvePengiriman(id) {
        if (confirm('Setujui pengajuan pengiriman ini? Stok akan berkurang.')) {
            const index = pengajuanPengiriman.findIndex(i => i.id === id);
            if (index !== -1) {
                pengajuanPengiriman[index].status = 'approved';
                pengajuanPengiriman[index].disetujuiPada = new Date().toLocaleString('id-ID');
                localStorage.setItem('pengajuanPengiriman', JSON.stringify(pengajuanPengiriman));
                renderApprovalPengiriman();
                alert('Pengajuan DISETUJUI! Stok akan dikurangi.');
            }
        }
    }
    
    function rejectPengiriman(id) {
        const alasan = prompt('Masukkan alasan penolakan:');
        if (alasan !== null) {
            const index = pengajuanPengiriman.findIndex(i => i.id === id);
            if (index !== -1) {
                pengajuanPengiriman[index].status = 'rejected';
                pengajuanPengiriman[index].alasanTolak = alasan;
                pengajuanPengiriman[index].ditolakPada = new Date().toLocaleString('id-ID');
                localStorage.setItem('pengajuanPengiriman', JSON.stringify(pengajuanPengiriman));
                renderApprovalPengiriman();
                alert(`Pengajuan DITOLAK dengan alasan: ${alasan}`);
            }
        }
    }
    
    setInterval(() => {
        pengajuanPengiriman = JSON.parse(localStorage.getItem('pengajuanPengiriman') || '[]');
        renderApprovalPengiriman();
    }, 2000);
    
    renderApprovalPengiriman();
</script>
@endsection