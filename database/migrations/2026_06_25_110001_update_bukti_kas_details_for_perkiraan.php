<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bukti_kas_details', function (Blueprint $table) {
            $table->foreignId('no_perkiraan_id')->nullable()->after('bukti_kas_id')->constrained('no_perkiraans')->nullOnDelete();
            $table->string('nama_perkiraan')->nullable()->after('no_account');
        });
    }

    public function down(): void
    {
        Schema::table('bukti_kas_details', function (Blueprint $table) {
            $table->dropForeign(['no_perkiraan_id']);
            $table->dropColumn(['no_perkiraan_id', 'nama_perkiraan']);
        });
    }
};
