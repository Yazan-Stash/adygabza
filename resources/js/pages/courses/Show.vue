<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ExerciseRenderer from '@/components/learn/ExerciseRenderer.vue';
import type {
    Course,
    Exercise,
    UserCourseProgress,
    AnswerResult,
} from '@/types/learn';

const props = defineProps<{
    course: Course;
    exercises: Exercise[];
    progress: UserCourseProgress | null;
}>();

const completedIds = ref<number[]>(
    props.progress?.completed_exercise_ids ?? [],
);
const currentIndex = ref<number>(
    (() => {
        if (!props.progress?.current_exercise_id) return 0;
        const idx = props.exercises.findIndex(
            (e) => e.id === props.progress!.current_exercise_id,
        );
        return idx >= 0 ? idx : 0;
    })(),
);
const score = ref(props.progress?.score ?? 0);
const finished = ref(false);

const currentExercise = computed(
    () => props.exercises[currentIndex.value] ?? null,
);
const progressPercent = computed(() =>
    props.exercises.length > 0
        ? (completedIds.value.length / props.exercises.length) * 100
        : 0,
);

function handleAnswered(result: AnswerResult) {
    if (
        result.correct &&
        currentExercise.value &&
        !completedIds.value.includes(currentExercise.value.id)
    ) {
        if (currentExercise.value.type !== 'concept_text') {
            score.value += 1;
        }

        completedIds.value = [...completedIds.value, currentExercise.value.id];
    }
}

function handleNext() {
    if (currentIndex.value < props.exercises.length - 1) {
        currentIndex.value += 1;
    } else {
        finished.value = true;
    }
}
</script>

<template>
    <Head :title="course.title" />

    <div class="flex min-h-screen flex-col bg-background">
        <!-- Top bar -->
        <header
            class="flex items-center justify-between border-b border-border px-4 py-3"
        >
            <Link
                href="/courses"
                class="flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                ← Back to courses
            </Link>
            <div class="flex items-center gap-3">
                <span
                    class="text-sm font-medium text-emerald-600 dark:text-emerald-400"
                >
                    ⭐ {{ score }} pts
                </span>
                <span class="text-xs text-muted-foreground">
                    {{ completedIds.length }}/{{ exercises.length }} done
                </span>
            </div>
        </header>

        <!-- Course header -->
        <div class="border-b border-border bg-muted/30 px-4 py-4">
            <div class="mx-auto max-w-2xl">
                <div
                    class="flex items-center gap-2 text-xs text-muted-foreground"
                >
                    <span>{{ course.language_from }}</span>
                    <span>→</span>
                    <span>{{ course.language_to }}</span>
                </div>
                <h1 class="mt-1 text-xl font-bold text-foreground">
                    {{ course.title }}
                </h1>
                <!-- Overall progress bar -->
                <div class="mt-3 overflow-hidden rounded-full bg-muted">
                    <div
                        class="h-1.5 rounded-full bg-emerald-500 transition-all duration-700"
                        :style="{ width: `${progressPercent}%` }"
                    />
                </div>
            </div>
        </div>

        <!-- Main content -->
        <main class="flex flex-1 items-start justify-center px-4 py-8">
            <div class="w-full max-w-2xl">
                <!-- Finished state -->
                <div v-if="finished" class="space-y-6 text-center">
                    <div class="text-6xl">🎉</div>
                    <h2 class="text-2xl font-bold text-foreground">
                        Course Complete!
                    </h2>
                    <p class="text-muted-foreground">
                        You scored
                        <span class="font-bold text-emerald-600">{{
                            score
                        }}</span>
                        out of
                        <span class="font-bold">{{ exercises.length }}</span>
                        exercises correctly.
                    </p>
                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:justify-center"
                    >
                        <Link
                            href="/courses"
                            class="rounded-xl border border-border bg-card px-6 py-3 font-semibold text-foreground transition-colors hover:bg-muted"
                        >
                            Browse more courses
                        </Link>
                        <button
                            type="button"
                            class="rounded-xl bg-emerald-500 px-6 py-3 font-semibold text-white transition-colors hover:bg-emerald-600"
                            @click="
                                currentIndex = 0;
                                finished = false;
                                completedIds = [];
                                score = 0;
                            "
                        >
                            Restart course
                        </button>
                    </div>
                </div>

                <!-- Exercise flow -->
                <ExerciseRenderer
                    v-else-if="currentExercise"
                    :exercise="currentExercise"
                    :exercise-number="currentIndex + 1"
                    :total-exercises="exercises.length"
                    @answered="handleAnswered"
                    @next="handleNext"
                />

                <!-- No exercises -->
                <div
                    v-else
                    class="rounded-xl border border-dashed border-border py-16 text-center"
                >
                    <p class="text-muted-foreground">
                        This course has no exercises yet.
                    </p>
                </div>
            </div>
        </main>
    </div>
</template>
