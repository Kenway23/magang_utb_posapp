<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Gudang\GudangProdukController;
use App\Http\Controllers\Owner\ApprovalProdukController;
use App\Http\Controllers\Owner\OwnerStokController;
use App\Http\Controllers\Owner\ProdukController;
use App\Http\Middleware\RoleMiddleware;

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

        // Approval Produk Baru (dari Gudang)
        Route::prefix('approval-produk')->name('approval.produk.')->group(function () {
            Route::get('/', [ApprovalProdukController::class, 'index'])->name('index');
            Route::get('/data', [ApprovalProdukController::class, 'getData'])->name('data');
            Route::post('/{id}/approve', [ApprovalProdukController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [ApprovalProdukController::class, 'reject'])->name('reject');
            Route::post('/bulk-approve', [ApprovalProdukController::class, 'bulkApprove'])->name('bulkApprove');
        });

        // Manajemen Stok
        Route::prefix('stok')->name('stok.')->group(function () {

            // Penerimaan Stok
            Route::get('/penerimaan', [OwnerStokController::class, 'penerimaan'])->name('penerimaan');
            Route::post('/penerimaan/{id}/approve', [OwnerStokController::class, 'approvePenerimaan'])->name('penerimaan.approve');
            Route::post('/penerimaan/{id}/reject', [OwnerStokController::class, 'rejectPenerimaan'])->name('penerimaan.reject');

            // Pengiriman Stok
            Route::get('/pengiriman', [OwnerStokController::class, 'pengiriman'])->name('pengiriman');
            Route::post('/pengiriman/{id}/approve', [OwnerStokController::class, 'approvePengiriman'])->name('pengiriman.approve');
            Route::post('/pengiriman/{id}/reject', [OwnerStokController::class, 'rejectPengiriman'])->name('pengiriman.reject');
            Route::post('/kirim-ke-toko', [OwnerStokController::class, 'kirimKeToko'])->name('kirimKeToko');

            // Approval Terpusat (Semua Approval)
            Route::get('/approval', [OwnerStokController::class, 'approval'])->name('approval');
            Route::get('/approval/data', [OwnerStokController::class, 'getApprovalData'])->name('approval.data');
            Route::post('/approval/produk/{id}/approve', [OwnerStokController::class, 'approveProduk'])->name('approval.produk.approve');
            Route::post('/approval/produk/{id}/reject', [OwnerStokController::class, 'rejectProduk'])->name('approval.produk.reject');
            Route::post('/approval/penerimaan/{id}/approve', [OwnerStokController::class, 'approvePenerimaanStok'])->name('approval.penerimaan.approve');
            Route::post('/approval/penerimaan/{id}/reject', [OwnerStokController::class, 'rejectPenerimaanStok'])->name('approval.penerimaan.reject');
            Route::post('/approval/pengiriman/{id}/approve', [OwnerStokController::class, 'approvePengirimanStok'])->name('approval.pengiriman.approve');
            Route::post('/approval/pengiriman/{id}/reject', [OwnerStokController::class, 'rejectPengirimanStok'])->name('approval.pengiriman.reject');

            // Laporan Stok
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

        Route::get('/dashboard', function () {
            return view('kasir.d_kasir');
        })->name('dashboard');

        Route::get('/transaksi', function () {
            return view('kasir.transaksi');
        })->name('transaksi');

        Route::get('/riwayat-transaksi', function () {
            return view('kasir.riwayat_transaksi');
        })->name('riwayat_transaksi');

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
            $produks = \App\Models\Produk::with('kategori')->orderBy('created_at', 'desc')->limit(5)->get();
            return view('gudang.d_gudang', compact('produks'));
        })->name('dashboard');

        // Manajemen Produk (CRUD)
        Route::get('/produk', [GudangProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/data', [GudangProdukController::class, 'getData'])->name('produk.data');
        Route::post('/produk', [GudangProdukController::class, 'store'])->name('produk.store');
        Route::put('/produk/{id}', [GudangProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{id}', [GudangProdukController::class, 'destroy'])->name('produk.destroy');

        // Manajemen Stok
        Route::prefix('stok')->name('stok.')->group(function () {
            Route::get('/penerimaan', function () {
                return view('gudang.stok.penerimaan');
            })->name('penerimaan');

            Route::get('/pengiriman', function () {
                return view('gudang.stok.pengiriman');
            })->name('pengiriman');

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