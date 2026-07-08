<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::create([
            'title' => 'Spanish for Beginners',
            'description' => 'Learn everyday Spanish from scratch. Master greetings, numbers, and common phrases.',
            'language_from' => 'English',
            'language_to' => 'Spanish',
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Everyday basics',
            'description' => 'Start with greetings and common phrases.',
            'order' => 1,
        ]);

        $exercises = [
            [
                'type' => 'complete_sentence_input',
                'prompt' => 'The word for "hello" in Spanish is ___.',
                'answer' => ['hola', 'Hola'],
                'options' => null,
                'explanation' => '"Hola" is the most common Spanish greeting, used throughout the day.',
                'order' => 1,
                'metadata' => [
                    'word_tokens' => [
                        ['text' => 'hello', 'translation' => 'hola'],
                        ['text' => 'Spanish', 'translation' => 'español'],
                    ],
                ],
            ],
            [
                'type' => 'complete_sentence_mcq',
                'prompt' => '___ llamo María. (My name is María.)',
                'answer' => 'Me',
                'options' => ['Me', 'Te', 'Se', 'Le'],
                'explanation' => '"Me llamo" literally means "I call myself" and is used to introduce your name.',
                'order' => 2,
                'metadata' => [
                    'word_tokens' => [
                        ['text' => 'llamo', 'translation' => 'I call myself / my name is'],
                        ['text' => 'María', 'translation' => 'a common Spanish name'],
                    ],
                ],
            ],
            [
                'type' => 'reorder_translation',
                'prompt' => 'Arrange the words to say: "I want a coffee please"',
                'answer' => ['Quiero', 'un', 'café', 'por', 'favor'],
                'options' => ['favor', 'Quiero', 'café', 'un', 'por'],
                'explanation' => '"Quiero un café por favor" — quiero (I want), un (a), café (coffee), por favor (please).',
                'order' => 3,
                'metadata' => [
                    'word_tokens' => [
                        ['text' => 'Quiero', 'translation' => 'I want'],
                        ['text' => 'un', 'translation' => 'a / one (masculine)'],
                        ['text' => 'café', 'translation' => 'coffee'],
                        [
                            'text' => 'por favor',
                            'translation' => 'please',
                            'parts' => [
                                ['text' => 'por', 'translation' => 'for / by'],
                                ['text' => 'favor', 'translation' => 'favor / kindness'],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'complete_sentence_input',
                'prompt' => 'To say "thank you" in Spanish, you say ___.',
                'answer' => ['gracias', 'Gracias'],
                'options' => null,
                'explanation' => '"Gracias" comes from the Latin "gratia" meaning grace or thanks.',
                'order' => 4,
                'metadata' => [
                    'word_tokens' => [
                        ['text' => 'thank you', 'translation' => 'gracias'],
                    ],
                ],
            ],
            [
                'type' => 'complete_sentence_mcq',
                'prompt' => '¿Cómo ___ usted? (How are you? — formal)',
                'answer' => 'está',
                'options' => ['está', 'estás', 'estoy', 'estar'],
                'explanation' => 'With "usted" (formal you), use "está". With "tú" (informal), use "estás".',
                'order' => 5,
                'metadata' => [
                    'word_tokens' => [
                        ['text' => 'Cómo', 'translation' => 'how'],
                        ['text' => 'usted', 'translation' => 'you (formal)'],
                        [
                            'text' => 'está',
                            'translation' => 'are (formal)',
                            'parts' => [
                                ['text' => 'est', 'translation' => 'root of estar (to be)'],
                                ['text' => 'á', 'translation' => 'third person singular ending'],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'reorder_translation',
                'prompt' => 'Arrange the words to say: "Where is the bathroom?"',
                'answer' => ['¿Dónde', 'está', 'el', 'baño?'],
                'options' => ['el', '¿Dónde', 'baño?', 'está'],
                'explanation' => '"¿Dónde está el baño?" — dónde (where), está (is), el (the), baño (bathroom).',
                'order' => 6,
                'metadata' => [
                    'word_tokens' => [
                        ['text' => '¿Dónde', 'translation' => 'where'],
                        ['text' => 'está', 'translation' => 'is (location)'],
                        ['text' => 'el', 'translation' => 'the (masculine)'],
                        ['text' => 'baño', 'translation' => 'bathroom / bath'],
                    ],
                ],
            ],
        ];

        foreach ($exercises as $data) {
            $lesson->exercises()->create([
                ...$data,
                'course_id' => $course->id,
            ]);
        }
    }
}
