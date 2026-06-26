<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import type { useForm } from '@inertiajs/vue3';

type EngineForm = ReturnType<typeof useForm<{
    brand_id: number | null;
    name: string;
    slug: string;
}>>;

defineProps<{
    form: EngineForm;
    brands: Array<{
        id: number;
        name: string;
        slug: string;
    }>;
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
                v-model="form.brand_id"
                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
                <option :value="null">Выберите бренд</option>
                <option
                    v-for="brand in brands"
                    :key="brand.id"
                    :value="brand.id"
                >
                    {{ brand.name }} ({{ brand.slug }})
                </option>
            </select>
            <InputError class="mt-2" :message="form.errors.brand_id" />
        </div>

        <div>
            <InputLabel for="name" value="Название" />
            <TextInput
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
                autocomplete="off"
            />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
            <InputLabel for="slug" value="Идентификатор" />
            <TextInput
                id="slug"
                v-model="form.slug"
                type="text"
                class="mt-1 block w-full"
                autocomplete="off"
            />
            <p class="mt-1 text-sm text-gray-500">
                Если оставить поле пустым, идентификатор будет создан из названия.
            </p>
            <InputError class="mt-2" :message="form.errors.slug" />
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
