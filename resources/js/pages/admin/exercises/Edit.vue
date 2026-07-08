<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import type {
    Course,
    Exercise,
    ExerciseType,
    Lesson,
    WordToken,
} from '@/types/learn';

const props = defineProps<{
    course: Course;
    lesson: Lesson;
    exercise: Exercise | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Courses', href: '/admin/courses' },
            { title: 'Edit Exercise', href: '#' },
        ],
    },
});

const isEditing = !!props.exercise;

const exerciseTypes: { value: ExerciseType; label: string }[] = [
    { value: 'complete_sentence_input', label: 'Fill in the Blank (Input)' },
    { value: 'complete_sentence_mcq', label: 'Fill in the Blank (MCQ)' },
    { value: 'reorder_translation', label: 'Reorder Translation' },
    { value: 'concept_text', label: 'Concept Text' },
];

const form = useForm({
    type: (props.exercise?.type ?? 'complete_sentence_input') as ExerciseType,
    prompt: props.exercise?.prompt ?? '',
    answer_raw: (() => {
        const a = props.exercise?.answer;
        if (!a) return '';
        if (Array.isArray(a)) return a.join(', ');
        return String(a);
    })(),
    options_raw: (props.exercise?.options ?? []).join(', '),
    explanation: props.exercise?.explanation ?? '',
    order: props.exercise?.order ?? 0,
    word_tokens: (props.exercise?.metadata?.word_tokens ?? []) as WordToken[],
});

const showOptions = computed(
    () =>
        form.type === 'complete_sentence_mcq' ||
        form.type === 'reorder_translation',
);

const showAnswer = computed(() => form.type !== 'concept_text');

function buildPayload() {
    let answer: string | string[];
    if (form.type === 'concept_text') {
        answer = 'understood';
    } else if (form.type === 'complete_sentence_input') {
        const parts = form.answer_raw
            .split(',')
            .map((s) => s.trim())
            .filter(Boolean);
        answer = parts.length === 1 ? parts[0] : parts;
    } else if (form.type === 'reorder_translation') {
        answer = form.answer_raw
            .split(',')
            .map((s) => s.trim())
            .filter(Boolean);
    } else {
        answer = form.answer_raw.trim();
    }

    const options = showOptions.value
        ? form.options_raw
              .split(',')
              .map((s) => s.trim())
              .filter(Boolean)
        : null;

    return {
        type: form.type,
        prompt: form.prompt,
        answer,
        options,
        explanation: form.explanation,
        order: form.order,
        metadata:
            form.word_tokens.length > 0
                ? { word_tokens: form.word_tokens }
                : null,
    };
}

function submit() {
    const payload = buildPayload();
    if (isEditing) {
        form.transform(() => payload).put(
            `/admin/courses/${props.course.id}/lessons/${props.lesson.id}/exercises/${props.exercise!.id}`,
        );
    } else {
        form.transform(() => payload).post(
            `/admin/courses/${props.course.id}/lessons/${props.lesson.id}/exercises`,
        );
    }
}

// Word token helpers
function addToken() {
    form.word_tokens = [
        ...form.word_tokens,
        { text: '', translation: '', parts: [] },
    ];
}

function removeToken(i: number) {
    form.word_tokens = form.word_tokens.filter((_, idx) => idx !== i);
}

function addPart(tokenIndex: number) {
    const tokens = [...form.word_tokens];
    tokens[tokenIndex] = {
        ...tokens[tokenIndex],
        parts: [
            ...(tokens[tokenIndex].parts ?? []),
            { text: '', translation: '' },
        ],
    };
    form.word_tokens = tokens;
}

function removePart(tokenIndex: number, partIndex: number) {
    const tokens = [...form.word_tokens];
    tokens[tokenIndex] = {
        ...tokens[tokenIndex],
        parts: (tokens[tokenIndex].parts ?? []).filter(
            (_, i) => i !== partIndex,
        ),
    };
    form.word_tokens = tokens;
}
</script>

