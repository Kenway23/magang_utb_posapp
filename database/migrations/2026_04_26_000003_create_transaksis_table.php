<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->bigIncrements('transaksi_id');
            $table->unsignedBigInteger('kasir_id');
            $table->foreign('kasir_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('restrict');
            $table->decimal('total_harga', 12, 2);
            $table->decimal('bayar', 12, 2);
            $table->decimal('kembalian', 12, 2);
            $table->enum('status', ['COMPLETED', 'CANCELLED'])->default('COMPLETED');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};