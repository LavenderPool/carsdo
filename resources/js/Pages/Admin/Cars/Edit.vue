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
