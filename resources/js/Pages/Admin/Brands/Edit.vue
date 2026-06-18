<script setup lang="ts">
import BrandForm from '@/Components/Admin/BrandForm.vue';
import { update } from '@/actions/App/Http/Controllers/Admin/BrandController';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    brand: {
        id: number;
        name: string;
        slug: string;
        leave_from_russian: boolean;
        seo_title: string | null;
        seo_description: string | null;
        seo_h1: string | null;
        seo_og_image: string | null;
        seo_canonical_url: string | null;
        seo_robots: string | null;
    };
}>();

const form = useForm({
    name: props.brand.name,
    slug: props.brand.slug,
    leave_from_russian: props.brand.leave_from_russian,
    seo_title: props.brand.seo_title ?? '',
    seo_description: props.brand.seo_description ?? '',
    seo_h1: props.brand.seo_h1 ?? '',
    seo_og_image: props.brand.seo_og_image ?? '',
    seo_canonical_url: props.brand.seo_canonical_url ?? '',
    seo_robots: props.brand.seo_robots ?? '',
});

const submit = () => {
    form.submit(update(props.brand.id));
};
</script>

<template>
    <Head :title="`Редактирование: ${brand.name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Редактирование бренда
                </h2>

                <Link
                    :href="route('admin.brands.index')"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    Назад к списку
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <BrandForm
                        :form="form"
                        submit-label="Сохранить"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
