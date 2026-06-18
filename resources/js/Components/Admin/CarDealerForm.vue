<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import type { useForm } from '@inertiajs/vue3';

type CarDealerForm = ReturnType<typeof useForm<{
    car_id: number | null;
    dealer_id: number | null;
    city_id: number | null;
    address: string;
    phone: string;
    website: string;
    is_official: boolean;
}>>;

type Option = {
    id: number;
    label?: string;
    name?: string;
};

const props = defineProps<{
    form: CarDealerForm;
    cars: Option[];
    dealers: Option[];
    cities: Option[];
    submitLabel: string;
}>();

const emit = defineEmits<{
    submit: [];
}>();

const updateNumberField = (field: 'car_id' | 'dealer_id' | 'city_id', event: Event) => {
    const target = event.target as HTMLSelectElement;
    props.form[field] = target.value === '' ? null : Number(target.value);
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="emit('submit')">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <InputLabel for="car_id" value="Автомобиль" />
                <select
                    id="car_id"
                    :value="form.car_id ?? ''"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                    @change="updateNumberField('car_id', $event)"
                >
                    <option value="">Выберите автомобиль</option>
                    <option v-for="car in cars" :key="car.id" :value="car.id">
                        {{ car.label ?? car.name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.car_id" />
            </div>

            <div>
                <InputLabel for="dealer_id" value="Дилер" />
                <select
                    id="dealer_id"
                    :value="form.dealer_id ?? ''"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                    @change="updateNumberField('dealer_id', $event)"
                >
                    <option value="">Выберите дилера</option>
                    <option v-for="dealer in dealers" :key="dealer.id" :value="dealer.id">
                        {{ dealer.name ?? dealer.label }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.dealer_id" />
            </div>

            <div>
                <InputLabel for="city_id" value="Город" />
                <select
                    id="city_id"
                    :value="form.city_id ?? ''"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                    @change="updateNumberField('city_id', $event)"
                >
                    <option value="">Выберите город</option>
                    <option v-for="city in cities" :key="city.id" :value="city.id">
                        {{ city.name ?? city.label }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.city_id" />
            </div>
        </div>

        <div>
            <InputLabel for="address" value="Адрес" />
            <TextInput id="address" v-model="form.address" type="text" class="mt-1 block w-full" />
            <InputError class="mt-2" :message="form.errors.address" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <InputLabel for="phone" value="Телефон" />
                <TextInput id="phone" v-model="form.phone" type="text" class="mt-1 block w-full" />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div>
                <InputLabel for="website" value="Сайт" />
                <TextInput id="website" v-model="form.website" type="url" class="mt-1 block w-full" />
                <InputError class="mt-2" :message="form.errors.website" />
            </div>
        </div>

        <div>
            <label for="is_official" class="inline-flex items-center gap-2">
                <input
                    id="is_official"
                    v-model="form.is_official"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                />
                <span class="text-sm text-gray-700">Официальный дилер</span>
            </label>
            <InputError class="mt-2" :message="form.errors.is_official" />
        </div>

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">
                {{ submitLabel }}
            </PrimaryButton>
            <span v-if="form.processing" class="text-sm text-gray-500">
                Сохранение...
            </span>
        </div>
    </form>
</template>
