<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_banks', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['BBM', 'BBK']); // BBM=Bank Masuk, BBK=Bank Keluar
            $table->string('no_bukti')->nullable();
            $table->date('tanggal');
            $table->string('pihak'); // BBM: Diterima Dari / BBK: Dibayarkan Kepada
            $table->foreignId('bank_account_id')->nullable()->constrained('no_perkiraans')->nullOnDelete();
            $table->string('no_invoice')->nullable();
            $table->string('no_po')->nullable();
            $table->string('bg_cheque_no')->nullable();
            $table->string('keterangan_utama')->nullable();
            $table->decimal('total', 15, 0)->default(0);
            $table->text('terbilang')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bukti_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bukti_bank_id')->constrained('bukti_banks')->cascadeOnDelete();
            $table->foreignId('no_perkiraan_id')->nullable()->constrained('no_perkiraans')->nullOnDelete();
            $table->string('kode_perkiraan')->nullable();
            $table->string('nama_perkiraan')->nullable();
            $table->string('keterangan');
            $table->decimal('jumlah', 15, 0)->default(0);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_bank_details');
        Schema::dropIfExists('bukti_banks');
    }
};
