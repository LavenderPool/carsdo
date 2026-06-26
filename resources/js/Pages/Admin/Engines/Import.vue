<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const MAX_FILE_SIZE_BYTES = 100 * 1024 * 1024;
const POLL_INTERVAL_MS = 2000;

type ImportStatus = 'queued' | 'running' | 'succeeded' | 'failed';

interface ImportStats {
    new: number;
    updated: number;
    unchanged: number;
    processed: number;
}

interface EngineImportRun {
    id: number;
    status: ImportStatus;
    original_file_name: string;
    file_size: number;
    message: string | null;
    current_stage: string | null;
    total_engines: number;
    processed_engines: number;
    stats: ImportStats;
    error_message: string | null;
    started_at: string | null;
    finished_at: string | null;
    created_at: string | null;
    updated_at: string | null;
}

interface ImportRunResponse {
    message?: string;
    run: EngineImportRun;
}

const props = defineProps<{
    activeRun?: EngineImportRun | null;
}>();

const selectedFile = ref<File | null>(null);
const selectedFileName = ref<string | null>(null);
const activeRun = ref<EngineImportRun | null>(props.activeRun ?? null);
const isImporting = ref(
    props.activeRun !== null
    && props.activeRun !== undefined
    && !['succeeded', 'failed'].includes(props.activeRun.status),
);
const phaseMessage = ref<string | null>(props.activeRun?.message ?? null);
const errorMessage = ref<string | null>(
    props.activeRun?.status === 'failed'
        ? props.activeRun.error_message
        : null,
);

let pollingTimer: number | null = null;

const hasResults = computed(() => activeRun.value !== null || errorMessage.value !== null);

const fileSizeLabel = computed(() => {
    if (selectedFile.value === null) {
        return null;
    }

    return formatBytes(selectedFile.value.size);
});

const progressPercent = computed(() => {
    const totalEngines = activeRun.value?.total_engines ?? 0;

    if (totalEngines <= 0) {
        return 0;
    }

    return Math.round(((activeRun.value?.processed_engines ?? 0) / totalEngines) * 100);
});

const statusLabel = computed(() => {
    switch (activeRun.value?.status) {
        case 'queued':
            return 'В очереди';
        case 'running':
            return 'Выполняется';
        case 'succeeded':
            return 'Завершен';
        case 'failed':
            return 'Ошибка';
        default:
            return 'Не запущен';
    }
});

const stageLabel = computed(() => {
    switch (activeRun.value?.current_stage) {
        case 'reading_file':
            return 'Чтение файла';
        case 'persisting_engines':
            return 'Импорт двигателей';
        case 'completed':
            return 'Завершено';
        case 'failed':
            return 'Ошибка';
        default:
            return 'Подготовка';
    }
});

const isTerminalStatus = (status: ImportStatus): boolean => (
    status === 'succeeded'
    || status === 'failed'
);

const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    selectedFile.value = file;
    selectedFileName.value = file?.name ?? null;
    errorMessage.value = null;
};

