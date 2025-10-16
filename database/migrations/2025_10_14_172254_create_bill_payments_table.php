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
        Schema::create('bill_payments', function (Blueprint $table) {
            $table->id(); // payment_id
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('biller_id')->constrained('bills')->restrictOnDelete();
            $table->decimal('amount', 18, 2);
            $table->string('reference_no', 80)->unique();
            $table->enum('status', ['pending','posted','failed','reversed'])->default('posted')->index();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['account_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_payments');
    }
};
