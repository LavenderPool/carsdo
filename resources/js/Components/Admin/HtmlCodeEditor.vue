<script setup lang="ts">
import { html } from '@codemirror/lang-html';
import { Compartment, EditorState } from '@codemirror/state';
import { EditorView } from '@codemirror/view';
import { basicSetup } from 'codemirror';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = withDefaults(defineProps<{
    id?: string;
    modelValue: string | null;
    disabled?: boolean;
}>(), {
    disabled: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editorElement = ref<HTMLDivElement | null>(null);
const editableCompartment = new Compartment();

let editorView: EditorView | null = null;

const editorTheme = EditorView.theme({
    '&': {
        minHeight: '18rem',
        borderRadius: '0.375rem',
        border: '1px solid rgb(209 213 219)',
        backgroundColor: 'rgb(255 255 255)',
        fontSize: '0.875rem',
    },
    '&.cm-focused': {
        outline: 'none',
        borderColor: 'rgb(99 102 241)',
        boxShadow: '0 0 0 1px rgb(99 102 241)',
    },
    '&.cm-editor.cm-disabled': {
        backgroundColor: 'rgb(243 244 246)',
    },
    '.cm-scroller': {
        overflow: 'auto',
        fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace',
        lineHeight: '1.6',
    },
    '.cm-content': {
        minHeight: '18rem',
        padding: '0.75rem 1rem',
    },
    '.cm-gutters': {
        borderRight: '1px solid rgb(229 231 235)',
        backgroundColor: 'rgb(249 250 251)',
        color: 'rgb(107 114 128)',
    },
});

const createEditorState = (value: string) => EditorState.create({
    doc: value,
    extensions: [
        basicSetup,
        html(),
        EditorView.lineWrapping,
        editorTheme,
        editableCompartment.of(EditorView.editable.of(!props.disabled)),
        EditorView.updateListener.of((update) => {
            if (!update.docChanged) {
                return;
            }

            emit('update:modelValue', update.state.doc.toString());
        }),
    ],
});

onMounted(() => {
    if (!editorElement.value) {
        return;
    }

    editorView = new EditorView({
        state: createEditorState(props.modelValue ?? ''),
        parent: editorElement.value,
    });

    editorView.dom.classList.toggle('cm-disabled', props.disabled);
});

watch(
    () => props.modelValue,
    (value) => {
        if (!editorView) {
            return;
        }

        const nextValue = value ?? '';
        const currentValue = editorView.state.doc.toString();

        if (nextValue === currentValue) {
            return;
        }

        editorView.dispatch({
            changes: {
                from: 0,
                to: currentValue.length,
                insert: nextValue,
            },
        });
    },
);

watch(
    () => props.disabled,
    (disabled) => {
        if (!editorView) {
            return;
        }

        editorView.dispatch({
            effects: editableCompartment.reconfigure(EditorView.editable.of(!disabled)),
        });
        editorView.dom.classList.toggle('cm-disabled', disabled);
    },
);

onBeforeUnmount(() => {
    editorView?.destroy();
    editorView = null;
});
</script>

<template>
    <div :id="id" ref="editorElement" />
</template>
