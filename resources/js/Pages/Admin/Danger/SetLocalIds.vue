<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface ConfigurationPreview {
    id: number;
    car_configuration_group_id: number;
    current_local_id: null;
    new_local_id: number;
}

interface CarPreview {
    car_id: number;
    car_name: string;
    brand_name: string | null;
    slug: string | null;
    brand_slug: string | null;
    configurations_count: number;
    has_conflicts: boolean;
    starting_local_id: number;
    existing_local_ids: number[];
    configurations: ConfigurationPreview[];
}

defineProps<{
    carsCount: number;
    configurationsCount: number;
    cars: CarPreview[];
    flash?: {
        success?: string | null;
    };
}>();

const form = useForm({});

const submit = () => {
    form.post(route('admin.danger.set-local-ids.apply'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Заполнение local_id" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Заполнение local_id
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

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="space-y-6 p-6 text-gray-900">
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            Операция массово заполнит <code>local_id</code> у конфигураций, где это поле сейчас пустое.
                            Сначала будут использованы свободные номера внутри текущей последовательности, а если их не хватит,
                            будут добавлены новые значения после уже занятых.
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

                        <div
                            v-if="cars.some((car) => car.brand_slug && car.slug)"
                            class="rounded-xl border border-slate-200 bg-slate-50 p-5"
                        >
                            <h3 class="text-sm font-semibold text-slate-900">Все URL затронутых автомобилей</h3>
                            <div class="mt-3 space-y-2">
                                <div
                                    v-for="car in cars.filter((car) => car.brand_slug && car.slug)"
                                    :key="`top-url-${car.car_id}`"
                                    class="text-sm"
                                >
                                    <a
                                        :href="`https://carsdo.ru/${car.brand_slug}/${car.slug}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        {{ `https://carsdo.ru/${car.brand_slug}/${car.slug}` }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700 disabled:opacity-25"
                                :disabled="form.processing || configurationsCount === 0"
                                @click="submit"
                            >
                                {{ form.processing ? 'Изменение...' : 'Изменить' }}
                            </button>

                            <Link
                                :href="route('admin.dashboard')"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Отменить
                            </Link>
                        </div>

                        <div v-if="cars.length === 0" class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-600">
                            Конфигурации с пустым <code>local_id</code> не найдены.
                        </div>

                        <div v-else class="space-y-6">
                            <section
                                v-for="car in cars"
                                :key="car.car_id"
                                class="overflow-hidden rounded-xl border border-slate-200"
                            >
                                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                                    <div class="flex flex-col gap-2 lg:flex-row lg:items-start lg:justify-between">
                                        <div>
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

                                        <div
                                            v-if="car.has_conflicts"
                                            class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900"
                                        >
                                            Уже заняты `local_id`: {{ car.existing_local_ids.join(', ') }}.
                                            Свободные значения будут подобраны начиная с {{ car.starting_local_id }}.
                                        </div>
                                        <div
                                            v-else
                                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800"
                                        >
                                            Конфликтов нет. Новые значения: с {{ car.starting_local_id }}.
                                        </div>
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
                                                    ID группы
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Текущий local_id
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Новый local_id
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            <tr v-for="configuration in car.configurations" :key="configuration.id">
                                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                    {{ configuration.id }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                                    {{ configuration.car_configuration_group_id }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                                    NULL
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                                    {{ configuration.new_local_id }}
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
