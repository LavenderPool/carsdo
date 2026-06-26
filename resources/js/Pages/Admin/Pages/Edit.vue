<script setup lang="ts">
import PageForm from '@/Components/Admin/PageForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    page: {
        id: number;
        title: string;
        slug: string;
        excerpt: string | null;
        body: string;
        body_json: string | null;
        is_published: boolean;
        published_at: string | null;
        sort_order: number;
        seo_title: string | null;
        seo_description: string | null;
        seo_h1: string | null;
        seo_og_image: string | null;
        seo_canonical_url: string | null;
        seo_robots: string | null;
    };
}>();

const form = useForm({
    title: props.page.title,
    slug: props.page.slug,
    excerpt: props.page.excerpt ?? '',
    body_json: props.page.body_json ?? '',
    is_published: props.page.is_published,
    published_at: props.page.published_at ?? '',
    sort_order: props.page.sort_order ?? 0,
    seo_title: props.page.seo_title ?? '',
    seo_description: props.page.seo_description ?? '',
    seo_h1: props.page.seo_h1 ?? '',
    seo_og_image: props.page.seo_og_image ?? '',
    seo_canonical_url: props.page.seo_canonical_url ?? '',
    seo_robots: props.page.seo_robots ?? '',
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            _method: 'put',
        }))
        .post(route('admin.pages.update', props.page.id));
};
</script>

<template>
    <Head :title="`Редактирование: ${page.title}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Редактирование страницы
                </h2>

                <Link
                    :href="route('admin.pages.index')"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    Назад к списку
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <PageForm
                        :form="form"
                        :legacy-body="page.body"
                        submit-label="Сохранить"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
