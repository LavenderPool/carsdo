<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    brandsCount: number;
    carsCount: number;
    topBrands: Array<{
        id: number;
        name: string;
        slug: string;
        views_count: number;
    }>;
    topCars: Array<{
        id: number;
        name: string;
        slug: string;
        brand: string | null;
        brand_slug: string | null;
        views_count: number;
    }>;
}>();
</script>

<template>
    <Head title="Админка" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Админка
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="space-y-4 p-6 text-gray-900">
                        <p>Вы вошли в административную часть проекта.</p>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 shadow-sm">
                                <div>
                                    <p class="text-sm text-slate-500">Количество брендов</p>
                                    <p class="text-3xl font-bold tracking-tight text-slate-900">
                                        {{ brandsCount }}
                                    </p>
                                </div>
                                <Link :href="route('admin.brands.index')" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                                    К брендам
                                </Link>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 shadow-sm">
                                <div>
                                    <p class="text-sm text-slate-500">Количество автомобилей</p>
                                    <p class="text-3xl font-bold tracking-tight text-slate-900">
                                        {{ carsCount }}
                                    </p>
                                </div>
                                <Link :href="route('admin.cars.index')" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                                    К автомобилям
                                </Link>
                            </div>
                        </div>

                        <div class="grid gap-4 xl:grid-cols-2">
                            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="mb-3 flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-slate-900">Топ брендов по просмотрам</h3>
                                    <Link :href="route('admin.brands.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                        Все бренды
                                    </Link>
                                </div>

                                <div v-if="topBrands.length > 0" class="space-y-2">
                                    <div
                                        v-for="(brand, index) in topBrands"
                                        :key="brand.id"
                                        class="flex items-center justify-between rounded-md border border-slate-100 px-3 py-2 text-sm"
                                    >
                                        <div class="truncate text-slate-700">
                                            <span class="mr-2 font-medium text-slate-400">#{{ index + 1 }}</span>
                                            {{ brand.name }}
                                        </div>
                                        <div class="font-semibold text-slate-900">{{ brand.views_count }}</div>
                                    </div>
                                </div>
                                <p v-else class="text-sm text-slate-500">Данных о просмотрах пока нет.</p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="mb-3 flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-slate-900">Топ автомобилей по просмотрам</h3>
                                    <Link :href="route('admin.cars.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                        Все автомобили
                                    </Link>
                                </div>

                                <div v-if="topCars.length > 0" class="space-y-2">
                                    <div
                                        v-for="(car, index) in topCars"
                                        :key="car.id"
                                        class="flex items-center justify-between rounded-md border border-slate-100 px-3 py-2 text-sm"
                                    >
                                        <div class="truncate text-slate-700">
                                            <span class="mr-2 font-medium text-slate-400">#{{ index + 1 }}</span>
                                            {{ car.brand ? `${car.brand} ${car.name}` : car.name }}
                                        </div>
                                        <div class="font-semibold text-slate-900">{{ car.views_count }}</div>
                                    </div>
                                </div>
                                <p v-else class="text-sm text-slate-500">Данных о просмотрах пока нет.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
