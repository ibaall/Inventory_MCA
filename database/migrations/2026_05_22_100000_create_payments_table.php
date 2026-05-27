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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type'); // 'purchase' or 'sale'
            $table->unsignedBigInteger('transaction_id'); // purchase_order_id or order_id
            $table->string('party_type'); // 'supplier' or 'customer'
            $table->string('party_name'); // supplier_name or customer_name
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
