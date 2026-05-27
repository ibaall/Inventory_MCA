<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('nama_pasien')->nullable();
            $table->string('operator')->nullable();
            $table->date('tanggal_operasi')->nullable();
            $table->text('alamat')->nullable();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->text('alamat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['nama_pasien', 'operator', 'tanggal_operasi', 'alamat']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['alamat']);
        });
    }
};
