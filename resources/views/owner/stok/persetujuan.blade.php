@extends('layouts.owner')

@section('title', 'Persetujuan Stok - PROShop')

@section('header-title', 'Persetujuan Stok')
@section('header-subtitle', 'Setujui atau tolak pengajuan stok masuk dan stok keluar')

@section('content')
    <div x-data="approvalApp()" x-init="init()" x-cloak>
        <div class="space-y-6">


            {{-- Konten Penerimaan Stok (Masuk) --}}
            <div x-show="activeMenu === 'masuk'" x-transition.duration.300ms>
                {{-- Statistik Penerimaan --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-sm">Menunggu Persetujuan</p>
                                <p class="text-3xl font-bold text-yellow-600" x-text="statistikMasuk.menunggu">0</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-sm">Disetujui</p>
                                <p class="text-3xl font-bold text-green-600" x-text="statistikMasuk.disetujui">0</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-sm">Ditolak</p>
                                <p class="text-3xl font-bold text-red-600" x-text="statistikMasuk.ditolak">0</p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Penerimaan Stok --}}
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center flex-wrap gap-3">
                        <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-green-600"></i>
                            Daftar Pengajuan Penerimaan Stok
                        </h3>
                        <div class="flex gap-2">
                            <input type="text" x-model="searchMasuk" placeholder="Cari produk atau supplier..."
                                class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <select x-model="filterStatusMasuk"
                                class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="semua">Semua Status</option>
                                <option value="pending">Menunggu</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl
                                        Pengajuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Jumlah
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Supplier
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl
                                        Terima</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="item in filteredStokMasuk" :key="item.id">
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-3 text-sm text-slate-600" x-text="item.tanggalPengajuan"></td>
                                        <td class="px-6 py-3 text-sm font-medium text-slate-700" x-text="item.produk"></td>
                                        <td class="px-6 py-3 text-sm text-green-600 font-semibold">
                                            +<span x-text="item.jumlah"></span> <span x-text="item.satuan"></span>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-slate-600" x-text="item.supplier"></td>
                                        <td class="px-6 py-3 text-sm text-slate-600" x-text="item.tanggalTerima"></td>
                                        <td class="px-6 py-3">
                                            <span x-show="item.status === 'pending'"
                                                class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                                <i class="fas fa-clock mr-1"></i>Menunggu
                                            </span>
                                            <span x-show="item.status === 'approved'"
                                                class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                                <i class="fas fa-check-circle mr-1"></i>Disetujui
                                            </span>
                                            <span x-show="item.status === 'rejected'"
                                                class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                                <i class="fas fa-times-circle mr-1"></i>Ditolak
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <template x-if="item.status === 'pending'">
                                                <div class="flex gap-2">
                                                    <button @click="approvePenerimaan(item)"
                                                        class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm flex items-center gap-1">
                                                        <i class="fas fa-check"></i> Setuju
                                                    </button>
                                                    <button @click="rejectPenerimaan(item)"
                                                        class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm flex items-center gap-1">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                </div>
                                            </template>
                                            <template x-if="item.status !== 'pending'">
                                                <button @click="printItem(item)"
                                                    class="text-indigo-600 hover:text-indigo-800" title="Cetak">
                                                    <i class="fas fa-print text-lg"></i>
                                                </button>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredStokMasuk.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                                        Tidak ada pengajuan penerimaan stok
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Konten Pengiriman Stok (Keluar) --}}
            <div x-show="activeMenu === 'keluar'" x-transition.duration.300ms>
                {{-- Statistik Pengiriman --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-sm">Menunggu Persetujuan</p>
                                <p class="text-3xl font-bold text-yellow-600" x-text="statistikKeluar.menunggu">0</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-sm">Disetujui</p>
                                <p class="text-3xl font-bold text-green-600" x-text="statistikKeluar.disetujui">0</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-500 text-sm">Ditolak</p>
                                <p class="text-3xl font-bold text-red-600" x-text="statistikKeluar.ditolak">0</p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Pengiriman Stok --}}
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center flex-wrap gap-3">
                        <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-orange-600"></i>
                            Daftar Pengajuan Pengiriman Stok
                        </h3>
                        <div class="flex gap-2">
                            <input type="text" x-model="searchKeluar" placeholder="Cari produk atau tujuan..."
                                class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <select x-model="filterStatusKeluar"
                                class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="semua">Semua Status</option>
                                <option value="pending">Menunggu</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl
                                        Pengajuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Produk
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Jumlah
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tujuan
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Tgl
                                        Kirim</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="item in filteredStokKeluar" :key="item.id">
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-3 text-sm text-slate-600" x-text="item.tanggalPengajuan"></td>
                                        <td class="px-6 py-3 text-sm font-medium text-slate-700" x-text="item.produk">
                                        </td>
                                        <td class="px-6 py-3 text-sm text-red-600 font-semibold">
                                            -<span x-text="item.jumlah"></span> <span x-text="item.satuan"></span>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-slate-600" x-text="item.tujuan"></td>
                                        <td class="px-6 py-3 text-sm text-slate-600" x-text="item.tanggalKirim"></td>
                                        <td class="px-6 py-3">
                                            <span x-show="item.status === 'pending'"
                                                class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                                <i class="fas fa-clock mr-1"></i>Menunggu
                                            </span>
                                            <span x-show="item.status === 'approved'"
                                                class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                                <i class="fas fa-check-circle mr-1"></i>Disetujui
                                            </span>
                                            <span x-show="item.status === 'rejected'"
                                                class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                                <i class="fas fa-times-circle mr-1"></i>Ditolak
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <template x-if="item.status === 'pending'">
                                                <div class="flex gap-2">
                                                    <button @click="approvePengiriman(item)"
                                                        class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm flex items-center gap-1">
                                                        <i class="fas fa-check"></i> Setuju
                                                    </button>
                                                    <button @click="rejectPengiriman(item)"
                                                        class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm flex items-center gap-1">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                </div>
                                            </template>
                                            <template x-if="item.status !== 'pending'">
                                                <button @click="printItem(item)"
                                                    class="text-indigo-600 hover:text-indigo-800" title="Cetak">
                                                    <i class="fas fa-print text-lg"></i>
                                                </button>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredStokKeluar.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                                        Tidak ada pengajuan pengiriman stok
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function approvalApp() {
            return {
                activeMenu: 'masuk', // Default menu: 'masuk' atau 'keluar'

                // Filter untuk stok masuk
                searchMasuk: '',
                filterStatusMasuk: 'semua',

                // Filter untuk stok keluar
                searchKeluar: '',
                filterStatusKeluar: 'semua',

                // Data Stok Masuk (Penerimaan)
                stokMasuk: [],

                // Data Stok Keluar (Pengiriman)
                stokKeluar: [],

                // Statistik Stok Masuk
                get statistikMasuk() {
                    return {
                        menunggu: this.stokMasuk.filter(item => item.status === 'pending').length,
                        disetujui: this.stokMasuk.filter(item => item.status === 'approved').length,
                        ditolak: this.stokMasuk.filter(item => item.status === 'rejected').length
                    };
                },

                // Statistik Stok Keluar
                get statistikKeluar() {
                    return {
                        menunggu: this.stokKeluar.filter(item => item.status === 'pending').length,
                        disetujui: this.stokKeluar.filter(item => item.status === 'approved').length,
                        ditolak: this.stokKeluar.filter(item => item.status === 'rejected').length
                    };
                },

                // Filter Stok Masuk
                get filteredStokMasuk() {
                    let filtered = this.stokMasuk;

                    if (this.filterStatusMasuk !== 'semua') {
                        filtered = filtered.filter(item => item.status === this.filterStatusMasuk);
                    }

                    if (this.searchMasuk) {
                        filtered = filtered.filter(item =>
                            item.produk.toLowerCase().includes(this.searchMasuk.toLowerCase()) ||
                            item.supplier.toLowerCase().includes(this.searchMasuk.toLowerCase())
                        );
                    }

                    return filtered;
                },

                // Filter Stok Keluar
                get filteredStokKeluar() {
                    let filtered = this.stokKeluar;

                    if (this.filterStatusKeluar !== 'semua') {
                        filtered = filtered.filter(item => item.status === this.filterStatusKeluar);
                    }

                    if (this.searchKeluar) {
                        filtered = filtered.filter(item =>
                            item.produk.toLowerCase().includes(this.searchKeluar.toLowerCase()) ||
                            item.tujuan.toLowerCase().includes(this.searchKeluar.toLowerCase())
                        );
                    }

                    return filtered;
                },

                // Approve Penerimaan (Stok Masuk)
                approvePenerimaan(item) {
                    if (confirm(`Setujui pengajuan penerimaan stok untuk produk ${item.produk}?`)) {
                        const index = this.stokMasuk.findIndex(i => i.id === item.id);
                        if (index !== -1) {
                            this.stokMasuk[index].status = 'approved';
                            this.stokMasuk[index].disetujuiPada = new Date().toLocaleString('id-ID');
                            this.saveToLocalStorage();
                            alert('Pengajuan DISETUJUI! Stok akan ditambahkan.');
                        }
                    }
                },

                // Reject Penerimaan (Stok Masuk)
                rejectPenerimaan(item) {
                    const alasan = prompt('Masukkan alasan penolakan:');
                    if (alasan !== null) {
                        const index = this.stokMasuk.findIndex(i => i.id === item.id);
                        if (index !== -1) {
                            this.stokMasuk[index].status = 'rejected';
                            this.stokMasuk[index].alasanTolak = alasan;
                            this.stokMasuk[index].ditolakPada = new Date().toLocaleString('id-ID');
                            this.saveToLocalStorage();
                            alert(`Pengajuan DITOLAK dengan alasan: ${alasan}`);
                        }
                    }
                },

                // Approve Pengiriman (Stok Keluar)
                approvePengiriman(item) {
                    if (confirm(`Setujui pengajuan pengiriman stok untuk produk ${item.produk}? Stok akan berkurang.`)) {
                        const index = this.stokKeluar.findIndex(i => i.id === item.id);
                        if (index !== -1) {
                            this.stokKeluar[index].status = 'approved';
                            this.stokKeluar[index].disetujuiPada = new Date().toLocaleString('id-ID');
                            this.saveToLocalStorage();
                            alert('Pengajuan DISETUJUI! Stok akan dikurangi.');
                        }
                    }
                },

                // Reject Pengiriman (Stok Keluar)
                rejectPengiriman(item) {
                    const alasan = prompt('Masukkan alasan penolakan:');
                    if (alasan !== null) {
                        const index = this.stokKeluar.findIndex(i => i.id === item.id);
                        if (index !== -1) {
                            this.stokKeluar[index].status = 'rejected';
                            this.stokKeluar[index].alasanTolak = alasan;
                            this.stokKeluar[index].ditolakPada = new Date().toLocaleString('id-ID');
                            this.saveToLocalStorage();
                            alert(`Pengajuan DITOLAK dengan alasan: ${alasan}`);
                        }
                    }
                },

                printItem(item) {
                    alert(`Mencetak dokumen approval untuk ${item.produk}`);
                },

                saveToLocalStorage() {
                    localStorage.setItem('pengajuanPenerimaan', JSON.stringify(this.stokMasuk));
                    localStorage.setItem('pengajuanPengiriman', JSON.stringify(this.stokKeluar));
                },

                loadFromLocalStorage() {
                    // Load Stok Masuk
                    const savedMasuk = localStorage.getItem('pengajuanPenerimaan');
                    if (savedMasuk) {
                        this.stokMasuk = JSON.parse(savedMasuk);
                    } else {
                        // Data dummy stok masuk
                        this.stokMasuk = [{
                                id: 1,
                                tanggalPengajuan: '25/04/2026',
                                produk: 'Pocari Sweat',
                                jumlah: 50,
                                satuan: 'pcs',
                                supplier: 'PT. Nutrisi Sehat',
                                tanggalTerima: '26/04/2026',
                                status: 'pending'
                            },
                            {
                                id: 2,
                                tanggalPengajuan: '24/04/2026',
                                produk: 'Goodday',
                                jumlah: 20,
                                satuan: 'pack',
                                supplier: 'PT. Indofood Sukses',
                                tanggalTerima: '25/04/2026',
                                status: 'approved'
                            },
                            {
                                id: 3,
                                tanggalPengajuan: '23/04/2026',
                                produk: 'Sunlight',
                                jumlah: 30,
                                satuan: 'pcs',
                                supplier: 'PT. Unilever Indonesia',
                                tanggalTerima: '24/04/2026',
                                status: 'rejected'
                            },
                            {
                                id: 4,
                                tanggalPengajuan: '22/04/2026',
                                produk: 'Aqua 600ml',
                                jumlah: 100,
                                satuan: 'botol',
                                supplier: 'PT. Aqua Golden',
                                tanggalTerima: '23/04/2026',
                                status: 'pending'
                            },
                            {
                                id: 5,
                                tanggalPengajuan: '21/04/2026',
                                produk: 'Indomie Goreng',
                                jumlah: 200,
                                satuan: 'pcs',
                                supplier: 'PT. Indofood',
                                tanggalTerima: '22/04/2026',
                                status: 'pending'
                            }
                        ];
                    }

                    // Load Stok Keluar
                    const savedKeluar = localStorage.getItem('pengajuanPengiriman');
                    if (savedKeluar) {
                        this.stokKeluar = JSON.parse(savedKeluar);
                    } else {
                        // Data dummy stok keluar
                        this.stokKeluar = [{
                                id: 1,
                                tanggalPengajuan: '25/04/2026',
                                produk: 'Teh Botol Sosro',
                                jumlah: 25,
                                satuan: 'botol',
                                tujuan: 'Karyawan',
                                tanggalKirim: '26/04/2026',
                                status: 'pending'
                            },
                            {
                                id: 2,
                                tanggalPengajuan: '24/04/2026',
                                produk: 'Roma Sari Gandum',
                                jumlah: 15,
                                satuan: 'pack',
                                tujuan: 'Donasi',
                                tanggalKirim: '25/04/2026',
                                status: 'approved'
                            },
                            {
                                id: 3,
                                tanggalPengajuan: '23/04/2026',
                                produk: 'Beng-Beng',
                                jumlah: 30,
                                satuan: 'pcs',
                                tujuan: 'Rusak',
                                tanggalKirim: '24/04/2026',
                                status: 'rejected'
                            },
                            {
                                id: 4,
                                tanggalPengajuan: '22/04/2026',
                                produk: 'Ultra Milk',
                                jumlah: 10,
                                satuan: 'kotak',
                                tujuan: 'Sample',
                                tanggalKirim: '23/04/2026',
                                status: 'pending'
                            },
                            {
                                id: 5,
                                tanggalPengajuan: '21/04/2026',
                                produk: 'Mie Sedap',
                                jumlah: 50,
                                satuan: 'pcs',
                                tujuan: 'Karyawan',
                                tanggalKirim: '22/04/2026',
                                status: 'pending'
                            }
                        ];
                    }
                },

                init() {
                    this.loadFromLocalStorage();

                    // Refresh data dari localStorage setiap 2 detik
                    setInterval(() => {
                        this.loadFromLocalStorage();
                    }, 2000);
                }
            };
        }
    </script>
@endsection
