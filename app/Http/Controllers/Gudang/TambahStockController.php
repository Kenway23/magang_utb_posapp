<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\TambahStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TambahStockController extends Controller
{
    public function index()
    {
        // Ambil produk yang sudah approved untuk ditampilkan di dropdown
        $produk = Produk::where('status', 'approved')
            ->orderBy('nama_produk')
            ->get();

        // Ambil riwayat request dari gudang yang sedang login
        $requests = TambahStock::with(['produk', 'requester', 'approver'])
            ->where('requested_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // 🔥 VIEW berada di gudang/stok/tambah_stok
        return view('gudang.stok.tambah_stok', compact('produk', 'requests'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'produk_id' => 'required|exists:produks,produk_id',
                'jumlah_request' => 'required|integer|min:1',
                'supplier' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string'
            ]);

            $produk = Produk::findOrFail($request->produk_id);

            $tambahStock = TambahStock::create([
                'produk_id' => $request->produk_id,
                'jumlah_request' => $request->jumlah_request,
                'stok_sebelum' => $produk->stok_gudang,
                'stok_sesudah' => $produk->stok_gudang + $request->jumlah_request,
                'keterangan' => $request->keterangan,
                'supplier' => $request->supplier,
                'status' => 'pending',
                'requested_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request tambah stok berhasil dikirim! Menunggu persetujuan Owner.',
                'data' => $tambahStock
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tambahStock = TambahStock::findOrFail($id);

            // Cek apakah sudah approved, jika sudah tidak bisa edit
            if ($tambahStock->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request sudah disetujui, tidak dapat diedit'
                ], 403);
            }

            $request->validate([
                'produk_id' => 'required|exists:produks,produk_id',
                'jumlah_request' => 'required|integer|min:1',
                'supplier' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string'
            ]);

            $produk = Produk::findOrFail($request->produk_id);

            $tambahStock->update([
                'produk_id' => $request->produk_id,
                'jumlah_request' => $request->jumlah_request,
                'stok_sebelum' => $produk->stok_gudang,
                'stok_sesudah' => $produk->stok_gudang + $request->jumlah_request,
                'keterangan' => $request->keterangan,
                'supplier' => $request->supplier,
                'status' => 'pending', // Kembali ke pending untuk minta approve ulang
                'approved_by' => null,
                'approved_at' => null,
                'alasan_ditolak' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request berhasil diupdate! Menunggu persetujuan ulang dari Owner.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tambahStock = TambahStock::findOrFail($id);

            // Cek apakah sudah approved, jika sudah tidak bisa dihapus
            if ($tambahStock->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request sudah disetujui, tidak dapat dihapus'
                ], 403);
            }

            $tambahStock->delete();

            return response()->json([
                'success' => true,
                'message' => 'Request berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus request: ' . $e->getMessage()
            ], 500);
        }
    }
}