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
        Schema::create('kyc_profiles', function (Blueprint $table) {
            $table->id(); // kyc_id
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->enum('kyc_level', ['basic','standard','enhanced'])->default('basic');
            $table->enum('id_type', ['passport','national_id','driver_license','sss','umid','other'])->nullable();
            $table->string('id_number', 128)->nullable();
            $table->date('id_expiry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_profiles');
    }
};
