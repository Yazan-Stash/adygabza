<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/admin/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Admin Login" />

    <div class="flex min-h-screen items-center justify-center bg-muted/30 px-4">
        <div class="w-full max-w-sm space-y-6">
            <!-- Header -->
            <div class="text-center">
                <div class="text-4xl">🛡️</div>
                <h1 class="mt-3 text-2xl font-bold text-foreground">Admin Login</h1>
                <p class="mt-1 text-sm text-muted-foreground">Sign in to the admin panel</p>
            </div>

            <!-- Card -->
            <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <form class="space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            required
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="w-full rounded-xl border border-input bg-background px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-input accent-emerald-500"
                        />
                        <label for="remember" class="text-sm text-muted-foreground">Remember me</label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-xl bg-emerald-500 py-2.5 font-semibold text-white transition-colors hover:bg-emerald-600 disabled:opacity-50"
                    >
                        Sign in
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
