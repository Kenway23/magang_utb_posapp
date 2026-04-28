<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('kode_adjustment')->unique();
            $table->unsignedBigInteger('produk_id');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->integer('perubahan');
            $table->enum('jenis', ['plus', 'minus']);
            $table->string('alasan');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->unsignedBigInteger('created_by');      // Gudang yang membuat request
            $table->unsignedBigInteger('approved_by')->nullable(); // Owner yang approve
            $table->text('alasan_ditolak')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // 🔥 Foreign key ke produk (mengikuti pola pengiriman)
            $table->foreign('produk_id')
                ->references('produk_id')
                ->on('produks')
                ->onDelete('cascade');

            // 🔥 Foreign key ke users (mengikuti pola pengiriman - menggunakan 'user_id')
            $table->foreign('created_by')
                ->references('user_id')  // ← SAMA seperti di tabel pengiriman (requested_by)
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('approved_by')
                ->references('user_id')  // ← SAMA seperti di tabel pengiriman (approved_by)
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};