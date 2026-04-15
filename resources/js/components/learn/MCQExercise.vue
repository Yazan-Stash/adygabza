<script setup lang="ts">
import { ref, onMounted } from 'vue';
import type { Exercise } from '@/types/learn';

const props = defineProps<{
    exercise: Exercise;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    submit: [answer: string];
}>();

const selected = ref<string | null>(null);

function select(option: string) {
    if (props.disabled) return;
    selected.value = option;
}

function handleSubmit() {
    if (!selected.value || props.disabled) return;
    emit('submit', selected.value);
}

function handleKeydown(e: KeyboardEvent, option: string) {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        select(option);
    }
}
</script>

<template>
    <div class="space-y-4">
        <p class="text-lg leading-relaxed text-foreground">{{ props.exercise.prompt }}</p>

        <div class="grid grid-cols-2 gap-3">
            <button
                v-for="option in props.exercise.options"
                :key="option"
                type="button"
                :disabled="props.disabled"
                :class="[
                    'rounded-xl border-2 px-4 py-3 text-left font-medium transition-all',
                    selected === option
                        ? 'border-emerald-500 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                        : 'border-border bg-card text-card-foreground hover:border-emerald-400 hover:bg-muted',
                    'disabled:cursor-not-allowed disabled:opacity-50',
                ]"
                @click="select(option)"
                @keydown="handleKeydown($event, option)"
            >
                {{ option }}
            </button>
        </div>

        <button
            type="button"
            :disabled="!selected || props.disabled"
            class="w-full rounded-xl bg-emerald-500 py-3 font-semibold text-white transition-colors hover:bg-emerald-600 disabled:cursor-not-allowed disabled:opacity-40"
            @click="handleSubmit"
        >
            Check
        </button>
    </div>
</template>
