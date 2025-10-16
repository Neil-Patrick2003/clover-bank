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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('to_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->decimal('amount', 18, 2);
            $table->char('currency', 3);
            $table->foreignId('trx_out_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('trx_in_id')->constrained('transactions')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['from_account_id']);
            $table->index(['to_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
