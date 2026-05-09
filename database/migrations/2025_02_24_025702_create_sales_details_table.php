<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity'); // Jumlah barang yang dibeli
            $table->decimal('price', 10, 2); // Harga barang saat transaksi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_details');
    }
};
