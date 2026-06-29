<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import type { EngineFormData } from '@/Pages/Admin/Engines/form';
import type { useForm } from '@inertiajs/vue3';

type EngineForm = ReturnType<typeof useForm<EngineFormData>>;

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
        <section class="rounded-xl border border-gray-200 p-5">
            <div class="mb-4">
                <h3 class="text-base font-semibold text-gray-900">Основная информация</h3>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
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
                    <InputLabel for="engine_type" value="Тип двигателя" />
                    <TextInput
                        id="engine_type"
                        v-model="form.engine_type"
                        type="text"
                        class="mt-1 block w-full"
                        autocomplete="off"
                    />
                    <InputError class="mt-2" :message="form.errors.engine_type" />
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

                <div class="md:col-span-2">
                    <InputLabel for="engine_url" value="Ссылка на двигатель" />
                    <TextInput
                        id="engine_url"
                        v-model="form.engine_url"
                        type="url"
                        class="mt-1 block w-full"
                        autocomplete="off"
                    />
                    <InputError class="mt-2" :message="form.errors.engine_url" />
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5">
            <div class="mb-4">
                <h3 class="text-base font-semibold text-gray-900">Технические характеристики</h3>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <InputLabel for="displacement_cc" value="Объем двигателя, куб. см" />
                    <TextInput
                        id="displacement_cc"
                        v-model="form.displacement_cc"
                        type="text"
                        inputmode="numeric"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.displacement_cc" />
                </div>

                <div>
                    <InputLabel for="max_horsepower" value="Максимальная мощность, л.с." />
                    <TextInput
                        id="max_horsepower"
                        v-model="form.max_horsepower"
                        type="text"
                        inputmode="decimal"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.max_horsepower" />
                </div>

                <div>
                    <InputLabel for="max_power_output_at_rpm" value="Мощность при об/мин" />
                    <TextInput
                        id="max_power_output_at_rpm"
                        v-model="form.max_power_output_at_rpm"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.max_power_output_at_rpm" />
                </div>

                <div>
                    <InputLabel for="max_torque_at_rpm" value="Крутящий момент при об/мин" />
                    <TextInput
                        id="max_torque_at_rpm"
                        v-model="form.max_torque_at_rpm"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.max_torque_at_rpm" />
                </div>

                <div>
                    <InputLabel for="valves_per_cylinder" value="Клапанов на цилиндр" />
                    <TextInput
                        id="valves_per_cylinder"
                        v-model="form.valves_per_cylinder"
                        type="text"
                        inputmode="numeric"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.valves_per_cylinder" />
                </div>

                <div>
                    <InputLabel for="compression_ratio" value="Степень сжатия" />
                    <TextInput
                        id="compression_ratio"
                        v-model="form.compression_ratio"
                        type="text"
                        inputmode="decimal"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.compression_ratio" />
                </div>

                <div>
                    <InputLabel for="cylinder_bore_mm" value="Диаметр цилиндра, мм" />
                    <TextInput
                        id="cylinder_bore_mm"
                        v-model="form.cylinder_bore_mm"
                        type="text"
                        inputmode="decimal"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.cylinder_bore_mm" />
                </div>

                <div>
                    <InputLabel for="piston_stroke_mm" value="Ход поршня, мм" />
                    <TextInput
                        id="piston_stroke_mm"
                        v-model="form.piston_stroke_mm"
                        type="text"
                        inputmode="decimal"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.piston_stroke_mm" />
                </div>

                <div class="md:col-span-2">
                    <InputLabel for="valvetrain" value="Привод клапанов" />
                    <TextInput
                        id="valvetrain"
                        v-model="form.valvetrain"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.valvetrain" />
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5">
            <div class="mb-4">
                <h3 class="text-base font-semibold text-gray-900">Топливо и экология</h3>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <InputLabel for="recommended_fuel_type" value="Рекомендуемое топливо" />
                    <TextInput
                        id="recommended_fuel_type"
                        v-model="form.recommended_fuel_type"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.recommended_fuel_type" />
                </div>

                <div>
                    <InputLabel for="has_start_stop_system" value="Система старт-стоп" />
                    <select
                        id="has_start_stop_system"
                        v-model="form.has_start_stop_system"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Не указано</option>
                        <option value="1">Да</option>
                        <option value="0">Нет</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.has_start_stop_system" />
                </div>

                <div>
                    <InputLabel for="fuel_consumption_l_per_100_km" value="Расход топлива, л/100 км" />
                    <TextInput
                        id="fuel_consumption_l_per_100_km"
                        v-model="form.fuel_consumption_l_per_100_km"
                        type="text"
                        inputmode="decimal"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.fuel_consumption_l_per_100_km" />
                </div>

                <div>
                    <InputLabel for="co2_emissions_g_per_km" value="Выбросы CO2, г/км" />
                    <TextInput
                        id="co2_emissions_g_per_km"
                        v-model="form.co2_emissions_g_per_km"
                        type="text"
                        inputmode="decimal"
                        class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" :message="form.errors.co2_emissions_g_per_km" />
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 p-5">
            <div class="mb-4">
                <h3 class="text-base font-semibold text-gray-900">Описание</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <InputLabel for="engine_notes" value="Дополнительная информация" />
                    <textarea
                        id="engine_notes"
                        v-model="form.engine_notes"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <InputError class="mt-2" :message="form.errors.engine_notes" />
                </div>

                <div>
                    <InputLabel for="page_text" value="Текст страницы" />
                    <textarea
                        id="page_text"
                        v-model="form.page_text"
                        rows="10"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <InputError class="mt-2" :message="form.errors.page_text" />
                </div>
            </div>
        </section>

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
