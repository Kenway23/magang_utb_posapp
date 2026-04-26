<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TambahStock extends Model
{
    protected $table = 'tambah_stocks';

    protected $fillable = [
        'produk_id',
        'jumlah_request',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
        'supplier',
        'status',
        'requested_by',
        'approved_by',
        'alasan_ditolak',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }
}