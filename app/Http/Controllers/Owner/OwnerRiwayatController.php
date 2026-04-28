<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerRiwayatController extends Controller
{
    // Halaman riwayat transaksi owner
    public function index()
    {
        return view('owner.riwayat_transaksi');
    }

    // API untuk mengambil data riwayat transaksi (SEMUA KASIR)
    public function getData()
    {
        try {
            $transaksi = Transaksi::with(['kasir', 'details.produk'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->transaksi_id,
                        'transaction_number' => $item->kode_transaksi,
                        'date' => $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : '-',
                        'cashier' => $item->kasir ? $item->kasir->name : 'Kasir',
                        'total_items' => $item->details ? $item->details->sum('jumlah') : 0,
                        'total_amount' => (float) ($item->total_harga ?? 0),
                        'payment_amount' => (float) ($item->bayar ?? 0),
                        'change_amount' => (float) ($item->kembalian ?? 0),
                        'status' => $item->status ?? 'COMPLETED',
                        'items' => $item->details ? $item->details->map(function ($detail) {
                            return [
                                'name' => $detail->produk ? $detail->produk->nama_produk : '-',
                                'price' => (float) ($detail->harga_satuan ?? 0),
                                'qty' => $detail->jumlah ?? 0,
                                'subtotal' => (float) ($detail->subtotal ?? 0)
                            ];
                        }) : []
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $transaksi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }
}