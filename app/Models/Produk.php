<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produks';
    protected $primaryKey = 'produk_id';

    protected $fillable = [
        'nama_produk',
        'kategori_id',
        'harga',
        'stok_toko',
        'stok_gudang',
        'gambar_produk',
        'status',
        'approved_by',
        'alasan_ditolak',
        'approved_at',
        'created_by'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relasi ke tabel kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }

    // Relasi ke user yang approve (Owner)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    // Relasi ke user yang membuat (Gudang)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    // Helper methods untuk cek status
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // Total stok (gudang + toko)
    public function getTotalStokAttribute()
    {
        return ($this->stok_gudang ?? 0) + ($this->stok_toko ?? 0);
    }

    // Scope query untuk filtering
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    
}