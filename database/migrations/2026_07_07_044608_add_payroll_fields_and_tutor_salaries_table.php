<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->string('grade')->default('C')->after('specialization');
        });

        Schema::table('journals', function (Blueprint $table) {
            $table->integer('jp_count')->default(2)->after('materials');
        });

        Schema::create('tutor_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained()->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->integer('total_jp')->default(0);
            $table->integer('total_amount')->default(0);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->date('paid_at')->nullable();
            $table->timestamps();
            
            $table->unique(['tutor_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_salaries');
        
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn('jp_count');
        });

        Schema::table('tutors', function (Blueprint $table) {
            $table->dropColumn('grade');
        });
    }
};
