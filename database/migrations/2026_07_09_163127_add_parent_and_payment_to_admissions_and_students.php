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
        Schema::table('admissions', function (Blueprint $table) {
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('mother_job')->nullable();
            
            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_email')->nullable();
            $table->string('father_job')->nullable();

            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('mother_job')->nullable();
            
            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_email')->nullable();
            $table->string('father_job')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'mother_name', 'mother_phone', 'mother_email', 'mother_job',
                'father_name', 'father_phone', 'father_email', 'father_job'
            ]);
        });

        Schema::table('admissions', function (Blueprint $table) {
            $table->dropColumn([
                'mother_name', 'mother_phone', 'mother_email', 'mother_job',
                'father_name', 'father_phone', 'father_email', 'father_job',
                'payment_status'
            ]);
        });
    }
};
