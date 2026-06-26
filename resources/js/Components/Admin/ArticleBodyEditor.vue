<script setup lang="ts">
import DragHandle from '@tiptap/extension-drag-handle-vue-3';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import TaskItem from '@tiptap/extension-task-item';
import TaskList from '@tiptap/extension-task-list';
import Underline from '@tiptap/extension-underline';
import type { Editor as CoreEditor } from '@tiptap/core';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { marked } from 'marked';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = withDefaults(defineProps<{
    modelValue: string | null;
    disabled?: boolean;
    legacyHtml?: string | null;
}>(), {
    disabled: false,
    legacyHtml: null,
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const lastSerializedDocument = ref('');

const defaultDocument = {
    type: 'doc',
    content: [
        {
            type: 'paragraph',
        },
    ],
};

const parseInitialContent = () => {
    if (typeof props.modelValue === 'string' && props.modelValue.trim() !== '') {
        try {
            return JSON.parse(props.modelValue);
        } catch {
            return props.legacyHtml?.trim() ? props.legacyHtml : defaultDocument;
        }
    }

    return props.legacyHtml?.trim() ? props.legacyHtml : defaultDocument;
};

const serializeEditorDocument = (editorInstance: CoreEditor) => JSON.stringify(editorInstance.getJSON());

const emitDocument = (editorInstance: CoreEditor) => {
    const serialized = serializeEditorDocument(editorInstance);

    lastSerializedDocument.value = serialized;
    emit('update:modelValue', serialized);
};

const editor = useEditor({
    content: parseInitialContent(),
    editable: !props.disabled,
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [2, 3],
            },
            codeBlock: false,
        }),
        Underline,
        Link.configure({
            openOnClick: false,
            autolink: true,
            protocols: ['http', 'https', 'mailto', 'tel'],
            HTMLAttributes: {
                rel: 'noopener noreferrer',
            },
        }),
        TaskList,
        TaskItem.configure({
            nested: true,
        }),
        Placeholder.configure({
            placeholder: 'Начните писать статью. Поддерживаются вставка Markdown и обычного текста.',
        }),
    ],
    editorProps: {
        attributes: {
            class: 'article-editor__content',
        },
        handlePaste(view, event) {
            const clipboardData = event.clipboardData;

            if (!clipboardData) {
                return false;
            }

            const html = clipboardData.getData('text/html').trim();
            const text = clipboardData.getData('text/plain');

            if (html !== '' || text.trim() === '') {
                return false;
            }

            event.preventDefault();

            const rendered = marked.parse(text, {
                async: false,
                gfm: true,
                breaks: true,
            });

            view.dispatch(view.state.tr);
            editor.value?.chain().focus().insertContent(rendered).run();

            return true;
        },
    },
    onCreate: ({ editor: editorInstance }) => {
        emitDocument(editorInstance);
    },
    onUpdate: ({ editor: editorInstance }) => {
        emitDocument(editorInstance);
    },
});

const withEditor = (callback: (editorInstance: CoreEditor) => void) => {
    if (!editor.value || props.disabled) {
        return;
    }

    callback(editor.value);
};

const setLink = () => {
    withEditor((editorInstance) => {
        const previousHref = String(editorInstance.getAttributes('link').href ?? '');
        const nextHref = window.prompt('Введите URL ссылки', previousHref);

        if (nextHref === null) {
            return;
        }

        const normalizedHref = nextHref.trim();

        if (normalizedHref === '') {
            editorInstance.chain().focus().extendMarkRange('link').unsetLink().run();

            return;
        }

        editorInstance
            .chain()
            .focus()
            .extendMarkRange('link')
            .setLink({
                href: normalizedHref,
                target: normalizedHref.startsWith('http') ? '_blank' : null,
            })
            .run();
    });
};

const editorIsActive = (name: string, attributes: Record<string, unknown> = {}) =>
    editor.value?.isActive(name, attributes) ?? false;

const canUndo = computed(() => editor.value?.can().chain().focus().undo().run() ?? false);
const canRedo = computed(() => editor.value?.can().chain().focus().redo().run() ?? false);

watch(
    () => props.disabled,
    (disabled) => {
        editor.value?.setEditable(!disabled);
    },
);

watch(
    () => props.modelValue,
    (value) => {
        if (!editor.value || typeof value !== 'string' || value.trim() === '' || value === lastSerializedDocument.value) {
            return;
        }

        try {
            editor.value.commands.setContent(JSON.parse(value), { emitUpdate: false });
            lastSerializedDocument.value = value;
        } catch {
            if (props.legacyHtml?.trim()) {
                editor.value.commands.setContent(props.legacyHtml, { emitUpdate: false });
                lastSerializedDocument.value = serializeEditorDocument(editor.value);
            }
        }
    },
);

