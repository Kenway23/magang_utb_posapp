<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris';
    protected $primaryKey = 'kategori_id';
    protected $fillable = ['nama_kategori'];

    public function produks()
    {
        return $this->hasMany(Produk::class, 'kategori_id', 'kategori_id');
    }
}