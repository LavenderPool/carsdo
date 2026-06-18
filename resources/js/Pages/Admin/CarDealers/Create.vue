<script setup lang="ts">
import CarDealerForm from '@/Components/Admin/CarDealerForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Option {
    id: number;
    name?: string;
    label?: string;
}

const props = defineProps<{
    options: {
        cars: Option[];
        dealers: Option[];
        cities: Option[];
    };
}>();

const form = useForm({
    car_id: null as number | null,
    dealer_id: null as number | null,
    city_id: null as number | null,
    address: '',
    phone: '',
    website: '',
    is_official: false,
});

const submit = () => {
    form.post(route('admin.car-dealers.store'));
};
</script>

<template>
    <Head title="Новая связка дилера" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Новая связка дилера
                </h2>

                <Link
                    :href="route('admin.car-dealers.index')"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    Назад к списку
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <CarDealerForm
                        :form="form"
                        :cars="props.options.cars"
                        :dealers="props.options.dealers"
                        :cities="props.options.cities"
                        submit-label="Создать"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
