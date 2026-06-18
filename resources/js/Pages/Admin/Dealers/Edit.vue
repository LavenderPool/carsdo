<script setup lang="ts">
import DealerForm from '@/Components/Admin/DealerForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    dealer: {
        id: number;
        name: string;
    };
}>();

const form = useForm({
    name: props.dealer.name,
});

const submit = () => {
    form.put(route('admin.dealers.update', props.dealer.id));
};
</script>

<template>
    <Head :title="`Редактирование: ${dealer.name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Редактирование дилера
                </h2>

                <Link
                    :href="route('admin.dealers.index')"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    Назад к списку
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <DealerForm
                        :form="form"
                        submit-label="Сохранить"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