const submit = async () => {
    if (selectedFile.value === null || isImporting.value) {
        return;
    }

    validateSelectedFile(selectedFile.value);

    stopPolling();
    activeRun.value = null;
    errorMessage.value = null;
    isImporting.value = true;
    phaseMessage.value = 'Загрузка JSON-файла...';

    try {
        const formData = new FormData();
        formData.append('file', selectedFile.value);

        const response = await fetch(route('admin.engines.import.store'), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': resolveCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        const payload = await parseJsonResponse(response, 'Сервер вернул неожиданный ответ при запуске импорта.');

        if (!response.ok) {
            throw new Error(payload?.message ?? 'Не удалось запустить импорт двигателей.');
        }

        const importRun = (payload as ImportRunResponse).run;

        applyRun(importRun);

        if (isTerminalStatus(importRun.status)) {
            isImporting.value = false;
        } else {
            schedulePolling(importRun.id);
        }
    } catch (error) {
        errorMessage.value = resolveErrorMessage(error);
        phaseMessage.value = 'Импорт не был запущен.';
        isImporting.value = false;
    }
};

const schedulePolling = (importRunId: number) => {
    stopPolling();

    pollingTimer = window.setTimeout(async () => {
        try {
            const importRun = await fetchRun(importRunId);

            applyRun(importRun);

            if (isTerminalStatus(importRun.status)) {
                stopPolling();
                isImporting.value = false;
                return;
            }

            schedulePolling(importRunId);
        } catch (error) {
            stopPolling();
            isImporting.value = false;
            errorMessage.value = resolveErrorMessage(error);
            phaseMessage.value = 'Не удалось обновить статус импорта.';
        }
    }, POLL_INTERVAL_MS);
};

const stopPolling = () => {
    if (pollingTimer !== null) {
        window.clearTimeout(pollingTimer);
        pollingTimer = null;
    }
};

const fetchRun = async (importRunId: number): Promise<EngineImportRun> => {
    const response = await fetch(route('admin.engines.import.status', importRunId), {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    const payload = await parseJsonResponse(response, 'Сервер вернул неожиданный ответ при получении статуса импорта.');

    if (!response.ok) {
        throw new Error(payload?.message ?? 'Не удалось получить статус импорта.');
    }

    return (payload as ImportRunResponse).run;
};

const applyRun = (importRun: EngineImportRun) => {
    activeRun.value = importRun;
    phaseMessage.value = importRun.message ?? null;

    if (importRun.status === 'failed' && importRun.error_message) {
        errorMessage.value = importRun.error_message;
        return;
    }

    errorMessage.value = null;
};

const validateSelectedFile = (file: File) => {
    if (file.size > MAX_FILE_SIZE_BYTES) {
        throw new Error('Размер JSON-файла не должен превышать 100 МБ.');
    }
};

const parseJsonResponse = async (response: Response, fallbackMessage: string) => {
    const contentType = response.headers.get('content-type') ?? '';

    if (response.redirected || !contentType.includes('application/json')) {
        throw new Error(fallbackMessage);
    }

    return await response.json().catch(() => null) as { message?: string } | ImportRunResponse | null;
};

const resolveCsrfToken = (): string => {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (!token) {
        throw new Error('Не найден CSRF-токен для отправки запроса.');
    }

    return token;
};

const formatBytes = (value: number): string => {
    const units = ['Б', 'КБ', 'МБ', 'ГБ'];
    let size = value;
    let unitIndex = 0;

    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }

    const fractionDigits = unitIndex === 0 ? 0 : 1;

    return `${size.toFixed(fractionDigits)} ${units[unitIndex]}`;
};

const resolveErrorMessage = (error: unknown): string => {
    if (error instanceof Error) {
        return error.message;
    }

    return 'Во время импорта произошла непредвиденная ошибка.';
};

onBeforeUnmount(() => {
    stopPolling();
});

onMounted(() => {
    if (activeRun.value !== null && !isTerminalStatus(activeRun.value.status)) {
        schedulePolling(activeRun.value.id);
    }
});
</script>

<template>
    <Head title="Импорт двигателей" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Импорт двигателей
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Загрузите JSON-файл с двигателями. Импорт выполнится в фоне на сервере.
                    </p>
                </div>

                <Link
                    :href="route('admin.engines.index')"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    К списку двигателей
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <form class="space-y-6" @submit.prevent="submit">
                        <div class="space-y-2">
                            <InputLabel for="engine-import-json" value="JSON файл с двигателями" />
                            <input
                                id="engine-import-json"
                                type="file"
                                accept=".json,application/json"
                                class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700"
                                :disabled="isImporting"
                                @change="onFileChange"
                            />
                            <p class="text-sm text-gray-500">
                                Поддерживается один JSON-файл до 100 МБ.
                            </p>
                            <p v-if="selectedFileName" class="text-sm text-gray-600">
                                Выбран файл: {{ selectedFileName }}
                                <span v-if="fileSizeLabel">({{ fileSizeLabel }})</span>
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="!selectedFile || isImporting">
                                {{ isImporting ? 'Импорт запускается...' : 'Запустить импорт' }}
                            </PrimaryButton>
                            <span v-if="phaseMessage" class="text-sm text-gray-500">
                                {{ phaseMessage }}
                            </span>
                        </div>
                    </form>
                </div>

                <div v-if="activeRun" class="grid gap-6 lg:grid-cols-2">
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">
                                Статус импорта
                            </h3>
                            <span class="text-sm text-gray-600">
                                {{ statusLabel }}
                            </span>
                        </div>

                        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-lg bg-gray-50 px-4 py-3">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">
                                    Файл
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">
                                    {{ activeRun.original_file_name }}
                                </dd>
                            </div>
                            <div class="rounded-lg bg-gray-50 px-4 py-3">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">
                                    Размер
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">
                                    {{ formatBytes(activeRun.file_size) }}
                                </dd>
                            </div>
                            <div class="rounded-lg bg-gray-50 px-4 py-3">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">
                                    Этап
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">
                                    {{ stageLabel }}
                                </dd>
                            </div>
                            <div class="rounded-lg bg-gray-50 px-4 py-3">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">
                                    Всего двигателей
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">
                                    {{ activeRun.total_engines }}
                                </dd>
                            </div>
                        </dl>

                        <div v-if="activeRun.total_engines > 0" class="mt-6">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span>Обработано двигателей</span>
                                <span>{{ activeRun.processed_engines }}/{{ activeRun.total_engines }}</span>
                            </div>
                            <div class="mt-3 h-3 overflow-hidden rounded-full bg-gray-200">
                                <div
                                    class="h-full rounded-full bg-indigo-600 transition-all"
                                    :style="{ width: `${progressPercent}%` }"
                                />
                            </div>
                            <p class="mt-3 text-sm text-gray-500">
                                {{ progressPercent }}% завершено
                            </p>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Сообщение сервера
                        </h3>
                        <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                            {{ activeRun.message ?? 'Ожидание обновления статуса...' }}
                        </div>
                        <div
                            v-if="activeRun.error_message"
                            class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                        >
                            {{ activeRun.error_message }}
                        </div>
                    </div>
                </div>

                <div v-if="hasResults" class="grid gap-6 lg:grid-cols-[2fr,1fr]">
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <div
                            v-if="activeRun?.status === 'succeeded'"
                            class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                        >
                            Импорт двигателей завершен успешно.
                        </div>

                        <div
                            v-if="errorMessage"
                            class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                        >
                            {{ errorMessage }}
                        </div>

                        <h3 class="text-sm font-semibold text-gray-900">
                            Детали
                        </h3>

                        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-lg border border-gray-200 px-4 py-3">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">
                                    Статус
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">
                                    {{ statusLabel }}
                                </dd>
                            </div>
                            <div class="rounded-lg border border-gray-200 px-4 py-3">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">
                                    Последнее обновление
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">
                                    {{ activeRun?.updated_at ?? '—' }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Статистика
                        </h3>

                        <dl class="mt-4 space-y-4">
                            <div class="rounded-lg bg-gray-50 px-4 py-3">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">
                                    Новых
                                </dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                    {{ activeRun?.stats.new ?? 0 }}
                                </dd>
                            </div>

                            <div class="rounded-lg bg-gray-50 px-4 py-3">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">
                                    Изменено
                                </dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                    {{ activeRun?.stats.updated ?? 0 }}
                                </dd>
                            </div>

                            <div class="rounded-lg bg-gray-50 px-4 py-3">
                                <dt class="text-xs uppercase tracking-wide text-gray-500">
                                    Не тронуто
                                </dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                    {{ activeRun?.stats.unchanged ?? 0 }}
                                </dd>
                            </div>

                            <div class="rounded-lg bg-gray-900 px-4 py-3 text-white">
                                <dt class="text-xs uppercase tracking-wide text-gray-300">
                                    Всего обработано
                                </dt>
                                <dd class="mt-1 text-2xl font-semibold">
                                    {{ activeRun?.stats.processed ?? 0 }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
