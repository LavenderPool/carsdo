<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

type Field = {
    name: string;
    label: string;
    type: 'text' | 'number' | 'textarea' | 'checkbox' | 'select';
    required?: boolean;
    options?: Array<{ value: string | number; label: string }>;
};

type FormFieldValue = string | number | boolean | null;
type NestedCarFormData = Record<string, FormFieldValue>;

const props = defineProps<{
    title: string;
    car: { id: number; name: string };
    backUrl: string;
    submit: { method: 'post' | 'put' | 'patch'; url: string; label: string };
    item: Record<string, unknown>;
    fields: Field[];
}>();

const form = useForm<NestedCarFormData>({ ...(props.item as NestedCarFormData) });

const submitForm = () => {
    form.submit(props.submit.method, props.submit.url);
};

const normalizeSelectValue = (value: unknown): string | number => {
    if (typeof value === 'number') {
        return value;
    }
    if (typeof value === 'string') {
        return value;
    }
    return '';
};

const onSelectChange = (field: Field, event: Event) => {
    const target = event.target as HTMLSelectElement;
    const matchedOption = (field.options ?? []).find((option) => String(option.value) === target.value);
    const value: FormFieldValue = target.value === '' ? null : (matchedOption?.value ?? target.value);
    form[field.name] = value;
};
</script>

<template>
    <Head :title="title" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ title }}</h2>
                    <p class="text-sm text-gray-500">Автомобиль: {{ car.name }}</p>
                </div>
                <Link :href="backUrl" class="text-sm font-medium text-gray-600 hover:text-gray-900">Назад к списку</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <form class="space-y-6" @submit.prevent="submitForm">
                        <div v-for="field in fields" :key="field.name">
                            <InputLabel :for="field.name" :value="field.label" />

                            <TextInput
                                v-if="field.type === 'text' || field.type === 'number'"
                                :id="field.name"
                                v-model="form[field.name]"
                                :type="field.type"
                                class="mt-1 block w-full"
                                :required="field.required"
                            />

                            <textarea
                                v-else-if="field.type === 'textarea'"
                                :id="field.name"
                                v-model="form[field.name] as string"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                rows="4"
                            />

                            <label v-else-if="field.type === 'checkbox'" class="mt-1 inline-flex items-center gap-2">
                                <input
                                    :id="field.name"
                                    v-model="form[field.name] as boolean"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <span class="text-sm text-gray-700">{{ field.label }}</span>
                            </label>

                            <select
                                v-else-if="field.type === 'select'"
                                :id="field.name"
                                :value="normalizeSelectValue(form[field.name])"
                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                :required="field.required"
                                @change="onSelectChange(field, $event)"
                            >
                                <option value="">Не выбрано</option>
                                <option
                                    v-for="option in field.options ?? []"
                                    :key="`${field.name}-${option.value}`"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="form.errors[field.name]" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">{{ submit.label }}</PrimaryButton>
                            <span v-if="form.processing" class="text-sm text-gray-500">Сохранение...</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
