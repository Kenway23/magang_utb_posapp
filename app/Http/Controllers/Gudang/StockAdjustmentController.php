<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentController extends Controller
{
    // Halaman utama penyesuaian stok
    public function index()
    {
        // Tampilkan semua adjustment milik gudang (draft, pending, approved, rejected)
        $adjustments = StockAdjustment::with(['produk', 'creator', 'approver'])
            ->where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $produk = Produk::where('status', 'approved')
            ->orderBy('nama_produk')
            ->get();

        return view('gudang.stok.penyesuaian', compact('adjustments', 'produk'));
    }

    // Simpan penyesuaian stok sebagai DRAFT
    public function store(Request $request)
    {
        try {
            $request->validate([
                'produk_id' => 'required|exists:produks,produk_id',
                'stok_baru' => 'required|integer|min:0',
                'alasan' => 'required|string',
                'keterangan' => 'nullable|string'
            ]);

            $produk = Produk::findOrFail($request->produk_id);
            $stokLama = $produk->stok_gudang;
            $stokBaru = $request->stok_baru;
            $perubahan = $stokBaru - $stokLama;
            $jenis = $perubahan >= 0 ? 'plus' : 'minus';

            if ($stokBaru < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak boleh negatif!'
                ], 400);
            }

            $adjustment = StockAdjustment::create([
                'kode_adjustment' => StockAdjustment::generateKode(),
                'produk_id' => $request->produk_id,
                'stok_sebelum' => $stokLama,
                'stok_sesudah' => $stokBaru,
                'perubahan' => $perubahan,
                'jenis' => $jenis,
                'alasan' => $request->alasan,
                'keterangan' => $request->keterangan,
                'status' => 'draft',
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penyesuaian stok berhasil disimpan sebagai draft.',
                'data' => $adjustment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    // UPDATE Penyesuaian (DRAFT atau PENDING bisa diedit)
    public function update(Request $request, $id)
    {
        try {
            $adjustment = StockAdjustment::findOrFail($id);

            // Cek kepemilikan
            if ($adjustment->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengedit adjustment ini'
                ], 403);
            }

            // 🔥 Yang bisa diedit: DRAFT atau PENDING (menunggu owner)
            if (!in_array($adjustment->status, ['draft', 'pending'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment dengan status ' . ucfirst($adjustment->status) . ' tidak dapat diedit'
                ], 403);
            }

            $request->validate([
                'produk_id' => 'required|exists:produks,produk_id',
                'stok_baru' => 'required|integer|min:0',
                'alasan' => 'required|string',
                'keterangan' => 'nullable|string'
            ]);

            $produk = Produk::findOrFail($request->produk_id);
            $stokLama = $produk->stok_gudang;
            $stokBaru = $request->stok_baru;
            $perubahan = $stokBaru - $stokLama;
            $jenis = $perubahan >= 0 ? 'plus' : 'minus';

            if ($stokBaru < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak boleh negatif!'
                ], 400);
            }

            // Update adjustment
            $adjustment->update([
                'produk_id' => $request->produk_id,
                'stok_sebelum' => $stokLama,
                'stok_sesudah' => $stokBaru,
                'perubahan' => $perubahan,
                'jenis' => $jenis,
                'alasan' => $request->alasan,
                'keterangan' => $request->keterangan,
                // Status tetap sama (draft/pending)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penyesuaian stok berhasil diupdate',
                'data' => $adjustment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update: ' . $e->getMessage()
            ], 500);
        }
    }

    // Kirim draft ke owner untuk approval
    public function submit($id)
    {
        try {
            $adjustment = StockAdjustment::findOrFail($id);

            if ($adjustment->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyesuaian sudah diproses'
                ]);
            }

            if ($adjustment->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses'
                ]);
            }

            $adjustment->update([
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penyesuaian telah dikirim ke Owner untuk disetujui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal submit: ' . $e->getMessage()
            ], 500);
        }
    }

    // HAPUS Penyesuaian (DRAFT atau PENDING bisa dihapus)
    public function destroy($id)
    {
        try {
            $adjustment = StockAdjustment::findOrFail($id);

            // Cek kepemilikan
            if ($adjustment->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus adjustment ini'
                ], 403);
            }

            // 🔥 Yang bisa dihapus: DRAFT atau PENDING (menunggu owner)
            if (!in_array($adjustment->status, ['draft', 'pending'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment dengan status ' . ucfirst($adjustment->status) . ' tidak dapat dihapus'
                ], 403);
            }

            $adjustment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Penyesuaian berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get detail adjustment
    public function getDetail($id)
    {
        try {
            $adjustment = StockAdjustment::with(['produk', 'creator', 'approver'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $adjustment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }
}