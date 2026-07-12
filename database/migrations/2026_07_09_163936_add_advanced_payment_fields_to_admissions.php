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
            $table->dropColumn('payment_status');
            $table->integer('tuition_fee')->default(4000000);
            $table->integer('registration_fee')->default(100000);
            $table->boolean('has_discount')->default(false);
            $table->integer('total_fee')->default(4100000);
            $table->enum('payment_method', ['full', 'installment'])->nullable();
            $table->integer('amount_paid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->dropColumn([
                'tuition_fee', 'registration_fee', 'has_discount', 
                'total_fee', 'payment_method', 'amount_paid'
            ]);
        });
    }
};
