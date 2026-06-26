<script setup lang="ts">
import ArticleBodyEditor from '@/Components/Admin/ArticleBodyEditor.vue';
import SeoFieldsSection from '@/Components/Admin/SeoFieldsSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import type { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

type ArticleForm = ReturnType<typeof useForm<{
    title: string;
    slug: string;
    excerpt: string;
    body_json: string;
    cover: File | null;
    is_published: boolean;
    published_at: string;
    seo_title: string;
    seo_description: string;
    seo_h1: string;
    seo_og_image: string;
    seo_canonical_url: string;
    seo_robots: string;
}>>;

const props = defineProps<{
    form: ArticleForm;
    submitLabel: string;
    currentCoverUrl?: string | null;
    legacyBody?: string | null;
}>();

const emit = defineEmits<{
    submit: [];
}>();

const selectedCoverName = ref<string | null>(null);

const onCoverChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    props.form.cover = file;
    selectedCoverName.value = file?.name ?? null;
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="emit('submit')">
        <div>
            <InputLabel for="title" value="Заголовок" />
            <TextInput
                id="title"
                v-model="form.title"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
                autocomplete="off"
            />
            <InputError class="mt-2" :message="form.errors.title" />
        </div>

        <div>
            <InputLabel for="slug" value="Идентификатор" />
            <TextInput
                id="slug"
                v-model="form.slug"
                type="text"
                class="mt-1 block w-full"
                autocomplete="off"
            />
            <p class="mt-1 text-sm text-gray-500">
                Если поле пустое, идентификатор будет создан из заголовка.
            </p>
            <InputError class="mt-2" :message="form.errors.slug" />
        </div>

        <div>
            <InputLabel for="excerpt" value="Краткое описание" />
            <textarea
                id="excerpt"
                v-model="form.excerpt"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
            <InputError class="mt-2" :message="form.errors.excerpt" />
        </div>

        <div>
            <InputLabel for="body" value="Текст статьи" />
            <ArticleBodyEditor v-model="form.body_json" :legacy-html="legacyBody" />
            <InputError class="mt-2" :message="form.errors.body_json" />
        </div>

        <div>
            <InputLabel for="cover" value="Обложка" />
            <input
                id="cover"
                type="file"
                accept="image/*"
                class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700"
                @change="onCoverChange"
            >
            <p v-if="selectedCoverName" class="mt-1 text-sm text-gray-600">
                Выбран файл: {{ selectedCoverName }}
            </p>
            <InputError class="mt-2" :message="form.errors.cover" />
        </div>

        <div v-if="currentCoverUrl" class="space-y-2">
            <p class="text-sm font-medium text-gray-700">Текущая обложка</p>
            <img
                :src="currentCoverUrl"
                alt="Текущая обложка статьи"
                class="max-h-52 rounded-lg border border-gray-200 bg-white object-contain p-2"
            >
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <label for="is_published" class="inline-flex items-center gap-2">
                <input
                    id="is_published"
                    v-model="form.is_published"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                >
                <span class="text-sm text-gray-700">Опубликовано</span>
            </label>

            <div>
                <InputLabel for="published_at" value="Дата публикации" />
                <TextInput
                    id="published_at"
                    v-model="form.published_at"
                    type="datetime-local"
                    class="mt-1 block w-full"
                />
                <InputError class="mt-2" :message="form.errors.published_at" />
            </div>
        </div>

        <SeoFieldsSection
            :form="form"
            prefix="seo"
            title="SEO статьи"
            description="Если поле пустое, сайт использует текущую автогенерацию SEO."
            placeholders-hint="Доступные плейсхолдеры: {article}, {site_name}, {published_at}, {page}."
        />

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">
                {{ submitLabel }}
            </PrimaryButton>
            <span v-if="form.processing" class="text-sm text-gray-500">
                Сохранение...
            </span>
        </div>
    </form>
</template>
