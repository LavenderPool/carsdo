<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface CarItem {
    id: number;
    name: string;
    slug: string;
    brand: string | null;
    year: string | null;
    is_soon: boolean;
    is_electric_car: boolean;
    start_price: number | null;
    end_price: number | null;
    views_count: number;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    cars: {
        data: CarItem[];
        links: PaginationLink[];
    };
    brands: Array<{ id: number; name: string }>;
    filters: {
        search: string;
        brand_id?: number | null;
        is_soon?: string | null;
        is_electric_car?: string | null;
    };
    flash?: { success?: string | null };
}>();

const search = ref(props.filters.search || '');
const brandId = ref<number | null>(props.filters.brand_id ?? null);
const isSoon = ref<string>(props.filters.is_soon ?? '');
const isElectric = ref<string>(props.filters.is_electric_car ?? '');

const hasCars = computed(() => props.cars.data.length > 0);

const normalizedPaginationLinks = computed(() =>
    props.cars.links.map((link) => ({
        ...link,
        label: link.label.replace(/&laquo;\s*Previous/i, 'Назад').replace(/Next\s*&raquo;/i, 'Вперед'),
    })),
);

const applyFilters = () => {
    router.get(route('admin.cars.index'), {
        search: search.value || undefined,
        brand_id: brandId.value || undefined,
        is_soon: isSoon.value || undefined,
        is_electric_car: isElectric.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const remove = (id: number, name: string) => {
    if (!window.confirm(`Удалить автомобиль "${name}"?`)) {
        return;
    }

    router.delete(route('admin.cars.destroy', id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Автомобили" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Автомобили</h2>
                <Link :href="route('admin.cars.create')" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                    Добавить автомобиль
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div v-if="flash?.success" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ flash.success }}
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <form class="grid gap-3 md:grid-cols-4" @submit.prevent="applyFilters">
                            <input v-model="search" type="text" placeholder="Поиск по названию или slug" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <select v-model.number="brandId" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option :value="null">Все бренды</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                            </select>
                            <select v-model="isSoon" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Скоро в продаже: все</option>
                                <option value="1">Да</option>
                                <option value="0">Нет</option>
                            </select>
                            <select v-model="isElectric" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Электромобиль: все</option>
                                <option value="1">Да</option>
                                <option value="0">Нет</option>
                            </select>
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700 md:col-span-4 md:w-fit">
                                Применить
                            </button>
                        </form>
                    </div>

                    <div v-if="hasCars" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Название</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Slug</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Бренд</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Год</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Просмотры</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Флаги</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Действия</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="car in cars.data" :key="car.id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ car.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ car.slug }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ car.brand ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ car.year ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ car.views_count }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        <span>{{ car.is_soon ? 'Скоро' : '' }}</span>
                                        <span>{{ car.is_electric_car ? (car.is_soon ? ', Электро' : 'Электро') : '' }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex justify-end gap-4">
                                            <Link :href="route('admin.cars.edit', car.id)" class="text-indigo-600 hover:text-indigo-900">Изменить</Link>
                                            <button type="button" class="text-red-600 hover:text-red-900" @click="remove(car.id, car.name)">Удалить</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="p-6 text-sm text-gray-500">Автомобили пока не созданы.</div>
                </div>

                <div v-if="cars.links.length > 3" class="flex flex-wrap items-center gap-2">
                    <template v-for="(link, index) in normalizedPaginationLinks" :key="`${index}-${link.label}`">
                        <Link v-if="link.url" :href="link.url" class="rounded-md px-3 py-2 text-sm" :class="link.active ? 'bg-gray-800 text-white' : 'bg-white text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50'" v-html="link.label" />
                        <span v-else class="rounded-md bg-gray-100 px-3 py-2 text-sm text-gray-400" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
