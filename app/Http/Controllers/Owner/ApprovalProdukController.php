<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalProdukController extends Controller
{
    public function index()
    {
        return view('owner.stok.approval');
    }

    public function getData()
    {
        $produk = Produk::with(['kategori', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $produk
        ]);
    }

    public function approval()
    {
        $produkPending = Produk::with(['kategori', 'creator'])
            ->where('status', 'pending')
            ->get();

        return view('owner.stok.approval', compact('produkPending'));
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string|min:5']);

        $produk = Produk::findOrFail($id);
        $produk->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'alasan_ditolak' => $request->alasan
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk ditolak'
        ]);
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->ids;
        Produk::whereIn('produk_id', $ids)->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' produk berhasil disetujui'
        ]);
    }
}