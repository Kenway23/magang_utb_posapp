<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengiriman')->unique();
            $table->unsignedBigInteger('produk_id');
            $table->integer('jumlah');
            $table->integer('stok_gudang_sebelum');
            $table->integer('stok_gudang_sesudah');
            $table->integer('stok_toko_sebelum');
            $table->integer('stok_toko_sesudah');
            $table->string('tujuan_toko')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('requested_by'); // Gudang yang request
            $table->unsignedBigInteger('approved_by')->nullable(); // Owner yang approve
            $table->text('alasan_ditolak')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('produk_id')->references('produk_id')->on('produks')->onDelete('cascade');
            $table->foreign('requested_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};