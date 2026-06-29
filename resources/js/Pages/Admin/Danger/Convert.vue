<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface ConfigurationPreview {
    id: number;
    price: number | null;
    current_currency: string | null;
}

interface CarPreview {
    car_id: number;
    car_name: string;
    brand_name: string | null;
    slug: string | null;
    brand_slug: string | null;
    configurations_count: number;
    configurations: ConfigurationPreview[];
}

defineProps<{
    threshold: number;
    targetCurrency: string;
    carsCount: number;
    configurationsCount: number;
    cars: CarPreview[];
    flash?: {
        success?: string | null;
        warning?: string | null;
    };
}>();

const form = useForm({});

const formatNumber = (value: number): string =>
    new Intl.NumberFormat('ru-RU').format(value);

const submit = () => {
    form.post(route('admin.danger.convert.apply'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Смена валюты конфигураций" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Смена валюты конфигураций
                    </h2>
                    <p class="text-sm text-gray-500">
                        Страница доступна только по прямому URL и не добавлена в навигацию.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="flash?.success"
                    class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                >
                    {{ flash.success }}
                </div>
                <div
                    v-if="flash?.warning"
                    class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900"
                >
                    {{ flash.warning }}
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="space-y-6 p-6 text-gray-900">
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            Операция массово установит валюту <code>{{ targetCurrency }}</code> у всех конфигураций,
                            где цена меньше <strong>{{ formatNumber(threshold) }}</strong> и валюта ещё не равна
                            <code>{{ targetCurrency }}</code>.
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm text-slate-500">Будет затронуто машин</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                    {{ carsCount }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm text-slate-500">Будет изменено конфигураций</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                    {{ configurationsCount }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700 disabled:opacity-25"
                                :disabled="form.processing || configurationsCount === 0"
                                @click="submit"
                            >
                                {{ form.processing ? 'Изменение...' : 'Изменить валюту' }}
                            </button>

                            <Link
                                :href="route('admin.dashboard')"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Отменить
                            </Link>
                        </div>

                        <div
                            v-if="cars.length === 0"
                            class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-600"
                        >
                            Конфигурации с ценой меньше {{ formatNumber(threshold) }} для смены валюты не найдены.
                        </div>

                        <div v-else class="space-y-6">
                            <section
                                v-for="car in cars"
                                :key="car.car_id"
                                class="overflow-hidden rounded-xl border border-slate-200"
                            >
                                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                                    <h3 class="text-base font-semibold text-slate-900">
                                        {{ car.brand_name ? `${car.brand_name} ${car.car_name}` : car.car_name }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        ID автомобиля: {{ car.car_id }}. Конфигураций к обновлению: {{ car.configurations_count }}.
                                    </p>
                                    <div
                                        v-if="car.brand_slug && car.slug"
                                        class="mt-2 flex flex-wrap items-center gap-3 text-sm"
                                    >
                                        <a
                                            :href="`https://carsdo.ru/${car.brand_slug}/${car.slug}`"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            carsdo
                                        </a>
                                        <a
                                            :href="`http://localhost:8000/${car.brand_slug}/${car.slug}`"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            localhost
                                        </a>
                                    </div>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-white">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    ID конфигурации
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Цена
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Текущая валюта
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Новая валюта
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            <tr v-for="configuration in car.configurations" :key="configuration.id">
                                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                    {{ configuration.id }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                                    {{ configuration.price !== null ? formatNumber(configuration.price) : '-' }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                                    {{ configuration.current_currency ?? 'NULL' }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                                    {{ targetCurrency }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
