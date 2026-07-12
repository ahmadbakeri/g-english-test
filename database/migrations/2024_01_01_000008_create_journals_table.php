<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('topic');
            $table->text('description')->nullable();
            $table->text('materials')->nullable();
            $table->enum('status', ['draft', 'submitted', 'validated', 'rejected'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('journal_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('validated_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['approved', 'rejected'])->default('approved');
            $table->text('notes')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_validations');
        Schema::dropIfExists('journals');
    }
};
