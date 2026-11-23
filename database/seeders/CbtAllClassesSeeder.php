<?php

namespace Database\Seeders;

use App\Models\CbtExam;
use App\Models\CbtQuestion;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class CbtAllClassesSeeder extends Seeder
{
    /**
     * Seed English CBT quizzes (both Internal CBT and Google Form link mode)
     * for every ClassRoom in the database.
     */
    public function run(): void
    {
        $fallbackSubject = Subject::first();
        if (!$fallbackSubject) {
            return;
        }

        $classes = ClassRoom::all();

        foreach ($classes as $class) {
            $subjectId = $class->subject_id ?: $fallbackSubject->id;
            $level = $this->detectLevel($class->name);

            // 1. Internal CBT Exam (Real English Questions)
            $internalExam = CbtExam::updateOrCreate(
                [
                    'class_id' => $class->id,
                    'title' => "English Quiz CBT — {$class->name}",
                ],
                [
                    'description' => "Kuis Bahasa Inggris ({$level}) untuk kelas {$class->name}. Kerjakan dalam mode layar penuh (maks. 3 pelanggaran keluar/pindah tab).",
                    'subject_id' => $subjectId,
                    'cbt_mode' => 'internal_excel',
                    'duration_minutes' => 45,
                    'max_attempts' => 3,
                    'is_active' => true,
                    'shuffle_questions' => true,
                    'shuffle_options' => false,
                    'show_result_immediately' => true,
                    'allow_review' => true,
                ]
            );

            if ($internalExam->questions()->count() === 0) {
                foreach ($this->questionsForLevel($level) as $q) {
                    CbtQuestion::create([
                        'cbt_exam_id' => $internalExam->id,
                        'question_text' => $q[0],
                        'option_a' => $q[1],
                        'option_b' => $q[2],
                        'option_c' => $q[3],
                        'option_d' => $q[4],
                        'option_e' => $q[5],
                        'correct_answer' => $q[6],
                        'points' => 10,
                    ]);
                }
            }

            // 2. Google Form Mode CBT Exam (linked to youtube.com/@ahmadbakeri)
            CbtExam::updateOrCreate(
                [
                    'class_id' => $class->id,
                    'title' => "English Listening / Form — {$class->name}",
                ],
                [
                    'description' => "Ujian Bahasa Inggris mode link eksternal untuk kelas {$class->name}.",
                    'subject_id' => $subjectId,
                    'cbt_mode' => 'google_form',
                    'gform_embed_url' => 'https://youtube.com/@ahmadbakeri',
                    'duration_minutes' => 30,
                    'max_attempts' => 3,
                    'is_active' => true,
                    'show_result_immediately' => false,
                ]
            );
        }
    }

    private function detectLevel(string $className): string
    {
        $upper = strtoupper($className);
        if (str_contains($upper, 'SMA') || str_contains($upper, 'MA ') || str_contains($upper, 'ADVANCED')) {
            return 'SMA';
        }
        if (str_contains($upper, 'SMP') || str_contains($upper, 'MTS') || str_contains($upper, 'INTERMEDIATE')) {
            return 'SMP';
        }
        return 'SD';
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: string, 3: string, 4: string, 5: string, 6: string}>
     */
    private function questionsForLevel(string $level): array
    {
        if ($level === 'SMA') {
            return [
                ['Had I known about the traffic jam, I ___ earlier.', 'would leave', 'would have left', 'will leave', 'left', 'had left', 'B'],
                ['Not only ___ the presentation, but she also answered all questions.', 'she finished', 'did she finish', 'she did finish', 'finished she', 'was she finish', 'B'],
                ['The report ___ by the committee before the deadline tomorrow.', 'will complete', 'will be completed', 'completed', 'completing', 'has completed', 'B'],
                ['She speaks English as though she ___ in London for years.', 'lives', 'lived', 'had lived', 'living', 'will live', 'C'],
                ['Despite ___ tired, he continued studying until midnight.', 'he was', 'of being', 'being', 'to be', 'been', 'C'],
                ['I would rather you ___ me the truth right now.', 'tell', 'told', 'telling', 'to tell', 'have told', 'B'],
                ['The manager insisted that every employee ___ on time.', 'arrives', 'arrived', 'arrive', 'arriving', 'to arrive', 'C'],
                ['Scarcely had we arrived ___ it started to pour heavily.', 'than', 'when', 'after', 'while', 'since', 'B'],
                ['The research paper, ___ findings were published last week, gained global attention.', 'who', 'whom', 'which', 'whose', 'that', 'D'],
                ['By next July, they ___ in this course for three full years.', 'will study', 'will be studying', 'will have been studying', 'are studying', 'studied', 'C'],
            ];
        }

        if ($level === 'SMP') {
            return [
                ['I ___ a great English movie with my friends yesterday.', 'watch', 'watches', 'watched', 'watching', '', 'C'],
                ['She has ___ in this city since 2018.', 'live', 'lives', 'lived', 'living', '', 'C'],
                ['If it rains tomorrow, we ___ at home.', 'will stay', 'stayed', 'staying', 'would stay', '', 'A'],
                ['They were ___ when the teacher entered the classroom.', 'talk', 'talks', 'talked', 'talking', '', 'D'],
                ['The letter ___ by Budi yesterday morning.', 'wrote', 'writes', 'was written', 'is written', '', 'C'],
                ['He told me that he ___ very excited about the competition.', 'is', 'was', 'were', 'will be', '', 'B'],
                ['I look forward to ___ you at the next meeting.', 'see', 'seeing', 'saw', 'seen', '', 'B'],
                ['She is the student ___ won the first prize.', 'who', 'which', 'whom', 'whose', '', 'A'],
                ['You had better ___ your homework before playing.', 'do', 'to do', 'doing', 'did', '', 'A'],
                ['By the time we arrived at the station, the train ___.', 'left', 'leaves', 'has left', 'had left', '', 'D'],
            ];
        }

        return [
            ['I ___ an apple every morning.', 'eat', 'eats', 'eating', 'ate', '', 'A'],
            ['She ___ a very smart student.', 'am', 'is', 'are', 'be', '', 'B'],
            ['___ you like English?', 'Do', 'Does', 'Are', 'Is', '', 'A'],
            ['He ___ to school by bicycle every day.', 'go', 'goes', 'going', 'went', '', 'B'],
            ['They ___ playing football in the yard right now.', 'am', 'is', 'are', 'do', '', 'C'],
            ['My mother ___ a nurse at the hospital.', 'am', 'is', 'are', 'does', '', 'B'],
            ['___ is your favorite color?', 'Who', 'Where', 'What', 'How', '', 'C'],
            ['I have two cute ___ at home.', 'cat', 'cats', 'cates', 'caties', '', 'B'],
            ['The dictionary is ___ the table.', 'in', 'on', 'under', 'between', '', 'B'],
            ['She ___ not like spicy food.', 'do', 'does', 'is', 'are', '', 'B'],
        ];
    }
}
