@extends('layouts.owner')

@section('title', 'Beranda Owner - PROShop')
@section('header-title', 'Beranda')
@section('header-subtitle', 'Selamat datang, Owner! Kelola toko Anda dengan mudah')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-md p-6 card-hover border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Produk</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">20</p>
                </div>
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center"><i
                        class="fas fa-boxes text-indigo-600 text-2xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-md p-6 card-hover border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">Rp 72.900</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center"><i
                        class="fas fa-rupiah-sign text-green-600 text-2xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-md p-6 card-hover border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Transaksi</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">5</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center"><i
                        class="fas fa-receipt text-blue-600 text-2xl"></i></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-clock text-indigo-600 mr-2"></i>Aktivitas Terakhir</h3>
                <a href="#" class="text-indigo-600 text-sm">Lihat Semua <i class="fas fa-arrow-right text-xs"></i></a>
            </div>
            <div class="divide-y divide-slate-100">
                <div class="px-6 py-3 flex items-start gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center"><i
                            class="fas fa-cart-shopping text-green-600 text-sm"></i></div>
                    <div>
                        <p class="font-medium">Transaksi Baru</p>
                        <p class="text-xs text-slate-400">1 menit yang lalu</p>
                    </div>
                </div>
                <div class="px-6 py-3 flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"><i
                            class="fas fa-plus-circle text-blue-600 text-sm"></i></div>
                    <div>
                        <p class="font-medium">Penerimaan Stok</p>
                        <p class="text-sm text-slate-600">Pocari Sweat +50 pcs</p>
                        <p class="text-xs text-slate-400">3 menit lalu</p>
                    </div>
                </div>
                <div class="px-6 py-3 flex items-start gap-3">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center"><i
                            class="fas fa-minus-circle text-red-600 text-sm"></i></div>
                    <div>
                        <p class="font-medium">Pengeluaran Stok</p>
                        <p class="text-sm text-slate-600">Indomie goreng -46 pcs</p>
                        <p class="text-xs text-slate-400">8 menit lalu</p>
                    </div>
                </div>
                <div class="px-6 py-3 flex items-start gap-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center"><i
                            class="fas fa-check-circle text-yellow-600 text-sm"></i></div>
                    <div>
                        <p class="font-medium">Persetujuan Stok</p>
                        <p class="text-sm text-slate-600">Goodday +20 pack</p>
                        <p class="text-xs text-slate-400">1 jam lalu</p>
                    </div>
                </div>
                <div class="px-6 py-3 flex items-start gap-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center"><i
                            class="fas fa-box-open text-purple-600 text-sm"></i></div>
                    <div>
                        <p class="font-medium">Produk Baru</p>
                        <p class="text-xs text-slate-400">3 jam lalu</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-trophy text-yellow-500 mr-2"></i>Penjualan Terbaik</h3>
                <a href="#" class="text-indigo-600 text-sm">Lihat Semua <i class="fas fa-arrow-right text-xs"></i></a>
            </div>
            <div class="divide-y divide-slate-100">
                <div class="px-6 py-3">
                    <div class="flex justify-between"><span class="font-semibold">Indomie Goreng</span><span
                            class="text-green-600">Rp 1.250.000</span></div>
                    <p class="text-xs text-slate-500">Terjual: 250 pcs</p>
                </div>
                <div class="px-6 py-3">
                    <div class="flex justify-between"><span class="font-semibold">Pocari Sweat</span><span
                            class="text-green-600">Rp 980.000</span></div>
                    <p class="text-xs text-slate-500">Terjual: 196 pcs</p>
                </div>
                <div class="px-6 py-3">
                    <div class="flex justify-between"><span class="font-semibold">Goodday</span><span
                            class="text-green-600">Rp 720.000</span></div>
                    <p class="text-xs text-slate-500">Terjual: 144 pack</p>
                </div>
                <div class="px-6 py-3">
                    <div class="flex justify-between"><span class="font-semibold">Tolak Angin</span><span
                            class="text-green-600">Rp 540.000</span></div>
                    <p class="text-xs text-slate-500">Terjual: 108 pcs</p>
                </div>
                <div class="px-6 py-3">
                    <div class="flex justify-between"><span class="font-semibold">Aqua Mineral</span><span
                            class="text-green-600">Rp 425.000</span></div>
                    <p class="text-xs text-slate-500">Terjual: 85 dus</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100 mb-6">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-semibold"><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>Stok Rendah</h3>
            <a href="#" class="text-indigo-600 text-sm">Lihat Semua <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold">Produk</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold">Stok</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2 font-medium">Indomie Goreng</td>
                    <td class="px-4 py-2">Makanan</td>
                    <td class="px-4 py-2 text-red-600">20</td>
                    <td class="px-4 py-2"><span
                            class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Kritis</span></td>
                    <td class="px-4 py-2"><button onclick="showSuccess('Stok akan segera ditambah')"
                            class="text-indigo-600">Detail</button></td>
                </tr>
                <tr>
                    <td class="px-4 py-2">2</td>
                    <td class="px-4 py-2 font-medium">Kayu Putih</td>
                    <td class="px-4 py-2">Kesehatan</td>
                    <td class="px-4 py-2 text-orange-600">7</td>
                    <td class="px-4 py-2"><span
                            class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700">Menipis</span></td>
                    <td class="px-4 py-2"><button onclick="showWarning('Stok menipis, segera lakukan restok!')"
                            class="text-indigo-600">Detail</button></td>
                </tr>
                <tr>
                    <td class="px-4 py-2">3</td>
                    <td class="px-4 py-2 font-medium">Japota Honey</td>
                    <td class="px-4 py-2">Makanan</td>
                    <td class="px-4 py-2 text-red-600">0</td>
                    <td class="px-4 py-2"><span
                            class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Habis</span></td>
                    <td class="px-4 py-2"><button onclick="showError('Stok habis, tidak dapat diproses!')"
                            class="text-indigo-600">Detail</button></td>
                </tr>
                <tr>
                    <td class="px-4 py-2">4</td>
                    <td class="px-4 py-2 font-medium">Rinso Bubuk</td>
                    <td class="px-4 py-2">Kebersihan</td>
                    <td class="px-4 py-2 text-orange-600">5</td>
                    <td class="px-4 py-2"><span
                            class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700">Menipis</span></td>
                    <td class="px-4 py-2"><button
                            onclick="showConfirmDelete('Apakah Anda yakin ingin menghapus produk ini?', () => showSuccess('Produk berhasil dihapus'))"
                            class="text-red-600">Hapus</button></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-semibold"><i class="fas fa-user-plus text-green-600 mr-2"></i>Pengguna Terbaru</h3>
            <a href="#" class="text-indigo-600 text-sm">Lihat Semua <i class="fas fa-arrow-right text-xs"></i></a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 divide-x divide-slate-100">
            <div class="p-4 flex justify-between items-center">
                <div>
                    <p class="font-semibold">Kasir 3</p>
                    <p class="text-sm text-slate-500">Kasir_3</p>
                </div><button onclick="showInfo('Kasir 3 bergabung 2 hari lalu')" class="text-indigo-600">Detail</button>
            </div>
            <div class="p-4 flex justify-between items-center">
                <div>
                    <p class="font-semibold">Gudang 2</p>
                    <p class="text-sm text-slate-500">Gudang_2</p>
                </div><button onclick="showInfo('Gudang 2 bergabung 5 hari lalu')" class="text-indigo-600">Detail</button>
            </div>
            <div class="p-4 flex justify-between items-center">
                <div>
                    <p class="font-semibold">Kasir 2</p>
                    <p class="text-sm text-slate-500">Kasir_2</p>
                </div><button onclick="showInfo('Kasir 2 bergabung 1 minggu lalu')"
                    class="text-indigo-600">Detail</button>
            </div>
        </div>
    </div>
@endsection
