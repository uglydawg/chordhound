<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\LessonModule;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Services\AchievementService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LearningSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create achievements first
        app(AchievementService::class)->createDefaultAchievements();
        
        // Create lesson modules
        $fundamentalsModule = LessonModule::create([
            'name' => 'Chord Fundamentals',
            'slug' => 'chord-fundamentals',
            'description' => 'Learn the basics of piano chords and how they are constructed',
            'icon' => 'musical-note',
            'order_index' => 1,
        ]);

        $typesModule = LessonModule::create([
            'name' => 'Chord Types & Extensions',
            'slug' => 'chord-types-extensions',
            'description' => 'Explore different chord types including 7ths and extended harmonies',
            'icon' => 'sparkles',
            'order_index' => 2,
        ]);

        $inversionsModule = LessonModule::create([
            'name' => 'Inversions & Voice Leading',
            'slug' => 'inversions-voice-leading',
            'description' => 'Master chord inversions and smooth voice leading techniques',
            'icon' => 'arrows-up-down',
            'order_index' => 3,
        ]);

        $progressionsModule = LessonModule::create([
            'name' => 'Chord Progressions',
            'slug' => 'chord-progressions',
            'description' => 'Learn common chord progressions used in popular music',
            'icon' => 'queue-list',
            'order_index' => 4,
        ]);

        // Create lessons for Fundamentals module
        $lesson1 = Lesson::create([
            'module_id' => $fundamentalsModule->id,
            'title' => 'What is a Chord?',
            'slug' => 'what-is-a-chord',
            'description' => 'Understanding intervals, triads, and basic chord construction',
            'content' => [
                'type' => 'structured',
                'sections' => [
                    [
                        'type' => 'text',
                        'content' => 'A chord is a group of notes played together. The most basic chord is a triad, which consists of three notes.',
                    ],
                    [
                        'type' => 'interactive',
                        'component' => 'chord-builder',
                        'data' => ['chord' => 'C', 'type' => 'major'],
                    ],
                ],
            ],
            'difficulty_level' => 1,
            'estimated_time' => 15,
            'order_index' => 1,
        ]);

        $lesson2 = Lesson::create([
            'module_id' => $fundamentalsModule->id,
            'title' => 'Major vs Minor Chords',
            'slug' => 'major-vs-minor-chords',
            'description' => 'Learn the difference between major and minor chords through visual and audio comparison',
            'content' => [
                'type' => 'structured',
                'sections' => [
                    [
                        'type' => 'text',
                        'content' => 'Major chords sound happy and bright, while minor chords sound sad or melancholic. The difference is in the third note.',
                    ],
                    [
                        'type' => 'comparison',
                        'component' => 'chord-comparison',
                        'data' => [
                            'chord1' => ['root' => 'C', 'type' => 'major'],
                            'chord2' => ['root' => 'C', 'type' => 'minor'],
                        ],
                    ],
                ],
            ],
            'difficulty_level' => 1,
            'estimated_time' => 20,
            'order_index' => 2,
        ]);

        // Create a quiz for the first lesson
        $quiz1 = Quiz::create([
            'lesson_id' => $lesson1->id,
            'title' => 'Chord Basics Quiz',
            'description' => 'Test your understanding of basic chord concepts',
            'type' => 'multiple_choice',
            'passing_score' => 70,
            'time_limit' => 300, // 5 minutes
            'order_index' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'How many notes are in a basic triad?',
            'options' => ['2', '3', '4', '5'],
            'correct_answer' => ['3'],
            'explanation' => 'A triad consists of three notes: the root, third, and fifth.',
            'points' => 1,
            'order_index' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'question' => 'What interval determines if a chord is major or minor?',
            'options' => ['The root', 'The third', 'The fifth', 'The octave'],
            'correct_answer' => ['The third'],
            'explanation' => 'The third interval (major third vs minor third) determines whether a chord is major or minor.',
            'points' => 1,
            'order_index' => 2,
        ]);

        // Create a chord identification quiz
        $quiz2 = Quiz::create([
            'lesson_id' => $lesson2->id,
            'title' => 'Chord Identification',
            'description' => 'Identify major and minor chords by their sound',
            'type' => 'chord_identification',
            'passing_score' => 80,
            'time_limit' => 600, // 10 minutes
            'order_index' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'question' => 'Identify the notes in a C major chord',
            'options' => null,
            'correct_answer' => ['C', 'E', 'G'],
            'explanation' => 'C major consists of C (root), E (major third), and G (perfect fifth).',
            'points' => 2,
            'order_index' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'question' => 'Identify the notes in an A minor chord',
            'options' => null,
            'correct_answer' => ['A', 'C', 'E'],
            'explanation' => 'A minor consists of A (root), C (minor third), and E (perfect fifth).',
            'points' => 2,
            'order_index' => 2,
        ]);

        // Create lessons for Inversions module
        $lesson3 = Lesson::create([
            'module_id' => $inversionsModule->id,
            'title' => 'Introduction to Inversions',
            'slug' => 'introduction-to-inversions',
            'description' => 'Learn how chord inversions work and why they are important',
            'content' => [
                'type' => 'structured',
                'sections' => [
                    [
                        'type' => 'text',
                        'content' => 'Inversions are different ways to arrange the notes of a chord. They help create smooth voice leading between chords.',
                    ],
                    [
                        'type' => 'interactive',
                        'component' => 'inversion-explorer',
                        'data' => ['chord' => 'C', 'type' => 'major'],
                    ],
                ],
            ],
            'difficulty_level' => 2,
            'estimated_time' => 25,
            'order_index' => 1,
        ]);

        // Create a lesson for progressions
        $lesson4 = Lesson::create([
            'module_id' => $progressionsModule->id,
            'title' => 'The I-IV-V Progression',
            'slug' => 'i-iv-v-progression',
            'description' => 'Master the most common chord progression in rock and blues',
            'content' => [
                'type' => 'structured',
                'sections' => [
                    [
                        'type' => 'text',
                        'content' => 'The I-IV-V progression is fundamental to rock, blues, and many other genres. In the key of C, this would be C-F-G.',
                    ],
                    [
                        'type' => 'progression',
                        'component' => 'progression-player',
                        'data' => [
                            'progression' => ['C', 'F', 'G', 'C'],
                            'key' => 'C',
                        ],
                    ],
                ],
            ],
            'difficulty_level' => 2,
            'estimated_time' => 30,
            'order_index' => 1,
            'is_premium' => false,
        ]);

        $this->command->info('Learning system seeded successfully!');
    }
}
