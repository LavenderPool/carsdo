<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    pages: Array<{
        page_key: string;
        name: string;
        car_seo_prefix: string;
        has_overrides: boolean;
    }>;
    flash?: {
        success?: string | null;
    };
}>();
</script>

<template>
    <Head title="SEO авто-страниц" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">SEO авто-страниц</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="flash?.success"
                    class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                >
                    {{ flash.success }}
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 p-6">
                        <h3 class="text-base font-semibold text-gray-900">Выберите страницу</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            На следующем экране можно задать общий SEO-шаблон для страницы автомобиля. Если у конкретной машины заполнены свои SEO-поля, они будут приоритетнее.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Страница</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Префикс SEO машины</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Статус</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Действие</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="page in pages" :key="page.page_key">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ page.name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ page.car_seo_prefix }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ page.has_overrides ? 'Заполнено' : 'Пока по умолчанию' }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <Link :href="route('admin.car-page-seos.edit', page.page_key)" class="text-indigo-600 hover:text-indigo-900">
                                            Редактировать
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
