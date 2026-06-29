<script setup lang="ts">
import EngineForm from '@/Components/Admin/EngineForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { createEngineFormData, type EngineFormSource } from '@/Pages/Admin/Engines/form';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    engine: EngineFormSource & {
        id: number;
    };
    brands: Array<{
        id: number;
        name: string;
        slug: string;
    }>;
}>();

const form = useForm(createEngineFormData(props.engine));

const submit = () => {
    form.put(route('admin.engines.update', props.engine.id));
};
</script>

<template>
    <Head :title="`Редактирование: ${engine.name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Редактирование двигателя
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
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <EngineForm
                        :form="form"
                        :brands="props.brands"
                        submit-label="Сохранить"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
