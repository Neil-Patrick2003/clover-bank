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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->enum('type', ['deposit','withdrawal','transfer_in','transfer_out','bill_payment']);
            $table->decimal('amount', 18, 2);
            $table->char('currency', 3);
            $table->string('reference_no', 80)->unique();
            $table->enum('status', ['pending','posted','failed','reversed'])->default('posted')->index();
            $table->string('remarks', 255)->nullable();
            $table->timestamps();

            $table->index(['account_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
