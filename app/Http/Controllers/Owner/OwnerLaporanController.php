<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerLaporanController extends Controller
{
    // Halaman laporan penjualan
    public function index()
    {
        return view('owner.laporan_penjualan');
    }

    // API untuk mendapatkan data laporan
    public function getData(Request $request)
    {
        try {
            $dariTanggal = $request->get('dari_tanggal');
            $sampaiTanggal = $request->get('sampai_tanggal');
            $kasirId = $request->get('kasir_id');

            // Query transaksi dengan filter
            $query = Transaksi::with(['kasir', 'details.produk.kategori'])
                ->where('status', 'COMPLETED');

            if ($dariTanggal) {
                $query->whereDate('created_at', '>=', $dariTanggal);
            }
            if ($sampaiTanggal) {
                $query->whereDate('created_at', '<=', $sampaiTanggal);
            }
            if ($kasirId && $kasirId !== 'all') {
                $query->where('kasir_id', $kasirId);
            }

            $transactions = $query->orderBy('created_at', 'desc')->get();

            // Hitung total pendapatan dan item
            $totalPendapatan = $transactions->sum('total_harga');
            $totalItem = 0;
            $productSales = [];
            $categorySales = [];

            foreach ($transactions as $transaction) {
                foreach ($transaction->details as $detail) {
                    $totalItem += $detail->jumlah;

                    $productName = $detail->produk->nama_produk;
                    $categoryName = $detail->produk->kategori->nama_kategori ?? 'Lainnya';
                    $subtotal = $detail->subtotal;

                    // Akumulasi per produk
                    if (!isset($productSales[$productName])) {
                        $productSales[$productName] = [
                            'name' => $productName,
                            'category' => $categoryName,
                            'qty' => 0,
                            'revenue' => 0
                        ];
                    }
                    $productSales[$productName]['qty'] += $detail->jumlah;
                    $productSales[$productName]['revenue'] += $subtotal;

                    // Akumulasi per kategori
                    if (!isset($categorySales[$categoryName])) {
                        $categorySales[$categoryName] = [
                            'name' => $categoryName,
                            'qty' => 0,
                            'revenue' => 0
                        ];
                    }
                    $categorySales[$categoryName]['qty'] += $detail->jumlah;
                    $categorySales[$categoryName]['revenue'] += $subtotal;
                }
            }

            // Format transaksi
            $formattedTransactions = $transactions->map(function ($item) {
                return [
                    'id' => $item->kode_transaksi,
                    'date' => $item->created_at->format('d/m/Y H:i'),
                    'cashier' => $item->kasir->name ?? 'Kasir',
                    'totalItem' => $item->details->sum('jumlah'),
                    'totalAmount' => (float) $item->total_harga,
                    'status' => $item->status
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => array_values($productSales),
                    'categories' => array_values($categorySales),
                    'transactions' => $formattedTransactions,
                    'summary' => [
                        'total_transaksi' => $transactions->count(),
                        'total_pendapatan' => $totalPendapatan,
                        'total_item' => $totalItem
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    // API untuk mendapatkan daftar kasir
    public function getKasirList()
    {
        try {
            $kasir = \App\Models\User::where('role', 'kasir')
                ->select('id', 'name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $kasir
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kasir'
            ], 500);
        }
    }
}