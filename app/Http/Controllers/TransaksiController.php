<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        $transaksi = Transaksi::create([
            'user_id' => 1, 
            'tanggal' => now(),
            'total' => 0
        ]);

        $total = 0;

        foreach ($request->produk as $item) {

            $produk = Produk::find($item['produk_id']);

            if ($produk->stok < $item['qty']) {
                return back()->with('error', 'Stok tidak cukup');
            }

            $subtotal = $produk->harga * $item['qty'];
            $total += $subtotal;

            DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $item['produk_id'],
                'qty' => $item['qty'],
                'subtotal' => $subtotal
            ]);

            $produk->stok -= $item['qty'];
            $produk->save();
        }

        $transaksi->update([
            'total' => $total
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil');
    }
}