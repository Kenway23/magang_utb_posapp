<?php

namespace Database\Seeders;

use App\Models\Kategori;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategoris')->insert([
        ['nama_kategori' => 'Makanan'],
        ['nama_kategori' => 'Minuman'],
        ['nama_kategori' => 'Makanan Ringan'],
        ['nama_kategori' => 'Makanan Sehat'],
        ['nama_kategori' => 'Produk Kebersihan'],
        ['nama_kategori' => 'Kebutuhan Harian'], 
        ['nama_kategori' => 'Makanan Siap Saji'],
        ['nama_kategori' => 'Produk Segar & Beku'],
        ['nama_kategori' => 'Kebutuhan Ibu & Anak'],
        ['nama_kategori' => 'Makanan Hewan'],
        ['nama_kategori' => 'Mainan'],
        ['nama_kategori' => 'Kecantikan'],
        ['nama_kategori' => 'Perawatan Diri'],
    ]);
        $this->command->info('KategoriSeeder done');
    }
}
