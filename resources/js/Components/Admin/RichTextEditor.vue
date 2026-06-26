<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';

const props = withDefaults(defineProps<{
    modelValue: string;
    disabled?: boolean;
}>(), {
    disabled: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editor = ref<HTMLDivElement | null>(null);
const focused = ref(false);
const internalValue = ref(props.modelValue ?? '');

const exec = (command: string, value?: string) => {
    if (props.disabled) {
        return;
    }

    editor.value?.focus();
    document.execCommand(command, false, value);
    emit('update:modelValue', editor.value?.innerHTML ?? '');
};

const setLink = () => {
    if (props.disabled) {
        return;
    }

    const url = window.prompt('Введите URL ссылки');
    if (!url) {
        return;
    }

    exec('createLink', url.trim());
};

watch(
    () => props.modelValue,
    (value) => {
        const normalized = value ?? '';
        if (normalized === internalValue.value) {
            return;
        }

        internalValue.value = normalized;

        if (editor.value && !focused.value) {
            editor.value.innerHTML = normalized;
        }
    },
);

onMounted(() => {
    if (editor.value) {
        editor.value.innerHTML = internalValue.value;
    }
});

const onInput = () => {
    const value = editor.value?.innerHTML ?? '';
    internalValue.value = value;
    emit('update:modelValue', value);
};
</script>

<template>
    <div class="rounded-md border border-gray-300">
        <div class="flex flex-wrap gap-1 border-b border-gray-200 bg-gray-50 p-2">
            <button type="button" class="rounded px-2 py-1 text-sm text-gray-700 hover:bg-gray-200" :disabled="disabled" @click="exec('bold')">
                B
            </button>
            <button type="button" class="rounded px-2 py-1 text-sm text-gray-700 hover:bg-gray-200" :disabled="disabled" @click="exec('italic')">
                I
            </button>
            <button type="button" class="rounded px-2 py-1 text-sm text-gray-700 hover:bg-gray-200" :disabled="disabled" @click="exec('underline')">
                U
            </button>
            <button type="button" class="rounded px-2 py-1 text-sm text-gray-700 hover:bg-gray-200" :disabled="disabled" @click="exec('insertUnorderedList')">
                Список
            </button>
            <button type="button" class="rounded px-2 py-1 text-sm text-gray-700 hover:bg-gray-200" :disabled="disabled" @click="exec('formatBlock', 'h2')">
                H2
            </button>
            <button type="button" class="rounded px-2 py-1 text-sm text-gray-700 hover:bg-gray-200" :disabled="disabled" @click="exec('formatBlock', 'blockquote')">
                Quote
            </button>
            <button type="button" class="rounded px-2 py-1 text-sm text-gray-700 hover:bg-gray-200" :disabled="disabled" @click="setLink">
                Link
            </button>
            <button type="button" class="rounded px-2 py-1 text-sm text-gray-700 hover:bg-gray-200" :disabled="disabled" @click="exec('removeFormat')">
                Clear
            </button>
        </div>
        <div
            ref="editor"
            class="min-h-72 w-full p-4 text-sm leading-6 text-gray-900 focus:outline-none"
            contenteditable="true"
            :class="{ 'pointer-events-none bg-gray-100 text-gray-500': disabled }"
            @focus="focused = true"
            @blur="focused = false"
            @input="onInput"
        />
    </div>
</template>
