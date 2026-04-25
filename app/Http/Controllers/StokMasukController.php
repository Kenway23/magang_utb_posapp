<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokMasuk;
use App\Models\Produk;

class StokMasukController extends Controller
{
    public function store(Request $request)
    {
        StokMasuk::create([
            'produk_id' => $request->produk_id,
            'qty' => $request->qty,
            'tanggal' => now()
        ]);

        $produk = Produk::find($request->produk_id);
        $produk->stok += $request->qty;
        $produk->save();

        $request->validate([
        'produk_id' => 'required|exists:produks,id',
        'qty' => 'required|integer|min:1'
]);

        return redirect()->back()->with('success', 'Stok berhasil ditambahkan');
    }
}