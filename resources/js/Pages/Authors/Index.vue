<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestion des Auteurs
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <template #content>
                        <Toolbar class="mb-6">
                            <template #start>
                                <h3 class="text-lg font-medium text-gray-900">
                                    Auteurs
                                </h3>
                            </template>
                            <template #end>
                                <Button
                                    v-if="canCreate('authors')"
                                    label="Nouveau"
                                    icon="pi pi-plus"
                                    @click="openNew"
                                    class="p-button-success"
                                />
                            </template>
                        </Toolbar>

                        <DataTable
                            :value="authors.data"
                            :paginator="true"
                            :rows="15"
                            :totalRecords="authors.total"
                            :lazy="true"
                            @page="onPage"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            currentPageReportTemplate="Affichage {first} à {last} de {totalRecords} auteurs"
                            responsiveLayout="scroll"
                            :loading="loading"
                        >
                            <template #header>
                                <div class="flex justify-between items-center">
                                    <span class="text-xl text-900 font-bold">
                                        Liste des auteurs
                                    </span>
                                    <InputGroup>
                                        <InputText 
                                            v-model="searchTerm" 
                                            placeholder="Trouver un auteur..." 
                                            @input="onSearch"
                                        />
                                        <InputGroupAddon>
                                            <i class="pi pi-search"></i>
                                        </InputGroupAddon>
                                    </InputGroup>
                                </div>
                            </template>

                            <Column field="full_name" header="Nom" sortable>
                                <template #body="slotProps">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ getInitials(slotProps.data) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ slotProps.data.full_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ slotProps.data.nationality || 'Non spécifié' }}
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Column>

                            <Column field="birth_date" header="Date de naissance" sortable>
                                <template #body="slotProps">
                                    {{ slotProps.data.birth_date ? formatDate(slotProps.data.birth_date) : '-' }}
                                </template>
                            </Column>

                            <Column field="books_count" header="Livres" sortable>
                                <template #body="slotProps">
                                    <Tag :value="slotProps.data.books_count || 0" severity="info" />
                                </template>
                            </Column>

                            <Column header="Actions">
                                <template #body="slotProps">
                                    <div class="flex space-x-2">
                                        <Button
                                            icon="pi pi-eye"
                                            class="p-button-rounded p-button-text p-button-info"
                                            @click="viewAuthor(slotProps.data)"
                                            v-tooltip="'View'"
                                        />
                                        <Button
                                            v-if="canUpdate('authors')"
                                            icon="pi pi-pencil"
                                            class="p-button-rounded p-button-text p-button-warning"
                                            @click="editAuthor(slotProps.data)"
                                            v-tooltip="'Edit'"
                                        />
                                        <Button
                                            v-if="canDelete('authors')"
                                            icon="pi pi-trash"
                                            class="p-button-rounded p-button-text p-button-danger"
                                            @click="confirmDeleteAuthor(slotProps.data)"
                                            v-tooltip="'Supprimer'"
                                        />
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                </Card>

                <!-- Author Dialog -->
                <Dialog 
                    v-model:visible="authorDialog" 
                    :modal="true" 
                    :closable="true"
                    :style="{ width: '50vw' }"
                    :header="dialogTitle"
                >
                    <form @submit.prevent="saveAuthor" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="field">
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Prénom *
                                </label>
                                <InputText
                                    id="first_name"
                                    v-model="form.first_name"
                                    required
                                    autofocus
                                    :class="{ 'p-invalid': form.errors.first_name }"
                                    class="w-full"
                                />
                                <small v-if="form.errors.first_name" class="p-error">
                                    {{ form.errors.first_name }}
                                </small>
                            </div>

                            <div class="field">
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom *
                                </label>
                                <InputText
                                    id="last_name"
                                    v-model="form.last_name"
                                    required
                                    :class="{ 'p-invalid': form.errors.last_name }"
                                    class="w-full"
                                />
                                <small v-if="form.errors.last_name" class="p-error">
                                    {{ form.errors.last_name }}
                                </small>
                            </div>

                            <div class="field">
                                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date de naissance
                                </label>
                                <Calendar
                                    id="birth_date"
                                    v-model="birthDateModel"
                                    dateFormat="yy-mm-dd"
                                    showIcon
                                    :class="{ 'p-invalid': form.errors.birth_date }"
                                    class="w-full"
                                />
                                <small v-if="form.errors.birth_date" class="p-error">
                                    {{ form.errors.birth_date }}
                                </small>
                            </div>

                            <div class="field">
                                <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nationalité
                                </label>
                                <InputText
                                    id="nationality"
                                    v-model="form.nationality"
                                    :class="{ 'p-invalid': form.errors.nationality }"
                                    class="w-full"
                                />
                                <small v-if="form.errors.nationality" class="p-error">
                                    {{ form.errors.nationality }}
                                </small>
                            </div>
                        </div>

                        <div class="field">
                            <label for="biography" class="block text-sm font-medium text-gray-700 mb-2">
                                Biographie
                            </label>
                            <Textarea
                                id="biography"
                                v-model="form.biography"
                                rows="4"
                                :class="{ 'p-invalid': form.errors.biography }"
                                class="w-full"
                            />
                            <small v-if="form.errors.biography" class="p-error">
                                {{ form.errors.biography }}
                            </small>
                        </div>
                    </form>

                    <template #footer>
                        <Button 
                            label="Annuler" 
                            icon="pi pi-times" 
                            @click="hideDialog" 
                            class="p-button-text" 
                        />
                        <Button 
                            :label="isEditing ? 'Mettre à jour' : 'Créer'"
                            icon="pi pi-check" 
                            @click="saveAuthor" 
                            :loading="form.processing"
                        />
                    </template>
                </Dialog>

                <!-- View Author Dialog -->
                <Dialog 
                    v-model:visible="viewDialog" 
                    :modal="true" 
                    :closable="true"
                    :style="{ width: '50vw' }"
                    header="Détails de l'Auteur"
                >
                    <div v-if="selectedAuthor" class="space-y-4">
                        <div class="flex items-center space-x-4 pb-4 border-b">
                            <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-lg font-medium text-gray-700">
                                    {{ getInitials(selectedAuthor) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold">{{ selectedAuthor.full_name }}</h3>
                                <p class="text-gray-600">{{ selectedAuthor.nationality || 'Non spécifié' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Date de naissance</label>
                                <p>{{ selectedAuthor.birth_date ? formatDate(selectedAuthor.birth_date) : 'Non spécifié' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Nombre de livres</label>
                                <p>{{ selectedAuthor.books_count || 0 }} livres</p>
                            </div>
                        </div>

                        <div v-if="selectedAuthor.biography">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Biographie</label>
                            <p class="text-gray-900">{{ selectedAuthor.biography }}</p>
                        </div>
                    </div>

                    <template #footer>
                        <Button 
                            label="Fermer" 
                            icon="pi pi-times" 
                            @click="viewDialog = false" 
                            class="p-button-text" 
                        />
                    </template>
                </Dialog>
            </div>
        </div>

        <Toast />
        <ConfirmDialog />
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import type { Author, PaginatedData } from '@/Types';
import { usePermissions } from '@/Utils/permissions';
import { formatDate, formatDateForInput } from '@/Utils/helpers';

interface Props {
    authors: PaginatedData<Author>;
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

console.log('Authors:', props.authors);

const { canCreate, canUpdate, canDelete } = usePermissions();
const toast = useToast();
const confirm = useConfirm();

// Reactive data
const authorDialog = ref(false);
const viewDialog = ref(false);
const selectedAuthor = ref<Author | null>(null);
const loading = ref(false);
const searchTerm = ref(props.filters.search || '');
const isEditing = ref(false);

// Form handling
const form = useForm({
    first_name: '',
    last_name: '',
    biography: '',
    birth_date: '',
    nationality: '',
});

const birthDateModel = ref<Date | null>(null);

// Watch birth date changes
watch(birthDateModel, (newDate) => {
    form.birth_date = newDate ? formatDateForInput(newDate) : '';
});

// Computed properties
const dialogTitle = computed(() => {
    return isEditing.value ? 'Modifier un auteur' : 'Nouveau';
});

// Methods
const getInitials = (author: Author): string => {
    return `${author.first_name[0]}${author.last_name[0]}`;
};

const openNew = () => {
    form.reset();
    form.clearErrors();
    birthDateModel.value = null;
    isEditing.value = false;
    authorDialog.value = true;
};

const editAuthor = (author: Author) => {
    form.first_name = author.first_name;
    form.last_name = author.last_name;
    form.biography = author.biography || '';
    form.birth_date = author.birth_date || '';
    form.nationality = author.nationality || '';
    
    birthDateModel.value = author.birth_date ? new Date(author.birth_date) : null;
    
    selectedAuthor.value = author;
    isEditing.value = true;
    authorDialog.value = true;
};

const viewAuthor = (author: Author) => {
    selectedAuthor.value = author;
    viewDialog.value = true;
};

const hideDialog = () => {
    authorDialog.value = false;
    form.reset();
    form.clearErrors();
    selectedAuthor.value = null;
    birthDateModel.value = null;
};

const saveAuthor = () => {
    if (isEditing.value && selectedAuthor.value) {
        form.put(route('authors.update', selectedAuthor.value.id), {
            onSuccess: () => {
                hideDialog();
                toast.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Auteur mis à jour avec succès',
                    life: 3000
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'La mise à jour de l\'auteur a échoué',
                    life: 3000
                });
            }
        });
    } else {
        form.post(route('authors.store'), {
            onSuccess: () => {
                hideDialog();
                toast.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Auteur créé avec succès',
                    life: 3000
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Échec de la création d\'un auteur',
                    life: 3000
                });
            }
        });
    }
};

const confirmDeleteAuthor = (author: Author) => {
    confirm.require({
        message: `Êtes-vous sûr de vouloir supprimer ${author.full_name}?`,
        header: 'Supprimer Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-secondary p-button-outlined',
        rejectLabel: 'Annuler',
        acceptLabel: 'Supprimer',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('authors.destroy', author.id), {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'Auteur supprimé avec succès',
                        life: 3000
                    });
                },
                onError: () => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Échec de la suppression de l\'auteur',
                        life: 3000
                    });
                }
            });
        }
    });
};

const onPage = (event: any) => {
    router.get(route('authors.index'), {
        page: event.page + 1,
        search: searchTerm.value
    }, {
        preserveState: true,
        replace: true
    });
};

const onSearch = () => {
    router.get(route('authors.index'), {
        search: searchTerm.value
    }, {
        preserveState: true,
        replace: true
    });
};
</script>