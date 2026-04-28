<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksis';
    protected $primaryKey = 'transaksi_id';

    protected $fillable = [
        'kode_transaksi',
        'kasir_id',
        'total_harga',
        'bayar',
        'kembalian',
        'status'
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id', 'user_id');
    }

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id', 'transaksi_id');
    }
}