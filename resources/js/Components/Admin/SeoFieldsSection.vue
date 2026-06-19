<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed } from 'vue';

const props = withDefaults(defineProps<{
    form: Record<string, any>;
    prefix: string;
    title: string;
    description?: string;
    placeholdersHint?: string;
    fallbackImage?: string | null;
}>(), {
    description: '',
    placeholdersHint: '',
    fallbackImage: null,
});

const fieldName = (suffix: string): string => props.prefix !== '' ? `${props.prefix}_${suffix}` : suffix;

const previewImage = computed(() => {
    const customImage = props.form[fieldName('og_image')];

    if (typeof customImage === 'string' && customImage.trim() !== '') {
        return customImage;
    }

    return props.fallbackImage;
});
</script>

<template>
    <section class="rounded-xl border border-gray-200 p-5">
        <div class="mb-4">
            <h3 class="text-base font-semibold text-gray-900">{{ title }}</h3>
            <p v-if="description" class="mt-1 text-sm text-gray-500">{{ description }}</p>
            <p v-if="placeholdersHint" class="mt-2 text-xs text-gray-500">{{ placeholdersHint }}</p>
        </div>

        <div class="space-y-4">
            <div>
                <InputLabel :for="fieldName('title')" value="SEO title" />
                <TextInput
                    :id="fieldName('title')"
                    v-model="form[fieldName('title')]"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError class="mt-2" :message="form.errors[fieldName('title')]" />
            </div>

            <div>
                <InputLabel :for="fieldName('description')" value="SEO description" />
                <textarea
                    :id="fieldName('description')"
                    v-model="form[fieldName('description')]"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <InputError class="mt-2" :message="form.errors[fieldName('description')]" />
            </div>

            <div>
                <InputLabel :for="fieldName('h1')" value="H1" />
                <TextInput
                    :id="fieldName('h1')"
                    v-model="form[fieldName('h1')]"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError class="mt-2" :message="form.errors[fieldName('h1')]" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <InputLabel :for="fieldName('canonical_url')" value="Canonical URL" />
                    <TextInput
                        :id="fieldName('canonical_url')"
                        v-model="form[fieldName('canonical_url')]"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <p class="mt-1 text-xs text-gray-500">Можно указать абсолютный URL или путь вида `/brand/model/`.</p>
                    <InputError class="mt-2" :message="form.errors[fieldName('canonical_url')]" />
                </div>

                <div>
                    <InputLabel :for="fieldName('robots')" value="Robots" />
                    <TextInput
                        :id="fieldName('robots')"
                        v-model="form[fieldName('robots')]"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <p class="mt-1 text-xs text-gray-500">Например: `index, follow` или `noindex, nofollow`.</p>
                    <InputError class="mt-2" :message="form.errors[fieldName('robots')]" />
                </div>
            </div>

            <div>
                <InputLabel :for="fieldName('og_image')" value="OG image" />
                <TextInput
                    :id="fieldName('og_image')"
                    v-model="form[fieldName('og_image')]"
                    type="text"
                    class="mt-1 block w-full"
                />
                <p class="mt-1 text-xs text-gray-500">Поддерживается абсолютный URL или относительный путь к изображению.</p>
                <InputError class="mt-2" :message="form.errors[fieldName('og_image')]" />
            </div>

            <div v-if="previewImage" class="space-y-2">
                <p class="text-sm font-medium text-gray-700">Превью изображения</p>
                <img
                    :src="previewImage"
                    alt="SEO preview"
                    class="max-h-40 rounded-lg border border-gray-200 bg-white object-contain p-2"
                />
            </div>
        </div>
    </section>
</template>