onBeforeUnmount(() => {
    editor.value?.destroy();
});
</script>

<template>
    <div class="article-editor" :class="{ 'article-editor--disabled': disabled }">
        <div class="article-editor__toolbar">
            <button type="button" class="article-editor__button" :class="{ 'is-active': editorIsActive('bold') }" :disabled="disabled" @click="withEditor((instance) => instance.chain().focus().toggleBold().run())">
                B
            </button>
            <button type="button" class="article-editor__button" :class="{ 'is-active': editorIsActive('italic') }" :disabled="disabled" @click="withEditor((instance) => instance.chain().focus().toggleItalic().run())">
                I
            </button>
            <button type="button" class="article-editor__button" :class="{ 'is-active': editorIsActive('underline') }" :disabled="disabled" @click="withEditor((instance) => instance.chain().focus().toggleUnderline().run())">
                U
            </button>
            <button type="button" class="article-editor__button" :class="{ 'is-active': editorIsActive('heading', { level: 2 }) }" :disabled="disabled" @click="withEditor((instance) => instance.chain().focus().toggleHeading({ level: 2 }).run())">
                H2
            </button>
            <button type="button" class="article-editor__button" :class="{ 'is-active': editorIsActive('heading', { level: 3 }) }" :disabled="disabled" @click="withEditor((instance) => instance.chain().focus().toggleHeading({ level: 3 }).run())">
                H3
            </button>
            <button type="button" class="article-editor__button" :class="{ 'is-active': editorIsActive('bulletList') }" :disabled="disabled" @click="withEditor((instance) => instance.chain().focus().toggleBulletList().run())">
                Список
            </button>
            <button type="button" class="article-editor__button" :class="{ 'is-active': editorIsActive('orderedList') }" :disabled="disabled" @click="withEditor((instance) => instance.chain().focus().toggleOrderedList().run())">
                Нумерация
            </button>
            <button type="button" class="article-editor__button" :class="{ 'is-active': editorIsActive('taskList') }" :disabled="disabled" @click="withEditor((instance) => instance.chain().focus().toggleTaskList().run())">
                Чеклист
            </button>
            <button type="button" class="article-editor__button" :class="{ 'is-active': editorIsActive('blockquote') }" :disabled="disabled" @click="withEditor((instance) => instance.chain().focus().toggleBlockquote().run())">
                Цитата
            </button>
            <button type="button" class="article-editor__button" :class="{ 'is-active': editorIsActive('link') }" :disabled="disabled" @click="setLink">
                Ссылка
            </button>
            <button type="button" class="article-editor__button" :disabled="disabled || !canUndo" @click="withEditor((instance) => instance.chain().focus().undo().run())">
                Назад
            </button>
            <button type="button" class="article-editor__button" :disabled="disabled || !canRedo" @click="withEditor((instance) => instance.chain().focus().redo().run())">
                Вперёд
            </button>
            <button type="button" class="article-editor__button" :disabled="disabled" @click="withEditor((instance) => instance.chain().focus().clearNodes().unsetAllMarks().run())">
                Очистить
            </button>
        </div>

        <div class="article-editor__surface">
            <EditorContent :editor="editor" />

            <DragHandle
                v-if="editor"
                :editor="editor"
                :nested="true"
                class="article-editor__drag-handle"
            >
                <button type="button" class="article-editor__drag-button" tabindex="-1" aria-label="Перетащить блок">
                    ⋮⋮
                </button>
            </DragHandle>
        </div>

        <div class="article-editor__footer">
            <span>Поддерживаются вставка Markdown и обычного текста</span>
            <span>JSON хранится автоматически</span>
        </div>
    </div>
</template>

<style scoped>
.article-editor {
    border: 1px solid rgb(209 213 219);
    border-radius: 0.75rem;
    background: rgb(255 255 255);
    overflow: hidden;
}

.article-editor--disabled {
    opacity: 0.72;
}

.article-editor__toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.875rem;
    border-bottom: 1px solid rgb(229 231 235);
    background: linear-gradient(180deg, rgb(249 250 251) 0%, rgb(255 255 255) 100%);
}

