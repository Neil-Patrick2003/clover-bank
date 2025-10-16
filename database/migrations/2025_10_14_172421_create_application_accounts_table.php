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
        Schema::create('application_accounts', function (Blueprint $table) {
            $table->id(); // ← new primary key
            $table->foreignId('application_id')->constrained('customer_applications')->cascadeOnDelete();
            $table->enum('requested_type', ['savings','current','time_deposit']);
            $table->char('currency', 3)->default('PHP');
            $table->decimal('initial_deposit', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_accounts');
    }
};
