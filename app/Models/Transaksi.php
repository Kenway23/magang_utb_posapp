<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksis';
    protected $primaryKey = 'transaksi_id';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'kasir_id',
        'total_harga',
        'bayar',
        'kembalian',
        'status',
        'kode_transaksi'
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id', 'user_id');
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id', 'transaksi_id');
    }
}