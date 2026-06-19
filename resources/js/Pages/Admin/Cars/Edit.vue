<script setup lang="ts">
import CarForm from '@/Components/Admin/CarForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    car: {
        id: number;
        brand_id: number;
        name: string;
        slug: string;
        year: string | null;
        cover_path: string | null;
        start_price: number | null;
        end_price: number | null;
        official_site: string | null;
        is_electric_car: boolean;
        is_soon: boolean;
        is_another_models: boolean;
        seo_title: string | null;
        seo_description: string | null;
        seo_h1: string | null;
        seo_og_image: string | null;
        seo_canonical_url: string | null;
        seo_robots: string | null;
        equipment_seo_title: string | null;
        equipment_seo_description: string | null;
        equipment_seo_h1: string | null;
        equipment_seo_og_image: string | null;
        equipment_seo_canonical_url: string | null;
        equipment_seo_robots: string | null;
        dealer_seo_title: string | null;
        dealer_seo_description: string | null;
        dealer_seo_h1: string | null;
        dealer_seo_og_image: string | null;
        dealer_seo_canonical_url: string | null;
        dealer_seo_robots: string | null;
        reviews_seo_title: string | null;
        reviews_seo_description: string | null;
        reviews_seo_h1: string | null;
        reviews_seo_og_image: string | null;
        reviews_seo_canonical_url: string | null;
        reviews_seo_robots: string | null;
        crash_test_seo_title: string | null;
        crash_test_seo_description: string | null;
        crash_test_seo_h1: string | null;
        crash_test_seo_og_image: string | null;
        crash_test_seo_canonical_url: string | null;
        crash_test_seo_robots: string | null;
        test_drive_seo_title: string | null;
        test_drive_seo_description: string | null;
        test_drive_seo_h1: string | null;
        test_drive_seo_og_image: string | null;
        test_drive_seo_canonical_url: string | null;
        test_drive_seo_robots: string | null;
        photo_seo_title: string | null;
        photo_seo_description: string | null;
        photo_seo_h1: string | null;
        photo_seo_og_image: string | null;
        photo_seo_canonical_url: string | null;
        photo_seo_robots: string | null;
    };
    brands: Array<{ id: number; name: string }>;
    nestedLinks: Record<string, string>;
}>();

const form = useForm({
    brand_id: props.car.brand_id,
    name: props.car.name,
    slug: props.car.slug,
    year: props.car.year ?? '',
    cover_path: props.car.cover_path ?? '',
    start_price: props.car.start_price,
    end_price: props.car.end_price,
    official_site: props.car.official_site ?? '',
    is_electric_car: props.car.is_electric_car,
    is_soon: props.car.is_soon,
    is_another_models: props.car.is_another_models,
    seo_title: props.car.seo_title ?? '',
    seo_description: props.car.seo_description ?? '',
    seo_h1: props.car.seo_h1 ?? '',
    seo_og_image: props.car.seo_og_image ?? '',
    seo_canonical_url: props.car.seo_canonical_url ?? '',
    seo_robots: props.car.seo_robots ?? '',
    equipment_seo_title: props.car.equipment_seo_title ?? '',
    equipment_seo_description: props.car.equipment_seo_description ?? '',
    equipment_seo_h1: props.car.equipment_seo_h1 ?? '',
    equipment_seo_og_image: props.car.equipment_seo_og_image ?? '',
    equipment_seo_canonical_url: props.car.equipment_seo_canonical_url ?? '',
    equipment_seo_robots: props.car.equipment_seo_robots ?? '',
    dealer_seo_title: props.car.dealer_seo_title ?? '',
    dealer_seo_description: props.car.dealer_seo_description ?? '',
    dealer_seo_h1: props.car.dealer_seo_h1 ?? '',
    dealer_seo_og_image: props.car.dealer_seo_og_image ?? '',
    dealer_seo_canonical_url: props.car.dealer_seo_canonical_url ?? '',
    dealer_seo_robots: props.car.dealer_seo_robots ?? '',
    reviews_seo_title: props.car.reviews_seo_title ?? '',
    reviews_seo_description: props.car.reviews_seo_description ?? '',
    reviews_seo_h1: props.car.reviews_seo_h1 ?? '',
    reviews_seo_og_image: props.car.reviews_seo_og_image ?? '',
    reviews_seo_canonical_url: props.car.reviews_seo_canonical_url ?? '',
    reviews_seo_robots: props.car.reviews_seo_robots ?? '',
    crash_test_seo_title: props.car.crash_test_seo_title ?? '',
    crash_test_seo_description: props.car.crash_test_seo_description ?? '',
    crash_test_seo_h1: props.car.crash_test_seo_h1 ?? '',
    crash_test_seo_og_image: props.car.crash_test_seo_og_image ?? '',
    crash_test_seo_canonical_url: props.car.crash_test_seo_canonical_url ?? '',
    crash_test_seo_robots: props.car.crash_test_seo_robots ?? '',
    test_drive_seo_title: props.car.test_drive_seo_title ?? '',
    test_drive_seo_description: props.car.test_drive_seo_description ?? '',
    test_drive_seo_h1: props.car.test_drive_seo_h1 ?? '',
    test_drive_seo_og_image: props.car.test_drive_seo_og_image ?? '',
    test_drive_seo_canonical_url: props.car.test_drive_seo_canonical_url ?? '',
    test_drive_seo_robots: props.car.test_drive_seo_robots ?? '',
    photo_seo_title: props.car.photo_seo_title ?? '',
    photo_seo_description: props.car.photo_seo_description ?? '',
    photo_seo_h1: props.car.photo_seo_h1 ?? '',
    photo_seo_og_image: props.car.photo_seo_og_image ?? '',
    photo_seo_canonical_url: props.car.photo_seo_canonical_url ?? '',
    photo_seo_robots: props.car.photo_seo_robots ?? '',
});

const submit = () => {
    form.put(route('admin.cars.update', props.car.id));
};

const nestedItems: Array<{ key: keyof typeof props.nestedLinks; label: string }> = [
    { key: 'crash_test', label: 'Краш-тесты' },
    { key: 'test_drives', label: 'Тест-драйвы' },
    { key: 'reviews', label: 'Отзывы' },
    { key: 'configuration_groups', label: 'Группы комплектаций' },
    { key: 'configurations', label: 'Комплектации' },
    { key: 'equipment_categories', label: 'Категории оснащения' },
    { key: 'equipment', label: 'Опции оснащения' },
    { key: 'photo_groups', label: 'Группы фото' },
    { key: 'photos', label: 'Фото' },
];
</script>

<template>
    <Head :title="`Редактирование: ${car.name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Редактирование автомобиля</h2>
                <Link :href="route('admin.cars.index')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Назад к списку</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <CarForm :form="form" :brands="brands" submit-label="Сохранить" @submit="submit" />
                </div>

                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Подчиненные сущности</h3>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <Link
                            v-for="item in nestedItems"
                            :key="item.key"
                            :href="nestedLinks[item.key]"
                            class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-gray-900"
                        >
                            {{ item.label }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
