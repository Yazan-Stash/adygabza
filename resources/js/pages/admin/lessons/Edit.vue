<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { Course, Lesson } from '@/types/learn';

const props = defineProps<{
    course: Course;
    lesson: Lesson | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Courses', href: '/admin/courses' },
            { title: 'Edit Lesson', href: '#' },
        ],
    },
});

const isEditing = !!props.lesson;

const form = useForm({
    title: props.lesson?.title ?? '',
    description: props.lesson?.description ?? '',
    order: props.lesson?.order ?? 0,
});

function submit() {
    if (isEditing) {
        form.put(
            `/admin/courses/${props.course.id}/lessons/${props.lesson!.id}`,
        );
    } else {
        form.post(`/admin/courses/${props.course.id}/lessons`);
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Edit Lesson' : 'New Lesson'" />

    <div class="space-y-8 p-4">
        <form class="space-y-6" @submit.prevent="submit">
            <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-semibold text-foreground">
                    {{ isEditing ? 'Edit Lesson' : 'Create Lesson' }}
                </h2>

                <div class="space-y-4">
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-foreground"
                            >Title *</label
                        >
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="e.g. Greetings"
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                        <p
                            v-if="form.errors.title"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-foreground"
                            >Description</label
                        >
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="What this lesson covers..."
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>

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
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-600 disabled:opacity-50"
                >
                    {{ isEditing ? 'Save Lesson' : 'Create Lesson' }}
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
