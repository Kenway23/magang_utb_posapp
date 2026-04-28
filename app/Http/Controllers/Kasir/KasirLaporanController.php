<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasirLaporanController extends Controller
{
    // Halaman laporan penjualan kasir
    public function index()
    {
        // 🔥 PERBAIKAN: Ganti 'kasir.laporan' menjadi 'kasir.laporan_penjualan'
        return view('kasir.laporan_penjualan');
    }

    // API untuk mengambil data laporan
    public function getData(Request $request)
    {
        try {
            $dariTanggal = $request->get('dari_tanggal');
            $sampaiTanggal = $request->get('sampai_tanggal');

            $query = Transaksi::with(['details.produk'])
                ->where('kasir_id', Auth::id())
                ->where('status', 'COMPLETED')
                ->orderBy('created_at', 'desc');

            if ($dariTanggal) {
                $query->whereDate('created_at', '>=', $dariTanggal);
            }
            if ($sampaiTanggal) {
                $query->whereDate('created_at', '<=', $sampaiTanggal);
            }

            $transactions = $query->get();

            // Hitung produk terjual
            $productSummary = [];
            $totalPendapatan = 0;
            $totalItem = 0;

            foreach ($transactions as $transaction) {
                $totalPendapatan += $transaction->total_harga;

                foreach ($transaction->details as $detail) {
                    $totalItem += $detail->jumlah;
                    $productName = $detail->produk->nama_produk;

                    if (!isset($productSummary[$productName])) {
                        $productSummary[$productName] = [
                            'name' => $productName,
                            'qty' => 0,
                            'revenue' => 0
                        ];
                    }
                    $productSummary[$productName]['qty'] += $detail->jumlah;
                    $productSummary[$productName]['revenue'] += $detail->subtotal;
                }
            }

            // Urutkan berdasarkan qty terbanyak
            $topProducts = collect($productSummary)->sortByDesc('qty')->values()->take(10);

            // Data harian untuk chart
            $dailyData = $transactions->groupBy(function ($item) {
                return $item->created_at->format('d/m/Y');
            })->map(function ($items, $date) {
                return [
                    'date' => $date,
                    'total' => $items->sum('total_harga'),
                    'count' => $items->count()
                ];
            })->values();

            $statistik = [
                'total_transaksi' => $transactions->count(),
                'total_item' => $totalItem,
                'total_pendapatan' => $totalPendapatan
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'top_products' => $topProducts,
                    'daily_data' => $dailyData,
                    'statistik' => $statistik
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat数据: ' . $e->getMessage()
            ], 500);
        }
    }
}