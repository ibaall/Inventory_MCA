<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('no_perkiraans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_perkiraan', 10)->unique();
            $table->string('nama_perkiraan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('no_perkiraans');
    }
};
