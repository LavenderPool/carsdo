<script setup lang="ts">
import SeoFieldsSection from '@/Components/Admin/SeoFieldsSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    setting: {
        brand_name: string;
        favicon_url: string | null;
        seo_title_suffix: string | null;
        seo_default_description: string | null;
        seo_default_robots: string | null;
        seo_default_og_image: string | null;
        home_seo_title: string | null;
        home_seo_description: string | null;
        home_seo_h1: string | null;
        home_seo_og_image: string | null;
        home_seo_canonical_url: string | null;
        home_seo_robots: string | null;
        new_cars_seo_title: string | null;
        new_cars_seo_description: string | null;
        new_cars_seo_h1: string | null;
        new_cars_seo_og_image: string | null;
        new_cars_seo_canonical_url: string | null;
        new_cars_seo_robots: string | null;
        electric_cars_seo_title: string | null;
        electric_cars_seo_description: string | null;
        electric_cars_seo_h1: string | null;
        electric_cars_seo_og_image: string | null;
        electric_cars_seo_canonical_url: string | null;
        electric_cars_seo_robots: string | null;
        crash_tests_seo_title: string | null;
        crash_tests_seo_description: string | null;
        crash_tests_seo_h1: string | null;
        crash_tests_seo_og_image: string | null;
        crash_tests_seo_canonical_url: string | null;
        crash_tests_seo_robots: string | null;
        test_drives_seo_title: string | null;
        test_drives_seo_description: string | null;
        test_drives_seo_h1: string | null;
        test_drives_seo_og_image: string | null;
        test_drives_seo_canonical_url: string | null;
        test_drives_seo_robots: string | null;
        cars_photo_seo_title: string | null;
        cars_photo_seo_description: string | null;
        cars_photo_seo_h1: string | null;
        cars_photo_seo_og_image: string | null;
        cars_photo_seo_canonical_url: string | null;
        cars_photo_seo_robots: string | null;
        blog_seo_title: string | null;
        blog_seo_description: string | null;
        blog_seo_h1: string | null;
        blog_seo_og_image: string | null;
        blog_seo_canonical_url: string | null;
        blog_seo_robots: string | null;
    };
    flash?: {
        success?: string | null;
    };
}>();

