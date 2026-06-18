<script setup lang="ts">
import BrandForm from '@/Components/Admin/BrandForm.vue';
import { store } from '@/actions/App/Http/Controllers/Admin/BrandController';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    slug: '',
    leave_from_russian: false,
    seo_title: '',
    seo_description: '',
    seo_h1: '',
    seo_og_image: '',
    seo_canonical_url: '',
    seo_robots: '',
});

const submit = () => {
    form.submit(store());
};
</script>

<template>
    <Head title="Новый бренд" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Новый бренд
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
                        submit-label="Создать"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
