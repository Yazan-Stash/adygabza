<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Admin', href: '/admin' }],
    },
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="space-y-6 p-4">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-muted-foreground">Manage courses and exercises.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                href="/admin/courses"
                class="group flex flex-col gap-2 rounded-2xl border border-border bg-card p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
            >
                <div class="text-3xl">📚</div>
                <h2 class="font-semibold text-foreground group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                    Courses
                </h2>
                <p class="text-sm text-muted-foreground">Create, edit, publish and delete language courses.</p>
            </Link>
        </div>
    </div>
</template>
