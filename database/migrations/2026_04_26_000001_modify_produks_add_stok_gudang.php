<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            // Rename old stok column to stok_toko (stok di kasir/etalase - siap jual)
           
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('stok_gudang');
            $table->renameColumn('stok_toko', 'stok');
        });
    }
};