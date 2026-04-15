<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import type { Exercise } from '@/types/learn';

const props = defineProps<{
    exercise: Exercise;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    submit: [answer: string[]];
}>();

const shuffled = ref<string[]>([]);
const selected = ref<string[]>([]);

onMounted(() => {
    shuffled.value = [...(props.exercise.options ?? [])];
});

const remaining = computed(() =>
    shuffled.value.filter((w) => !selected.value.includes(w) || countOccurrences(shuffled.value, w) > countOccurrences(selected.value, w))
);

function countOccurrences(arr: string[], val: string): number {
    return arr.filter((x) => x === val).length;
}

function pickWord(word: string) {
    if (props.disabled) return;
    selected.value = [...selected.value, word];
}

function removeWord(index: number) {
    if (props.disabled) return;
    const next = [...selected.value];
    next.splice(index, 1);
    selected.value = next;
}

function handleSubmit() {
    if (selected.value.length === 0 || props.disabled) return;
    emit('submit', [...selected.value]);
}
</script>

<template>
    <div class="space-y-5">
        <p class="text-lg leading-relaxed text-foreground">{{ props.exercise.prompt }}</p>

        <!-- Answer zone -->
        <div
            class="min-h-14 flex flex-wrap gap-2 rounded-xl border-2 border-dashed border-border bg-muted/30 p-3"
        >
            <button
                v-for="(word, i) in selected"
                :key="`sel-${i}`"
                type="button"
                :disabled="props.disabled"
                class="rounded-lg border-2 border-emerald-400 bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700 transition-colors hover:bg-emerald-100 disabled:cursor-not-allowed dark:bg-emerald-950 dark:text-emerald-300"
                @click="removeWord(i)"
            >
                {{ word }}
            </button>
            <span
                v-if="selected.length === 0"
                class="self-center text-sm text-muted-foreground"
            >
                Tap words below to build your answer…
            </span>
        </div>

        <!-- Word bank -->
        <div class="flex flex-wrap gap-2">
            <button
                v-for="(word, i) in remaining"
                :key="`bank-${i}`"
                type="button"
                :disabled="props.disabled"
                class="rounded-lg border-2 border-border bg-card px-3 py-1.5 text-sm font-medium text-card-foreground transition-colors hover:border-emerald-400 hover:bg-muted disabled:cursor-not-allowed disabled:opacity-50"
                @click="pickWord(word)"
            >
                {{ word }}
            </button>
        </div>

        <button
            type="button"
            :disabled="selected.length === 0 || props.disabled"
            class="w-full rounded-xl bg-emerald-500 py-3 font-semibold text-white transition-colors hover:bg-emerald-600 disabled:cursor-not-allowed disabled:opacity-40"
            @click="handleSubmit"
        >
            Check
        </button>
    </div>
</template>
