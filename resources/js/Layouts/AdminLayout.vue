<script setup lang="ts">
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import type { PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, defineComponent, h, onMounted, ref, watch, type Component } from 'vue';

const SIDEBAR_STORAGE_KEY = 'admin-sidebar-collapsed';

const createOutlineIcon = (paths: Array<Record<string, string>>) =>
    defineComponent({
        name: 'SidebarIcon',
        render() {
            return h(
                'svg',
                {
                    viewBox: '0 0 24 24',
                    fill: 'none',
                    stroke: 'currentColor',
                    'stroke-width': '1.5',
                    'aria-hidden': 'true',
                    class: 'h-5 w-5 shrink-0',
                },
                paths.map((attributes) => h('path', attributes)),
            );
        },
    });

const Squares2x2Icon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M3.75 3.75h6v6h-6v-6Zm10.5 0h6v6h-6v-6Zm-10.5 10.5h6v6h-6v-6Zm10.5 0h6v6h-6v-6Z',
    },
]);

const TagIcon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'm9.568 3.051 8.25 8.25a2.25 2.25 0 0 1 0 3.182l-3.336 3.336a2.25 2.25 0 0 1-3.182 0l-8.25-8.25V5.25A2.25 2.25 0 0 1 5.3 3h4.268Z',
    },
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M6.75 6.75h.008v.008H6.75V6.75Z',
    },
]);

const BuildingStorefrontIcon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M3.75 9.776c0-.656.527-1.188 1.183-1.193 1.26-.01 2.22-.861 2.562-1.964.178-.574.7-.952 1.302-.952h6.407c.601 0 1.123.378 1.301.952.343 1.103 1.303 1.954 2.563 1.964.656.005 1.183.537 1.183 1.193V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18V9.776Z',
    },
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M9 14.25h6',
    },
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M12 5.25V3.75',
    },
]);

const LinkIcon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M13.19 8.688a4.5 4.5 0 0 1 0 6.364l-1.757 1.757a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757a4.5 4.5 0 0 1 6.364 0m-2.122 6.364a4.5 4.5 0 0 1 0-6.364l1.757-1.757a4.5 4.5 0 1 1 6.364 6.364l-1.757 1.757a4.5 4.5 0 0 1-6.364 0',
    },
]);

const CarIcon = defineComponent({
    name: 'CarIcon',
    render() {
        return h(
            'svg',
            {
                viewBox: '0 -960 960 960',
                fill: 'currentColor',
                'aria-hidden': 'true',
                class: 'h-5 w-5 shrink-0',
            },
            [
                h('path', {
                    d: 'M240-200v40q0 17-11.5 28.5T200-120h-40q-17 0-28.5-11.5T120-160v-320l84-240q6-18 21.5-29t34.5-11h440q19 0 34.5 11t21.5 29l84 240v320q0 17-11.5 28.5T800-120h-40q-17 0-28.5-11.5T720-160v-40H240Zm-8-360h496l-42-120H274l-42 120Zm-32 80v200-200Zm100 160q25 0 42.5-17.5T360-380q0-25-17.5-42.5T300-440q-25 0-42.5 17.5T240-380q0 25 17.5 42.5T300-320Zm360 0q25 0 42.5-17.5T720-380q0-25-17.5-42.5T660-440q-25 0-42.5 17.5T600-380q0 25 17.5 42.5T660-320Zm-460 40h560v-200H200v200Z',
                }),
            ],
        );
    },
});

const UserCircleIcon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.964 0a9 9 0 1 0-11.964 0m11.964 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75A3 3 0 1 1 9 9.75a3 3 0 0 1 6 0Z',
    },
]);

const ArrowRightOnRectangleIcon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m-3-3h9m0 0-3-3m3 3-3 3',
    },
]);

const ChevronDoubleLeftIcon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'm11.25 19.5-7.5-7.5 7.5-7.5m9 15-7.5-7.5 7.5-7.5',
    },
]);

