<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img
        src="/fittable_logo.svg"
        alt="Fittable Logo"
        :class="className"
        v-bind="$attrs"
    />
</template>
