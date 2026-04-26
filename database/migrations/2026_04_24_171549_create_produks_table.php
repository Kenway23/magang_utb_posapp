<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->bigIncrements('produk_id');
            $table->string('nama_produk');
            $table->unsignedBigInteger('kategori_id');
            $table->foreign('kategori_id')
                ->references('kategori_id')
                ->on('kategoris')
                ->onDelete('cascade');
            $table->decimal('harga', 10, 2);
            $table->integer('stok_toko')->default(0);
            $table->integer('stok_gudang')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('alasan_ditolak')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('gambar_produk')->nullable();
            $table->timestamps();

            $table->foreign('approved_by')
                ->references('user_id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};