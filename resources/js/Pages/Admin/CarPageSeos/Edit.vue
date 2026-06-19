<script setup lang="ts">
import SeoFieldsSection from '@/Components/Admin/SeoFieldsSection.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    page: {
        page_key: string;
        name: string;
        car_seo_prefix: string;
        placeholders_hint: string;
        title: string | null;
        description: string | null;
        h1: string | null;
        og_image: string | null;
        canonical_url: string | null;
        robots: string | null;
    };
    flash?: {
        success?: string | null;
    };
}>();

const form = useForm({
    title: props.page.title ?? '',
    description: props.page.description ?? '',
    h1: props.page.h1 ?? '',
    og_image: props.page.og_image ?? '',
    canonical_url: props.page.canonical_url ?? '',
    robots: props.page.robots ?? '',
});

const submit = () => {
    form.put(route('admin.car-page-seos.update', props.page.page_key), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`SEO: ${page.name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ page.name }}</h2>
                <Link :href="route('admin.car-page-seos.index')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Назад к списку</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="flash?.success"
                    class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                >
                    {{ flash.success }}
                </div>

                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="mb-6 rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                        <p>Ключ страницы: <span class="font-medium text-gray-900">{{ page.page_key }}</span></p>
                        <p class="mt-1">Префикс SEO у конкретной машины: <span class="font-medium text-gray-900">{{ page.car_seo_prefix }}</span></p>
                        <p class="mt-1">Приоритет на сайте: SEO машины -> SEO этой страницы -> дефолт из кода.</p>
                    </div>

                    <form class="space-y-6" @submit.prevent="submit">
                        <SeoFieldsSection
                            :form="form"
                            prefix=""
                            :title="`SEO шаблон: ${page.name}`"
                            description="Эти значения используются, если у конкретной машины не заполнены собственные SEO-поля для этой страницы."
                            :placeholders-hint="page.placeholders_hint"
                            :fallback-image="form.og_image || null"
                        />

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                Сохранить
                            </PrimaryButton>
                            <span v-if="form.processing" class="text-sm text-gray-500">
                                Сохранение...
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
