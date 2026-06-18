<script setup lang="ts">
import CarForm from '@/Components/Admin/CarForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    brands: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    brand_id: null as number | null,
    name: '',
    slug: '',
    year: '',
    cover_path: '',
    start_price: null as number | null,
    end_price: null as number | null,
    official_site: '',
    is_electric_car: false,
    is_soon: false,
    is_another_models: false,
    seo_title: '',
    seo_description: '',
    seo_h1: '',
    seo_og_image: '',
    seo_canonical_url: '',
    seo_robots: '',
    equipment_seo_title: '',
    equipment_seo_description: '',
    equipment_seo_h1: '',
    equipment_seo_og_image: '',
    equipment_seo_canonical_url: '',
    equipment_seo_robots: '',
    reviews_seo_title: '',
    reviews_seo_description: '',
    reviews_seo_h1: '',
    reviews_seo_og_image: '',
    reviews_seo_canonical_url: '',
    reviews_seo_robots: '',
    crash_test_seo_title: '',
    crash_test_seo_description: '',
    crash_test_seo_h1: '',
    crash_test_seo_og_image: '',
    crash_test_seo_canonical_url: '',
    crash_test_seo_robots: '',
    test_drive_seo_title: '',
    test_drive_seo_description: '',
    test_drive_seo_h1: '',
    test_drive_seo_og_image: '',
    test_drive_seo_canonical_url: '',
    test_drive_seo_robots: '',
});

const submit = () => {
    form.post(route('admin.cars.store'));
};
</script>

<template>
    <Head title="Новый автомобиль" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Новый автомобиль</h2>
                <Link :href="route('admin.cars.index')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Назад к списку</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <CarForm :form="form" :brands="brands" submit-label="Создать" @submit="submit" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
