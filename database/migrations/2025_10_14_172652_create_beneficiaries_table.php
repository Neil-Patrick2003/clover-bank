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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id(); // beneficiary_id
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // owner
            $table->string('nickname', 100)->nullable();
            $table->string('bank_code', 20);
            $table->string('account_number', 64);
            $table->string('account_name', 120);
            $table->timestamps();

            $table->index(['user_id', 'bank_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
