<script setup lang="ts">
import { ref } from 'vue';
import type { WordToken } from '@/types/learn';

const props = defineProps<{
    token: WordToken;
}>();

const visible = ref(false);
</script>

<template>
    <span class="relative inline-block">
        <span
            class="cursor-help border-b border-dashed border-current transition-colors hover:text-emerald-600 dark:hover:text-emerald-400"
            @mouseenter="visible = true"
            @mouseleave="visible = false"
            @focus="visible = true"
            @blur="visible = false"
            tabindex="0"
        >{{ props.token.text }}</span>

        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95 -translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 -translate-y-1"
        >
            <div
                v-if="visible"
                class="absolute bottom-full left-1/2 z-50 mb-2 min-w-max -translate-x-1/2 rounded-lg border border-border bg-popover px-3 py-2 text-sm shadow-lg"
            >
                <!-- Simple word translation -->
                <template v-if="!props.token.parts || props.token.parts.length === 0">
                    <span class="font-medium text-popover-foreground">{{ props.token.translation }}</span>
                </template>

                <!-- Parts breakdown -->
                <template v-else>
                    <div class="space-y-1">
                        <div
                            v-for="part in props.token.parts"
                            :key="part.text"
                            class="flex items-center gap-2"
                        >
                            <span class="font-mono text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ part.text }}</span>
                            <span class="text-muted-foreground">→</span>
                            <span class="text-popover-foreground">{{ part.translation }}</span>
                        </div>
                    </div>
                </template>

                <!-- Tooltip arrow -->
                <div class="absolute left-1/2 top-full -translate-x-1/2 border-4 border-transparent border-t-border" />
                <div class="absolute left-1/2 top-full -translate-x-1/2 mt-[-1px] border-4 border-transparent border-t-popover" />
            </div>
        </Transition>
    </span>
</template>
