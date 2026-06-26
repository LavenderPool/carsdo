<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface SourceStats {
    key: string;
    label: string;
    total: number;
    eligible: number;
    converted: number;
    pending: number;
    skipped: number;
}

interface SummaryStats {
    total: number;
    eligible: number;
    converted: number;
    pending: number;
    skipped: number;
}

defineProps<{
    summary: SummaryStats;
    sources: SourceStats[];
    isRunning: boolean;
    flash?: {
        success?: string | null;
        warning?: string | null;
    };
}>();

const form = useForm({});

const submit = () => {
    form.post(route('admin.danger.webp-convert.apply'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="WebP convert" />

    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        WebP convert
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
                            Кнопка ниже запускает фоновую конвертацию только для локальных изображений без готового
                            <code>webp</code>. В расчёт входят фото автомобилей и cover-изображения.
                        </div>

                        <div
                            v-if="isRunning"
                            class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900"
                        >
                            Конвертация уже запущена. Повторный старт временно заблокирован.
                        </div>

                        <div class="grid gap-4 md:grid-cols-4">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm text-slate-500">Всего записей</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                    {{ summary.total }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm text-slate-500">Готово в WebP</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight text-emerald-700">
                                    {{ summary.converted }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm text-slate-500">Ожидают конвертацию</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight text-amber-700">
                                    {{ summary.pending }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm text-slate-500">Пропущено</p>
                                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">
                                    {{ summary.skipped }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700 disabled:opacity-25"
                                :disabled="form.processing || isRunning || summary.pending === 0"
                                @click="submit"
                            >
                                {{
                                    form.processing
                                        ? 'Запуск...'
                                        : isRunning
                                          ? 'Уже запущено'
                                          : 'Запустить конвертацию'
                                }}
                            </button>

                            <Link
                                :href="route('admin.dashboard')"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Назад
                            </Link>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-white">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Источник
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Всего
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Подходят
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Готово
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Ожидают
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Пропущено
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="source in sources" :key="source.key">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ source.label }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                            {{ source.total }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                            {{ source.eligible }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-emerald-700">
                                            {{ source.converted }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-amber-700">
                                            {{ source.pending }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                            {{ source.skipped }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
