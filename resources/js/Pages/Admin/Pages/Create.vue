<script setup lang="ts">
import PageForm from '@/Components/Admin/PageForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    slug: '',
    excerpt: '',
    body_json: '',
    is_published: false,
    published_at: '',
    sort_order: 0,
    seo_title: '',
    seo_description: '',
    seo_h1: '',
    seo_og_image: '',
    seo_canonical_url: '',
    seo_robots: '',
});

const submit = () => {
    form.post(route('admin.pages.store'));
};
</script>

<template>
    <Head title="Новая страница" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Новая страница
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
                        submit-label="Создать"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
