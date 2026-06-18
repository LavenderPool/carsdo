<script setup lang="ts">
import SeoFieldsSection from '@/Components/Admin/SeoFieldsSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import type { useForm } from '@inertiajs/vue3';

type CarForm = ReturnType<typeof useForm<{
    brand_id: number | null;
    name: string;
    slug: string;
    year: string;
    cover_path: string;
    start_price: number | null;
    end_price: number | null;
    official_site: string;
    is_electric_car: boolean;
    is_soon: boolean;
    is_another_models: boolean;
    seo_title: string;
    seo_description: string;
    seo_h1: string;
    seo_og_image: string;
    seo_canonical_url: string;
    seo_robots: string;
    equipment_seo_title: string;
    equipment_seo_description: string;
    equipment_seo_h1: string;
    equipment_seo_og_image: string;
    equipment_seo_canonical_url: string;
    equipment_seo_robots: string;
    reviews_seo_title: string;
    reviews_seo_description: string;
    reviews_seo_h1: string;
    reviews_seo_og_image: string;
    reviews_seo_canonical_url: string;
    reviews_seo_robots: string;
    crash_test_seo_title: string;
    crash_test_seo_description: string;
    crash_test_seo_h1: string;
    crash_test_seo_og_image: string;
    crash_test_seo_canonical_url: string;
    crash_test_seo_robots: string;
    test_drive_seo_title: string;
    test_drive_seo_description: string;
    test_drive_seo_h1: string;
    test_drive_seo_og_image: string;
    test_drive_seo_canonical_url: string;
    test_drive_seo_robots: string;
}>>;

defineProps<{
    form: CarForm;
    brands: Array<{ id: number; name: string }>;
    submitLabel: string;
}>();

const emit = defineEmits<{
    submit: [];
}>();
</script>

<template>
    <form class="space-y-6" @submit.prevent="emit('submit')">
        <div>
            <InputLabel for="brand_id" value="Бренд" />
            <select
                id="brand_id"
                v-model.number="form.brand_id"
                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
                <option :value="null">Выберите бренд</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                    {{ brand.name }}
                </option>
            </select>
            <InputError class="mt-2" :message="form.errors.brand_id" />
        </div>

        <div>
            <InputLabel for="name" value="Название" />
            <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
            <InputLabel for="slug" value="Идентификатор" />
            <TextInput id="slug" v-model="form.slug" type="text" class="mt-1 block w-full" />
            <p class="mt-1 text-sm text-gray-500">
                Если оставить поле пустым, идентификатор будет создан из названия.
            </p>
            <InputError class="mt-2" :message="form.errors.slug" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <InputLabel for="year" value="Год" />
                <TextInput id="year" v-model="form.year" type="text" class="mt-1 block w-full" />
                <InputError class="mt-2" :message="form.errors.year" />
            </div>
            <div>
                <InputLabel for="cover_path" value="Путь к обложке" />
                <TextInput id="cover_path" v-model="form.cover_path" type="text" class="mt-1 block w-full" />
                <InputError class="mt-2" :message="form.errors.cover_path" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <InputLabel for="start_price" value="Цена от" />
                <TextInput id="start_price" v-model.number="form.start_price" type="number" min="0" class="mt-1 block w-full" />
                <InputError class="mt-2" :message="form.errors.start_price" />
            </div>
            <div>
                <InputLabel for="end_price" value="Цена до" />
                <TextInput id="end_price" v-model.number="form.end_price" type="number" min="0" class="mt-1 block w-full" />
                <InputError class="mt-2" :message="form.errors.end_price" />
            </div>
        </div>

        <div>
            <InputLabel for="official_site" value="Официальный сайт" />
            <TextInput id="official_site" v-model="form.official_site" type="url" class="mt-1 block w-full" />
            <InputError class="mt-2" :message="form.errors.official_site" />
        </div>

        <div class="space-y-2">
            <label class="inline-flex items-center gap-2">
                <input v-model="form.is_electric_car" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span class="text-sm text-gray-700">Электромобиль</span>
            </label>
            <InputError class="mt-1" :message="form.errors.is_electric_car" />

            <label class="inline-flex items-center gap-2">
                <input v-model="form.is_soon" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span class="text-sm text-gray-700">Скоро в продаже</span>
            </label>
            <InputError class="mt-1" :message="form.errors.is_soon" />

            <label class="inline-flex items-center gap-2">
                <input v-model="form.is_another_models" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span class="text-sm text-gray-700">Другие модели</span>
            </label>
            <InputError class="mt-1" :message="form.errors.is_another_models" />
        </div>

        <SeoFieldsSection
            :form="form"
            prefix="seo"
            title="SEO основной страницы"
            description="SEO для основной карточки автомобиля."
            placeholders-hint="Плейсхолдеры: {brand}, {car}, {year}, {site_name}, {price}, {price_range}, {configurations_count}."
            :fallback-image="form.cover_path || null"
        />

        <SeoFieldsSection
            :form="form"
            prefix="equipment_seo"
            title="SEO страницы комплектаций"
            description="Используется на страницах вида `equipment-N`."
            placeholders-hint="Плейсхолдеры: {brand}, {car}, {group}, {configuration_price}, {site_name}."
            :fallback-image="form.cover_path || null"
        />

        <SeoFieldsSection
            :form="form"
            prefix="reviews_seo"
            title="SEO страницы отзывов"
            description="Используется на странице отзывов владельцев."
            placeholders-hint="Плейсхолдеры: {brand}, {car}, {reviews_count}, {site_name}."
            :fallback-image="form.cover_path || null"
        />

        <SeoFieldsSection
            :form="form"
            prefix="crash_test_seo"
            title="SEO страницы краш-теста"
            description="Используется на странице краш-теста."
            placeholders-hint="Плейсхолдеры: {brand}, {car}, {crash_test_year}, {crash_test_rating}, {site_name}."
            :fallback-image="form.cover_path || null"
        />

        <SeoFieldsSection
            :form="form"
            prefix="test_drive_seo"
            title="SEO страницы тест-драйва"
            description="Используется на странице тест-драйвов."
            placeholders-hint="Плейсхолдеры: {brand}, {car}, {test_drives_count}, {site_name}."
            :fallback-image="form.cover_path || null"
        />

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">
                {{ submitLabel }}
            </PrimaryButton>
            <span v-if="form.processing" class="text-sm text-gray-500">Сохранение...</span>
        </div>
    </form>
</template>
