<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('nama_varian');   // contoh: M3x10mm, S, L, Merah, dll
            $table->integer('stock')->default(0);
            $table->decimal('price', 15, 2)->nullable(); // jika null, pakai harga produk
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_variants');
    }
};
