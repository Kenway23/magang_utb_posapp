<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use App\Models\TambahStock;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatGudangController extends Controller
{
    public function index()
    {
        return view('gudang.riwayat_gudang');
    }

    public function getData(Request $request)
    {
        try {
            $riwayat = [];

            // 1. TAMBAH STOK (PENERIMAAN)
            $tambahStok = TambahStock::with(['produk', 'requester'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'kode' => 'TSK-' . $item->id,
                        'type' => 'Penerimaan',
                        'type_icon' => '📥',
                        'type_color' => 'bg-green-100 text-green-700',
                        'produk' => $item->produk->nama_produk ?? 'Produk tidak ditemukan',
                        'qty_display' => '+' . $item->jumlah_request,
                        'tanggal' => $item->created_at->format('d/m/Y H:i'),
                        'user' => $item->requester->name ?? 'Gudang',
                        'keterangan' => 'Request tambah stok dari gudang',
                        'status' => $item->status,
                        'detail' => [
                            'supplier' => $item->supplier ?? '-',
                            'stok_sebelum' => $item->stok_sebelum,
                            'stok_sesudah' => $item->stok_sesudah
                        ]
                    ];
                });

            // 2. PENGIRIMAN (PENGELUARAN) - DARI GUDANG
            $pengiriman = Pengiriman::with(['produk', 'requester'])
                ->where('requested_by', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'kode' => $item->kode_pengiriman,
                        'type' => 'Pengeluaran',
                        'type_icon' => '📤',
                        'type_color' => 'bg-red-100 text-red-700',
                        'produk' => $item->produk->nama_produk ?? 'Produk tidak ditemukan',
                        'qty_display' => '-' . $item->jumlah,
                        'tanggal' => $item->created_at->format('d/m/Y H:i'),
                        'user' => $item->requester->name ?? 'Gudang',
                        'keterangan' => 'Pengiriman stok ke ' . ($item->tujuan_toko ?? 'Toko'),
                        'status' => $item->status,
                        'detail' => [
                            'tujuan_toko' => $item->tujuan_toko ?? '-',
                            'stok_gudang_sebelum' => $item->stok_gudang_sebelum,
                            'stok_gudang_sesudah' => $item->stok_gudang_sesudah
                        ]
                    ];
                });

            // 3. PENGIRIMAN DARI KASIR (REQUEST KASIR)
            $kasirRequests = Pengiriman::with(['produk', 'requester'])
                ->where('tujuan_toko', 'Permintaan Kasir')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'kode' => $item->kode_pengiriman,
                        'type' => 'Request Kasir',
                        'type_icon' => '🏪',
                        'type_color' => 'bg-purple-100 text-purple-700',
                        'produk' => $item->produk->nama_produk ?? 'Produk tidak ditemukan',
                        'qty_display' => '+' . $item->jumlah,
                        'tanggal' => $item->created_at->format('d/m/Y H:i'),
                        'user' => $item->requester->name ?? 'Kasir',
                        'keterangan' => 'Request stok dari kasir',
                        'status' => $item->status,
                        'detail' => [
                            'alasan_ditolak' => $item->alasan_ditolak
                        ]
                    ];
                });

            // 4. PENYESUAIAN STOK
            $penyesuaian = StockAdjustment::with(['produk', 'creator'])
                ->where('created_by', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    $perubahanText = $item->perubahan >= 0 ? '+' . $item->perubahan : (string) $item->perubahan;
                    $jenisText = $item->jenis === 'plus' ? 'Penambahan' : 'Pengurangan';
                    return [
                        'kode' => $item->kode_adjustment,
                        'type' => 'Penyesuaian',
                        'type_icon' => '⚙️',
                        'type_color' => 'bg-blue-100 text-blue-700',
                        'produk' => $item->produk->nama_produk ?? 'Produk tidak ditemukan',
                        'qty_display' => $perubahanText,
                        'tanggal' => $item->created_at->format('d/m/Y H:i'),
                        'user' => $item->creator->name ?? 'Gudang',
                        'keterangan' => $item->alasan ?? 'Penyesuaian stok (' . $jenisText . ')',
                        'status' => $item->status,
                        'detail' => [
                            'alasan' => $item->alasan,
                            'stok_sebelum' => $item->stok_sebelum,
                            'stok_sesudah' => $item->stok_sesudah,
                            'jenis' => $jenisText
                        ]
                    ];
                });

            // Gabungkan semua data
            $allRiwayat = collect()
                ->concat($tambahStok)
                ->concat($pengiriman)
                ->concat($kasirRequests)
                ->concat($penyesuaian)
                ->sortByDesc('tanggal')
                ->values();

            return response()->json([
                'success' => true,
                'data' => $allRiwayat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat数据: ' . $e->getMessage()
            ], 500);
        }
    }
}