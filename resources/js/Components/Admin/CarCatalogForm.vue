<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SeoFieldsSection from '@/Components/Admin/SeoFieldsSection.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed } from 'vue';

interface FormLike {
    [key: string]: any;
}

interface BrandOption {
    id: number;
    name: string;
}

interface CarOption {
    id: number;
    label: string;
}

const props = defineProps<{
    form: FormLike;
    submitLabel: string;
    brands: BrandOption[];
    cars: CarOption[];
    driveTypes: string[];
    engineTypes: string[];
}>();

const emit = defineEmits<{
    (event: 'submit'): void;
}>();

const selectedBrandId = computed<number | null>({
    get: () => {
        const firstBrandId = Array.isArray(props.form.brand_ids) ? props.form.brand_ids[0] : null;

        return typeof firstBrandId === 'number' ? firstBrandId : null;
    },
    set: (value) => {
        props.form.brand_ids = value === null ? [] : [value];
    },
});

const selectedDriveType = computed<string | null>({
    get: () => {
        const firstDriveType = Array.isArray(props.form.drive_types) ? props.form.drive_types[0] : null;

        return typeof firstDriveType === 'string' && firstDriveType !== '' ? firstDriveType : null;
    },
    set: (value) => {
        props.form.drive_types = value ? [value] : [];
    },
});

const selectedEngineType = computed<string | null>({
    get: () => {
        const firstEngineType = Array.isArray(props.form.engine_types) ? props.form.engine_types[0] : null;

        return typeof firstEngineType === 'string' && firstEngineType !== '' ? firstEngineType : null;
    },
    set: (value) => {
        props.form.engine_types = value ? [value] : [];
    },
});

const addManualCar = () => {
    if (!Array.isArray(props.form.manual_cars)) {
        props.form.manual_cars = [];
    }

    props.form.manual_cars.push({
        car_id: null,
        sort_order: props.form.manual_cars.length,
    });
};

const removeManualCar = (index: number) => {
    props.form.manual_cars.splice(index, 1);
};

const onSubmit = () => {
    emit('submit');
};
</script>

<template>
    <form class="space-y-8" @submit.prevent="onSubmit">
        <section class="grid gap-6 md:grid-cols-2">
            <div>
                <InputLabel for="name" value="Название" />
                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="slug" value="Slug" />
                <TextInput id="slug" v-model="form.slug" type="text" class="mt-1 block w-full" required />
                <InputError class="mt-2" :message="form.errors.slug" />
            </div>
            <div class="md:col-span-2">
                <InputLabel for="description" value="Описание" />
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <InputError class="mt-2" :message="form.errors.description" />
            </div>
            <div>
                <InputLabel for="sort_order" value="Порядок" />
                <TextInput id="sort_order" v-model.number="form.sort_order" type="number" min="0" class="mt-1 block w-full" />
                <InputError class="mt-2" :message="form.errors.sort_order" />
            </div>
            <div class="flex items-center">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input
                        v-model="form.is_published"
                        type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    >
                    Опубликован
                </label>
                <InputError class="mt-2" :message="form.errors.is_published" />
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5">
            <h3 class="text-base font-semibold text-gray-900">Фильтры автоподбора</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <InputLabel for="price_min" value="Цена от" />
                    <TextInput id="price_min" v-model.number="form.price_min" type="number" min="0" class="mt-1 block w-full" />
                    <InputError class="mt-2" :message="form.errors.price_min" />
                </div>
                <div>
                    <InputLabel for="price_max" value="Цена до" />
                    <TextInput id="price_max" v-model.number="form.price_max" type="number" min="0" class="mt-1 block w-full" />
                    <InputError class="mt-2" :message="form.errors.price_max" />
                </div>
                <div>
                    <InputLabel for="year_from" value="Год от" />
                    <TextInput id="year_from" v-model.number="form.year_from" type="number" min="1900" class="mt-1 block w-full" />
                    <InputError class="mt-2" :message="form.errors.year_from" />
                </div>
                <div>
                    <InputLabel for="year_to" value="Год до" />
                    <TextInput id="year_to" v-model.number="form.year_to" type="number" min="1900" class="mt-1 block w-full" />
                    <InputError class="mt-2" :message="form.errors.year_to" />
                </div>
                <div>
                    <InputLabel for="is_electric_car" value="Электромобиль" />
                    <select
                        id="is_electric_car"
                        v-model="form.is_electric_car"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option :value="null">Любой</option>
                        <option :value="true">Да</option>
                        <option :value="false">Нет</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.is_electric_car" />
                </div>
                <div>
                    <InputLabel for="brand_ids" value="Бренды" />
                    <select
                        id="brand_ids"
                        v-model="selectedBrandId"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option :value="null">Любой бренд</option>
                        <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.brand_ids" />
                </div>
                <div>
                    <InputLabel for="drive_types" value="Тип привода" />
                    <select
                        id="drive_types"
                        v-model="selectedDriveType"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option :value="null">Любой тип привода</option>
                        <option v-for="driveType in driveTypes" :key="driveType" :value="driveType">{{ driveType }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.drive_types" />
                </div>
                <div>
                    <InputLabel for="engine_types" value="Тип двигателя" />
                    <select
                        id="engine_types"
                        v-model="selectedEngineType"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option :value="null">Любой тип двигателя</option>
                        <option v-for="engineType in engineTypes" :key="engineType" :value="engineType">{{ engineType }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.engine_types" />
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Ручной список автомобилей</h3>
                <button
                    type="button"
                    class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                    @click="addManualCar"
                >
                    Добавить авто
                </button>
            </div>
            <div class="space-y-3">
                <div
                    v-for="(item, index) in form.manual_cars"
                    :key="`manual-${index}`"
                    class="grid gap-3 rounded-lg border border-gray-200 p-3 md:grid-cols-[1fr_120px_auto]"
                >
                    <select
                        v-model.number="item.car_id"
                        class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option :value="null">Выберите автомобиль</option>
                        <option v-for="car in cars" :key="car.id" :value="car.id">{{ car.label }}</option>
                    </select>
                    <TextInput v-model.number="item.sort_order" type="number" min="0" class="block w-full" />
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-md border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50"
                        @click="removeManualCar(index)"
                    >
                        Удалить
                    </button>
                    <InputError class="md:col-span-3" :message="form.errors[`manual_cars.${index}.car_id`]" />
                </div>
            </div>
            <InputError class="mt-2" :message="form.errors.manual_cars" />
        </section>

        <SeoFieldsSection
            :form="form"
            prefix="seo"
            title="SEO каталога"
            description="Переопределения мета-тегов для страницы каталога."
            :fallback-image="null"
        />

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">{{ submitLabel }}</PrimaryButton>
            <span v-if="form.processing" class="text-sm text-gray-500">Сохранение...</span>
        </div>
    </form>
</template>
