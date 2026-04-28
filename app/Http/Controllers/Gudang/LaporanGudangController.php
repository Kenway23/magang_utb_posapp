<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Pengiriman;
use App\Models\TambahStock;
use App\Models\StockAdjustment;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanGudangController extends Controller
{
    // Halaman laporan stok gudang
    public function index()
    {
        $kategori = Kategori::all();
        return view('gudang.stok.laporan_gudang', compact('kategori'));
    }

    // API untuk mengambil data laporan
    public function getData(Request $request)
    {
        try {
            $dariTanggal = $request->get('dari_tanggal');
            $sampaiTanggal = $request->get('sampai_tanggal');
            $kategoriId = $request->get('kategori_id');

            // Ambil semua produk yang sudah approved
            $query = Produk::with('kategori')
                ->where('status', 'approved');

            if ($kategoriId && $kategoriId !== 'all') {
                $query->where('kategori_id', $kategoriId);
            }

            $produk = $query->orderBy('nama_produk')->get();

            $laporan = [];
            $totalAwal = 0;
            $totalMasuk = 0;
            $totalKeluar = 0;
            $totalPenyesuaian = 0;
            $totalAkhir = 0;
            $totalNilai = 0;

            foreach ($produk as $item) {
                // Stok awal (stok gudang saat ini)
                $stokAwal = $item->stok_gudang;

                // Hitung penerimaan (Tambah Stok yang approved)
                $penerimaan = TambahStock::where('produk_id', $item->produk_id)
                    ->where('status', 'approved')
                    ->when($dariTanggal, function ($q) use ($dariTanggal) {
                        return $q->whereDate('created_at', '>=', $dariTanggal);
                    })
                    ->when($sampaiTanggal, function ($q) use ($sampaiTanggal) {
                        return $q->whereDate('created_at', '<=', $sampaiTanggal);
                    })
                    ->sum('jumlah_request');

                // Hitung pengeluaran (Pengiriman yang approved)
                $pengeluaran = Pengiriman::where('produk_id', $item->produk_id)
                    ->where('status', 'approved')
                    ->when($dariTanggal, function ($q) use ($dariTanggal) {
                        return $q->whereDate('created_at', '>=', $dariTanggal);
                    })
                    ->when($sampaiTanggal, function ($q) use ($sampaiTanggal) {
                        return $q->whereDate('created_at', '<=', $sampaiTanggal);
                    })
                    ->sum('jumlah');

                // Hitung penyesuaian (Stock Adjustment yang approved)
                $penyesuaian = StockAdjustment::where('produk_id', $item->produk_id)
                    ->where('status', 'approved')
                    ->when($dariTanggal, function ($q) use ($dariTanggal) {
                        return $q->whereDate('created_at', '>=', $dariTanggal);
                    })
                    ->when($sampaiTanggal, function ($q) use ($sampaiTanggal) {
                        return $q->whereDate('created_at', '<=', $sampaiTanggal);
                    })
                    ->sum('perubahan');

                // Hitung stok akhir
                $stokAkhir = $stokAwal + $penerimaan - $pengeluaran + $penyesuaian;

                // Status stok
                $status = 'Aman';
                $statusClass = 'bg-green-100 text-green-700';
                if ($stokAkhir <= 5) {
                    $status = 'Kritis';
                    $statusClass = 'bg-red-100 text-red-700';
                } elseif ($stokAkhir <= 10) {
                    $status = 'Menipis';
                    $statusClass = 'bg-yellow-100 text-yellow-700';
                }

                // Akumulasi total
                $totalAwal += $stokAwal;
                $totalMasuk += $penerimaan;
                $totalKeluar += $pengeluaran;
                $totalPenyesuaian += $penyesuaian;
                $totalAkhir += $stokAkhir;
                $totalNilai += $stokAkhir * ($item->harga ?? 0);

                $laporan[] = [
                    'id' => $item->produk_id,
                    'nama_produk' => $item->nama_produk,
                    'kategori' => $item->kategori->nama_kategori ?? '-',
                    'stok_awal' => $stokAwal,
                    'penerimaan' => $penerimaan,
                    'pengeluaran' => $pengeluaran,
                    'penyesuaian' => $penyesuaian,
                    'stok_akhir' => $stokAkhir,
                    'status' => $status,
                    'status_class' => $statusClass,
                    'satuan' => 'pcs',
                    'harga' => $item->harga ?? 0
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $laporan,
                'summary' => [
                    'total_awal' => $totalAwal,
                    'total_masuk' => $totalMasuk,
                    'total_keluar' => $totalKeluar,
                    'total_penyesuaian' => $totalPenyesuaian,
                    'total_akhir' => $totalAkhir,
                    'total_nilai' => $totalNilai,
                    'stok_menipis' => collect($laporan)->where('status', 'Menipis')->count(),
                    'stok_kritis' => collect($laporan)->where('status', 'Kritis')->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }
}