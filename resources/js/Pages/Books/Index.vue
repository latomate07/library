<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Books Management
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <template #content>
                        <Toolbar class="mb-6">
                            <template #start>
                                <h3 class="text-lg font-medium text-gray-900">Books</h3>
                            </template>
                            <template #end>
                                <Button
                                    v-if="canCreate('books')"
                                    label="Add Book"
                                    icon="pi pi-plus"
                                    @click="openNew"
                                    class="p-button-success"
                                />
                            </template>
                        </Toolbar>

                        <DataTable
                            :value="books.data"
                            :paginator="true"
                            :rows="15"
                            :totalRecords="books.total"
                            :lazy="true"
                            @page="onPage"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} books"
                            responsiveLayout="scroll"
                            :loading="loading"
                        >
                            <template #header>
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-xl text-900 font-bold">Books Library</span>
                                    <div class="flex space-x-4">
                                        <Dropdown
                                            v-model="selectedAuthor"
                                            :options="authorOptions"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Filter by Author"
                                            class="w-64"
                                            @change="onAuthorFilter"
                                            showClear
                                        />
                                        <InputGroup>
                                            <InputText 
                                                v-model="searchTerm" 
                                                placeholder="Search books..." 
                                                @input="onSearch"
                                            />
                                            <InputGroupAddon>
                                                <i class="pi pi-search"></i>
                                            </InputGroupAddon>
                                        </InputGroup>
                                    </div>
                                </div>
                            </template>

                            <Column field="title" header="Book" sortable>
                                <template #body="slotProps">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 h-12 w-8">
                                            <div class="h-12 w-8 rounded bg-gray-300 flex items-center justify-center">
                                                <i class="pi pi-book text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ slotProps.data.title }}
                                            </div>
                                            <div v-if="slotProps.data.isbn" class="text-sm text-gray-500">
                                                ISBN: {{ slotProps.data.isbn }}
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Column>

                            <Column field="author.full_name" header="Author" sortable>
                                <template #body="slotProps">
                                    <div class="text-sm text-gray-900">
                                        {{ slotProps.data.author?.full_name }}
                                    </div>
                                </template>
                            </Column>

                            <Column field="price" header="Price" sortable>
                                <template #body="slotProps">
                                    <Tag 
                                        :value="formatCurrency(slotProps.data.price)" 
                                        severity="success" 
                                    />
                                </template>
                            </Column>

                            <Column field="publication_date" header="Publication Date" sortable>
                                <template #body="slotProps">
                                    {{ formatDate(slotProps.data.publication_date) }}
                                </template>
                            </Column>

                            <Column field="language" header="Language">
                                <template #body="slotProps">
                                    <Tag 
                                        :value="slotProps.data.language.toUpperCase()" 
                                        severity="info" 
                                    />
                                </template>
                            </Column>

                            <Column header="Actions">
                                <template #body="slotProps">
                                    <div class="flex space-x-2">
                                        <Button
                                            icon="pi pi-eye"
                                            class="p-button-rounded p-button-text p-button-info"
                                            @click="viewBook(slotProps.data)"
                                            v-tooltip="'View'"
                                        />
                                        <Button
                                            v-if="canUpdate('books')"
                                            icon="pi pi-pencil"
                                            class="p-button-rounded p-button-text p-button-warning"
                                            @click="editBook(slotProps.data)"
                                            v-tooltip="'Edit'"
                                        />
                                        <Button
                                            v-if="canDelete('books')"
                                            icon="pi pi-trash"
                                            class="p-button-rounded p-button-text p-button-danger"
                                            @click="confirmDeleteBook(slotProps.data)"
                                            v-tooltip="'Delete'"
                                        />
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                </Card>

                <!-- Book Dialog -->
                <Dialog 
                    v-model:visible="bookDialog" 
                    :modal="true" 
                    :closable="true"
                    :style="{ width: '70vw' }"
                    :header="dialogTitle"
                >
                    <form @submit.prevent="saveBook" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="field md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Title *
                                </label>
                                <InputText
                                    id="title"
                                    v-model="form.title"
                                    required
                                    autofocus
                                    :class="{ 'p-invalid': form.errors.title }"
                                    class="w-full"
                                />
                                <small v-if="form.errors.title" class="p-error">
                                    {{ form.errors.title }}
                                </small>
                            </div>

                            <div class="field">
                                <label for="author_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Author *
                                </label>
                                <Dropdown
                                    id="author_id"
                                    v-model="form.author_id"
                                    :options="authors"
                                    optionLabel="full_name"
                                    optionValue="id"
                                    placeholder="Select an author"
                                    :class="{ 'p-invalid': form.errors.author_id }"
                                    class="w-full"
                                    filter
                                />
                                <small v-if="form.errors.author_id" class="p-error">
                                    {{ form.errors.author_id }}
                                </small>
                            </div>

                            <div class="field">
                                <label for="isbn" class="block text-sm font-medium text-gray-700 mb-2">
                                    ISBN
                                </label>
                                <InputText
                                    id="isbn"
                                    v-model="form.isbn"
                                    placeholder="9781234567890"
                                    :class="{ 'p-invalid': form.errors.isbn }"
                                    class="w-full"
                                />
                                <small v-if="form.errors.isbn" class="p-error">
                                    {{ form.errors.isbn }}
                                </small>
                            </div>

                            <div class="field">
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                    Price ($) *
                                </label>
                                <InputNumber
                                    id="price"
                                    v-model="form.price"
                                    mode="currency"
                                    currency="USD"
                                    locale="en-US"
                                    :min="0"
                                    :class="{ 'p-invalid': form.errors.price }"
                                    class="w-full"
                                />
                                <small v-if="form.errors.price" class="p-error">
                                    {{ form.errors.price }}
                                </small>
                            </div>

                            <div class="field">
                                <label for="publication_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Publication Date *
                                </label>
                                <Calendar
                                    id="publication_date"
                                    v-model="publicationDateModel"
                                    dateFormat="yy-mm-dd"
                                    showIcon
                                    :class="{ 'p-invalid': form.errors.publication_date }"
                                    class="w-full"
                                />
                                <small v-if="form.errors.publication_date" class="p-error">
                                    {{ form.errors.publication_date }}
                                </small>
                            </div>

                            <div class="field">
                                <label for="pages" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pages
                                </label>
                                <InputNumber
                                    id="pages"
                                    v-model="form.pages"
                                    :min="1"
                                    :class="{ 'p-invalid': form.errors.pages }"
                                    class="w-full"
                                />
                                <small v-if="form.errors.pages" class="p-error">
                                    {{ form.errors.pages }}
                                </small>
                            </div>

                            <div class="field">
                                <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                                    Language *
                                </label>
                                <Dropdown
                                    id="language"
                                    v-model="form.language"
                                    :options="languageOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Select language"
                                    :class="{ 'p-invalid': form.errors.language }"
                                    class="w-full"
                                />
                                <small v-if="form.errors.language" class="p-error">
                                    {{ form.errors.language }}
                                </small>
                            </div>
                        </div>

                        <div class="field">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                placeholder="Brief description of the book..."
                                :class="{ 'p-invalid': form.errors.description }"
                                class="w-full"
                            />
                            <small v-if="form.errors.description" class="p-error">
                                {{ form.errors.description }}
                            </small>
                        </div>
                    </form>

                    <template #footer>
                        <Button 
                            label="Cancel" 
                            icon="pi pi-times" 
                            @click="hideDialog" 
                            class="p-button-text" 
                        />
                        <Button 
                            :label="isEditing ? 'Update' : 'Create'"
                            icon="pi pi-check" 
                            @click="saveBook" 
                            :loading="form.processing"
                        />
                    </template>
                </Dialog>

                <!-- View Book Dialog -->
                <Dialog 
                    v-model:visible="viewDialog" 
                    :modal="true" 
                    :closable="true"
                    :style="{ width: '60vw' }"
                    header="Book Details"
                >
                    <div v-if="selectedBook" class="space-y-6">
                        <div class="flex items-start space-x-6 pb-6 border-b">
                            <div class="h-32 w-24 rounded bg-gray-300 flex items-center justify-center">
                                <i class="pi pi-book text-3xl text-gray-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ selectedBook.title }}</h3>
                                <p class="text-lg text-gray-600 mb-2">by {{ selectedBook.author?.full_name }}</p>
                                <div class="flex space-x-4">
                                    <Tag :value="formatCurrency(selectedBook.price)" severity="success" />
                                    <Tag :value="selectedBook.language.toUpperCase()" severity="info" />
                                    <Tag v-if="selectedBook.pages" :value="`${selectedBook.pages} pages`" severity="secondary" />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">ISBN</label>
                                <p class="text-gray-900">{{ selectedBook.isbn || 'Not available' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Publication Date</label>
                                <p class="text-gray-900">{{ formatDate(selectedBook.publication_date) }}</p>
                            </div>
                        </div>

                        <div v-if="selectedBook.description">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
                            <p class="text-gray-900 leading-relaxed">{{ selectedBook.description }}</p>
                        </div>
                    </div>

                    <template #footer>
                        <Button 
                            label="Close" 
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
import type { Book, Author, PaginatedData } from '@/Types';
import { usePermissions } from '@/Utils/permissions';
import { formatDate, formatCurrency, formatDateForInput } from '@/Utils/helpers';

interface Props {
    books: PaginatedData<Book>;
    authors: Author[];
    filters: {
        search?: string;
        author_id?: string;
    };
}

const props = defineProps<Props>();

const { canCreate, canUpdate, canDelete } = usePermissions();
const toast = useToast();
const confirm = useConfirm();

// Reactive data
const bookDialog = ref(false);
const viewDialog = ref(false);
const selectedBook = ref<Book | null>(null);
const loading = ref(false);
const searchTerm = ref(props.filters.search || '');
const selectedAuthor = ref(props.filters.author_id || '');
const isEditing = ref(false);

// Form handling
const form = useForm({
    title: '',
    isbn: '',
    price: 0,
    publication_date: '',
    description: '',
    pages: null as number | null,
    language: '',
    author_id: null as number | null,
});

const publicationDateModel = ref<Date | null>(null);

// Watch publication date changes
watch(publicationDateModel, (newDate) => {
    form.publication_date = newDate ? formatDateForInput(newDate) : '';
});

// Computed properties
const dialogTitle = computed(() => {
    return isEditing.value ? 'Edit Book' : 'Add New Book';
});

console.log('props.authors', props.authors);

const authorOptions = computed(() => {
    return [
        { label: 'All Authors', value: '' },
        ...props.authors.map(author => ({
            label: author.full_name,
            value: author.id.toString()
        }))
    ];
});

const languageOptions = [
    { label: 'English', value: 'en' },
    { label: 'French', value: 'fr' },
    { label: 'Spanish', value: 'es' },
    { label: 'German', value: 'de' },
    { label: 'Italian', value: 'it' },
    { label: 'Portuguese', value: 'pt' },
    { label: 'Russian', value: 'ru' },
    { label: 'Chinese', value: 'zh' },
    { label: 'Japanese', value: 'ja' },
];

// Methods
const openNew = () => {
    form.reset();
    form.clearErrors();
    publicationDateModel.value = null;
    isEditing.value = false;
    bookDialog.value = true;
};

const editBook = (book: Book) => {
    form.title = book.title;
    form.isbn = book.isbn || '';
    form.price = book.price;
    form.publication_date = book.publication_date;
    form.description = book.description || '';
    form.pages = book.pages;
    form.language = book.language;
    form.author_id = book.author_id;
    
    publicationDateModel.value = new Date(book.publication_date);
    
    selectedBook.value = book;
    isEditing.value = true;
    bookDialog.value = true;
};

const viewBook = (book: Book) => {
    selectedBook.value = book;
    viewDialog.value = true;
};

const hideDialog = () => {
    bookDialog.value = false;
    form.reset();
    form.clearErrors();
    selectedBook.value = null;
    publicationDateModel.value = null;
};

const saveBook = () => {
    if (isEditing.value && selectedBook.value) {
        form.put(route('books.update', selectedBook.value.id), {
            onSuccess: () => {
                hideDialog();
                toast.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Book updated successfully',
                    life: 3000
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to update book',
                    life: 3000
                });
            }
        });
    } else {
        form.post(route('books.store'), {
            onSuccess: () => {
                hideDialog();
                toast.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Book created successfully',
                    life: 3000
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to create book',
                    life: 3000
                });
            }
        });
    }
};

const confirmDeleteBook = (book: Book) => {
    confirm.require({
        message: `Are you sure you want to delete "${book.title}"?`,
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-secondary p-button-outlined',
        rejectLabel: 'Cancel',
        acceptLabel: 'Delete',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('books.destroy', book.id), {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'Book deleted successfully',
                        life: 3000
                    });
                },
                onError: () => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Failed to delete book',
                        life: 3000
                    });
                }
            });
        }
    });
};

const onPage = (event: any) => {
    router.get(route('books.index'), {
        page: event.page + 1,
        search: searchTerm.value,
        author_id: selectedAuthor.value
    }, {
        preserveState: true,
        replace: true
    });
};

const onSearch = () => {
    router.get(route('books.index'), {
        search: searchTerm.value,
        author_id: selectedAuthor.value
    }, {
        preserveState: true,
        replace: true
    });
};

const onAuthorFilter = () => {
    router.get(route('books.index'), {
        search: searchTerm.value,
        author_id: selectedAuthor.value
    }, {
        preserveState: true,
        replace: true
    });
};
</script>