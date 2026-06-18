<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface CarDealerItem {
    id: number;
    car: string;
    dealer: string;
    city: string;
    address: string | null;
    phone: string | null;
    website: string | null;
    is_official: boolean;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface NamedOption {
    id: number;
    name?: string;
    label?: string;
}

const props = defineProps<{
    carDealers: {
        data: CarDealerItem[];
        links: PaginationLink[];
    };
    filters: {
        search: string;
        car_id: number | null;
        dealer_id: number | null;
        city_id: number | null;
    };
    options: {
        cars: NamedOption[];
        dealers: NamedOption[];
        cities: NamedOption[];
    };
    flash?: {
        success?: string | null;
    };
}>();

const search = ref(props.filters.search);
const selectedCarId = ref<number | null>(props.filters.car_id);
const selectedDealerId = ref<number | null>(props.filters.dealer_id);
const selectedCityId = ref<number | null>(props.filters.city_id);

const hasCarDealers = computed(() => props.carDealers.data.length > 0);

const normalizedPaginationLinks = computed(() =>
    props.carDealers.links.map((link) => ({
        ...link,
        label: link.label
            .replace(/&laquo;\s*Previous/i, 'Назад')
            .replace(/Next\s*&raquo;/i, 'Вперед'),
    })),
);

const updateNumberRef = (target: typeof selectedCarId, event: Event) => {
    const value = (event.target as HTMLSelectElement).value;
    target.value = value === '' ? null : Number(value);
};

const applyFilters = () => {
    router.get(
        route('admin.car-dealers.index'),
        {
            search: search.value || undefined,
            car_id: selectedCarId.value ?? undefined,
            dealer_id: selectedDealerId.value ?? undefined,
            city_id: selectedCityId.value ?? undefined,
        },
        { preserveState: true, replace: true },
    );
};

const resetFilters = () => {
    search.value = '';
    selectedCarId.value = null;
    selectedDealerId.value = null;
    selectedCityId.value = null;
    applyFilters();
};

const remove = (item: CarDealerItem) => {
    if (!window.confirm(`Удалить связку "${item.car} / ${item.dealer} / ${item.city}"?`)) {
        return;
    }

    router.delete(route('admin.car-dealers.destroy', item.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Связки дилеров" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Связки дилеров
                </h2>

                <Link
                    :href="route('admin.car-dealers.create')"
                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700"
                >
                    Добавить связку
                </Link>
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
                    <div class="border-b border-gray-200 p-6">
                        <form class="space-y-4" @submit.prevent="applyFilters">
                            <div class="grid gap-4 md:grid-cols-4">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Поиск по авто, дилеру, городу, адресу"
                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 md:col-span-4"
                                />

                                <select
                                    :value="selectedCarId ?? ''"
                                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @change="updateNumberRef(selectedCarId, $event)"
                                >
                                    <option value="">Все автомобили</option>
                                    <option v-for="car in options.cars" :key="car.id" :value="car.id">
                                        {{ car.label ?? car.name }}
                                    </option>
                                </select>

                                <select
                                    :value="selectedDealerId ?? ''"
                                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @change="updateNumberRef(selectedDealerId, $event)"
                                >
                                    <option value="">Все дилеры</option>
                                    <option v-for="dealer in options.dealers" :key="dealer.id" :value="dealer.id">
                                        {{ dealer.name ?? dealer.label }}
                                    </option>
                                </select>

                                <select
                                    :value="selectedCityId ?? ''"
                                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @change="updateNumberRef(selectedCityId, $event)"
                                >
                                    <option value="">Все города</option>
                                    <option v-for="city in options.cities" :key="city.id" :value="city.id">
                                        {{ city.name ?? city.label }}
                                    </option>
                                </select>

                                <div class="flex gap-3">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700"
                                    >
                                        Найти
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                                        @click="resetFilters"
                                    >
                                        Сбросить
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div v-if="hasCarDealers" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Автомобиль
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Дилер
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Город
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Контакты
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Статус
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Действия
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in carDealers.data" :key="item.id">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ item.car }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ item.dealer }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ item.city }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <div class="space-y-1">
                                            <div>{{ item.address ?? '-' }}</div>
                                            <div>{{ item.phone ?? '-' }}</div>
                                            <a
                                                v-if="item.website"
                                                :href="item.website"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                {{ item.website }}
                                            </a>
                                            <div v-else>-</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ item.is_official ? 'Официальный' : 'Неофициальный' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex justify-end gap-4">
                                            <Link
                                                :href="route('admin.car-dealers.edit', item.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Изменить
                                            </Link>
                                            <button
                                                type="button"
                                                class="text-red-600 hover:text-red-900"
                                                @click="remove(item)"
                                            >
                                                Удалить
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="p-6 text-sm text-gray-500">
                        Связки дилеров пока не созданы.
                    </div>
                </div>

                <div
                    v-if="carDealers.links.length > 3"
                    class="flex flex-wrap items-center gap-2"
                >
                    <template
                        v-for="(link, index) in normalizedPaginationLinks"
                        :key="`${index}-${link.label}`"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded-md px-3 py-2 text-sm"
                            :class="link.active
                                ? 'bg-gray-800 text-white'
                                : 'bg-white text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="rounded-md bg-gray-100 px-3 py-2 text-sm text-gray-400"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
