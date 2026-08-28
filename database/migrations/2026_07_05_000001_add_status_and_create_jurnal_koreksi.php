<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add status column to bukti_kas
        Schema::table('bukti_kas', function (Blueprint $table) {
            $table->enum('status', ['draft', 'konfirmasi'])->default('draft')->after('terbilang');
        });

        // Add status column to bukti_banks
        Schema::table('bukti_banks', function (Blueprint $table) {
            $table->enum('status', ['draft', 'konfirmasi'])->default('draft')->after('terbilang');
        });

        // Create jurnal_koreksis table
        Schema::create('jurnal_koreksis', function (Blueprint $table) {
            $table->id();
            $table->string('no_jurnal')->nullable();
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->enum('status', ['draft', 'konfirmasi'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Create jurnal_koreksi_details table
        Schema::create('jurnal_koreksi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_koreksi_id')->constrained('jurnal_koreksis')->cascadeOnDelete();
            $table->foreignId('no_perkiraan_id')->nullable()->constrained('no_perkiraans')->nullOnDelete();
            $table->string('kode_perkiraan')->nullable();
            $table->string('nama_perkiraan')->nullable();
            $table->string('keterangan')->nullable();
            $table->decimal('debit', 15, 0)->default(0);
            $table->decimal('kredit', 15, 0)->default(0);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_koreksi_details');
        Schema::dropIfExists('jurnal_koreksis');

        Schema::table('bukti_banks', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('bukti_kas', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
