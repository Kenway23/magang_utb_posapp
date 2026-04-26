<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Gudang\GudangProdukController;
use App\Http\Controllers\Gudang\TambahStockController;
use App\Http\Controllers\Owner\OwnerStokController;
use App\Http\Controllers\Owner\ProdukController;

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

        // ==================== PRODUK (LIHAT) ====================
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk');
        Route::get('/produk/data', [ProdukController::class, 'getData'])->name('produk.data');

        // ==================== MANAJEMEN STOK & APPROVAL (TERPUSAT) ====================
        Route::prefix('stok')->name('stok.')->group(function () {

            // APPROVAL TERPUSAT (Semua jenis approval)
            Route::get('/approval', [OwnerStokController::class, 'approval'])->name('approval');
            Route::get('/approval/data', [OwnerStokController::class, 'getApprovalData'])->name('approval.data');

            // Approve/Reject PRODUK BARU
            Route::post('/approval/produk/{id}/approve', [OwnerStokController::class, 'approveProduk'])->name('approval.produk.approve');
            Route::post('/approval/produk/{id}/reject', [OwnerStokController::class, 'rejectProduk'])->name('approval.produk.reject');

            // Approve/Reject TAMBAH STOK (Request dari Gudang)
            Route::post('/approval/tambah-stok/{id}/approve', [OwnerStokController::class, 'approveTambahStok'])->name('approval.tambah_stok.approve');
            Route::post('/approval/tambah-stok/{id}/reject', [OwnerStokController::class, 'rejectTambahStok'])->name('approval.tambah_stok.reject');

            // PENERIMAAN STOK (Dari Supplier ke Gudang)
            Route::get('/penerimaan', [OwnerStokController::class, 'penerimaan'])->name('penerimaan');
            Route::post('/penerimaan/{id}/approve', [OwnerStokController::class, 'approvePenerimaan'])->name('penerimaan.approve');
            Route::post('/penerimaan/{id}/reject', [OwnerStokController::class, 'rejectPenerimaan'])->name('penerimaan.reject');

            // PENGIRIMAN STOK (Dari Gudang ke Toko)
            Route::get('/pengiriman', [OwnerStokController::class, 'pengiriman'])->name('pengiriman');
            Route::post('/pengiriman/{id}/approve', [OwnerStokController::class, 'approvePengiriman'])->name('pengiriman.approve');
            Route::post('/pengiriman/{id}/reject', [OwnerStokController::class, 'rejectPengiriman'])->name('pengiriman.reject');
            Route::post('/kirim-ke-toko', [OwnerStokController::class, 'kirimKeToko'])->name('kirimKeToko');

            // LAPORAN STOK
            Route::get('/laporan', [OwnerStokController::class, 'laporan'])->name('laporan');
        });

        // ==================== RIWAYAT & LAPORAN ====================
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

        // Request Tambah Stok (CRUD)
        Route::prefix('tambah-stok')->name('tambah_stok.')->group(function () {
            Route::get('/', [TambahStockController::class, 'index'])->name('index');
            Route::post('/', [TambahStockController::class, 'store'])->name('store');
            Route::put('/{id}', [TambahStockController::class, 'update'])->name('update');
            Route::delete('/{id}', [TambahStockController::class, 'destroy'])->name('destroy');
        });

        // Manajemen Stok Lainnya
        Route::prefix('stok')->name('stok.')->group(function () {
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

        Route::get('/dashboard', function () {
            $produks = \App\Models\Produk::with('kategori')
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->get();

            // Statistik tambahan
            $totalProduk = $produks->count();
            $totalStok = $produks->sum('stok_gudang');
            $produkMenipis = $produks->where('stok_gudang', '<', 10)->count();
            $produkPending = \App\Models\Produk::where('status', 'pending')->count();

            return view('gudang.d_gudang', compact('produks', 'totalProduk', 'totalStok', 'produkMenipis', 'produkPending'));
        })->name('dashboard');

        Route::get('/produk', [GudangProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/data', [GudangProdukController::class, 'getData'])->name('produk.data');
        Route::post('/produk', [GudangProdukController::class, 'store'])->name('produk.store');
        Route::put('/produk/{id}', [GudangProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{id}', [GudangProdukController::class, 'destroy'])->name('produk.destroy');

        Route::prefix('stok')->name('stok.')->group(function () {

            Route::prefix('tambah-stok')->name('tambah_stok.')->group(function () {
                Route::get('/', [TambahStockController::class, 'index'])->name('index');
                Route::post('/', [TambahStockController::class, 'store'])->name('store');
                Route::put('/{id}', [TambahStockController::class, 'update'])->name('update');
                Route::delete('/{id}', [TambahStockController::class, 'destroy'])->name('destroy');
            });

            // Halaman stok lainnya
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