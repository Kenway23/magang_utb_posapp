<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\StokMasukController;
=======
use App\Http\Controllers\LoginController;
>>>>>>> 2b633a9868aea0b356a86fbd3828e85b2a6e727d

//Login
Route::get('/', function () {
    return view('splash');
});
<<<<<<< HEAD
Route::post('/stok-masuk', [StokMasukController::class, 'store']);
Route::get('/stok-masuk/approve/{id}', [StokMasukController::class, 'approve']);
Route::get('/stok-masuk/reject/{id}', [StokMasukController::class, 'reject']);
=======
Route::post('/login', [LoginController::class, 'login']);

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        return view('owner.d_owner');
    })->name('dashboard');

    // Halaman Produk
    Route::get('/produk', function () {
        return view('owner.produk');
    })->name('produk');

    // Halaman Stok
    Route::prefix('stok')->name('stok.')->group(function () {
        Route::get('/penerimaan', function () {
            return view('owner.stok.penerimaan');
        })->name('penerimaan');

        Route::get('/pengiriman', function () {
            return view('owner.stok.pengiriman');
        })->name('pengiriman');

        Route::get('/persetujuan', function () {
            return view('owner.stok.persetujuan');
        })->name('persetujuan');

        Route::get('/laporan', function () {
            return view('owner.stok.laporan');
        })->name('laporan');
    });

    Route::get('/riwayat-transaksi', function () {
        return view('owner.riwayat_transaksi');
    })->name('riwayat_transaksi');

    Route::get('/laporan-penjualan', function () {
        return view('owner.laporan_penjualan');
    })->name('laporan_penjualan');

    Route::get('/pengguna', function () {
        return view('owner.pengguna');
    })->name('pengguna');
});

Route::prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', function () {
        return view('kasir.d_kasir');
    })->name('dashboard');

    Route::get('/riwayat-transaksi', function () {
        return view('kasir.riwayat_transaksi');
    })->name('riwayat_transaksi');

    Route::get('/laporan', function () {
        return view('kasir.laporan');
    })->name('laporan');
});

Route::prefix('gudang')->name('gudang.')->group(function () {
    // Dashboard Gudang
    Route::get('/dashboard', function () {
        return view('gudang.d_gudang');
    })->name('dashboard');

    //Halaman Produk
    Route::get('/produk', function () {
        return view('gudang.produk');
    })->name('produk');

    // Halaman Stok
    Route::prefix('stok')->name('stok.')->group(function () {
        // Penerimaan Stok
        Route::get('/penerimaan', function () {
            return view('gudang.stok.penerimaan');
        })->name('penerimaan');

        // Pengiriman Stok
        Route::get('/pengiriman', function () {
            return view('gudang.stok.pengiriman');
        })->name('pengiriman');

        // Penyesuaian Stok
        Route::get('/penyesuaian', function () {
            return view('gudang.stok.penyesuaian');
        })->name('penyesuaian');

        // Laporan Stok
        Route::get('/laporan', function () {
            return view('gudang.laporan');
        })->name('laporan');
    });
});

// Route logout
Route::post('/logout', function () {
    return redirect('/login');
})->name('logout');
>>>>>>> 2b633a9868aea0b356a86fbd3828e85b2a6e727d
