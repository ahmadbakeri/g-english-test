<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('honors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('achievement'); // achievement, certificate, award
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('certificate_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('honors');
    }
};
