<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Rebuild bukti_kas tables:
     * - Rename bukti_kas_bank → bukti_kas
     * - Rename bukti_kas_bank_details → bukti_kas_details
     * - Remove bank_ac_no & bg_cheque_no (not needed for kas harian)
     */
    public function up(): void
    {
        // Drop old tables if they exist
        Schema::dropIfExists('bukti_kas_bank_details');
        Schema::dropIfExists('bukti_kas_bank');
        Schema::dropIfExists('bukti_kas_details');
        Schema::dropIfExists('bukti_kas');

        Schema::create('bukti_kas', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['BKK', 'BKM']); // BKK = Kas Keluar, BKM = Kas Masuk
            $table->string('no_bukti')->nullable();
            $table->date('tanggal');
            $table->string('pihak'); // BKK: Dibayarkan Kepada / BKM: Diterima Dari
            $table->string('keterangan_utama')->nullable();
            $table->decimal('total', 15, 0)->default(0);
            $table->text('terbilang')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bukti_kas_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bukti_kas_id')->constrained('bukti_kas')->cascadeOnDelete();
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
        Schema::dropIfExists('bukti_kas_details');
        Schema::dropIfExists('bukti_kas');
    }
};
