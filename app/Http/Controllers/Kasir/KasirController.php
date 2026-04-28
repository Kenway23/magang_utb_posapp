<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\Pengiriman;
use App\Models\DetailTransaksi;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    // Halaman utama (Dashboard + POS) - menggunakan d_kasir.blade.php
    public function dashboard()
    {
        $today = date('Y-m-d');

        $transaksiHariIni = Transaksi::whereDate('created_at', $today)->count();
        $pendapatanHariIni = Transaksi::whereDate('created_at', $today)->sum('total_harga');
        $transaksiTerbaru = Transaksi::with('kasir')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('kasir.d_kasir', compact('transaksiHariIni', 'pendapatanHariIni', 'transaksiTerbaru'));
    }

    // API: Dashboard Stats
    public function dashboardStats()
    {
        $today = date('Y-m-d');
        $transaksiHariIni = Transaksi::whereDate('created_at', $today)->count();
        $pendapatanHariIni = Transaksi::whereDate('created_at', $today)->sum('total_harga');

        return response()->json([
            'success' => true,
            'transaksi_hari_ini' => $transaksiHariIni,
            'pendapatan_hari_ini' => $pendapatanHariIni
        ]);
    }

    // 🔥 HAPUS METHOD INDEX() INI - TIDAK DIPERLUKAN
    // public function index()
    // {
    //     return view('kasir.pos');
    // }

    // Riwayat Transaksi
    public function riwayat()
    {
        $transaksi = Transaksi::with('kasir')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('kasir.riwayat_transaksi', compact('transaksi'));
    }

    // API: Get Products
    public function getProducts()
    {
        try {
            $produk = Produk::with('kategori')
                ->where('status', 'approved')
                ->where('stok_toko', '>', 0)
                ->orderBy('nama_produk')
                ->get()
                ->map(function ($item) {
                    // 🔥 TAMBAHKAN URL GAMBAR 🔥
                    $imageUrl = null;
                    if ($item->gambar_produk) {
                        // Jika gambar disimpan di storage/public
                        $imageUrl = asset('storage/' . $item->gambar_produk);
                        // Atau jika disimpan langsung di folder public/images
                        // $imageUrl = asset('images/produk/' . $item->gambar_produk);
                    }

                    return [
                        'id' => $item->produk_id,
                        'name' => $item->nama_produk,
                        'category' => $item->kategori->nama_kategori ?? 'Lainnya',
                        'price' => (float) $item->harga,
                        'stock' => $item->stok_toko,
                        'image' => $imageUrl,  // 🔥 TAMBAHKAN INI
                        'bgColor' => $this->getCategoryColor($item->kategori->nama_kategori ?? 'Lainnya', 'bg'),
                        'borderColor' => $this->getCategoryColor($item->kategori->nama_kategori ?? 'Lainnya', 'border'),
                        'badgeColor' => $this->getCategoryColor($item->kategori->nama_kategori ?? 'Lainnya', 'badge'),
                        'priceColor' => $this->getCategoryColor($item->kategori->nama_kategori ?? 'Lainnya', 'price'),
                        'categoryIcon' => $this->getCategoryIcon($item->kategori->nama_kategori ?? 'Lainnya')
                    ];
                });

            return response()->json(['success' => true, 'data' => $produk]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    // API: Get Categories
    public function getCategories()
    {
        try {
            $kategori = Kategori::all()->map(function ($item) {
                return [
                    'id' => $item->kategori_id,
                    'name' => $item->nama_kategori,
                    'icon' => $this->getCategoryIcon($item->nama_kategori),
                    'color' => $this->getCategoryColor($item->nama_kategori, 'btn')
                ];
            });

            $allCategories = collect([
                ['id' => 0, 'name' => 'Semua', 'icon' => 'fas fa-th-large', 'color' => 'bg-gray-600']
            ])->concat($kategori);

            return response()->json(['success' => true, 'data' => $allCategories]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // API: Store Transaction
    public function storeTransaction(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|exists:produks,produk_id',
                'items.*.qty' => 'required|integer|min:1',
                'payment_amount' => 'required|numeric|min:0'
            ]);

            $totalHarga = 0;
            $detailItems = [];

            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['id']);

                if ($produk->stok_toko < $item['qty']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$produk->nama_produk} tidak mencukupi! (Stok: {$produk->stok_toko})"
                    ], 400);
                }

                $subtotal = $produk->harga * $item['qty'];
                $totalHarga += $subtotal;

                $stokTokoSebelum = $produk->stok_toko;
                $produk->stok_toko -= $item['qty'];
                $produk->save();

                $detailItems[] = [
                    'produk_id' => $item['id'],
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $produk->harga,
                    'subtotal' => $subtotal,
                    'stok_sebelum' => $stokTokoSebelum,
                    'stok_sesudah' => $produk->stok_toko
                ];
            }

            $bayar = $request->payment_amount;
            $kembalian = $bayar - $totalHarga;

            if ($kembalian < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uang tidak mencukupi!'
                ], 400);
            }

            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'kasir_id' => Auth::id(),
                'total_harga' => $totalHarga,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'status' => 'COMPLETED'
            ]);

            foreach ($detailItems as $detail) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->transaksi_id,
                    'produk_id' => $detail['produk_id'],
                    'jumlah' => $detail['jumlah'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $detail['subtotal']
                ]);

                // 🔥 Gunakan 'OUT' karena enum tidak memiliki 'SALE'
                StockMovement::create([
                    'produk_id' => $detail['produk_id'],
                    'type' => 'OUT',  // ← Ganti dari 'SALE' ke 'OUT'
                    'jumlah' => $detail['jumlah'],
                    'stok_gudang_sebelum' => 0,
                    'stok_gudang_sesudah' => 0,
                    'stok_toko_sebelum' => $detail['stok_sebelum'],
                    'stok_toko_sesudah' => $detail['stok_sesudah'],
                    'referensi_tabel' => 'transaksis',
                    'referensi_id' => $transaksi->transaksi_id,
                    'keterangan' => 'Penjualan ke customer',
                    'created_by' => Auth::id()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'data' => [
                    'transaction_number' => $transaksi->kode_transaksi,
                    'date' => $transaksi->created_at->format('d/m/Y H:i:s'),
                    'total_amount' => $totalHarga,
                    'payment_amount' => $bayar,
                    'change_amount' => $kembalian
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCategoryColor($category, $type)
    {
        $colors = [
            'Makanan' => ['bg' => 'bg-red-600', 'border' => 'border-red-200', 'badge' => 'bg-red-600', 'price' => 'text-red-600', 'btn' => 'bg-red-600'],
            'Minuman' => ['bg' => 'bg-blue-600', 'border' => 'border-blue-200', 'badge' => 'bg-blue-600', 'price' => 'text-blue-600', 'btn' => 'bg-blue-600'],
            'Makanan Ringan' => ['bg' => 'bg-yellow-600', 'border' => 'border-yellow-200', 'badge' => 'bg-yellow-600', 'price' => 'text-yellow-600', 'btn' => 'bg-yellow-600'],
            'Snack' => ['bg' => 'bg-yellow-600', 'border' => 'border-yellow-200', 'badge' => 'bg-yellow-600', 'price' => 'text-yellow-600', 'btn' => 'bg-yellow-600'],
            'Rokok' => ['bg' => 'bg-gray-600', 'border' => 'border-gray-200', 'badge' => 'bg-gray-600', 'price' => 'text-gray-600', 'btn' => 'bg-gray-600'],
            'Perawatan Tubuh' => ['bg' => 'bg-green-600', 'border' => 'border-green-200', 'badge' => 'bg-green-600', 'price' => 'text-green-600', 'btn' => 'bg-green-600'],
            'Produk Kesehatan' => ['bg' => 'bg-emerald-600', 'border' => 'border-emerald-200', 'badge' => 'bg-emerald-600', 'price' => 'text-emerald-600', 'btn' => 'bg-emerald-600'],
            'Kebutuhan Harian' => ['bg' => 'bg-purple-600', 'border' => 'border-purple-200', 'badge' => 'bg-purple-600', 'price' => 'text-purple-600', 'btn' => 'bg-purple-600'],
            'Kecantikan' => ['bg' => 'bg-pink-600', 'border' => 'border-pink-200', 'badge' => 'bg-pink-600', 'price' => 'text-pink-600', 'btn' => 'bg-pink-600'],
            'Makanan Siap Saji' => ['bg' => 'bg-orange-600', 'border' => 'border-orange-200', 'badge' => 'bg-orange-600', 'price' => 'text-orange-600', 'btn' => 'bg-orange-600'],
            'Produk Segar & Beku' => ['bg' => 'bg-teal-600', 'border' => 'border-teal-200', 'badge' => 'bg-teal-600', 'price' => 'text-teal-600', 'btn' => 'bg-teal-600'],
            'Kebutuhan Ibu & Anak' => ['bg' => 'bg-pink-600', 'border' => 'border-pink-200', 'badge' => 'bg-pink-600', 'price' => 'text-pink-600', 'btn' => 'bg-pink-600'],
            'Makanan Hewan' => ['bg' => 'bg-amber-600', 'border' => 'border-amber-200', 'badge' => 'bg-amber-600', 'price' => 'text-amber-600', 'btn' => 'bg-amber-600'],
            'Mainan' => ['bg' => 'bg-lime-600', 'border' => 'border-lime-200', 'badge' => 'bg-lime-600', 'price' => 'text-lime-600', 'btn' => 'bg-lime-600'],
        ];

        return $colors[$category][$type] ?? ($type === 'btn' ? 'bg-gray-600' : ($type === 'bg' ? 'bg-indigo-600' : 'border-indigo-200'));
    }

    private function getCategoryIcon($category)
    {
        $icons = [
            'Makanan' => '🍚',
            'Minuman' => '🥤',
            'Makanan Ringan' => '🍿',
            'Snack' => '🍿',
            'Rokok' => '🚬',
            'Perawatan Tubuh' => '🧴',
            'Produk Kesehatan' => '💊',
            'Kebutuhan Harian' => '📦',
            'Kecantikan' => '💄',
            'Makanan Siap Saji' => '🍔',
            'Produk Segar & Beku' => '❄️',
            'Kebutuhan Ibu & Anak' => '👶',
            'Makanan Hewan' => '🐕',
            'Mainan' => '🎮',
        ];

        return $icons[$category] ?? '📦';
    }

    public function getRiwayatData()
    {
        try {
            // Cek apakah user login
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $userId = Auth::id();

            $transaksi = Transaksi::with(['kasir', 'details.produk'])
                ->where('kasir_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Jika tidak ada transaksi, return array kosong
            if ($transaksi->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $formattedData = $transaksi->map(function ($item) {
                return [
                    'id' => $item->transaksi_id,
                    'transaction_number' => $item->kode_transaksi ?? 'TRX-' . $item->transaksi_id,
                    'date' => $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : '-',
                    'cashier' => $item->kasir ? $item->kasir->name : 'Kasir',
                    'total_items' => $item->details ? $item->details->sum('jumlah') : 0,
                    'total_amount' => (float) ($item->total_harga ?? 0),
                    'payment_amount' => (float) ($item->bayar ?? 0),
                    'change_amount' => (float) ($item->kembalian ?? 0),
                    'items' => $item->details ? $item->details->map(function ($detail) {
                        return [
                            'name' => $detail->produk ? $detail->produk->nama_produk : '-',
                            'price' => (float) ($detail->harga_satuan ?? 0),
                            'qty' => $detail->jumlah ?? 0,
                            'subtotal' => (float) ($detail->subtotal ?? 0)
                        ];
                    }) : []
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getRiwayatData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Halaman request kirim stok ke toko (untuk kasir)
    public function requestKirimStok()
    {
        $produk = Produk::where('status', 'approved')
            ->where('stok_toko', '>', 0)
            ->orderBy('nama_produk')
            ->get();

        $requests = Pengiriman::with(['produk', 'requester', 'approver'])
            ->where('requested_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kasir.request_kirim_stok', compact('produk', 'requests'));
    }
    // Simpan request kirim stok dari kasir
    public function storeRequestKirimStok(Request $request)
    {
        try {
            $request->validate([
                'produk_id' => 'required|exists:produks,produk_id',
                'jumlah' => 'required|integer|min:1',
                'keterangan' => 'nullable|string'
            ]);

            $produk = Produk::findOrFail($request->produk_id);

            // Generate kode pengiriman
            $kode = 'KRM-' . date('Ymd') . '-' . strtoupper(uniqid());

            $pengiriman = Pengiriman::create([
                'kode_pengiriman' => $kode,
                'produk_id' => $request->produk_id,
                'jumlah' => $request->jumlah,
                'stok_gudang_sebelum' => $produk->stok_gudang,
                'stok_gudang_sesudah' => $produk->stok_gudang - $request->jumlah,
                'stok_toko_sebelum' => $produk->stok_toko,
                'stok_toko_sesudah' => $produk->stok_toko + $request->jumlah,
                'tujuan_toko' => 'Permintaan Kasir',
                'keterangan' => $request->keterangan,
                'status' => 'pending', // Menunggu approve Gudang dulu, lalu Owner
                'requested_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request pengiriman stok berhasil dikirim! Menunggu persetujuan Gudang.',
                'data' => $pengiriman
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRequestKirimStok(Request $request, $id)
    {
        try {
            \Log::info('Update request called for ID: ' . $id);

            $pengiriman = Pengiriman::findOrFail($id);

            // Cek apakah request milik kasir yang login
            if ($pengiriman->requested_by != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengedit request ini'
                ], 403);
            }

            // Cek apakah sudah diproses
            if ($pengiriman->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request sudah diproses, tidak dapat diedit'
                ], 403);
            }

            $request->validate([
                'produk_id' => 'required|exists:produks,produk_id',
                'jumlah' => 'required|integer|min:1',
                'keterangan' => 'nullable|string'
            ]);

            $produk = Produk::findOrFail($request->produk_id);

            $pengiriman->update([
                'produk_id' => $request->produk_id,
                'jumlah' => $request->jumlah,
                'stok_gudang_sebelum' => $produk->stok_gudang,
                'stok_gudang_sesudah' => $produk->stok_gudang - $request->jumlah,
                'stok_toko_sebelum' => $produk->stok_toko,
                'stok_toko_sesudah' => $produk->stok_toko + $request->jumlah,
                'keterangan' => $request->keterangan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request berhasil diupdate!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Update request error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal update request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyRequestKirimStok($id)
    {
        try {
            $pengiriman = Pengiriman::findOrFail($id);

            // Cek apakah request milik kasir yang login
            if ($pengiriman->requested_by != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus request ini'
                ], 403);
            }

            // Cek apakah sudah diproses
            if ($pengiriman->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request sudah diproses, tidak dapat dihapus'
                ], 403);
            }

            $pengiriman->delete();

            return response()->json([
                'success' => true,
                'message' => 'Request berhasil dihapus'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus request: ' . $e->getMessage()
            ], 500);
        }
    }
}