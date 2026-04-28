<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pengiriman', function (Blueprint $table) {
            DB::statement("ALTER TABLE pengiriman MODIFY COLUMN status ENUM('pending', 'waiting_owner', 'approved', 'rejected') DEFAULT 'pending'");
        });
    }

    public function down(): void
    {
        Schema::table('pengiriman', function (Blueprint $table) {
            DB::statement("ALTER TABLE pengiriman MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        });
    }
};