<script setup lang="ts">
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
    };
    flash?: {
        success?: string | null;
    };
}>();

const form = useForm({
    brand_name: props.setting.brand_name,
    favicon: null as File | null,
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
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
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
