<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        align?: 'left' | 'right';
        placement?: 'bottom-end' | 'right';
        width?: '48';
        contentClasses?: string;
    }>(),
    {
        align: 'right',
        placement: 'bottom-end',
        width: '48',
        contentClasses: 'py-1 bg-white',
    },
);

const widthClass = computed(() => {
    return {
        48: 'w-48',
    }[props.width.toString()];
});

const popperPlacement = computed(() => {
    if (props.placement === 'right') {
        return 'right-start';
    }

    if (props.align === 'left') {
        return 'bottom-start';
    }

    return 'bottom-end';
});
</script>

<template>
    <VDropdown
        :placement="popperPlacement"
        :triggers="['click']"
        :auto-hide="true"
        :distance="8"
        :popper-triggers="[]"
        strategy="fixed"
    >
        <slot name="trigger" />

        <template #popper>
            <div
                class="rounded-md ring-1 ring-black ring-opacity-5 shadow-lg"
                :class="[widthClass, contentClasses]"
            >
                <slot name="content" />
            </div>
        </template>
    </VDropdown>
</template>
