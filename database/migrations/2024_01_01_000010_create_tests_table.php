<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->enum('type', ['daily', 'midterm', 'final'])->default('daily');
            $table->date('date');
            $table->integer('max_score')->default(100);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('test_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['test_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_scores');
        Schema::dropIfExists('tests');
    }
};
