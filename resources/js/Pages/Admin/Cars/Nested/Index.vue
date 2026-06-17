<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    title: string;
    car: { id: number; name: string };
    items: Array<Record<string, unknown> & { id: number }>;
    columns: Array<{ key: string; label: string }>;
    createUrl: string;
    editBaseUrl: string;
    destroyBaseUrl: string;
    backUrl: string;
    createLabel: string;
    emptyMessage: string;
    deleteMessageTemplate: string;
}>();

const resolveUrl = (base: string, id: number) => base.replace('__ID__', String(id));

const valueToString = (value: unknown) => {
    if (typeof value === 'boolean') {
        return value ? 'Да' : 'Нет';
    }
    return value ?? '-';
};

const remove = (item: Record<string, unknown> & { id: number }) => {
    const message = props.deleteMessageTemplate
        .replace('{id}', String(item.id))
        .replace('{name}', String(item.name ?? item.id))
        .replace('{author}', String(item.author ?? item.id));

    if (!window.confirm(message)) {
        return;
    }

    router.delete(resolveUrl(props.destroyBaseUrl, item.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="title" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ title }}</h2>
                    <p class="text-sm text-gray-500">Автомобиль: {{ car.name }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <Link :href="backUrl" class="text-sm font-medium text-gray-600 hover:text-gray-900">Назад к автомобилю</Link>
                    <Link :href="createUrl" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                        {{ createLabel }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div v-if="items.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        v-for="column in columns"
                                        :key="column.key"
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    >
                                        {{ column.label }}
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Действия</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in items" :key="item.id">
                                    <td
                                        v-for="column in columns"
                                        :key="`${item.id}-${column.key}`"
                                        class="px-6 py-4 text-sm text-gray-600"
                                    >
                                        {{ valueToString(item[column.key]) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex justify-end gap-4">
                                            <Link :href="resolveUrl(editBaseUrl, item.id)" class="text-indigo-600 hover:text-indigo-900">Изменить</Link>
                                            <button type="button" class="text-red-600 hover:text-red-900" @click="remove(item)">Удалить</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="p-6 text-sm text-gray-500">{{ emptyMessage }}</div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
