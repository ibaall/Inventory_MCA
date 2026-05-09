<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Relasi ke user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Data customer
            $table->string('customer_name');

            // Total harga
            $table->integer('total_price');

            // Waktu order
            $table->timestamp('ordered_at')->nullable();

            // Status pembayaran
            $table->string('status_pembayaran')->default('belum dibayar');

            // Metode pembayaran (cash, qris, transfer, dll)
            $table->string('metode_pembayaran')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
