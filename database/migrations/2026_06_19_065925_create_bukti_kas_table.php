<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop old tables if exist (from previous iteration)
        Schema::dropIfExists('bukti_kas_items');
        Schema::dropIfExists('bukti_kas');

        Schema::create('bukti_kas_bank', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['BKK', 'BKM']); // BKK = Keluar, BKM = Masuk
            $table->string('no_bukti')->nullable();
            $table->date('tanggal');
            $table->string('pihak'); // Dibayarkan Kepada (BKK) / Di Terima Dari (BKM)
            $table->string('keterangan_utama')->nullable();
            $table->string('bank_ac_no')->nullable(); // Hanya BKK
            $table->string('bg_cheque_no')->nullable(); // Hanya BKK
            $table->decimal('total', 15, 0)->default(0);
            $table->text('terbilang')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bukti_kas_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bukti_kas_bank_id')->constrained('bukti_kas_bank')->cascadeOnDelete();
            $table->string('no_account')->nullable();
            $table->string('keterangan');
            $table->decimal('jumlah', 15, 0)->default(0);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_kas_bank_details');
        Schema::dropIfExists('bukti_kas_bank');
    }
};
