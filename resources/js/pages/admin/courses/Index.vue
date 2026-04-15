<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import type { Course } from '@/types/learn';

defineProps<{
    courses: Course[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Courses', href: '/admin/courses' },
        ],
    },
});

function deleteCourse(course: Course) {
    if (!confirm(`Delete "${course.title}"? This will also delete all its exercises.`)) return;
    router.delete(`/admin/courses/${course.id}`);
}

function togglePublish(course: Course) {
    router.put(`/admin/courses/${course.id}`, {
        title: course.title,
        description: course.description ?? '',
        language_from: course.language_from,
        language_to: course.language_to,
        is_published: !course.is_published,
    });
}
</script>

<template>
    <Head title="Manage Courses" />

    <div class="space-y-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Courses</h1>
                <p class="mt-1 text-sm text-muted-foreground">{{ courses.length }} total</p>
            </div>
            <Link
                href="/admin/courses/create"
                class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-emerald-600"
            >
                + New Course
            </Link>
        </div>

        <div v-if="courses.length === 0" class="rounded-xl border border-dashed border-border py-16 text-center">
            <p class="text-muted-foreground">No courses yet.</p>
            <Link href="/admin/courses/create" class="mt-3 inline-block text-sm font-medium text-emerald-600 hover:underline dark:text-emerald-400">
                Create your first course →
            </Link>
        </div>

        <div v-else class="overflow-hidden rounded-xl border border-border">
            <table class="w-full text-sm">
                <thead class="border-b border-border bg-muted/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Title</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Languages</th>
                        <th class="px-4 py-3 text-center font-medium text-muted-foreground">Exercises</th>
                        <th class="px-4 py-3 text-center font-medium text-muted-foreground">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="course in courses" :key="course.id" class="bg-card hover:bg-muted/30">
                        <td class="px-4 py-3 font-medium text-foreground">{{ course.title }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ course.language_from }} → {{ course.language_to }}</td>
                        <td class="px-4 py-3 text-center text-muted-foreground">{{ course.exercises_count ?? 0 }}</td>
                        <td class="px-4 py-3 text-center">
                            <button
                                type="button"
                                :class="[
                                    'rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors',
                                    course.is_published
                                        ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-950 dark:text-emerald-300'
                                        : 'bg-muted text-muted-foreground hover:bg-muted/80',
                                ]"
                                @click="togglePublish(course)"
                            >
                                {{ course.is_published ? 'Published' : 'Draft' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="`/admin/courses/${course.id}/edit`"
                                    class="rounded-lg border border-border px-3 py-1.5 text-xs font-medium text-foreground transition-colors hover:bg-muted"
                                >
                                    Edit
                                </Link>
                                <button
                                    type="button"
                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition-colors hover:bg-red-50 dark:border-red-900 dark:hover:bg-red-950/50"
                                    @click="deleteCourse(course)"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
