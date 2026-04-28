<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Gudang\GudangProdukController;
use App\Http\Controllers\Gudang\TambahStockController;
use App\Http\Controllers\Gudang\PengirimanController;
use App\Http\Controllers\Gudang\ApprovalRequestKasirController;
use App\Http\Controllers\Owner\OwnerStokController;
use App\Http\Controllers\Owner\ProdukController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\Gudang\StockAdjustmentController;
use App\Http\Controllers\Owner\OwnerRiwayatController;
use App\Http\Controllers\Owner\OwnerLaporanController;
use App\Http\Controllers\Owner\LaporanApprovalController;
use App\Http\Controllers\Kasir\KasirLaporanController;
use App\Http\Controllers\Gudang\LaporanGudangController;
use App\Http\Controllers\Gudang\RiwayatGudangController;
use App\Http\Controllers\Owner\OwnerDashboardController;
// ==================== HALAMAN AWAL ====================
Route::get('/', function () {
    return view('splash');
});

// ==================== AUTHENTICATION ====================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout']);

// ==================== ROUTE OWNER ====================
Route::prefix('owner')
    ->middleware(['auth', 'role:owner'])
    ->name('owner.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

        // Manajemen Pengguna
        Route::resource('pengguna', UserController::class)->names([
            'index' => 'pengguna.index',
            'store' => 'pengguna.store',
            'edit' => 'pengguna.edit',
            'update' => 'pengguna.update',
            'destroy' => 'pengguna.destroy',
        ]);

        // Produk (Lihat)
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk');
        Route::get('/produk/data', [ProdukController::class, 'getData'])->name('produk.data');

        // ==================== MANAJEMEN STOK & APPROVAL ====================
        Route::prefix('stok')->name('stok.')->group(function () {

            // APPROVAL TERPUSAT (Semua jenis approval)
            Route::get('/approval', [OwnerStokController::class, 'approval'])->name('approval');
            Route::get('/approval/data', [OwnerStokController::class, 'getApprovalData'])->name('approval.data');

            // Approve/Reject PRODUK BARU
            Route::post('/approval/produk/{id}/approve', [OwnerStokController::class, 'approveProduk'])->name('approval.produk.approve');
            Route::post('/approval/produk/{id}/reject', [OwnerStokController::class, 'rejectProduk'])->name('approval.produk.reject');

            // Approve/Reject TAMBAH STOK
            Route::post('/approval/tambah-stok/{id}/approve', [OwnerStokController::class, 'approveTambahStok'])->name('approval.tambah_stok.approve');
            Route::post('/approval/tambah-stok/{id}/reject', [OwnerStokController::class, 'rejectTambahStok'])->name('approval.tambah_stok.reject');

            // Approve/Reject PENGIRIMAN (Gudang → Toko)
            Route::post('/approval/pengiriman/{id}/approve', [OwnerStokController::class, 'approvePengiriman'])->name('approval.pengiriman.approve');
            Route::post('/approval/pengiriman/{id}/reject', [OwnerStokController::class, 'rejectPengiriman'])->name('approval.pengiriman.reject');

            // Approve/Reject PENYESUAIAN STOK
            Route::post('/approval/penyesuaian/{id}/approve', [OwnerStokController::class, 'approvePenyesuaian'])->name('approval.penyesuaian.approve');
            Route::post('/approval/penyesuaian/{id}/reject', [OwnerStokController::class, 'rejectPenyesuaian'])->name('approval.penyesuaian.reject');

            // PENERIMAAN STOK
            Route::get('/penerimaan', [OwnerStokController::class, 'penerimaan'])->name('penerimaan');
            Route::post('/penerimaan/{id}/approve', [OwnerStokController::class, 'approvePenerimaan'])->name('penerimaan.approve');
            Route::post('/penerimaan/{id}/reject', [OwnerStokController::class, 'rejectPenerimaan'])->name('penerimaan.reject');

            // PENGIRIMAN STOK (View)
            Route::get('/pengiriman', [OwnerStokController::class, 'pengiriman'])->name('pengiriman');
            Route::post('/kirim-ke-toko', [OwnerStokController::class, 'kirimKeToko'])->name('kirimKeToko');

            // 🔥 LAPORAN APPROVAL - TAMBAHKAN DI SINI (DI DALAM GRUP STOK)
            Route::get('/laporan-approval', [LaporanApprovalController::class, 'index'])->name('laporan_approval');
            Route::get('/laporan-approval/data', [LaporanApprovalController::class, 'getData'])->name('laporan_approval.data');
        });

        // Riwayat Transaksi & Laporan Penjualan (di luar grup stok)
        Route::get('/riwayat-transaksi', [OwnerRiwayatController::class, 'index'])->name('riwayat_transaksi');
        Route::get('/riwayat-transaksi/data', [OwnerRiwayatController::class, 'getData'])->name('riwayat_transaksi.data');

        Route::get('/laporan-penjualan', [OwnerLaporanController::class, 'index'])->name('laporan_penjualan');
        Route::get('/laporan-penjualan/data', [OwnerLaporanController::class, 'getData'])->name('laporan_penjualan.data');
        Route::get('/laporan-penjualan/kasir-list', [OwnerLaporanController::class, 'getKasirList'])->name('laporan_penjualan.kasir_list');
    });