.article-editor__button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 2.25rem;
    padding: 0 0.875rem;
    border: 1px solid rgb(229 231 235);
    border-radius: 0.625rem;
    background: rgb(255 255 255);
    color: rgb(55 65 81);
    font-size: 0.875rem;
    font-weight: 600;
    transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
}

.article-editor__button:hover:not(:disabled) {
    border-color: rgb(209 213 219);
    background: rgb(249 250 251);
}

.article-editor__button:disabled {
    cursor: not-allowed;
    opacity: 0.45;
}

.article-editor__button.is-active {
    border-color: rgb(79 70 229);
    background: rgb(238 242 255);
    color: rgb(67 56 202);
    box-shadow: 0 0 0 1px rgb(199 210 254);
}

.article-editor__surface {
    position: relative;
    padding: 1rem 1rem 1.25rem;
    background: rgb(255 255 255);
}

.article-editor__drag-handle {
    z-index: 20;
}

.article-editor__drag-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.9rem;
    height: 1.9rem;
    border: 1px solid rgb(229 231 235);
    border-radius: 999px;
    background: rgb(255 255 255);
    color: rgb(107 114 128);
    font-size: 1rem;
    line-height: 1;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
}

.article-editor__footer {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.75rem 1rem 0.875rem;
    border-top: 1px solid rgb(229 231 235);
    background: rgb(249 250 251);
    color: rgb(107 114 128);
    font-size: 0.75rem;
    line-height: 1.5;
}

.article-editor :deep(.article-editor__content) {
    min-height: 24rem;
    color: rgb(17 24 39);
    font-size: 0.95rem;
    line-height: 1.75;
    outline: none;
}

.article-editor :deep(.article-editor__content > * + *) {
    margin-top: 1rem;
}

.article-editor :deep(.article-editor__content p.is-editor-empty:first-child::before) {
    content: attr(data-placeholder);
    color: rgb(156 163 175);
    float: left;
    height: 0;
    pointer-events: none;
}

.article-editor :deep(.article-editor__content h2),
.article-editor :deep(.article-editor__content h3) {
    color: rgb(17 24 39);
    font-weight: 700;
    line-height: 1.25;
}

.article-editor :deep(.article-editor__content h2) {
    font-size: 1.5rem;
}

.article-editor :deep(.article-editor__content h3) {
    font-size: 1.25rem;
}

.article-editor :deep(.article-editor__content p) {
    color: rgb(31 41 55);
}

.article-editor :deep(.article-editor__content ul),
.article-editor :deep(.article-editor__content ol) {
    padding-left: 1.5rem !important;
    list-style-position: outside !important;
}

.article-editor :deep(.article-editor__content ul:not([data-type='taskList'])) {
    list-style-type: disc !important;
}

.article-editor :deep(.article-editor__content ol) {
    list-style-type: decimal !important;
}

.article-editor :deep(.article-editor__content ul:not([data-type='taskList']) > li),
.article-editor :deep(.article-editor__content ol > li) {
    display: list-item !important;
}

.article-editor :deep(.article-editor__content blockquote) {
    margin: 0;
    padding: 0.875rem 1rem;
    border-radius: 0.875rem;
    border: 1px solid rgb(229 231 235);
    background: rgb(249 250 251);
    color: rgb(55 65 81);
}

.article-editor :deep(.article-editor__content a) {
    color: rgb(79 70 229);
    text-decoration: underline;
    text-decoration-thickness: 1px;
    text-underline-offset: 0.2em;
}

.article-editor :deep(.article-editor__content code) {
    padding: 0.125rem 0.375rem;
    border-radius: 0.375rem;
    background: rgb(243 244 246);
    font-size: 0.875em;
}

.article-editor :deep(.article-editor__content ul[data-type='taskList']) {
    list-style: none !important;
    padding-left: 0.25rem !important;
}

.article-editor :deep(.article-editor__content ul[data-type='taskList'] li) {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.article-editor :deep(.article-editor__content ul[data-type='taskList'] li > label) {
    margin-top: 0.25rem;
}

.article-editor :deep(.article-editor__content ul[data-type='taskList'] li > div) {
    flex: 1 1 auto;
}

@media (max-width: 768px) {
    .article-editor__toolbar {
        gap: 0.375rem;
        padding: 0.75rem;
    }

    .article-editor__button {
        min-height: 2.125rem;
        padding: 0 0.75rem;
        font-size: 0.8125rem;
    }

    .article-editor__surface {
        padding: 0.875rem 0.75rem 1rem;
    }

    .article-editor :deep(.article-editor__content) {
        min-height: 20rem;
    }

    .article-editor__footer {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
