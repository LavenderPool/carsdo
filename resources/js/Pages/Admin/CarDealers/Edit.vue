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
    carDealer: {
        id: number;
        car_id: number;
        dealer_id: number;
        city_id: number;
        address: string | null;
        phone: string | null;
        website: string | null;
        is_official: boolean;
    };
    options: {
        cars: Option[];
        dealers: Option[];
        cities: Option[];
    };
}>();

const form = useForm({
    car_id: props.carDealer.car_id,
    dealer_id: props.carDealer.dealer_id,
    city_id: props.carDealer.city_id,
    address: props.carDealer.address ?? '',
    phone: props.carDealer.phone ?? '',
    website: props.carDealer.website ?? '',
    is_official: props.carDealer.is_official,
});

const submit = () => {
    form.put(route('admin.car-dealers.update', props.carDealer.id));
};
</script>

<template>
    <Head title="Редактирование связки дилера" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Редактирование связки дилера
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
                        submit-label="Сохранить"
                        @submit="submit"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
