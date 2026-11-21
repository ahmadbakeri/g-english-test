<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cbt_exams', function (Blueprint $table) {
            if (!Schema::hasColumn('cbt_exams', 'cbt_mode')) {
                $table->enum('cbt_mode', ['google_form', 'internal_excel'])->default('google_form')->after('subject_id');
            }
            if (!Schema::hasColumn('cbt_exams', 'gform_embed_url')) {
                $table->text('gform_embed_url')->nullable();
            }
            if (!Schema::hasColumn('cbt_exams', 'gform_sheet_csv_url')) {
                $table->text('gform_sheet_csv_url')->nullable();
            }
            if (!Schema::hasColumn('cbt_exams', 'gform_nis_entry_id')) {
                $table->string('gform_nis_entry_id', 100)->nullable();
            }
            if (!Schema::hasColumn('cbt_exams', 'gform_name_entry_id')) {
                $table->string('gform_name_entry_id', 100)->nullable();
            }
            if (!Schema::hasColumn('cbt_exams', 'gform_score_column')) {
                $table->string('gform_score_column', 100)->nullable()->default('Skor');
            }
            if (!Schema::hasColumn('cbt_exams', 'imported_excel_path')) {
                $table->string('imported_excel_path', 255)->nullable();
            }
            if (!Schema::hasColumn('cbt_exams', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cbt_exams', function (Blueprint $table) {
            $cols = array_filter([
                'cbt_mode',
                'gform_embed_url',
                'gform_sheet_csv_url',
                'gform_nis_entry_id',
                'gform_name_entry_id',
                'gform_score_column',
                'imported_excel_path',
                'last_synced_at',
            ], fn ($col) => Schema::hasColumn('cbt_exams', $col));

            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
