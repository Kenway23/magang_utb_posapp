<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\TambahStock;
use App\Models\Pengiriman;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanApprovalController extends Controller
{
    // Halaman laporan approval
    public function index()
    {
        return view('owner.stok.laporan_approval');
    }

    // API untuk mengambil data laporan approval
    public function getData(Request $request)
    {
        try {
            $dariTanggal = $request->get('dari_tanggal');
            $sampaiTanggal = $request->get('sampai_tanggal');
            $jenis = $request->get('jenis');
            $status = $request->get('status');

            $histories = [];

            // 1. Produk Baru
            $produkQuery = Produk::with('kategori', 'creator', 'approver')
                ->whereIn('status', ['approved', 'rejected'])
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at');

            if ($dariTanggal) {
                $produkQuery->whereDate('approved_at', '>=', $dariTanggal);
            }
            if ($sampaiTanggal) {
                $produkQuery->whereDate('approved_at', '<=', $sampaiTanggal);
            }
            if ($status && $status !== 'all') {
                $produkQuery->where('status', $status);
            }

            $produkHistories = $produkQuery->orderBy('approved_at', 'desc')->get()->map(function ($item) {
                return [
                    'id' => $item->produk_id,
                    'type' => 'produk',
                    'type_label' => 'Produk Baru',
                    'type_icon' => 'fa-box',
                    'type_color' => 'text-indigo-600',
                    'kode' => 'PRD-' . $item->produk_id,
                    'nama' => $item->nama_produk,
                    'detail' => "Kategori: " . ($item->kategori->nama_kategori ?? '-') . " | Harga: Rp " . number_format($item->harga, 0, ',', '.'),
                    'jumlah' => $item->stok_gudang,
                    'satuan' => 'pcs',
                    'diajukan_oleh' => $item->creator->name ?? 'Gudang',
                    'diproses_oleh' => $item->approver->name ?? 'Owner',
                    'tanggal_diajukan' => $item->created_at->format('d/m/Y H:i'),
                    'tanggal_diproses' => $item->approved_at->format('d/m/Y H:i'),
                    'status' => $item->status,
                    'alasan_ditolak' => $item->alasan_ditolak
                ];
            });

            // 2. Tambah Stok
            $tambahStokQuery = TambahStock::with('produk', 'requester', 'approver')
                ->whereIn('status', ['approved', 'rejected'])
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at');

            if ($dariTanggal) {
                $tambahStokQuery->whereDate('approved_at', '>=', $dariTanggal);
            }
            if ($sampaiTanggal) {
                $tambahStokQuery->whereDate('approved_at', '<=', $sampaiTanggal);
            }
            if ($status && $status !== 'all') {
                $tambahStokQuery->where('status', $status);
            }

            $tambahStokHistories = $tambahStokQuery->orderBy('approved_at', 'desc')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'tambah_stok',
                    'type_label' => 'Tambah Stok',
                    'type_icon' => 'fa-plus-circle',
                    'type_color' => 'text-teal-600',
                    'kode' => 'TSK-' . $item->id,
                    'nama' => $item->produk->nama_produk ?? '-',
                    'detail' => "Jumlah: +{$item->jumlah_request} pcs | Supplier: " . ($item->supplier ?? '-'),
                    'jumlah' => $item->jumlah_request,
                    'satuan' => 'pcs',
                    'diajukan_oleh' => $item->requester->name ?? 'Gudang',
                    'diproses_oleh' => $item->approver->name ?? 'Owner',
                    'tanggal_diajukan' => $item->created_at->format('d/m/Y H:i'),
                    'tanggal_diproses' => $item->approved_at->format('d/m/Y H:i'),
                    'status' => $item->status,
                    'alasan_ditolak' => $item->alasan_ditolak
                ];
            });

            // 3. Pengiriman Stok
            $pengirimanQuery = Pengiriman::with('produk', 'requester', 'approver')
                ->whereIn('status', ['approved', 'rejected'])
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at');

            if ($dariTanggal) {
                $pengirimanQuery->whereDate('approved_at', '>=', $dariTanggal);
            }
            if ($sampaiTanggal) {
                $pengirimanQuery->whereDate('approved_at', '<=', $sampaiTanggal);
            }
            if ($status && $status !== 'all') {
                $pengirimanQuery->where('status', $status);
            }

            $pengirimanHistories = $pengirimanQuery->orderBy('approved_at', 'desc')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'pengiriman',
                    'type_label' => 'Pengiriman Stok',
                    'type_icon' => 'fa-arrow-up',
                    'type_color' => 'text-orange-600',
                    'kode' => $item->kode_pengiriman,
                    'nama' => $item->produk->nama_produk ?? '-',
                    'detail' => "Jumlah: -{$item->jumlah} pcs | Tujuan: " . ($item->tujuan_toko ?? 'Permintaan Kasir'),
                    'jumlah' => $item->jumlah,
                    'satuan' => 'pcs',
                    'diajukan_oleh' => $item->requester->name ?? 'Gudang/Kasir',
                    'diproses_oleh' => $item->approver->name ?? 'Owner',
                    'tanggal_diajukan' => $item->created_at->format('d/m/Y H:i'),
                    'tanggal_diproses' => $item->approved_at->format('d/m/Y H:i'),
                    'status' => $item->status,
                    'alasan_ditolak' => $item->alasan_ditolak
                ];
            });

            // 4. Penyesuaian Stok
            $penyesuaianQuery = StockAdjustment::with('produk', 'creator', 'approver')
                ->whereIn('status', ['approved', 'rejected'])
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at');

            if ($dariTanggal) {
                $penyesuaianQuery->whereDate('approved_at', '>=', $dariTanggal);
            }
            if ($sampaiTanggal) {
                $penyesuaianQuery->whereDate('approved_at', '<=', $sampaiTanggal);
            }
            if ($status && $status !== 'all') {
                $penyesuaianQuery->where('status', $status);
            }

            $penyesuaianHistories = $penyesuaianQuery->orderBy('approved_at', 'desc')->get()->map(function ($item) {
                $perubahanText = $item->perubahan >= 0 ? '+' . $item->perubahan : $item->perubahan;
                return [
                    'id' => $item->id,
                    'type' => 'penyesuaian',
                    'type_label' => 'Penyesuaian Stok',
                    'type_icon' => 'fa-sliders-h',
                    'type_color' => 'text-purple-600',
                    'kode' => $item->kode_adjustment,
                    'nama' => $item->produk->nama_produk ?? '-',
                    'detail' => "Perubahan: {$perubahanText} pcs | Alasan: " . ($item->alasan ?? '-'),
                    'jumlah' => abs($item->perubahan),
                    'satuan' => 'pcs',
                    'diajukan_oleh' => $item->creator->name ?? 'Gudang',
                    'diproses_oleh' => $item->approver->name ?? 'Owner',
                    'tanggal_diajukan' => $item->created_at->format('d/m/Y H:i'),
                    'tanggal_diproses' => $item->approved_at->format('d/m/Y H:i'),
                    'status' => $item->status,
                    'alasan_ditolak' => $item->alasan_ditolak
                ];
            });

            // Gabungkan semua data
            $allHistories = collect()
                ->concat($produkHistories)
                ->concat($tambahStokHistories)
                ->concat($pengirimanHistories)
                ->concat($penyesuaianHistories)
                ->sortByDesc('tanggal_diproses')
                ->values();

            // Filter berdasarkan jenis
            if ($jenis && $jenis !== 'all') {
                $allHistories = $allHistories->where('type', $jenis);
            }

            // Hitung statistik
            $statistik = [
                'total' => $allHistories->count(),
                'disetujui' => $allHistories->where('status', 'approved')->count(),
                'ditolak' => $allHistories->where('status', 'rejected')->count(),
                'produk' => $allHistories->where('type', 'produk')->count(),
                'tambah_stok' => $allHistories->where('type', 'tambah_stok')->count(),
                'pengiriman' => $allHistories->where('type', 'pengiriman')->count(),
                'penyesuaian' => $allHistories->where('type', 'penyesuaian')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $allHistories,
                'statistik' => $statistik
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }
}