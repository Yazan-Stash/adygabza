<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import InputExercise from '@/components/learn/InputExercise.vue';
import MCQExercise from '@/components/learn/MCQExercise.vue';
import ReorderExercise from '@/components/learn/ReorderExercise.vue';
import TooltipWord from '@/components/learn/TooltipWord.vue';
import type { Exercise, AnswerResult } from '@/types/learn';

const props = defineProps<{
    exercise: Exercise;
    exerciseNumber: number;
    totalExercises: number;
}>();

const emit = defineEmits<{
    answered: [result: AnswerResult];
    next: [];
}>();

const result = ref<AnswerResult | null>(null);
const loading = ref(false);

watch(() => props.exercise.id, () => {
    result.value = null;
    loading.value = false;
});

const wordTokens = computed(() => props.exercise.metadata?.word_tokens ?? []);

async function handleSubmit(answer: string | string[]) {
    if (loading.value) return;
    loading.value = true;

    try {
        const response = await fetch(`/exercises/${props.exercise.id}/answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({ answer }),
        });

        const data: AnswerResult = await response.json();
        result.value = data;
        emit('answered', data);
    } finally {
        loading.value = false;
    }
}

function getCsrfToken(): string {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

function handleNext() {
    emit('next');
}
</script>

<template>
    <div class="space-y-6">
        <!-- Progress header -->
        <div class="flex items-center gap-3">
            <div class="flex-1 overflow-hidden rounded-full bg-muted">
                <div
                    class="h-2 rounded-full bg-emerald-500 transition-all duration-500"
                    :style="{ width: `${(exerciseNumber / totalExercises) * 100}%` }"
                />
            </div>
            <span class="shrink-0 text-sm text-muted-foreground">
                {{ exerciseNumber }} / {{ totalExercises }}
            </span>
        </div>

        <!-- Exercise type label -->
        <div class="flex items-center gap-2">
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                {{
                    exercise.type === 'complete_sentence_input' ? 'Fill in the blank'
                    : exercise.type === 'complete_sentence_mcq' ? 'Choose the correct word'
                    : 'Arrange the words'
                }}
            </span>
        </div>

        <!-- Word tokens row (hover translations) -->
        <div v-if="wordTokens.length > 0" class="flex flex-wrap gap-x-2 gap-y-1 rounded-xl bg-muted/50 px-4 py-3">
            <TooltipWord
                v-for="token in wordTokens"
                :key="token.text"
                :token="token"
            />
        </div>

        <!-- Exercise component -->
        <InputExercise
            v-if="exercise.type === 'complete_sentence_input'"
            :exercise="exercise"
            :disabled="!!result || loading"
            @submit="handleSubmit"
        />
        <MCQExercise
            v-else-if="exercise.type === 'complete_sentence_mcq'"
            :exercise="exercise"
            :disabled="!!result || loading"
            @submit="handleSubmit"
        />
        <ReorderExercise
            v-else-if="exercise.type === 'reorder_translation'"
            :exercise="exercise"
            :disabled="!!result || loading"
            @submit="handleSubmit"
        />

        <!-- Feedback panel -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
        >
            <div
                v-if="result"
                :class="[
                    'rounded-xl border-2 p-4 space-y-3',
                    result.correct
                        ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-950/50'
                        : 'border-red-400 bg-red-50 dark:bg-red-950/50',
                ]"
            >
                <div class="flex items-center gap-2">
                    <span class="text-xl">{{ result.correct ? '✅' : '❌' }}</span>
                    <span :class="['font-bold text-lg', result.correct ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300']">
                        {{ result.correct ? 'Correct!' : 'Incorrect' }}
                    </span>
                </div>

                <div v-if="!result.correct" class="text-sm text-muted-foreground">
                    Correct answer:
                    <span class="font-semibold text-foreground">
                        {{ Array.isArray(result.correct_answer) ? result.correct_answer.join(' / ') : result.correct_answer }}
                    </span>
                </div>

                <p v-if="result.explanation" class="text-sm text-muted-foreground">
                    {{ result.explanation }}
                </p>

                <button
                    type="button"
                    :class="[
                        'w-full rounded-xl py-3 font-semibold text-white transition-colors',
                        result.correct ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-slate-500 hover:bg-slate-600',
                    ]"
                    @click="handleNext"
                >
                    Continue
                </button>
            </div>
        </Transition>
    </div>
</template>
