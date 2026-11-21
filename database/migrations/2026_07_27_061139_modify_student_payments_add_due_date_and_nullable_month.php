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
        Schema::table('student_payments', function (Blueprint $table) {
            $table->integer('month')->nullable()->change();
            $table->integer('year')->nullable()->change();
            $table->date('due_date')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->integer('month')->nullable(false)->change();
            $table->integer('year')->nullable(false)->change();
            $table->dropColumn('due_date');
        });
    }
};
