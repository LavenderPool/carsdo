<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface CatalogItem {
    id: number;
    name: string;
    slug: string;
    is_published: boolean;
    sort_order: number;
    cars_count: number;
    updated_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    catalogs: {
        data: CatalogItem[];
        links: PaginationLink[];
    };
    filters: {
        search: string;
        status: string;
    };
    flash?: {
        success?: string | null;
    };
}>();

const search = ref(props.filters.search);
const status = ref(props.filters.status);
const hasCatalogs = computed(() => props.catalogs.data.length > 0);

const normalizedPaginationLinks = computed(() =>
    props.catalogs.links.map((link) => ({
        ...link,
        label: link.label.replace(/&laquo;\s*Previous/i, 'Назад').replace(/Next\s*&raquo;/i, 'Вперед'),
    })),
);

const applyFilters = () => {
    router.get(route('admin.car-catalogs.index'), {
        search: search.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const remove = (id: number, name: string) => {
    if (!window.confirm(`Удалить каталог "${name}"?`)) {
        return;
    }

    router.delete(route('admin.car-catalogs.destroy', id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="LSI каталоги" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">LSI каталоги</h2>
                <Link
                    :href="route('admin.car-catalogs.create')"
                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700"
                >
                    Добавить каталог
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
                        <form class="grid gap-3 md:grid-cols-[1fr_200px_auto]" @submit.prevent="applyFilters">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Поиск по названию или slug"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            <select
                                v-model="status"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Все статусы</option>
                                <option value="published">Опубликованные</option>
                                <option value="draft">Черновики</option>
                            </select>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700"
                            >
                                Найти
                            </button>
                        </form>
                    </div>

                    <div v-if="hasCatalogs" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Название</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Slug</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Статус</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Порядок</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Авто</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Действия</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="catalog in catalogs.data" :key="catalog.id">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ catalog.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ catalog.slug }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ catalog.is_published ? 'Опубликован' : 'Черновик' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ catalog.sort_order }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ catalog.cars_count }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex justify-end gap-4">
                                            <a
                                                :href="route('catalog.show', catalog.slug)"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-gray-600 hover:text-gray-900"
                                            >
                                                На сайте
                                            </a>
                                            <Link
                                                :href="route('admin.car-catalogs.edit', catalog.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Изменить
                                            </Link>
                                            <button
                                                type="button"
                                                class="text-red-600 hover:text-red-900"
                                                @click="remove(catalog.id, catalog.name)"
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
                        Каталоги пока не созданы.
                    </div>
                </div>

                <div v-if="catalogs.links.length > 3" class="flex flex-wrap items-center gap-2">
                    <template v-for="(link, index) in normalizedPaginationLinks" :key="`${index}-${link.label}`">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded-md px-3 py-2 text-sm"
                            :class="link.active ? 'bg-gray-800 text-white' : 'bg-white text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50'"
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
