<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import type { Course, Exercise } from '@/types/learn';

const props = defineProps<{
    course: (Course & { exercises?: Exercise[] }) | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Courses', href: '/admin/courses' },
            { title: 'Edit Course', href: '#' },
        ],
    },
});

const isEditing = !!props.course;

const form = useForm({
    title: props.course?.title ?? '',
    description: props.course?.description ?? '',
    language_from: props.course?.language_from ?? '',
    language_to: props.course?.language_to ?? '',
    is_published: props.course?.is_published ?? false,
});

function submit() {
    if (isEditing) {
        form.put(`/admin/courses/${props.course!.id}`);
    } else {
        form.post('/admin/courses');
    }
}

function deleteExercise(exercise: Exercise) {
    if (!confirm(`Delete exercise #${exercise.order}?`)) return;
    router.delete(`/admin/courses/${props.course!.id}/exercises/${exercise.id}`);
}
</script>

<template>
    <Head :title="isEditing ? 'Edit Course' : 'New Course'" />

    <div class="space-y-8 p-4">
        <!-- Course form -->
        <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-semibold text-foreground">
                {{ isEditing ? 'Edit Course' : 'Create New Course' }}
            </h2>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-foreground">Title *</label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="e.g. Spanish for Beginners"
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">Language From *</label>
                        <input
                            v-model="form.language_from"
                            type="text"
                            placeholder="e.g. English"
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                        <p v-if="form.errors.language_from" class="mt-1 text-xs text-red-500">{{ form.errors.language_from }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">Language To *</label>
                        <input
                            v-model="form.language_to"
                            type="text"
                            placeholder="e.g. Spanish"
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                        <p v-if="form.errors.language_to" class="mt-1 text-xs text-red-500">{{ form.errors.language_to }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-foreground">Description</label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Short course description…"
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                        <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            id="is_published"
                            v-model="form.is_published"
                            type="checkbox"
                            class="h-4 w-4 rounded border-input accent-emerald-500"
                        />
                        <label for="is_published" class="text-sm font-medium text-foreground">Published</label>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-600 disabled:opacity-50"
                    >
                        {{ isEditing ? 'Save Changes' : 'Create Course' }}
                    </button>
                    <Link href="/admin/courses" class="text-sm text-muted-foreground hover:text-foreground">
                        Cancel
                    </Link>
                </div>
            </form>
        </div>

        <!-- Exercises section (edit mode only) -->
        <div v-if="isEditing" class="rounded-2xl border border-border bg-card p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-foreground">
                    Exercises
                    <span class="ml-2 text-sm font-normal text-muted-foreground">({{ course!.exercises?.length ?? 0 }})</span>
                </h2>
                <Link
                    :href="`/admin/courses/${course!.id}/exercises/create`"
                    class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-emerald-600"
                >
                    + Add Exercise
                </Link>
            </div>

            <div v-if="!course!.exercises?.length" class="rounded-xl border border-dashed border-border py-10 text-center">
                <p class="text-sm text-muted-foreground">No exercises yet.</p>
            </div>

            <div v-else class="space-y-2">
                <div
                    v-for="exercise in course!.exercises"
                    :key="exercise.id"
                    class="flex items-start gap-4 rounded-xl border border-border bg-background px-4 py-3"
                >
                    <span class="mt-0.5 shrink-0 rounded-md bg-muted px-2 py-0.5 text-xs font-mono text-muted-foreground">
                        #{{ exercise.order }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span :class="[
                                'rounded-full px-2 py-0.5 text-xs font-semibold',
                                exercise.type === 'complete_sentence_input' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' :
                                exercise.type === 'complete_sentence_mcq' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300' :
                                'bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-300'
                            ]">
                                {{ exercise.type.replace(/_/g, ' ') }}
                            </span>
                        </div>
                        <p class="mt-1 truncate text-sm text-foreground">{{ exercise.prompt }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <Link
                            :href="`/admin/courses/${course!.id}/exercises/${exercise.id}/edit`"
                            class="rounded-lg border border-border px-3 py-1.5 text-xs font-medium text-foreground transition-colors hover:bg-muted"
                        >
                            Edit
                        </Link>
                        <button
                            type="button"
                            class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-50 dark:border-red-900 dark:hover:bg-red-950/50"
                            @click="deleteExercise(exercise)"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
