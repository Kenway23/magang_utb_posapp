<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokMasuk;
use App\Models\Produk;

class StokMasukController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required',
            'qty' => 'required|integer|min:1'
        ]);

        StokMasuk::create([
            'produk_id' => $request->produk_id,
            'qty' => $request->qty,
            'tanggal' => now(),
            'status' => 'pending'
        ]);

        return back()->with('success', 'Menunggu persetujuan');
    }
    public function approve($id)
    {
        $stok = StokMasuk::find($id);

        if (!$stok) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        if ($stok->status == 'approved') {
            return back()->with('error', 'Sudah disetujui');
        }

        $produk = Produk::find($stok->produk_id);
        $produk->stok += $stok->qty;
        $produk->save();

        $stok->status = 'approved';
        $stok->save();

        return back()->with('success', 'Stok disetujui');
    }
    public function reject($id)
    {
        $stok = StokMasuk::find($id);

        if (!$stok) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        $stok->status = 'rejected';
        $stok->save();

        return back()->with('success', 'Stok ditolak');
    }
}