const ChevronDoubleRightIcon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'm12.75 4.5 7.5 7.5-7.5 7.5m-9-15 7.5 7.5-7.5 7.5',
    },
]);

const ChevronRightIcon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'm9 5 7 7-7 7',
    },
]);

const Cog6ToothIcon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z',
    },
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
    },
]);

const ArrowUpTrayIcon = createOutlineIcon([
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5',
    },
    {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        d: 'M7.5 10.5 12 6m0 0 4.5 4.5M12 6v11.25',
    },
]);

type NavigationItem = {
    label: string;
    routeName: string;
    activePattern: string;
    icon: Component;
};

const showingNavigationDropdown = ref(false);
const sidebarCollapsed = ref(false);
const page = usePage<PageProps<{
    settings?: {
        brand_name?: string | null;
    };
}>>();

const navigationItems: NavigationItem[] = [
    {
        label: 'Панель управления',
        routeName: 'admin.dashboard',
        activePattern: 'admin.dashboard',
        icon: Squares2x2Icon,
    },
    {
        label: 'Бренды',
        routeName: 'admin.brands.index',
        activePattern: 'admin.brands.*',
        icon: TagIcon,
    },
    {
        label: 'Дилеры',
        routeName: 'admin.dealers.index',
        activePattern: 'admin.dealers.*',
        icon: BuildingStorefrontIcon,
    },
    {
        label: 'Связки дилеров',
        routeName: 'admin.car-dealers.index',
        activePattern: 'admin.car-dealers.*',
        icon: LinkIcon,
    },
    {
        label: 'Автомобили',
        routeName: 'admin.cars.index',
        activePattern: 'admin.cars.*',
        icon: CarIcon,
    },
    {
        label: 'Глобальные настройки',
        routeName: 'admin.settings.edit',
        activePattern: 'admin.settings.*',
        icon: Cog6ToothIcon,
    },
    {
        label: 'Импорт',
        routeName: 'admin.import.index',
        activePattern: 'admin.import.*',
        icon: ArrowUpTrayIcon,
    },
];

const sidebarWidthClasses = computed(() =>
    sidebarCollapsed.value ? 'lg:w-24' : 'lg:w-72',
);

const brandName = computed(() => page.props.settings?.brand_name?.trim() || 'carsDo');

const brandInitials = computed(() =>
    sidebarCollapsed.value ? brandName.value.slice(0, 2).toUpperCase() : brandName.value,
);

const desktopNavLinkClasses = (active: boolean) =>
    [
        'group flex items-center gap-3 rounded-lg py-3 text-sm font-medium transition',
        sidebarCollapsed.value ? 'justify-center px-3' : 'px-4',
        active
            ? 'bg-gray-900 text-white shadow-sm'
            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900',
    ].join(' ');

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};

onMounted(() => {
    if (typeof window === 'undefined') {
        return;
    }

    sidebarCollapsed.value = window.localStorage.getItem(SIDEBAR_STORAGE_KEY) === 'true';
});

watch(sidebarCollapsed, (value) => {
    if (typeof window === 'undefined') {
        return;
    }

    window.localStorage.setItem(SIDEBAR_STORAGE_KEY, String(value));
});
</script>

