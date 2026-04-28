<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalRequestKasirController extends Controller
{
    public function index()
    {
        // Ambil semua request dari kasir (pending, waiting_owner, approved, rejected)
        $requests = Pengiriman::with(['produk', 'requester', 'approver'])
            ->where('tujuan_toko', 'Permintaan Kasir')
            ->orderByRaw("FIELD(status, 'pending', 'waiting_owner', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->get();

        $statistik = [
            'total' => $requests->count(),
            'menunggu' => $requests->where('status', 'pending')->count(),
            'waiting_owner' => $requests->where('status', 'waiting_owner')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
        ];

        return view('gudang.approval_request_kasir', compact('requests', 'statistik'));
    }

    // 🔥 TAMBAHKAN METHOD INI UNTUK API (diambil oleh fetch JavaScript)
    public function getData()
    {
        try {
            $requests = Pengiriman::with(['produk', 'requester', 'approver'])
                ->where('tujuan_toko', 'Permintaan Kasir')
                ->orderByRaw("FIELD(status, 'pending', 'waiting_owner', 'approved', 'rejected')")
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    // 🔥 Ambil stok gudang TERBARU dari produk
                    $stokGudangTerkini = $item->produk ? $item->produk->stok_gudang : 0;

                    return [
                        'id' => $item->id,
                        'kode_pengiriman' => $item->kode_pengiriman,
                        'jumlah' => $item->jumlah,
                        'stok_toko_sebelum' => $item->stok_toko_sebelum,
                        'stok_gudang_sebelum' => $stokGudangTerkini,  // ← STOK TERBARU DARI DATABASE
                        'keterangan' => $item->keterangan,
                        'status' => $item->status,
                        'created_at' => $item->created_at,
                        'alasan_ditolak' => $item->alasan_ditolak,
                        'approved_at' => $item->approved_at,
                        'produk' => $item->produk ? [
                            'nama_produk' => $item->produk->nama_produk
                        ] : null,
                        'requester' => $item->requester ? [
                            'name' => $item->requester->name
                        ] : null,
                        'approver' => $item->approver ? [
                            'name' => $item->approver->name
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $requests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        try {
            $pengiriman = Pengiriman::findOrFail($id);

            if ($pengiriman->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request sudah diproses sebelumnya'
                ]);
            }

            // Cek stok gudang
            $produk = Produk::findOrFail($pengiriman->produk_id);
            if ($produk->stok_gudang < $pengiriman->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok gudang tidak mencukupi! Stok tersedia: ' . $produk->stok_gudang
                ], 400);
            }

            // Update status menjadi waiting_owner (menunggu Owner)
            $pengiriman->update([
                'status' => 'waiting_owner',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request disetujui! Sekarang menunggu persetujuan Owner.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal approve: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $pengiriman = Pengiriman::findOrFail($id);

            if ($pengiriman->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request sudah diproses sebelumnya'
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
                'message' => 'Request ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal reject: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detail($id)
    {
        try {
            $pengiriman = Pengiriman::with(['produk', 'requester'])->findOrFail($id);

            // Ambil stok gudang terbaru
            $stokGudangTerkini = $pengiriman->produk ? $pengiriman->produk->stok_gudang : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pengiriman->id,
                    'kode_pengiriman' => $pengiriman->kode_pengiriman,
                    'jumlah' => $pengiriman->jumlah,
                    'stok_toko_sebelum' => $pengiriman->stok_toko_sebelum,
                    'stok_gudang_sebelum' => $stokGudangTerkini,
                    'keterangan' => $pengiriman->keterangan,
                    'status' => $pengiriman->status,
                    'created_at' => $pengiriman->created_at,
                    'approved_at' => $pengiriman->approved_at,
                    'alasan_ditolak' => $pengiriman->alasan_ditolak,
                    'produk' => $pengiriman->produk,
                    'requester' => $pengiriman->requester,
                    'approver' => $pengiriman->approver
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail'
            ], 500);
        }
    }
}