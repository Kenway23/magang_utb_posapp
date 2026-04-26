<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerStokController extends Controller
{
    // ==================== PENERIMAAN STOK ====================
    public function penerimaan()
    {
        return view('owner.stok.penerimaan');
    }

    public function approvePenerimaan($id)
    {
        return response()->json(['success' => true, 'message' => 'Penerimaan disetujui']);
    }

    public function rejectPenerimaan($id)
    {
        return response()->json(['success' => true, 'message' => 'Penerimaan ditolak']);
    }

    // ==================== PENGIRIMAN STOK ====================
    public function pengiriman()
    {
        return view('owner.stok.pengiriman');
    }

    public function approvePengiriman($id)
    {
        return response()->json(['success' => true, 'message' => 'Pengiriman disetujui']);
    }

    public function rejectPengiriman($id)
    {
        return response()->json(['success' => true, 'message' => 'Pengiriman ditolak']);
    }

    public function kirimKeToko(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Stok dikirim ke toko']);
    }

    // ==================== APPROVAL PRODUK (untuk route /stok/approval/produk/{id}/approve) ====================
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

    // ==================== APPROVAL PENERIMAAN STOK ====================
    public function approvePenerimaanStok($id)
    {
        return response()->json(['success' => true, 'message' => 'Penerimaan stok disetujui']);
    }

    public function rejectPenerimaanStok($id, Request $request)
    {
        $alasan = $request->alasan ?? 'Tidak ada alasan';
        return response()->json(['success' => true, 'message' => 'Penerimaan stok ditolak: ' . $alasan]);
    }

    // ==================== APPROVAL PENGIRIMAN STOK ====================
    public function approvePengirimanStok($id)
    {
        return response()->json(['success' => true, 'message' => 'Pengiriman stok disetujui']);
    }

    public function rejectPengirimanStok($id, Request $request)
    {
        $alasan = $request->alasan ?? 'Tidak ada alasan';
        return response()->json(['success' => true, 'message' => 'Pengiriman stok ditolak: ' . $alasan]);
    }

    // ==================== APPROVAL AKTIVITAS GUDANG (VIEW & DATA) ====================
    public function approval()
    {
        $produkPending = Produk::with('kategori', 'creator')
            ->whereIn('status', ['pending', 'approved', 'rejected']) // Ambil semua status
            ->orderBy('created_at', 'desc')
            ->get();

        // Debug: cek jumlah data
        \Log::info('Jumlah produk di approval: ' . $produkPending->count());

        return view('owner.stok.approval', compact('produkPending'));
    }

    public function getApprovalData()
    {
        try {
            $produkBaru = Produk::with('kategori', 'creator')
                ->where('status', 'pending')
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
                        'gambar' => $item->gambar_produk
                    ];
                });

            $data = [
                'produk_baru' => $produkBaru,
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
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== LAPORAN STOK ====================
    public function laporan()
    {
        return view('owner.stok.laporan');
    }
}