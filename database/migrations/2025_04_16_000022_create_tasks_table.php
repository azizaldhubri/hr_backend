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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('sender_name')->nullable();
            $table->string('id_receiver')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('task_status')->default('To Do');
            $table->string('task_type')->default('General ');
            $table->longText('description');
            $table->date('start_task')->nullable();
            $table->date('end_task')->nullable();
            $table->json('file_paths')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
