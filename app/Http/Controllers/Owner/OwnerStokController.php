<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\TambahStock;
use App\Models\Pengiriman;  // 🔥 TAMBAHKAN MODEL PENGIRIMAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerStokController extends Controller
{
    // ==================== HALAMAN APPROVAL UTAMA ====================
    public function approval()
    {
        $produkPending = Produk::with('kategori', 'creator')
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 🔥 AMBIL DATA PENGIRIMAN UNTUK DITAMPILKAN DI VIEW
        $pengiriman = Pengiriman::with(['produk', 'requester', 'approver'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('owner.stok.approval', compact('produkPending', 'pengiriman'));
    }

    public function getApprovalData()
    {
        try {
            // Data produk baru
            $produkBaru = Produk::with('kategori', 'creator')
                ->whereIn('status', ['pending', 'approved', 'rejected'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->produk_id,
                        'type' => 'produk',
                        'nama' => $item->nama_produk,
                        'kategori' => $item->kategori->nama_kategori ?? 'Lainnya',
                        'harga' => number_format($item->harga, 0, ',', '.'),
                        'stok' => $item->stok_gudang,
                        'diajukan_oleh' => $item->creator->name ?? 'Gudang',
                        'tanggal' => $item->created_at->format('d/m/Y H:i'),
                        'status' => $item->status,
                        'gambar' => $item->gambar_produk,
                        'alasan_ditolak' => $item->alasan_ditolak
                    ];
                });

            // Data tambah stok
            $tambahStok = TambahStock::with(['produk', 'requester'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'tambah_stok',
                        'nama' => $item->produk->nama_produk ?? '-',
                        'jumlah_request' => $item->jumlah_request,
                        'stok_sebelum' => $item->stok_sebelum,
                        'stok_sesudah' => $item->stok_sesudah,
                        'supplier' => $item->supplier ?? '-',
                        'keterangan' => $item->keterangan,
                        'diajukan_oleh' => $item->requester->name ?? 'Gudang',
                        'tanggal' => $item->created_at->format('d/m/Y H:i'),
                        'status' => $item->status,
                        'alasan_ditolak' => $item->alasan_ditolak
                    ];
                });

            // 🔥 TAMBAH DATA PENGIRIMAN
            $pengiriman = \App\Models\Pengiriman::with(['produk', 'requester', 'approver'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'pengiriman',
                        'kode' => $item->kode_pengiriman,
                        'nama' => $item->produk->nama_produk ?? '-',
                        'jumlah' => $item->jumlah,
                        'stok_gudang_sebelum' => $item->stok_gudang_sebelum,
                        'stok_gudang_sesudah' => $item->stok_gudang_sesudah,
                        'stok_toko_sebelum' => $item->stok_toko_sebelum,
                        'stok_toko_sesudah' => $item->stok_toko_sesudah,
                        'tujuan_toko' => $item->tujuan_toko ?? '-',
                        'keterangan' => $item->keterangan,
                        'diajukan_oleh' => $item->requester->name ?? 'Gudang',
                        'tanggal' => $item->created_at->format('d/m/Y H:i'),
                        'status' => $item->status,
                        'alasan_ditolak' => $item->alasan_ditolak
                    ];
                });

            $data = [
                'produk_baru' => $produkBaru,
                'tambah_stok' => $tambahStok,
                'pengiriman' => $pengiriman,  // 🔥 TAMBAHKAN
                'penerimaan' => [],
                'penyesuaian' => []
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }
    // ==================== APPROVAL PRODUK BARU ====================
    public function approveProduk($id)
    {
        try {
            $item = Produk::findOrFail($id);

            if ($item->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk ini sudah diproses sebelumnya'
                ]);
            }

            $item->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_ditolak' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk "' . $item->nama_produk . '" berhasil disetujui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectProduk($id, Request $request)
    {
        try {
            $item = Produk::findOrFail($id);
            $alasan = $request->alasan ?? 'Tidak ada alasan';

            if ($item->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk ini sudah diproses sebelumnya'
                ]);
            }

            $item->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_ditolak' => $alasan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk "' . $item->nama_produk . '" ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== APPROVAL TAMBAH STOK ====================
    public function approveTambahStok($id)
    {
        try {
            $tambahStock = TambahStock::findOrFail($id);

            if ($tambahStock->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request sudah diproses sebelumnya'
                ]);
            }

            $produk = Produk::findOrFail($tambahStock->produk_id);
            $produk->stok_gudang += $tambahStock->jumlah_request;
            $produk->save();

            $tambahStock->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'stok_sesudah' => $produk->stok_gudang
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request tambah stok disetujui! Stok bertambah ' . $tambahStock->jumlah_request
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal approve: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectTambahStok(Request $request, $id)
    {
        try {
            $tambahStock = TambahStock::findOrFail($id);

            if ($tambahStock->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request sudah diproses sebelumnya'
                ]);
            }

            $alasan = $request->alasan ?? 'Tidak ada alasan';

            $tambahStock->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_ditolak' => $alasan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request tambah stok ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal reject: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== 🔥 APPROVAL PENGIRIMAN (GUDANG → TOKO) 🔥 ====================
    public function approvePengiriman($id)
    {
        try {
            $pengiriman = Pengiriman::findOrFail($id);

            if ($pengiriman->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request pengiriman sudah diproses sebelumnya'
                ]);
            }

            // Update stok produk
            $produk = Produk::findOrFail($pengiriman->produk_id);

            // Kurangi stok gudang, tambah stok toko
            $produk->stok_gudang -= $pengiriman->jumlah;
            $produk->stok_toko += $pengiriman->jumlah;
            $produk->save();

            // Update status pengiriman
            $pengiriman->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'stok_gudang_sesudah' => $produk->stok_gudang,
                'stok_toko_sesudah' => $produk->stok_toko
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengiriman stok disetujui! Stok sudah dipindahkan ke toko.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal approve pengiriman: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectPengiriman(Request $request, $id)
    {
        try {
            $pengiriman = Pengiriman::findOrFail($id);

            if ($pengiriman->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request pengiriman sudah diproses sebelumnya'
                ]);
            }

            $alasan = $request->alasan ?? 'Tidak ada alasan';

            $pengiriman->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_ditolak' => $alasan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengiriman stok ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal reject pengiriman: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== PENERIMAAN STOK (DARI SUPPLIER) ====================
    public function penerimaan()
    {
        return view('owner.stok.penerimaan');
    }

    public function approvePenerimaanStok($id)
    {
        return response()->json(['success' => true, 'message' => 'Penerimaan stok disetujui']);
    }

    public function rejectPenerimaanStok($id, Request $request)
    {
        $alasan = $request->alasan ?? 'Tidak ada alasan';
        return response()->json(['success' => true, 'message' => 'Penerimaan stok ditolak: ' . $alasan]);
    }

    // ==================== PENGIRIMAN STOK (KE TOKO) - VIEW ====================
    public function pengiriman()
    {
        return view('owner.stok.pengiriman');
    }

    public function kirimKeToko(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Stok dikirim ke toko']);
    }

    // ==================== LAPORAN STOK ====================
    public function laporan()
    {
        return view('owner.stok.laporan');
    }
}