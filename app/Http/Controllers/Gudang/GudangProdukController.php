<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class GudangProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::with('kategori')
            ->orderBy('created_at', 'desc')
            ->get();
        $kategori = Kategori::all();
        return view('gudang.produk', compact('produk', 'kategori'));
    }

    public function getData()
    {
        try {
            $produk = Produk::with('kategori')->get();
            return response()->json([
                'success' => true,
                'data' => $produk
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_produk' => 'required|string|max:255',
                'kategori_id' => 'required|exists:kategoris,kategori_id',
                'harga' => 'required|numeric|min:0',
                'stok_gudang' => 'required|integer|min:0',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $data = [
                'nama_produk' => $request->nama_produk,
                'kategori_id' => $request->kategori_id,
                'harga' => $request->harga,
                'stok_gudang' => $request->stok_gudang,
                'stok_toko' => 0,
                'status' => 'pending',
                'created_by' => Auth::id()
            ];

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $path = $file->storeAs('produk', $filename, 'public');
                $data['gambar_produk'] = $path;
            }

            $produk = Produk::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan! Menunggu persetujuan Owner.',
                'data' => $produk->load('kategori')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah produk: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $produk = Produk::findOrFail($id);

            if ($produk->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk sudah disetujui, tidak dapat diedit. Hubungi Owner untuk perubahan.'
                ], 403);
            }

            $request->validate([
                'nama_produk' => 'required|string|max:255',
                'kategori_id' => 'required|exists:kategoris,kategori_id',
                'harga' => 'required|numeric|min:0',
                'stok_gudang' => 'required|integer|min:0',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $data = [
                'nama_produk' => $request->nama_produk,
                'kategori_id' => $request->kategori_id,
                'harga' => $request->harga,
                'stok_gudang' => $request->stok_gudang,
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'alasan_ditolak' => null
            ];

            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($produk->gambar_produk && Storage::disk('public')->exists($produk->gambar_produk)) {
                    Storage::disk('public')->delete($produk->gambar_produk);
                }

                $file = $request->file('gambar');
                $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $path = $file->storeAs('produk', $filename, 'public');
                $data['gambar_produk'] = $path;
            }

            $produk->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diupdate! Menunggu persetujuan ulang dari Owner.',
                'data' => $produk->load('kategori')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update produk: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $produk = Produk::findOrFail($id);

            if ($produk->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk sudah disetujui, tidak dapat dihapus. Hubungi Owner untuk penghapusan.'
                ], 403);
            }

            if ($produk->gambar_produk && Storage::disk('public')->exists($produk->gambar_produk)) {
                Storage::disk('public')->delete($produk->gambar_produk);
            }

            $produk->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus produk: ' . $e->getMessage()
            ], 500);
        }
    }
}