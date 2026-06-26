<script setup lang="ts">
import ArticleForm from '@/Components/Admin/ArticleForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    article: {
        id: number;
        title: string;
        slug: string;
        excerpt: string | null;
        body: string;
        body_json: string | null;
        cover_url: string | null;
        is_published: boolean;
        published_at: string | null;
        seo_title: string | null;
        seo_description: string | null;
        seo_h1: string | null;
        seo_og_image: string | null;
        seo_canonical_url: string | null;
        seo_robots: string | null;
    };
}>();

const form = useForm({
    title: props.article.title,
    slug: props.article.slug,
    excerpt: props.article.excerpt ?? '',
    body_json: props.article.body_json ?? '',
    cover: null as File | null,
    is_published: props.article.is_published,
    published_at: props.article.published_at ?? '',
    seo_title: props.article.seo_title ?? '',
    seo_description: props.article.seo_description ?? '',
    seo_h1: props.article.seo_h1 ?? '',
    seo_og_image: props.article.seo_og_image ?? '',
    seo_canonical_url: props.article.seo_canonical_url ?? '',
    seo_robots: props.article.seo_robots ?? '',
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            _method: 'put',
        }))
        .post(route('admin.articles.update', props.article.id), {
            forceFormData: true,
        });
};
</script>

<template>
    <Head :title="`Редактирование: ${article.title}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Редактирование статьи
                </h2>

                <Link
                    :href="route('admin.articles.index')"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    Назад к списку
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <ArticleForm
                        :form="form"
                        :current-cover-url="article.cover_url"
                        :legacy-body="article.body"
                        submit-label="Сохранить"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
