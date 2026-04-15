<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import type { Course } from '@/types/learn';

defineProps<{
    courses: Course[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Courses', href: '/courses' }],
    },
});
</script>

<template>
    <Head title="Courses" />

    <div class="space-y-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Language Courses</h1>
                <p class="mt-1 text-sm text-muted-foreground">Pick a course and start learning today.</p>
            </div>
        </div>

        <div v-if="courses.length === 0" class="rounded-xl border border-dashed border-border py-16 text-center">
            <p class="text-muted-foreground">No courses available yet. Check back soon!</p>
        </div>

        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="course in courses"
                :key="course.id"
                :href="`/courses/${course.id}`"
                class="group relative flex flex-col gap-3 rounded-2xl border border-border bg-card p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            >
                <!-- Language badge -->
                <div class="flex items-center gap-2">
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                        {{ course.language_from }}
                    </span>
                    <span class="text-muted-foreground">→</span>
                    <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                        {{ course.language_to }}
                    </span>
                </div>

                <div>
                    <h2 class="font-semibold text-foreground group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                        {{ course.title }}
                    </h2>
                    <p v-if="course.description" class="mt-1 line-clamp-2 text-sm text-muted-foreground">
                        {{ course.description }}
                    </p>
                </div>

                <div class="mt-auto flex items-center justify-between border-t border-border pt-3 text-xs text-muted-foreground">
                    <span>{{ course.exercises_count ?? 0 }} exercises</span>
                    <span class="font-medium text-emerald-600 group-hover:underline dark:text-emerald-400">Start →</span>
                </div>
            </Link>
        </div>
    </div>
</template>
