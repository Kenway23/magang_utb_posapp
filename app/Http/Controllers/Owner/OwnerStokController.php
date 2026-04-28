<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\TambahStock;
use App\Models\Pengiriman;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OwnerStokController extends Controller
{
    // ==================== HALAMAN APPROVAL UTAMA ====================
    public function approval()
    {
        return view('owner.stok.approval');
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
                        'nama' => $item->nama_produk,
                        'kategori' => $item->kategori->nama_kategori ?? 'Lainnya',
                        'harga' => number_format($item->harga, 0, ',', '.'),
                        'stok' => $item->stok_gudang,
                        'diajukan_oleh' => $item->creator->name ?? 'Gudang',
                        'tanggal' => $item->created_at->toISOString(),
                        'status' => $item->status,
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
                        'nama' => $item->produk->nama_produk ?? '-',
                        'jumlah_request' => $item->jumlah_request,
                        'stok_sebelum' => $item->stok_sebelum,
                        'stok_sesudah' => $item->stok_sesudah,
                        'supplier' => $item->supplier ?? '-',
                        'diajukan_oleh' => $item->requester->name ?? 'Gudang',
                        'tanggal' => $item->created_at->format('d/m/Y H:i'),
                        'status' => $item->status,
                        'alasan_ditolak' => $item->alasan_ditolak
                    ];
                });

            // 🔥 DATA PENGIRIMAN - HANYA YANG SUDAH DI-ACC GUDANG (waiting_owner)
            $pengiriman = Pengiriman::with(['produk', 'requester', 'approver'])
                ->whereIn('status', ['waiting_owner', 'approved', 'rejected'])  // 🔥 TIDAK termasuk 'pending'
                ->orderByRaw("FIELD(status, 'waiting_owner', 'approved', 'rejected')")
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    // Ambil stok gudang TERBARU dari produk
                    $stokGudangTerkini = $item->produk ? $item->produk->stok_gudang : 0;

                    return [
                        'id' => $item->id,
                        'kode' => $item->kode_pengiriman,
                        'nama' => $item->produk->nama_produk ?? '-',
                        'jumlah' => $item->jumlah,
                        'stok_gudang_sebelum' => $stokGudangTerkini,
                        'stok_gudang_sesudah' => $item->stok_gudang_sesudah,
                        'tujuan_toko' => $item->tujuan_toko ?? '-',
                        'diajukan_oleh' => $item->requester->name ?? 'Gudang',
                        'tanggal' => $item->created_at->format('d/m/Y H:i'),
                        'status' => $item->status,
                        'alasan_ditolak' => $item->alasan_ditolak
                    ];
                });

            // Data penyesuaian stok
            $penyesuaian = StockAdjustment::with(['produk', 'creator', 'approver'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'kode' => $item->kode_adjustment,
                        'nama' => $item->produk->nama_produk ?? '-',
                        'stok_sebelum' => $item->stok_sebelum,
                        'stok_sesudah' => $item->stok_sesudah,
                        'perubahan' => $item->perubahan,
                        'jenis' => $item->jenis,
                        'alasan' => $item->alasan,
                        'keterangan' => $item->keterangan,
                        'diajukan_oleh' => $item->creator->name ?? 'Gudang',
                        'tanggal' => $item->created_at->format('d/m/Y H:i'),
                        'status' => $item->status,
                        'alasan_ditolak' => $item->alasan_ditolak
                    ];
                });

            $data = [
                'produk_baru' => $produkBaru,
                'tambah_stok' => $tambahStok,
                'pengiriman' => $pengiriman,
                'penyesuaian' => $penyesuaian,
                'penerimaan' => []
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
                return response()->json(['success' => false, 'message' => 'Produk sudah diproses']);
            }
            $item->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_ditolak' => null
            ]);
            return response()->json(['success' => true, 'message' => 'Produk "' . $item->nama_produk . '" berhasil disetujui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal approve: ' . $e->getMessage()], 500);
        }
    }

    public function rejectProduk(Request $request, $id)
    {
        try {
            $item = Produk::findOrFail($id);
            if ($item->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Produk sudah diproses']);
            }
            $alasan = $request->alasan ?? 'Tidak ada alasan';
            $item->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_ditolak' => $alasan
            ]);
            return response()->json(['success' => true, 'message' => 'Produk "' . $item->nama_produk . '" ditolak']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal reject: ' . $e->getMessage()], 500);
        }
    }

    // ==================== APPROVAL TAMBAH STOK ====================
    public function approveTambahStok($id)
    {
        try {
            $tambahStock = TambahStock::findOrFail($id);
            if ($tambahStock->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Request sudah diproses']);
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
            return response()->json(['success' => true, 'message' => 'Request tambah stok disetujui! Stok bertambah ' . $tambahStock->jumlah_request]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal approve: ' . $e->getMessage()], 500);
        }
    }

    public function rejectTambahStok(Request $request, $id)
    {
        try {
            $tambahStock = TambahStock::findOrFail($id);
            if ($tambahStock->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Request sudah diproses']);
            }
            $alasan = $request->alasan ?? 'Tidak ada alasan';
            $tambahStock->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_ditolak' => $alasan
            ]);
            return response()->json(['success' => true, 'message' => 'Request tambah stok ditolak']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal reject: ' . $e->getMessage()], 500);
        }
    }

    // ==================== APPROVAL PENGIRIMAN ====================
    // ==================== APPROVAL PENGIRIMAN ====================
    public function approvePengiriman($id)
    {
        try {
            DB::beginTransaction();

            $pengiriman = Pengiriman::findOrFail($id);

            if (!in_array($pengiriman->status, ['pending', 'waiting_owner'])) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Request sudah diproses']);
            }

            // 🔥 AMBIL PRODUK DENGAN LOCK UNTUK MENGHINDARI RACE CONDITION
            $produk = Produk::where('produk_id', $pengiriman->produk_id)->lockForUpdate()->first();

            if (!$produk) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
            }

            // 🔥 AMBIL STOK TERKINI DARI DATABASE (BUKAN DARI DATA LAMA)
            $stokTersedia = $produk->stok_gudang;
            $stokDibutuhkan = $pengiriman->jumlah;

            // 🔥 LOG UNTUK DEBUG
            \Log::info('Approve Pengiriman - ID: ' . $id);
            \Log::info('Stok Gudang Saat Ini: ' . $stokTersedia);
            \Log::info('Jumlah Request: ' . $stokDibutuhkan);

            // 🔥 VALIDASI STOK - CEK APAKAH CUKUP
            if ($stokTersedia <= 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "❌ Stok gudang HABIS (0 pcs)! Tidak dapat menyetujui pengiriman.\n\nSilakan tambah stok terlebih dahulu melalui menu 'Tambah Stok'."
                ], 400);
            }

            if ($stokTersedia < $stokDibutuhkan) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "⚠️ Stok gudang tidak mencukupi!\n\n📦 Stok tersedia: {$stokTersedia} pcs\n📤 Dibutuhkan: {$stokDibutuhkan} pcs\n❌ Kekurangan: " . ($stokDibutuhkan - $stokTersedia) . " pcs\n\n💡 Silakan tambah stok terlebih dahulu."
                ], 400);
            }

            // Update stok produk
            $produk->stok_gudang = $stokTersedia - $stokDibutuhkan;
            $produk->stok_toko += $stokDibutuhkan;
            $produk->save();

            // Update status pengiriman
            $pengiriman->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'stok_gudang_sesudah' => $produk->stok_gudang,
                'stok_toko_sesudah' => $produk->stok_toko
            ]);

            // Catat ke stock movement
            \App\Models\StockMovement::create([
                'produk_id' => $pengiriman->produk_id,
                'type' => 'OUT',
                'jumlah' => $stokDibutuhkan,
                'stok_gudang_sebelum' => $stokTersedia,
                'stok_gudang_sesudah' => $produk->stok_gudang,
                'stok_toko_sebelum' => $produk->stok_toko - $stokDibutuhkan,
                'stok_toko_sesudah' => $produk->stok_toko,
                'referensi_tabel' => 'pengiriman',
                'referensi_id' => $pengiriman->id,
                'keterangan' => 'Pengiriman stok ke toko: ' . ($pengiriman->tujuan_toko ?? 'Permintaan Kasir'),
                'created_by' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "✅ Pengiriman stok disetujui!\n\n🏭 Stok gudang: {$stokTersedia} → {$produk->stok_gudang} pcs\n🏪 Stok toko: +{$stokDibutuhkan} pcs"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approve pengiriman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal approve: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectPengiriman(Request $request, $id)
    {
        try {
            $pengiriman = Pengiriman::findOrFail($id);
            if ($pengiriman->status !== 'waiting_owner') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Menunggu approval dari gudang terlebih dahulu'
                ]);
            }
            $alasan = $request->alasan ?? 'Tidak ada alasan';
            $pengiriman->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_ditolak' => $alasan
            ]);
            return response()->json(['success' => true, 'message' => 'Pengiriman stok ditolak']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal reject: ' . $e->getMessage()], 500);
        }
    }

    // ==================== APPROVAL PENYESUAIAN STOK ====================
    // ==================== APPROVAL PENYESUAIAN STOK ====================
    public function approvePenyesuaian($id)
    {
        try {
            DB::beginTransaction();

            $adjustment = StockAdjustment::findOrFail($id);

            if ($adjustment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyesuaian sudah diproses sebelumnya'
                ]);
            }

            $produk = Produk::findOrFail($adjustment->produk_id);

            // Cek stok untuk pengurangan
            if ($adjustment->jenis === 'minus' && $produk->stok_gudang < abs($adjustment->perubahan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok gudang tidak mencukupi! Stok tersedia: ' . $produk->stok_gudang . ' pcs'
                ], 400);
            }

            // Update stok gudang
            $produk->stok_gudang = $adjustment->stok_sesudah;
            $produk->save();

            // Catat ke stock movement
            StockMovement::create([
                'produk_id' => $adjustment->produk_id,
                'type' => $adjustment->jenis === 'plus' ? 'IN' : 'OUT',
                'jumlah' => abs($adjustment->perubahan),
                'stok_gudang_sebelum' => $adjustment->stok_sebelum,
                'stok_gudang_sesudah' => $adjustment->stok_sesudah,
                'stok_toko_sebelum' => $produk->stok_toko,
                'stok_toko_sesudah' => $produk->stok_toko,
                'referensi_tabel' => 'stock_adjustments',
                'referensi_id' => $adjustment->id,
                'keterangan' => 'Penyesuaian stok: ' . $adjustment->alasan,
                'created_by' => Auth::id()
            ]);

            $adjustment->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penyesuaian stok disetujui! Stok telah diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal approve: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectPenyesuaian(Request $request, $id)
    {
        try {
            $adjustment = StockAdjustment::findOrFail($id);

            if ($adjustment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyesuaian sudah diproses sebelumnya'
                ]);
            }

            $alasan = $request->alasan ?? 'Tidak ada alasan';

            $adjustment->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_ditolak' => $alasan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penyesuaian stok ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal reject: ' . $e->getMessage()
            ], 500);
        }
    }
}