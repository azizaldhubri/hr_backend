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
        Schema::create('users', function (Blueprint $table) {
            $table->id();           
            $table->string('name',100);
            $table->string('email',100)->unique();
            $table->decimal('salary', 10, 2)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number', 15); // رقم الهاتف
            $table->string('national_id', 20)->unique(); // رقم الهوية الوطنية
            $table->string('job_title', 100);
            $table->date('birth_date'); // تاريخ الميلاد
            $table->date('hire_date'); // تاريخ التوظيف
            $table->string('nationality',40);                                    
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');          
            $table->string('gender')->default('ذكر');                       
            $table->string('employment_type')->default('دوام كامل'); 
            $table->string('password')->nullable(); 
            $table->string('status')->default('نشط');              
            $table->string('role')->default('user');
            $table->integer('role_id')->default(2);           
            $table->json('file_paths')->nullable();
            $table->string('google_id')->nullable();
            $table->string('google_token')->nullable();
            $table->rememberToken();        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
