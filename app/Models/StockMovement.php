<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $fillable = [
        'produk_id',
        'type',
        // 'location',  // 🔥 HAPUS - kolom tidak ada di database
        'jumlah',
        'stok_gudang_sebelum',
        'stok_gudang_sesudah',
        'stok_toko_sebelum',
        'stok_toko_sesudah',
        'referensi_tabel',
        'referensi_id',
        'keterangan',
        'created_by'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}