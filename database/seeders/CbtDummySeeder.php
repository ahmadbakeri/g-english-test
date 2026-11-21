<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CbtExam;
use App\Models\CbtQuestion;
use App\Models\ClassRoom;
use App\Models\Subject;

class CbtDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subject = Subject::first();
        if (!$subject) return;

        // --- SD Level ---
        $classSd = ClassRoom::where('name', 'like', '%SD%')->first();
        if ($classSd) {
            $examSd = CbtExam::create([
                'title' => 'Grammar Test (SD Level)',
                'description' => 'Ujian Bahasa Inggris tingkat Sekolah Dasar untuk menguji pemahaman Grammar dasar.',
                'class_id' => $classSd->id,
                'subject_id' => $subject->id,
                'duration_minutes' => 60,
                'max_attempts' => 3,
                'is_active' => true,
            ]);

            $questionsSd = [
                ['I ___ an apple.', 'eat', 'eats', 'eating', 'ate', '', 'A'],
                ['She ___ beautiful.', 'am', 'is', 'are', 'be', '', 'B'],
                ['___ you like banana?', 'Do', 'Does', 'Are', 'Is', '', 'A'],
                ['He ___ to school every day.', 'go', 'goes', 'going', 'went', '', 'B'],
                ['They ___ playing football now.', 'am', 'is', 'are', 'do', '', 'C'],
                ['My father ___ a doctor.', 'am', 'is', 'are', 'does', '', 'B'],
                ['___ is your name?', 'Who', 'Where', 'What', 'How', '', 'C'],
                ['I have two ___.', 'cat', 'cats', 'cates', 'caties', '', 'B'],
                ['The book is ___ the table.', 'in', 'on', 'at', 'under', '', 'B'],
                ['She ___ not like milk.', 'do', 'does', 'is', 'are', '', 'B'],
            ];

            foreach ($questionsSd as $q) {
                CbtQuestion::create([
                    'cbt_exam_id' => $examSd->id,
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

        // --- SMP Level ---
        $classSmp = ClassRoom::where('name', 'like', '%SMP%')->first();
        if ($classSmp) {
            $examSmp = CbtExam::create([
                'title' => 'Grammar Test (SMP Level)',
                'description' => 'Ujian Bahasa Inggris tingkat SMP untuk menguji pemahaman Grammar menengah.',
                'class_id' => $classSmp->id,
                'subject_id' => $subject->id,
                'duration_minutes' => 90,
                'max_attempts' => 3,
                'is_active' => true,
            ]);

            $questionsSmp = [
                ['I ___ a movie yesterday.', 'watch', 'watches', 'watched', 'watching', '', 'C'],
                ['She has ___ here since 2010.', 'live', 'lives', 'lived', 'living', '', 'C'],
                ['If it rains, we ___ at home.', 'will stay', 'stayed', 'staying', 'would stay', '', 'A'],
                ['They were ___ when the phone rang.', 'sleep', 'sleeps', 'slept', 'sleeping', '', 'D'],
                ['The letter ___ by John yesterday.', 'wrote', 'writes', 'was written', 'is written', '', 'C'],
                ['He told me that he ___ tired.', 'is', 'was', 'were', 'will be', '', 'B'],
                ['I look forward to ___ you soon.', 'see', 'seeing', 'saw', 'seen', '', 'B'],
                ['She is the girl ___ won the competition.', 'who', 'which', 'whom', 'whose', '', 'A'],
                ['You had better ___ home now.', 'go', 'to go', 'going', 'went', '', 'A'],
                ['By the time we arrived, the train ___.', 'left', 'leaves', 'has left', 'had left', '', 'D'],
            ];

            foreach ($questionsSmp as $q) {
                CbtQuestion::create([
                    'cbt_exam_id' => $examSmp->id,
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

        // --- SMA Level ---
        $classSma = ClassRoom::where('name', 'like', '%SMA%')->first();
        if ($classSma) {
            $examSma = CbtExam::create([
                'title' => 'Grammar Test (SMA Level)',
                'description' => 'Ujian Bahasa Inggris tingkat SMA untuk menguji pemahaman Grammar lanjutan dan kompleks.',
                'class_id' => $classSma->id,
                'subject_id' => $subject->id,
                'duration_minutes' => 120,
                'max_attempts' => 3,
                'is_active' => true,
            ]);

            $questionsSma = [
                ['If I ___ a bird, I would fly to you.', 'was', 'am', 'were', 'had been', 'be', 'C'],
                ['Hardly ___ arrived when the meeting started.', 'he had', 'had he', 'did he', 'he did', 'he has', 'B'],
                ['Despite ___ hard, he failed the exam.', 'study', 'studied', 'studies', 'studying', 'to study', 'D'],
                ['The committee ___ reached a decision on the new policy.', 'has', 'have', 'are', 'were', 'is', 'A'],
                ['I would rather you ___ it right now.', 'do', 'did', 'done', 'doing', 'to do', 'B'],
                ['Not only ___ the exam, but he also got a scholarship.', 'he passed', 'did he pass', 'passed he', 'he did pass', 'he passes', 'B'],
                ['She is used to ___ early in the morning.', 'wake up', 'waking up', 'woke up', 'wakes up', 'waken up', 'B'],
                ['It is imperative that the letter ___ sent immediately.', 'be', 'is', 'was', 'were', 'are', 'A'],
                ['I regret ___ you that your application has been denied.', 'to inform', 'informing', 'inform', 'informed', 'to informing', 'A'],
                ['By next year, I ___ from the university.', 'will graduate', 'will have graduated', 'am graduating', 'graduate', 'graduated', 'B'],
            ];

            foreach ($questionsSma as $q) {
                CbtQuestion::create([
                    'cbt_exam_id' => $examSma->id,
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
    }
}
