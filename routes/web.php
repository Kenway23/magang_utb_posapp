<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Gudang\GudangProdukController;
use App\Http\Controllers\Gudang\TambahStockController;
use App\Http\Controllers\Gudang\PengirimanController;
use App\Http\Controllers\Owner\OwnerStokController;
use App\Http\Controllers\Owner\ProdukController;
use App\Http\Controllers\Kasir\KasirController;

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
        Route::get('/dashboard', function () {
            return view('owner.d_owner');
        })->name('dashboard');

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

            // 🔥 Approve/Reject PENGIRIMAN (Gudang → Toko)
            Route::post('/approval/pengiriman/{id}/approve', [OwnerStokController::class, 'approvePengiriman'])->name('approval.pengiriman.approve');
            Route::post('/approval/pengiriman/{id}/reject', [OwnerStokController::class, 'rejectPengiriman'])->name('approval.pengiriman.reject');

            // PENERIMAAN STOK
            Route::get('/penerimaan', [OwnerStokController::class, 'penerimaan'])->name('penerimaan');
            Route::post('/penerimaan/{id}/approve', [OwnerStokController::class, 'approvePenerimaan'])->name('penerimaan.approve');
            Route::post('/penerimaan/{id}/reject', [OwnerStokController::class, 'rejectPenerimaan'])->name('penerimaan.reject');

            // PENGIRIMAN STOK (View)
            Route::get('/pengiriman', [OwnerStokController::class, 'pengiriman'])->name('pengiriman');
            Route::post('/kirim-ke-toko', [OwnerStokController::class, 'kirimKeToko'])->name('kirimKeToko');

            // LAPORAN STOK
            Route::get('/laporan', [OwnerStokController::class, 'laporan'])->name('laporan');
        });

        // Riwayat & Laporan
        Route::get('/riwayat-transaksi', function () {
            return view('owner.riwayat_transaksi');
        })->name('riwayat_transaksi');

        Route::get('/laporan-penjualan', function () {
            return view('owner.laporan_penjualan');
        })->name('laporan_penjualan');
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

        // Riwayat Transaksi
        Route::get('/riwayat-transaksi', [KasirController::class, 'riwayat'])->name('riwayat_transaksi');

        // Laporan
        Route::get('/laporan', function () {
            return view('kasir.laporan');
        })->name('laporan');
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

            return view('gudang.d_gudang', compact('produks', 'totalProduk', 'totalStok', 'produkMenipis', 'produkPending'));
        })->name('dashboard');

        // Manajemen Produk (CRUD)
        Route::get('/produk', [GudangProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/data', [GudangProdukController::class, 'getData'])->name('produk.data');
        Route::post('/produk', [GudangProdukController::class, 'store'])->name('produk.store');
        Route::put('/produk/{id}', [GudangProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{id}', [GudangProdukController::class, 'destroy'])->name('produk.destroy');

        // 🔥 REQUEST TAMBAH STOK
        Route::prefix('tambah-stok')->name('tambah_stok.')->group(function () {
            Route::get('/', [TambahStockController::class, 'index'])->name('index');
            Route::post('/', [TambahStockController::class, 'store'])->name('store');
            Route::put('/{id}', [TambahStockController::class, 'update'])->name('update');
            Route::delete('/{id}', [TambahStockController::class, 'destroy'])->name('destroy');
        });

        // 🔥 REQUEST PENGIRIMAN STOK (Gudang → Toko)
        Route::prefix('pengiriman')->name('pengiriman.')->group(function () {
            Route::get('/', [PengirimanController::class, 'index'])->name('index');
            Route::post('/', [PengirimanController::class, 'store'])->name('store');
            Route::put('/{id}', [PengirimanController::class, 'update'])->name('update');
            Route::delete('/{id}', [PengirimanController::class, 'destroy'])->name('destroy');
        });

        // Manajemen Stok Lainnya
        Route::prefix('stok')->name('stok.')->group(function () {
            Route::get('/penyesuaian', function () {
                return view('gudang.stok.penyesuaian');
            })->name('penyesuaian');
            Route::get('/laporan', function () {
                return view('gudang.laporan');
            })->name('laporan');
        });

        // Riwayat Gudang
        Route::get('/riwayat', function () {
            return view('gudang.riwayat_gudang');
        })->name('riwayat_gudang');
    });