<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $table = 'stock_adjustments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kode_adjustment',
        'produk_id',
        'stok_sebelum',
        'stok_sesudah',
        'perubahan',
        'jenis',
        'alasan',
        'keterangan',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'alasan_ditolak'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function generateKode()
    {
        $prefix = 'ADJ';
        $date = date('Ymd');
        $last = self::whereDate('created_at', today())->count() + 1;
        return $prefix . '-' . $date . '-' . str_pad($last, 3, '0', STR_PAD_LEFT);
    }
}