const form = useForm({
    brand_name: props.setting.brand_name,
    favicon: null as File | null,
    seo_title_suffix: props.setting.seo_title_suffix ?? '',
    seo_default_description: props.setting.seo_default_description ?? '',
    seo_default_robots: props.setting.seo_default_robots ?? '',
    seo_default_og_image: props.setting.seo_default_og_image ?? '',
    home_seo_title: props.setting.home_seo_title ?? '',
    home_seo_description: props.setting.home_seo_description ?? '',
    home_seo_h1: props.setting.home_seo_h1 ?? '',
    home_seo_og_image: props.setting.home_seo_og_image ?? '',
    home_seo_canonical_url: props.setting.home_seo_canonical_url ?? '',
    home_seo_robots: props.setting.home_seo_robots ?? '',
    new_cars_seo_title: props.setting.new_cars_seo_title ?? '',
    new_cars_seo_description: props.setting.new_cars_seo_description ?? '',
    new_cars_seo_h1: props.setting.new_cars_seo_h1 ?? '',
    new_cars_seo_og_image: props.setting.new_cars_seo_og_image ?? '',
    new_cars_seo_canonical_url: props.setting.new_cars_seo_canonical_url ?? '',
    new_cars_seo_robots: props.setting.new_cars_seo_robots ?? '',
    electric_cars_seo_title: props.setting.electric_cars_seo_title ?? '',
    electric_cars_seo_description: props.setting.electric_cars_seo_description ?? '',
    electric_cars_seo_h1: props.setting.electric_cars_seo_h1 ?? '',
    electric_cars_seo_og_image: props.setting.electric_cars_seo_og_image ?? '',
    electric_cars_seo_canonical_url: props.setting.electric_cars_seo_canonical_url ?? '',
    electric_cars_seo_robots: props.setting.electric_cars_seo_robots ?? '',
    crash_tests_seo_title: props.setting.crash_tests_seo_title ?? '',
    crash_tests_seo_description: props.setting.crash_tests_seo_description ?? '',
    crash_tests_seo_h1: props.setting.crash_tests_seo_h1 ?? '',
    crash_tests_seo_og_image: props.setting.crash_tests_seo_og_image ?? '',
    crash_tests_seo_canonical_url: props.setting.crash_tests_seo_canonical_url ?? '',
    crash_tests_seo_robots: props.setting.crash_tests_seo_robots ?? '',
    test_drives_seo_title: props.setting.test_drives_seo_title ?? '',
    test_drives_seo_description: props.setting.test_drives_seo_description ?? '',
    test_drives_seo_h1: props.setting.test_drives_seo_h1 ?? '',
    test_drives_seo_og_image: props.setting.test_drives_seo_og_image ?? '',
    test_drives_seo_canonical_url: props.setting.test_drives_seo_canonical_url ?? '',
    test_drives_seo_robots: props.setting.test_drives_seo_robots ?? '',
    cars_photo_seo_title: props.setting.cars_photo_seo_title ?? '',
    cars_photo_seo_description: props.setting.cars_photo_seo_description ?? '',
    cars_photo_seo_h1: props.setting.cars_photo_seo_h1 ?? '',
    cars_photo_seo_og_image: props.setting.cars_photo_seo_og_image ?? '',
    cars_photo_seo_canonical_url: props.setting.cars_photo_seo_canonical_url ?? '',
    cars_photo_seo_robots: props.setting.cars_photo_seo_robots ?? '',
    blog_seo_title: props.setting.blog_seo_title ?? '',
    blog_seo_description: props.setting.blog_seo_description ?? '',
    blog_seo_h1: props.setting.blog_seo_h1 ?? '',
    blog_seo_og_image: props.setting.blog_seo_og_image ?? '',
    blog_seo_canonical_url: props.setting.blog_seo_canonical_url ?? '',
    blog_seo_robots: props.setting.blog_seo_robots ?? '',
});

const selectedFaviconName = ref<string | null>(null);

const onFaviconChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    form.favicon = file;
    selectedFaviconName.value = file?.name ?? null;
};

