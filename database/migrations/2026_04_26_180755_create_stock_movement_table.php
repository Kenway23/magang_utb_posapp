<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produk_id');
            $table->enum('type', ['IN', 'OUT', 'ADJUSTMENT']);
            $table->integer('jumlah');
            $table->integer('stok_gudang_sebelum');
            $table->integer('stok_gudang_sesudah');
            $table->integer('stok_toko_sebelum');
            $table->integer('stok_toko_sesudah');
            $table->string('referensi_tabel')->nullable(); // 'tambah_stocks' atau 'pengiriman'
            $table->unsignedBigInteger('referensi_id')->nullable(); // ID di tabel asal
            $table->string('tujuan')->nullable(); // untuk pengiriman
            $table->string('supplier')->nullable(); // untuk tambah stok
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('produk_id')->references('produk_id')->on('produks')->onDelete('cascade');
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};