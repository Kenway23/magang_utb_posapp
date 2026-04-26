<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        // Ambil semua kategori untuk filter
        $kategori = Kategori::all();

        return view('owner.produk', compact('kategori'));
    }

    public function getData()
    {
        try {
            // Owner hanya melihat produk yang statusnya approved
            $produk = Produk::with('kategori')
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->get();

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
}