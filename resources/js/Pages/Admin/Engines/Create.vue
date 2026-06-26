<script setup lang="ts">
import EngineForm from '@/Components/Admin/EngineForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    brands: Array<{
        id: number;
        name: string;
        slug: string;
    }>;
}>();

const form = useForm({
    brand_id: null as number | null,
    name: '',
    slug: '',
});

const submit = () => {
    form.post(route('admin.engines.store'));
};
</script>

<template>
    <Head title="Новый двигатель" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Новый двигатель
                </h2>

                <Link
                    :href="route('admin.engines.index')"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    Назад к списку
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <EngineForm
                        :form="form"
                        :brands="props.brands"
                        submit-label="Создать"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