<template>
    <div class="min-h-screen overflow-x-hidden bg-gray-100 lg:flex">
        <aside
            class="hidden lg:flex lg:flex-col lg:border-r lg:border-gray-200 lg:bg-white lg:transition-[width] lg:duration-200"
            :class="sidebarWidthClasses"
        >
            <div
                class="flex items-center gap-3 border-b border-gray-200 py-5"
                :class="sidebarCollapsed ? 'justify-center px-3' : 'justify-between px-6'"
            >
                <div :class="sidebarCollapsed ? 'text-center' : ''">
                    <Link
                        :href="route('admin.dashboard')"
                        class="text-lg font-semibold text-gray-900"
                        v-tooltip="{ content: brandName, disabled: !sidebarCollapsed }"
                    >
                        {{ brandInitials }}
                    </Link>
                </div>

                <button
                    type="button"
                    class="inline-flex rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
                    v-tooltip="sidebarCollapsed ? 'Развернуть сайдбар' : 'Свернуть сайдбар'"
                    :aria-label="sidebarCollapsed ? 'Развернуть сайдбар' : 'Свернуть сайдбар'"
                    :aria-expanded="!sidebarCollapsed"
                    @click="toggleSidebar"
                >
                    <component :is="sidebarCollapsed ? ChevronDoubleRightIcon : ChevronDoubleLeftIcon" />
                </button>
            </div>

            <div class="flex flex-1 flex-col justify-between px-4 py-6">
                <nav class="space-y-2">
                    <Link
                        v-for="item in navigationItems"
                        :key="item.routeName"
                        :href="route(item.routeName)"
                        :class="desktopNavLinkClasses(route().current(item.activePattern))"
                        v-tooltip="{ content: item.label, disabled: !sidebarCollapsed }"
                        :aria-label="item.label"
                    >
                        <component :is="item.icon" />
                        <span v-if="!sidebarCollapsed" class="truncate">
                            {{ item.label }}
                        </span>
                    </Link>
                </nav>

                <div class="space-y-3">
                    <Link
                        href="/"
                        class="inline-flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-gray-900"
                    >
                        На сайт
                    </Link>

                    <Dropdown
                        :placement="sidebarCollapsed ? 'right' : 'bottom-end'"
                        align="right"
                        width="48"
                        content-classes="py-1 bg-white"
                    >
                        <template #trigger>
                            <button
                                type="button"
                                class="inline-flex w-full items-center rounded-lg border border-gray-200 bg-white text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300"
                                :class="sidebarCollapsed ? 'justify-center px-2 py-2' : 'justify-between px-3 py-3'"
                                :aria-label="`Опции пользователя ${$page.props.auth.user.name}`"
                            >
                                <span v-if="!sidebarCollapsed" class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-900 text-sm font-semibold text-white"
                                    >
                                        {{ $page.props.auth.user.name.slice(0, 2).toUpperCase() }}
                                    </span>
                                    <span class="min-w-0 text-start">
                                        <span class="block truncate text-sm font-semibold text-gray-900">
                                            {{ $page.props.auth.user.name }}
                                        </span>
                                        <span class="block truncate text-xs text-gray-500">
                                            {{ $page.props.auth.user.email }}
                                        </span>
                                    </span>
                                </span>

                                <span v-else class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-900 text-sm font-semibold text-white">
                                    {{ $page.props.auth.user.name.slice(0, 2).toUpperCase() }}
                                </span>

                                <ChevronRightIcon
                                    class="h-4 w-4 shrink-0 text-gray-400 transition"
                                    :class="sidebarCollapsed ? 'ms-1' : ''"
                                />
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')">
                                <span class="flex items-center gap-2">
                                    <UserCircleIcon />
                                    <span>Профиль</span>
                                </span>
                            </DropdownLink>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="block w-full px-4 py-2 text-start text-sm leading-5 text-red-600 transition duration-150 ease-in-out hover:bg-red-50 focus:bg-red-50 focus:outline-none"
                            >
                                <span class="flex items-center gap-2">
                                    <ArrowRightOnRectangleIcon />
                                    <span>Выйти</span>
                                </span>
                            </Link>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </aside>

        <div class="flex min-h-screen min-w-0 flex-1 flex-col">
            <nav class="border-b border-gray-200 bg-white lg:hidden">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6">
                    <div>
                        <Link :href="route('admin.dashboard')" class="text-lg font-semibold text-gray-900">
                            {{ brandName }}
                        </Link>
                    </div>

                    <button
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="border-t border-gray-200 lg:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink href="/">
                            На сайт
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-for="item in navigationItems"
                            :key="item.routeName"
                            :href="route(item.routeName)"
                            :active="route().current(item.activePattern)"
                        >
                            {{ item.label }}
                        </ResponsiveNavLink>
                    </div>

                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Профиль
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Выйти
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <header v-if="$slots.header" class="border-b border-gray-200 bg-white">
                <div class="px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
