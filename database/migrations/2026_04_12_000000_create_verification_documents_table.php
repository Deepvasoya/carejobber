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
        Schema::create('verification_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->string('document_type', 50);
            $table->longText('file_data');
            $table->string('original_filename', 255);
            $table->unsignedInteger('file_size');
            $table->string('mime_type', 100);
            $table->timestamp('uploaded_at');
            $table->timestamps();

            $table->index('company_id');
            $table->index('document_type');
            
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_documents');
    }
};