const submit = () => {
    form.put(route('admin.settings.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Глобальные настройки" />

    <AdminLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Глобальные настройки
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                <div
                    v-if="flash?.success"
                    class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                >
                    {{ flash.success }}
                </div>

                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <form class="space-y-6" @submit.prevent="submit">
                        <div>
                            <InputLabel for="brand_name" value="Название бренда" />
                            <TextInput
                                id="brand_name"
                                v-model="form.brand_name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="form.errors.brand_name" />
                        </div>

                        <div>
                            <InputLabel for="favicon" value="Favicon" />
                            <input
                                id="favicon"
                                type="file"
                                accept=".ico,.png,.svg"
                                class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700"
                                @change="onFaviconChange"
                            />
                            <p class="mt-2 text-sm text-gray-500">
                                Поддерживаются файлы .ico, .png и .svg до 2 МБ.
                            </p>
                            <p v-if="selectedFaviconName" class="mt-1 text-sm text-gray-600">
                                Выбран файл: {{ selectedFaviconName }}
                            </p>
                            <InputError class="mt-2" :message="form.errors.favicon" />
                        </div>

                        <div v-if="setting.favicon_url" class="space-y-2">
                            <p class="text-sm font-medium text-gray-700">
                                Текущий favicon
                            </p>
                            <img
                                :src="setting.favicon_url"
                                alt="Текущий favicon"
                                class="h-10 w-10 rounded border border-gray-200 bg-white object-contain p-1"
                            />
                        </div>

                        <div class="rounded-xl border border-gray-200 p-5">
                            <div class="mb-4">
                                <h3 class="text-base font-semibold text-gray-900">SEO по умолчанию</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Эти значения используются как дефолты, если на странице нет более точного SEO override.
                                </p>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <InputLabel for="seo_title_suffix" value="Суффикс title" />
                                    <TextInput
                                        id="seo_title_suffix"
                                        v-model="form.seo_title_suffix"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Например: `| CarsDo`.</p>
                                    <InputError class="mt-2" :message="form.errors.seo_title_suffix" />
                                </div>

                                <div>
                                    <InputLabel for="seo_default_description" value="Описание по умолчанию" />
                                    <textarea
                                        id="seo_default_description"
                                        v-model="form.seo_default_description"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <InputError class="mt-2" :message="form.errors.seo_default_description" />
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <InputLabel for="seo_default_robots" value="Robots по умолчанию" />
                                        <TextInput
                                            id="seo_default_robots"
                                            v-model="form.seo_default_robots"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError class="mt-2" :message="form.errors.seo_default_robots" />
                                    </div>

                                    <div>
                                        <InputLabel for="seo_default_og_image" value="OG image по умолчанию" />
                                        <TextInput
                                            id="seo_default_og_image"
                                            v-model="form.seo_default_og_image"
                                            type="text"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError class="mt-2" :message="form.errors.seo_default_og_image" />
                                    </div>
                                </div>

                                <div v-if="form.seo_default_og_image" class="space-y-2">
                                    <p class="text-sm font-medium text-gray-700">Текущее изображение по умолчанию</p>
                                    <img
                                        :src="form.seo_default_og_image"
                                        alt="SEO default preview"
                                        class="max-h-40 rounded-lg border border-gray-200 bg-white object-contain p-2"
                                    />
                                </div>
                            </div>
                        </div>

                        <SeoFieldsSection
                            :form="form"
                            prefix="home_seo"
                            title="Главная страница"
                            description="SEO главной страницы сайта."
                            placeholders-hint="Плейсхолдеры: {site_name}, {current_year}, {next_year}, {new_cars_count}, {soon_cars_count}, {popular_cars_count}, {page}."
                            :fallback-image="form.seo_default_og_image || null"
                        />

                        <SeoFieldsSection
                            :form="form"
                            prefix="new_cars_seo"
                            title="Страница новых автомобилей"
                            description="SEO для `/new-cars-{year}/`."
                            placeholders-hint="Плейсхолдеры: {site_name}, {year}, {items_count}, {total_count}, {page}."
                            :fallback-image="form.seo_default_og_image || null"
                        />

                        <SeoFieldsSection
                            :form="form"
                            prefix="electric_cars_seo"
                            title="Страница электромобилей"
                            description="SEO для `/electric-cars/`."
                            placeholders-hint="Плейсхолдеры: {site_name}, {current_year}, {items_count}, {total_count}, {soon_cars_count}, {page}."
                            :fallback-image="form.seo_default_og_image || null"
                        />

                        <SeoFieldsSection
                            :form="form"
                            prefix="crash_tests_seo"
                            title="Страницы краш-тестов"
                            description="Общий шаблон для последних, электромобилей и фильтра по бренду."
                            placeholders-hint="Плейсхолдеры: {site_name}, {brand}, {filter_label}, {items_count}, {page}."
                            :fallback-image="form.seo_default_og_image || null"
                        />

                        <SeoFieldsSection
                            :form="form"
                            prefix="test_drives_seo"
                            title="Страницы тест-драйвов"
                            description="Общий шаблон для последних, электромобилей и фильтра по бренду."
                            placeholders-hint="Плейсхолдеры: {site_name}, {brand}, {filter_label}, {items_count}, {page}."
                            :fallback-image="form.seo_default_og_image || null"
                        />

                        <SeoFieldsSection
                            :form="form"
                            prefix="cars_photo_seo"
                            title="Страницы фото автомобилей"
                            description="Общий шаблон для общей страницы и фильтра по бренду."
                            placeholders-hint="Плейсхолдеры: {site_name}, {brand}, {items_count}, {page}."
                            :fallback-image="form.seo_default_og_image || null"
                        />

                        <SeoFieldsSection
                            :form="form"
                            prefix="blog_seo"
                            title="Страница блога"
                            description="SEO для `/blog/`."
                            placeholders-hint="Плейсхолдеры: {site_name}, {items_count}, {total_count}, {page}."
                            :fallback-image="form.seo_default_og_image || null"
                        />

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                Сохранить
                            </PrimaryButton>
                            <span v-if="form.processing" class="text-sm text-gray-500">
                                Сохранение...
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
