<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cbt_exams', function (Blueprint $table) {
            if (!Schema::hasColumn('cbt_exams', 'scoring_mode')) {
                $table->string('scoring_mode', 20)->default('standard')->after('cbt_mode');
            }
        });

        Schema::table('cbt_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('cbt_questions', 'question_type')) {
                $table->string('question_type', 20)->default('mcq')->after('cbt_exam_id');
            }
            if (!Schema::hasColumn('cbt_questions', 'statements')) {
                $table->json('statements')->nullable()->after('option_e');
            }
            if (!Schema::hasColumn('cbt_questions', 'explanation')) {
                $table->longText('explanation')->nullable()->after('points');
            }
            if (!Schema::hasColumn('cbt_questions', 'order_number')) {
                $table->unsignedInteger('order_number')->default(0)->after('cbt_exam_id');
            }
        });

        Schema::table('cbt_questions', function (Blueprint $table) {
            $table->longText('question_text')->change();
            $table->longText('option_a')->nullable()->change();
            $table->longText('option_b')->nullable()->change();
            $table->longText('option_c')->nullable()->change();
            $table->longText('option_d')->nullable()->change();
            $table->longText('option_e')->nullable()->change();
            $table->string('correct_answer', 255)->nullable()->change();
        });

        Schema::table('cbt_answers', function (Blueprint $table) {
            $table->text('selected_answer')->nullable()->change();
            $table->boolean('is_correct')->nullable()->default(null)->change();
            if (!Schema::hasColumn('cbt_answers', 'answer_payload')) {
                $table->json('answer_payload')->nullable()->after('selected_answer');
            }
        });

        Schema::table('cbt_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('cbt_attempts', 'scoring_status')) {
                $table->string('scoring_status', 25)->default('graded')->after('score');
            }
        });

        Schema::create('cbt_import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cbt_exam_id')->nullable()->constrained('cbt_exams')->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('original_filename');
            $table->string('stored_path');
            $table->string('media_dir')->nullable();
            $table->string('status', 20)->default('pending'); // pending, parsed, failed, published
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('valid_count')->default(0);
            $table->unsignedInteger('warning_count')->default(0);
            $table->unsignedInteger('error_count')->default(0);
            $table->json('parse_summary')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('cbt_question_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('cbt_import_batches')->cascadeOnDelete();
            $table->unsignedInteger('sequence_no')->default(1);
            $table->string('question_type', 20)->default('mcq'); // mcq, complex_tf, short_answer
            $table->longText('question_html');
            $table->longText('option_a')->nullable();
            $table->longText('option_b')->nullable();
            $table->longText('option_c')->nullable();
            $table->longText('option_d')->nullable();
            $table->longText('option_e')->nullable();
            $table->json('statements')->nullable();
            $table->string('correct_answer', 255)->nullable();
            $table->unsignedInteger('points')->default(1);
            $table->longText('explanation_html')->nullable();
            $table->json('extracted_media')->nullable();
            $table->string('validation_status', 20)->default('valid'); // valid, warning, error
            $table->json('validation_issues')->nullable();
            $table->boolean('has_shape_warning')->default(false);
            $table->longText('raw_source_debug')->nullable();
            $table->foreignId('published_question_id')->nullable()->constrained('cbt_questions')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_question_drafts');
        Schema::dropIfExists('cbt_import_batches');

        Schema::table('cbt_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('cbt_attempts', 'scoring_status')) {
                $table->dropColumn('scoring_status');
            }
        });

        Schema::table('cbt_answers', function (Blueprint $table) {
            if (Schema::hasColumn('cbt_answers', 'answer_payload')) {
                $table->dropColumn('answer_payload');
            }
        });

        Schema::table('cbt_questions', function (Blueprint $table) {
            $cols = array_filter(['question_type', 'statements', 'explanation', 'order_number'], fn ($c) => Schema::hasColumn('cbt_questions', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('cbt_exams', function (Blueprint $table) {
            if (Schema::hasColumn('cbt_exams', 'scoring_mode')) {
                $table->dropColumn('scoring_mode');
            }
        });
    }
};