<template>
    <Head :title="isEditing ? 'Edit Exercise' : 'New Exercise'" />

    <div class="space-y-8 p-4">
        <form class="space-y-6" @submit.prevent="submit">
            <!-- Core fields -->
            <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-semibold text-foreground">
                    Exercise Details
                </h2>

                <div class="space-y-4">
                    <!-- Type -->
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-foreground"
                            >Exercise Type *</label
                        >
                        <select
                            v-model="form.type"
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option
                                v-for="t in exerciseTypes"
                                :key="t.value"
                                :value="t.value"
                            >
                                {{ t.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Order -->
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-foreground"
                            >Order *</label
                        >
                        <input
                            v-model.number="form.order"
                            type="number"
                            min="0"
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                        <p
                            v-if="form.errors.order"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.order }}
                        </p>
                    </div>

                    <!-- Prompt -->
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-foreground"
                            >Prompt *</label
                        >
                        <textarea
                            v-model="form.prompt"
                            rows="3"
                            :placeholder="
                                form.type === 'concept_text'
                                    ? 'Describe the concept the learner should understand.'
                                    : 'The sentence with ___ for blanks, or full sentence for reorder.'
                            "
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                        <p
                            v-if="form.errors.prompt"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.prompt }}
                        </p>
                    </div>

                    <!-- Answer -->
                    <div v-if="showAnswer">
                        <label
                            class="mb-1.5 block text-sm font-medium text-foreground"
                        >
                            Answer *
                            <span
                                class="ml-1 font-normal text-muted-foreground"
                            >
                                <template
                                    v-if="
                                        form.type === 'complete_sentence_input'
                                    "
                                    >(comma-separated for multiple accepted
                                    answers)</template
                                >
                                <template
                                    v-else-if="
                                        form.type === 'reorder_translation'
                                    "
                                    >(comma-separated words in correct
                                    order)</template
                                >
                                <template v-else>(the correct option)</template>
                            </span>
                        </label>
                        <input
                            v-model="form.answer_raw"
                            type="text"
                            :placeholder="
                                form.type === 'complete_sentence_input'
                                    ? 'hola, Hola'
                                    : form.type === 'reorder_translation'
                                      ? 'Quiero, un, café, por, favor'
                                      : 'Me'
                            "
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>

                    <!-- Options (MCQ / Reorder) -->
                    <div v-if="showOptions">
                        <label
                            class="mb-1.5 block text-sm font-medium text-foreground"
                        >
                            Options *
                            <span class="ml-1 font-normal text-muted-foreground"
                                >(comma-separated)</span
                            >
                        </label>
                        <input
                            v-model="form.options_raw"
                            type="text"
                            :placeholder="
                                form.type === 'complete_sentence_mcq'
                                    ? 'Me, Te, Se, Le'
                                    : 'favor, Quiero, café, un, por'
                            "
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>

                    <!-- Explanation -->
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-foreground"
                            >Explanation</label
                        >
                        <textarea
                            v-model="form.explanation"
                            rows="3"
                            placeholder="Explanation shown after the user answers…"
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>
                </div>
            </div>

            <!-- Word Tokens -->
            <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">
                            Word Translations
                        </h2>
                        <p class="mt-0.5 text-sm text-muted-foreground">
                            Hover tooltips shown above the prompt.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="rounded-xl border border-border px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-muted"
                        @click="addToken"
                    >
                        + Add Word
                    </button>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(token, ti) in form.word_tokens"
                        :key="ti"
                        class="rounded-xl border border-border bg-background p-4"
                    >
                        <div class="mb-3 flex items-center justify-between">
                            <span
                                class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                                >Word {{ ti + 1 }}</span
                            >
                            <button
                                type="button"
                                class="text-xs text-red-500 hover:underline"
                                @click="removeToken(ti)"
                            >
                                Remove
                            </button>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-medium text-muted-foreground"
                                    >Word Text</label
                                >
                                <input
                                    v-model="form.word_tokens[ti].text"
                                    type="text"
                                    placeholder="e.g. preflight"
                                    class="w-full rounded-lg border border-input bg-card px-3 py-2 text-sm outline-none focus:border-emerald-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-medium text-muted-foreground"
                                    >Translation</label
                                >
                                <input
                                    v-model="form.word_tokens[ti].translation"
                                    type="text"
                                    placeholder="e.g. before the flight"
                                    class="w-full rounded-lg border border-input bg-card px-3 py-2 text-sm outline-none focus:border-emerald-500"
                                />
                            </div>
                        </div>

                        <!-- Parts -->
                        <div class="mt-3 space-y-2">
                            <div
                                v-for="(part, pi) in token.parts"
                                :key="pi"
                                class="grid gap-2 rounded-lg bg-muted/50 p-3 sm:grid-cols-2"
                            >
                                <div>
                                    <label
                                        class="mb-1 block text-xs font-medium text-muted-foreground"
                                        >Part</label
                                    >
                                    <input
                                        v-model="
                                            form.word_tokens[ti].parts![pi].text
                                        "
                                        type="text"
                                        placeholder="e.g. pre"
                                        class="w-full rounded-lg border border-input bg-background px-3 py-1.5 text-sm outline-none focus:border-emerald-500"
                                    />
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label
                                            class="mb-1 block text-xs font-medium text-muted-foreground"
                                            >Translation</label
                                        >
                                        <input
                                            v-model="
                                                form.word_tokens[ti].parts![pi]
                                                    .translation
                                            "
                                            type="text"
                                            placeholder="e.g. before"
                                            class="w-full rounded-lg border border-input bg-background px-3 py-1.5 text-sm outline-none focus:border-emerald-500"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        class="mt-5 self-start text-xs text-red-500 hover:underline"
                                        @click="removePart(ti, pi)"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="mt-3 text-xs font-medium text-emerald-600 hover:underline dark:text-emerald-400"
                            @click="addPart(ti)"
                        >
                            + Add sub-part
                        </button>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-600 disabled:opacity-50"
                >
                    {{ isEditing ? 'Save Exercise' : 'Create Exercise' }}
                </button>
                <Link
                    :href="`/admin/courses/${course.id}/edit`"
                    class="text-sm text-muted-foreground hover:text-foreground"
                >
                    Cancel
                </Link>
            </div>
        </form>
    </div>
</template>
