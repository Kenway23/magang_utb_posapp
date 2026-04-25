<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        Transaksi::create([
            'user_id' => 1,
            'tanggal' => now(),
            'total' => 150000
        ]);

        Transaksi::create([
            'user_id' => 1,
            'tanggal' => now(),
            'total' => 200000
        ]);
    }
}