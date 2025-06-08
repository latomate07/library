<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tableau de bord
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-8">
                            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                                Management de la Bibliothèque
                            </h1>
                            <p class="text-lg text-gray-600">
                                Bienvenue sur le tableau de bord de gestion de la bibliothèque. Ici, vous pouvez
                                gérer les auteurs et les livres de la bibliothèque.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Authors Card -->
                            <Card class="hover:shadow-lg transition-shadow duration-300">
                                <template #content>
                                    <div class="flex items-center p-6">
                                        <div class="p-4 rounded-full bg-blue-100">
                                            <i class="pi pi-users text-3xl text-blue-600"></i>
                                        </div>
                                        <div class="ml-6">
                                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Auteurs</h3>
                                            <p class="text-gray-600 mb-4">Gérer les informations et les profils des auteurs</p>
                                            <Button v-if="canView('authors')" label="Voir les auteurs"
                                                icon="pi pi-arrow-right" @click="router.visit(route('authors.index'))"
                                                class="p-button-outlined" />
                                        </div>
                                    </div>
                                </template>
                            </Card>

                            <!-- Books Card -->
                            <Card class="hover:shadow-lg transition-shadow duration-300">
                                <template #content>
                                    <div class="flex items-center p-6">
                                        <div class="p-4 rounded-full bg-green-100">
                                            <i class="pi pi-book text-3xl text-green-600"></i>
                                        </div>
                                        <div class="ml-6">
                                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Livres</h3>
                                            <p class="text-gray-600 mb-4">Gérer votre catalogue de livres et vos stocks</p>
                                            <Button v-if="canView('books')" label="Voir les livres" icon="pi pi-arrow-right"
                                                @click="router.visit(route('books.index'))" class="p-button-outlined" />
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePermissions } from '@/Utils/permissions';

const { canView, canCreate } = usePermissions();
</script>