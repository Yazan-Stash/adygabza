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

const answer = ref('');
const inputRef = ref<HTMLInputElement | null>(null);

onMounted(() => {
    inputRef.value?.focus();
});

function handleSubmit() {
    if (!answer.value.trim() || props.disabled) return;
    emit('submit', answer.value.trim());
}

function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'Enter') {
        handleSubmit();
    }
}
</script>

<template>
    <div class="space-y-4">
        <p class="text-lg leading-relaxed text-foreground">{{ props.exercise.prompt }}</p>

        <div class="flex items-center gap-3">
            <input
                ref="inputRef"
                v-model="answer"
                type="text"
                placeholder="Type your answer…"
                :disabled="props.disabled"
                class="flex-1 rounded-xl border border-input bg-background px-4 py-3 text-base outline-none ring-offset-background transition-colors placeholder:text-muted-foreground focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 disabled:cursor-not-allowed disabled:opacity-50"
                @keydown="handleKeydown"
            />
            <button
                type="button"
                :disabled="!answer.trim() || props.disabled"
                class="rounded-xl bg-emerald-500 px-6 py-3 font-semibold text-white transition-colors hover:bg-emerald-600 disabled:cursor-not-allowed disabled:opacity-40"
                @click="handleSubmit"
            >
                Check
            </button>
        </div>
    </div>
</template>
