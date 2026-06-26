<script setup lang="ts">
import CarCatalogForm from '@/Components/Admin/CarCatalogForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import type { PageProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface OptionBrand {
    id: number;
    name: string;
}

interface OptionCar {
    id: number;
    label: string;
}

const props = defineProps<{
    catalog: {
        id: number;
        name: string;
        slug: string;
        description: string | null;
        is_published: boolean;
        sort_order: number;
        price_min: number | null;
        price_max: number | null;
        year_from: number | null;
        year_to: number | null;
        is_electric_car: boolean | null;
        brand_ids: number[];
        drive_types: string[];
        engine_types: string[];
        manual_cars: Array<{ car_id: number | null; sort_order: number }>;
        seo_title: string | null;
        seo_description: string | null;
        seo_h1: string | null;
        seo_og_image: string | null;
        seo_canonical_url: string | null;
        seo_robots: string | null;
    };
    options: {
        brands: OptionBrand[];
        cars: OptionCar[];
        drive_types: string[];
        engine_types: string[];
    };
}>();

const form = useForm({
    name: props.catalog.name,
    slug: props.catalog.slug,
    description: props.catalog.description ?? '',
    is_published: props.catalog.is_published,
    sort_order: props.catalog.sort_order,
    price_min: props.catalog.price_min,
    price_max: props.catalog.price_max,
    year_from: props.catalog.year_from,
    year_to: props.catalog.year_to,
    is_electric_car: props.catalog.is_electric_car,
    brand_ids: props.catalog.brand_ids ?? [],
    drive_types: props.catalog.drive_types ?? [],
    engine_types: props.catalog.engine_types ?? [],
    manual_cars: props.catalog.manual_cars ?? [],
    seo_title: props.catalog.seo_title ?? '',
    seo_description: props.catalog.seo_description ?? '',
    seo_h1: props.catalog.seo_h1 ?? '',
    seo_og_image: props.catalog.seo_og_image ?? '',
    seo_canonical_url: props.catalog.seo_canonical_url ?? '',
    seo_robots: props.catalog.seo_robots ?? '',
});

const page = usePage<PageProps<{ flash?: { success?: string } }>>();
const flashSuccess = computed(() => page.props.flash?.success);

const submit = () => {
    form.put(route('admin.car-catalogs.update', props.catalog.id));
};
</script>

<template>
    <Head :title="`Редактирование: ${catalog.name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Редактирование LSI каталога</h2>
                <div class="flex items-center gap-4">
                    <a
                        :href="route('catalog.show', catalog.slug)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-900"
                    >
                        Открыть на сайте
                    </a>
                    <Link :href="route('admin.car-catalogs.index')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        Назад к списку
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="flashSuccess"
                    class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                >
                    {{ flashSuccess }}
                </div>

                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <CarCatalogForm
                        :form="form"
                        submit-label="Сохранить"
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
