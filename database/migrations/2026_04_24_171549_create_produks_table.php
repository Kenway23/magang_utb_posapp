<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
    $table->integer('stok')->default(0);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};