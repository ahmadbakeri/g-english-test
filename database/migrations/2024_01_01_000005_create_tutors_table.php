<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add role column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->after('email'); // admin, tutor, staff
        });

        Schema::create('tutors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nip')->unique()->nullable(); // Nomor Induk Pegawai
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('specialization')->nullable(); // e.g. "Grammar", "Speaking", "TOEFL"
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutors');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
