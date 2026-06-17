<script setup lang="ts">
import { destroy } from '@/actions/App/Http/Controllers/Admin/BrandController';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface BrandItem {
    id: number;
    name: string;
    slug: string;
    leave_from_russian: boolean;
    views_count: number;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    brands: {
        data: BrandItem[];
        links: PaginationLink[];
    };
    filters: {
        search: string;
    };
    flash?: {
        success?: string | null;
    };
}>();

const search = ref(props.filters.search);

const hasBrands = computed(() => props.brands.data.length > 0);

const normalizedPaginationLinks = computed(() =>
    props.brands.links.map((link) => ({
        ...link,
        label: link.label
            .replace(/&laquo;\s*Previous/i, 'Назад')
            .replace(/Next\s*&raquo;/i, 'Вперед'),
    })),
);

const applySearch = () => {
    router.get(
        route('admin.brands.index'),
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
};

const remove = (id: number, name: string) => {
    if (!window.confirm(`Удалить бренд "${name}"?`)) {
        return;
    }

    router.visit(destroy(id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Бренды" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Бренды
                </h2>

                <Link
                    :href="route('admin.brands.create')"
                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700"
                >
                    Добавить бренд
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
                        <form class="flex flex-col gap-3 sm:flex-row" @submit.prevent="applySearch">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Поиск по названию или идентификатору"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700"
                            >
                                Найти
                            </button>
                        </form>
                    </div>

                    <div v-if="hasBrands" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Название
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Идентификатор
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Покинувшие ру рынок
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Просмотры
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Создано
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Действия
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="brand in brands.data" :key="brand.id">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ brand.name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ brand.slug }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ brand.leave_from_russian ? 'Да' : 'Нет' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ brand.views_count }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ brand.created_at ?? '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex justify-end gap-4">
                                            <Link
                                                :href="route('admin.brands.edit', brand.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Изменить
                                            </Link>
                                            <button
                                                type="button"
                                                class="text-red-600 hover:text-red-900"
                                                @click="remove(brand.id, brand.name)"
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
                        Бренды пока не созданы.
                    </div>
                </div>

                <div
                    v-if="brands.links.length > 3"
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
