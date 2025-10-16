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
        Schema::create('application_documents', function (Blueprint $table) {
            $table->id(); // document_id
            $table->foreignId('application_id')->constrained('customer_applications')->cascadeOnDelete();
            $table->enum('doc_type', ['valid_id','proof_of_address','other']);
            $table->string('file_url', 512);
            $table->enum('verified_status', ['pending','verified','rejected'])->default('pending');
            $table->timestamps();

            $table->index(['application_id', 'verified_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_documents');
    }
};
