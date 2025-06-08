<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('home')">
                                <div class="flex items-center space-x-2">
                                    <i class="pi pi-book text-2xl text-blue-600"></i>
                                    <span class="text-xl font-bold text-gray-900">Library</span>
                                </div>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    <i class="pi pi-home mr-2"></i>
                                    Tableau de bord
                                </NavLink>

                                <NavLink v-if="canView('authors')" :href="route('authors.index')"
                                    :active="route().current('authors.*')">
                                    <i class="pi pi-users mr-2"></i>
                                    Auteurs
                                </NavLink>

                                <NavLink v-if="canView('books')" :href="route('books.index')"
                                    :active="route().current('books.*')">
                                    <i class="pi pi-book mr-2"></i>
                                    Livres
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ml-6 sm:flex sm:items-center">
                            <!-- User Info -->
                            <div class="mr-4 text-sm text-gray-700">
                                <span class="font-medium">{{ $page.props.auth.user.name }}</span>
                                <span class="text-gray-500 ml-2">
                                    ({{ getUserRole() }})
                                </span>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="relative ml-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                                <i class="pi pi-user mr-2"></i>
                                                {{ $page.props.auth.user.name }}

                                                <svg class="-mr-0.5 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            <i class="pi pi-user mr-2"></i>
                                            Profil
                                        </DropdownLink>

                                        <!-- Authentication -->
                                        <DropdownLink as="button" :href="route('logout')" method="post">
                                            <i class="pi pi-sign-out mr-2"></i>
                                            Déconnexion
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            <i class="pi pi-home mr-2"></i>
                            Dashboard
                        </ResponsiveNavLink>

                        <ResponsiveNavLink v-if="canView('authors')" :href="route('authors.index')"
                            :active="route().current('authors.*')">
                            <i class="pi pi-users mr-2"></i>
                            Auteurs
                        </ResponsiveNavLink>

                        <ResponsiveNavLink v-if="canView('books')" :href="route('books.index')"
                            :active="route().current('books.*')">
                            <i class="pi pi-book mr-2"></i>
                            Livres
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
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
                                <i class="pi pi-user mr-2"></i>
                                Profil
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <Link :href="route('logout')" method="post" as="button"
                                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Déconnexion</Link>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Flash Messages -->
            <div v-if="hasFlashMessages" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
                <div v-if="$page.props.flash.success" class="mb-4">
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="pi pi-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ $page.props.flash.success }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="$page.props.flash.error" class="mb-4">
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="pi pi-times-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">
                                    {{ $page.props.flash.error }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="$page.props.flash.warning" class="mb-4">
                    <div class="rounded-md bg-yellow-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="pi pi-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800">
                                    {{ $page.props.flash.warning }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="$page.props.flash.info" class="mb-4">
                    <div class="rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="pi pi-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-800">
                                    {{ $page.props.flash.info }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { usePermissions } from '@/Utils/permissions';
import type { PageProps } from '@/Types';

const { canView } = usePermissions();
const page = usePage<PageProps>();

const showingNavigationDropdown = ref(false);

const logout = () => {
    console.log(route('logout'));
    router.post(route('logout'));
};

const getUserRole = (): string => {
    const user = page.props.auth.user;
    if (user?.roles && user.roles.length > 0) {
        return user.roles[0].charAt(0).toUpperCase() + user.roles[0].slice(1);
    }
    return 'User';
};

const hasFlashMessages = computed(() => {
    const flash = page.props.flash;
    return flash.success || flash.error || flash.warning || flash.info;
});
</script>