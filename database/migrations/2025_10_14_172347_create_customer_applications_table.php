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
        Schema::create('customer_applications', function (Blueprint $table) {
            $table->id(); // application_id
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // applicant
            $table->enum('product_type', ['account_opening'])->default('account_opening');
            $table->enum('channel', ['web','mobile','branch'])->default('web');
            $table->enum('status', ['draft','submitted','in_review','approved','rejected','withdrawn'])->default('submitted')->index();
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('remarks', 255)->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_applications');
    }
};
