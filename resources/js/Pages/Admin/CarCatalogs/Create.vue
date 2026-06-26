<script setup lang="ts">
import CarCatalogForm from '@/Components/Admin/CarCatalogForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface OptionBrand {
    id: number;
    name: string;
}

interface OptionCar {
    id: number;
    label: string;
}

const props = defineProps<{
    options: {
        brands: OptionBrand[];
        cars: OptionCar[];
        drive_types: string[];
        engine_types: string[];
    };
}>();

const form = useForm({
    name: '',
    slug: '',
    description: '',
    is_published: false,
    sort_order: 0,
    price_min: null as number | null,
    price_max: null as number | null,
    year_from: null as number | null,
    year_to: null as number | null,
    is_electric_car: null as boolean | null,
    brand_ids: [] as number[],
    drive_types: [] as string[],
    engine_types: [] as string[],
    manual_cars: [] as Array<{ car_id: number | null; sort_order: number }>,
    seo_title: '',
    seo_description: '',
    seo_h1: '',
    seo_og_image: '',
    seo_canonical_url: '',
    seo_robots: '',
});

const submit = () => {
    form.post(route('admin.car-catalogs.store'));
};
</script>

<template>
    <Head title="Новый LSI каталог" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Новый LSI каталог</h2>
                <Link :href="route('admin.car-catalogs.index')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Назад к списку
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <CarCatalogForm
                        :form="form"
                        submit-label="Создать"
                        :brands="options.brands"
                        :cars="options.cars"
                        :drive-types="options.drive_types"
                        :engine-types="options.engine_types"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