// ==================== ROUTE KASIR ====================
Route::prefix('kasir')
    ->middleware(['auth', 'role:kasir'])
    ->name('kasir.')
    ->group(function () {

        // Halaman utama (Dashboard + POS)
        Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');

        // API Routes
        Route::get('/dashboard-stats', [KasirController::class, 'dashboardStats'])->name('dashboard.stats');
        Route::get('/products', [KasirController::class, 'getProducts'])->name('products');
        Route::get('/categories', [KasirController::class, 'getCategories'])->name('categories');
        Route::post('/transaction', [KasirController::class, 'storeTransaction'])->name('transaction.store');

        Route::get('/request-kirim-stok', [KasirController::class, 'requestKirimStok'])->name('request_kirim_stok');
        Route::post('/request-kirim-stok', [KasirController::class, 'storeRequestKirimStok'])->name('request_kirim_stok.store');
        Route::put('/request-kirim-stok/{id}', [KasirController::class, 'updateRequestKirimStok'])->name('request_kirim_stok.update');
        Route::delete('/request-kirim-stok/{id}', [KasirController::class, 'destroyRequestKirimStok'])->name('request_kirim_stok.destroy');
        // Riwayat Transaksi
        Route::get('/riwayat-transaksi', [KasirController::class, 'riwayat'])->name('riwayat_transaksi');
        Route::get('/riwayat-data', [KasirController::class, 'getRiwayatData'])->name('riwayat_data');  // ← TAMBAHKAN INI
    
        // Laporan
        Route::get('/laporan-penjualan-kasir', [KasirLaporanController::class, 'index'])->name('laporan_penjualan_kasir');
        Route::get('/laporan-penjualan-kasir/data', [KasirLaporanController::class, 'getData'])->name('laporan_penjualan_kasir.data');
    });

// ==================== ROUTE GUDANG ====================
Route::prefix('gudang')
    ->middleware(['auth', 'role:gudang'])
    ->name('gudang.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {
            $produks = \App\Models\Produk::with('kategori')
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->get();

            $totalProduk = $produks->count();
            $totalStok = $produks->sum('stok_gudang');
            $produkMenipis = $produks->where('stok_gudang', '<', 10)->count();
            $produkPending = \App\Models\Produk::where('status', 'pending')->count();

            $latestTambahStok = \App\Models\TambahStock::with('produk')
                ->where('requested_by', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $latestPengiriman = \App\Models\Pengiriman::with('produk')
                ->where('requested_by', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $latestKasirRequests = \App\Models\Pengiriman::with(['produk', 'requester'])
                ->where('tujuan_toko', 'Permintaan Kasir')
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $pendingKasirRequests = \App\Models\Pengiriman::where('tujuan_toko', 'Permintaan Kasir')
                ->where('status', 'pending')
                ->count();

            return view('gudang.d_gudang', compact(
                'produks',
                'totalProduk',
                'totalStok',
                'produkMenipis',
                'produkPending',
                'latestTambahStok',
                'latestPengiriman',
                'latestKasirRequests',
                'pendingKasirRequests'
            ));
        })->name('dashboard');

        Route::get('/produk', [GudangProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/data', [GudangProdukController::class, 'getData'])->name('produk.data');
        Route::post('/produk', [GudangProdukController::class, 'store'])->name('produk.store');
        Route::put('/produk/{id}', [GudangProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{id}', [GudangProdukController::class, 'destroy'])->name('produk.destroy');

        Route::prefix('tambah-stok')->name('tambah_stok.')->group(function () {
            Route::get('/', [TambahStockController::class, 'index'])->name('index');
            Route::post('/', [TambahStockController::class, 'store'])->name('store');
            Route::put('/{id}', [TambahStockController::class, 'update'])->name('update');
            Route::delete('/{id}', [TambahStockController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('pengiriman')->name('pengiriman.')->group(function () {
            Route::get('/', [PengirimanController::class, 'index'])->name('index');
            Route::post('/', [PengirimanController::class, 'store'])->name('store');
            Route::put('/{id}', [PengirimanController::class, 'update'])->name('update');
            Route::delete('/{id}', [PengirimanController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('penyesuaian-stok')->name('penyesuaian_stok.')->group(function () {
            Route::get('/', [StockAdjustmentController::class, 'index'])->name('index');
            Route::post('/', [StockAdjustmentController::class, 'store'])->name('store');
            Route::put('/{id}', [StockAdjustmentController::class, 'update'])->name('update');
            Route::post('/{id}/submit', [StockAdjustmentController::class, 'submit'])->name('submit');
            Route::post('/{id}/approve', [StockAdjustmentController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [StockAdjustmentController::class, 'reject'])->name('reject');
            Route::delete('/{id}', [StockAdjustmentController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/detail', [StockAdjustmentController::class, 'getDetail'])->name('detail');
        });

        // 🔥 PERBAIKAN: Tambahkan route /data di sini
        Route::prefix('approval-request-kasir')->name('approval_request_kasir.')->group(function () {
            Route::get('/', [ApprovalRequestKasirController::class, 'index'])->name('index');
            Route::get('/data', [ApprovalRequestKasirController::class, 'getData'])->name('data');  // 🔥 TAMBAHKAN INI
            Route::get('/{id}/detail', [ApprovalRequestKasirController::class, 'detail'])->name('detail');
            Route::post('/{id}/approve', [ApprovalRequestKasirController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [ApprovalRequestKasirController::class, 'reject'])->name('reject');
        });

        // ==================== MANAJEMEN STOK ====================
        Route::prefix('stok')->name('stok.')->group(function () {
            Route::get('/penyesuaian', [StockAdjustmentController::class, 'index'])->name('penyesuaian');
            Route::get('/laporan-gudang', [LaporanGudangController::class, 'index'])->name('laporan_gudang');
            Route::get('/laporan-gudang/data', [LaporanGudangController::class, 'getData'])->name('laporan_gudang.data');
        });

        // Riwayat Gudang
        Route::get('/riwayat', [RiwayatGudangController::class, 'index'])->name('riwayat_gudang');
        Route::get('/riwayat/data', [RiwayatGudangController::class, 'getData'])->name('riwayat_gudang.data');
    });