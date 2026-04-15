<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\UserCourseProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseAnswerController extends Controller
{
    public function submit(Request $request, Exercise $exercise): JsonResponse
    {
        $request->validate(['answer' => ['required']]);

        $userAnswer = $request->input('answer');
        $correct = $this->checkAnswer($exercise, $userAnswer);

        if ($request->user()) {
            $this->updateProgress($request->user()->id, $exercise, $correct);
        }

        return response()->json([
            'correct' => $correct,
            'explanation' => $exercise->explanation,
            'correct_answer' => $exercise->answer,
        ]);
    }

    private function checkAnswer(Exercise $exercise, mixed $userAnswer): bool
    {
        $correctAnswer = $exercise->answer;

        return match ($exercise->type) {
            'complete_sentence_input' => $this->checkInputAnswer($correctAnswer, $userAnswer),
            'complete_sentence_mcq' => $this->checkMcqAnswer($correctAnswer, $userAnswer),
            'reorder_translation' => $this->checkReorderAnswer($correctAnswer, $userAnswer),
            default => false,
        };
    }

    private function checkInputAnswer(mixed $correct, mixed $userAnswer): bool
    {
        $normalized = strtolower(trim((string) $userAnswer));
        $accepted = is_array($correct) ? $correct : [$correct];

        foreach ($accepted as $option) {
            if (strtolower(trim((string) $option)) === $normalized) {
                return true;
            }
        }

        return false;
    }

    private function checkMcqAnswer(mixed $correct, mixed $userAnswer): bool
    {
        $correctStr = is_array($correct) ? ($correct[0] ?? '') : (string) $correct;

        return strtolower(trim((string) $userAnswer)) === strtolower(trim($correctStr));
    }

    private function checkReorderAnswer(mixed $correct, mixed $userAnswer): bool
    {
        if (! is_array($userAnswer) || ! is_array($correct)) {
            return false;
        }

        return array_map('strtolower', array_map('trim', $userAnswer))
            === array_map('strtolower', array_map('trim', $correct));
    }

    private function updateProgress(int $userId, Exercise $exercise, bool $correct): void
    {
        $progress = UserCourseProgress::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $exercise->course_id],
            ['completed_exercise_ids' => [], 'score' => 0]
        );

        $completedIds = $progress->completed_exercise_ids ?? [];

        if (! in_array($exercise->id, $completedIds)) {
            $completedIds[] = $exercise->id;
            $progress->completed_exercise_ids = $completedIds;

            if ($correct) {
                $progress->score += 1;
            }
        }

        $nextExercise = Exercise::where('course_id', $exercise->course_id)
            ->where('order', '>', $exercise->order)
            ->orderBy('order')
            ->first();

        $progress->current_exercise_id = $nextExercise?->id ?? $exercise->id;
        $progress->save();
    }
}
