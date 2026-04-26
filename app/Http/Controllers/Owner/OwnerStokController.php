<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\TambahStock;
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

        return view('owner.stok.approval', compact('produkPending'));
    }
    public function getApprovalData()
    {
        try {
            // 🔥 AMBIL SEMUA DATA (pending, approved, rejected) untuk produk baru
            $produkBaru = Produk::with('kategori', 'creator')
                ->whereIn('status', ['pending', 'approved', 'rejected'])  // Ambil semua status
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

            // 🔥 AMBIL SEMUA DATA (pending, approved, rejected) untuk tambah stok
            $tambahStok = TambahStock::with(['produk', 'requester'])
                ->orderBy('created_at', 'desc')  // Ambil semua, tidak difilter status
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

            $data = [
                'produk_baru' => $produkBaru,
                'tambah_stok' => $tambahStok,
                'penerimaan' => [],
                'pengiriman' => [],
                'penyesuaian' => []
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat数据: ' . $e->getMessage()
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

    // ==================== APPROVAL TAMBAH STOK (DARI GUDANG) ====================
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

            // Update stok produk
            $produk = Produk::findOrFail($tambahStock->produk_id);
            $produk->stok_gudang += $tambahStock->jumlah_request;
            $produk->save();

            // Update status request
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

    // ==================== PENERIMAAN STOK (DARI SUPPLIER) ====================
    public function penerimaan()
    {
        return view('owner.stok.penerimaan');
    }

    public function approvePenerimaan($id)
    {
        return response()->json(['success' => true, 'message' => 'Penerimaan stok disetujui']);
    }

    public function rejectPenerimaan($id, Request $request)
    {
        $alasan = $request->alasan ?? 'Tidak ada alasan';
        return response()->json(['success' => true, 'message' => 'Penerimaan stok ditolak: ' . $alasan]);
    }

    // ==================== PENGIRIMAN STOK (KE TOKO) ====================
    public function pengiriman()
    {
        return view('owner.stok.pengiriman');
    }

    public function approvePengiriman($id)
    {
        return response()->json(['success' => true, 'message' => 'Pengiriman stok disetujui']);
    }

    public function rejectPengiriman($id, Request $request)
    {
        $alasan = $request->alasan ?? 'Tidak ada alasan';
        return response()->json(['success' => true, 'message' => 'Pengiriman stok ditolak: ' . $alasan]);
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