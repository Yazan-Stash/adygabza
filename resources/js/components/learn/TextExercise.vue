<script setup lang="ts">
import type { Exercise } from '@/types/learn';

const props = defineProps<{
    exercise: Exercise;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    submit: [answer: string];
}>();

function handleSubmit() {
    if (props.disabled) return;
    emit('submit', 'understood');
}
</script>

<template>
    <div class="space-y-6">
        <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
            <p
                class="text-lg leading-relaxed whitespace-pre-line text-foreground"
            >
                {{ props.exercise.prompt }}
            </p>
        </div>

        <button
            type="button"
            :disabled="props.disabled"
            class="w-full rounded-xl bg-emerald-500 py-3 font-semibold text-white transition-colors hover:bg-emerald-600 disabled:cursor-not-allowed disabled:opacity-40"
            @click="handleSubmit"
        >
            Understood
        </button>
    </div>
</template>
