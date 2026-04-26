<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Pengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengirimanController extends Controller
{
    // Halaman daftar pengiriman
    public function index()
    {
        $produk = Produk::where('status', 'approved')
            ->where('stok_gudang', '>', 0)
            ->orderBy('nama_produk')
            ->get();

        $pengiriman = Pengiriman::with(['produk', 'requester', 'approver'])
            ->where('requested_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // 🔥 View berada di gudang/stok/pengiriman
        return view('gudang.stok.pengiriman', compact('produk', 'pengiriman'));
    }

    // Simpan request pengiriman
    public function store(Request $request)
    {
        try {
            $request->validate([
                'produk_id' => 'required|exists:produks,produk_id',
                'jumlah' => 'required|integer|min:1',
                'tujuan_toko' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string'
            ]);

            $produk = Produk::findOrFail($request->produk_id);

            // Cek stok mencukupi
            if ($produk->stok_gudang < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok gudang tidak mencukupi! Stok tersedia: ' . $produk->stok_gudang
                ], 400);
            }

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
                'tujuan_toko' => $request->tujuan_toko,
                'keterangan' => $request->keterangan,
                'status' => 'pending',
                'requested_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request pengiriman stok berhasil dikirim! Menunggu persetujuan Owner.',
                'data' => $pengiriman
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim request: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update request pengiriman
    public function update(Request $request, $id)
    {
        try {
            $pengiriman = Pengiriman::findOrFail($id);

            // Cek apakah sudah approved
            if ($pengiriman->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request sudah disetujui, tidak dapat diedit'
                ], 403);
            }

            $request->validate([
                'produk_id' => 'required|exists:produks,produk_id',
                'jumlah' => 'required|integer|min:1',
                'tujuan_toko' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string'
            ]);

            $produk = Produk::findOrFail($request->produk_id);

            if ($produk->stok_gudang < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok gudang tidak mencukupi! Stok tersedia: ' . $produk->stok_gudang
                ], 400);
            }

            $pengiriman->update([
                'produk_id' => $request->produk_id,
                'jumlah' => $request->jumlah,
                'stok_gudang_sebelum' => $produk->stok_gudang,
                'stok_gudang_sesudah' => $produk->stok_gudang - $request->jumlah,
                'stok_toko_sebelum' => $produk->stok_toko,
                'stok_toko_sesudah' => $produk->stok_toko + $request->jumlah,
                'tujuan_toko' => $request->tujuan_toko,
                'keterangan' => $request->keterangan,
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'alasan_ditolak' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request pengiriman berhasil diupdate! Menunggu persetujuan ulang.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update request: ' . $e->getMessage()
            ], 500);
        }
    }

    // Hapus request pengiriman
    public function destroy($id)
    {
        try {
            $pengiriman = Pengiriman::findOrFail($id);

            if ($pengiriman->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request sudah disetujui, tidak dapat dihapus'
                ], 403);
            }

            $pengiriman->delete();

            return response()->json([
                'success' => true,
                'message' => 'Request pengiriman berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus request: ' . $e->getMessage()
            ], 500);
        }
    }
}