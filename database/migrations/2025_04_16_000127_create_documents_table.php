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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();          
            $table->string('document_name')->nullable();
            $table->string('supervising_emp')->nullable();            
            $table->string('user_name')->nullable();
            $table->string('document_id')->nullable();             
            $table->date('start_document')->nullable();
            $table->date('end_document')->nullable();
            $table->date('date_alert')->nullable();
            $table->string('document_type')->nullable();             
            $table->json('file_paths')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
