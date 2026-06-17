<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import type { useForm } from '@inertiajs/vue3';

type BrandForm = ReturnType<typeof useForm<{
    name: string;
    slug: string;
    leave_from_russian: boolean;
}>>;

defineProps<{
    form: BrandForm;
    submitLabel: string;
}>();

const emit = defineEmits<{
    submit: [];
}>();
</script>

<template>
    <form class="space-y-6" @submit.prevent="emit('submit')">
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

        <div>
            <label for="leave_from_russian" class="inline-flex items-center gap-2">
                <input
                    id="leave_from_russian"
                    v-model="form.leave_from_russian"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                />
                <span class="text-sm text-gray-700">Покинувшие ру рынок</span>
            </label>
            <InputError class="mt-2" :message="form.errors.leave_from_russian" />
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
