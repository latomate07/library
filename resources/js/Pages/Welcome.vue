<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm sticky top-0 z-40">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <h1 class="text-xl font-bold text-gray-900">
                        <i class="pi pi-book text-2xl text-blue-600"></i>
                        Library Catalog
                    </h1>
                    <div class="flex items-center space-x-4">
                        <template v-if="$page.props.auth?.user">
                            <span class="text-sm text-gray-600">{{ $page.props.auth.user.name }}</span>
                            <Button label="Admin" icon="pi pi-cog" @click="router.visit(route('dashboard'))"
                                size="small" outlined />
                        </template>
                        <template v-else>
                            <Button v-if="canLogin" label="Staff Login" icon="pi pi-sign-in"
                                @click="router.visit(route('login'))" size="small" outlined />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white border-b">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex items-center gap-4 justify-between">
                    <div class="flex items-center space-x-3">
                        <Button label="Books" icon="pi pi-book" @click="switchView('books')"
                            :severity="activeView === 'books' ? 'primary' : 'secondary'"
                            :outlined="activeView !== 'books'" size="small" />
                        <Button label="Authors" icon="pi pi-users" @click="switchView('authors')"
                            :severity="activeView === 'authors' ? 'primary' : 'secondary'"
                            :outlined="activeView !== 'authors'" size="small" />
                    </div>

                    <InputGroup class="w-64">
                        <InputText v-model="searchTerm"
                            :placeholder="activeView === 'books' ? 'Search books...' : 'Search authors...'"
                            class="text-sm" />
                        <InputGroupAddon>
                            <i class="pi pi-search text-gray-400"></i>
                        </InputGroupAddon>
                    </InputGroup>
                </div>
            </div>
        </div>

        <!-- Content -->
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center py-8">
                <i class="pi pi-spin pi-spinner text-2xl text-gray-400"></i>
            </div>

            <!-- Empty State -->
            <div v-else-if="displayedItems.length === 0" class="text-center py-16">
                <i :class="activeView === 'books' ? 'pi pi-book' : 'pi pi-users'"
                    class="text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    No {{ activeView }} found
                </h3>
                <p class="text-gray-500">Try a different search term.</p>
            </div>

            <!-- Books Grid -->
            <div v-else-if="activeView === 'books'"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <div v-for="book in (displayedItems as Book[])" :key="book.id" @click="openBookModal(book)"
                    class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div
                                class="h-12 w-9 bg-blue-100 rounded flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <i class="pi pi-book text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 line-clamp-2 text-sm mb-1">
                                {{ book.title }}
                            </h3>
                            <p class="text-xs text-gray-600 mb-2">
                                {{ book.author?.full_name || 'Unknown' }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-green-600">
                                    {{ formatCurrency(book.price) }}
                                </span>
                                <span class="text-xs text-gray-500 uppercase">
                                    {{ book.language }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Authors Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="author in (displayedItems as Author[])" :key="author.id" @click="openAuthorModal(author)"
                    class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div
                                class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                <span class="text-green-600 font-medium text-sm">
                                    {{ getInitials(author) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 text-sm mb-1">
                                {{ author.full_name }}
                            </h3>
                            <p class="text-xs text-gray-600 mb-2">
                                {{ author.nationality || 'Unknown origin' }}
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    {{ author.books_count || 0 }} {{ author.books_count === 1 ? 'book' : 'books' }}
                                </span>
                                <span v-if="author.birth_date" class="text-xs text-gray-500">
                                    {{ new Date(author.birth_date).getFullYear() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More Button -->
            <div v-if="hasMore && !loading" class="text-center mt-8">
                <Button label="Load More" icon="pi pi-chevron-down" @click="loadMore" outlined size="small" />
            </div>

            <!-- Loading More -->
            <div v-if="loadingMore" class="text-center mt-8">
                <i class="pi pi-spin pi-spinner text-lg text-gray-400"></i>
            </div>
        </main>

        <!-- Book Modal -->
        <Dialog v-model:visible="bookModalVisible" :style="{ width: '50vw' }" :modal="true"
            :header="selectedBook?.title">
            <div v-if="selectedBook" class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="h-24 w-16 bg-blue-100 rounded flex items-center justify-center flex-shrink-0">
                        <i class="pi pi-book text-blue-600 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ selectedBook.title }}</h3>
                        <p class="text-gray-600 mb-3">by {{ selectedBook.author?.full_name || 'Unknown Author' }}</p>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-900">Price:</span>
                                <span class="text-green-600 font-semibold ml-2">{{ formatCurrency(selectedBook.price)
                                    }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">Language:</span>
                                <span class="ml-2">{{ selectedBook.language.toUpperCase() }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">Published:</span>
                                <span class="ml-2">{{ formatDate(selectedBook.publication_date) }}</span>
                            </div>
                            <div v-if="selectedBook.pages">
                                <span class="font-medium text-gray-900">Pages:</span>
                                <span class="ml-2">{{ selectedBook.pages }}</span>
                            </div>
                        </div>
                        <div v-if="selectedBook.isbn" class="mt-3">
                            <span class="font-medium text-gray-900">ISBN:</span>
                            <span class="ml-2 font-mono text-sm">{{ selectedBook.isbn }}</span>
                        </div>
                    </div>
                </div>
                <Divider />
                <div v-if="selectedBook.description">
                    <h4 class="font-medium text-gray-900 mb-2">Description</h4>
                    <p class="text-gray-700 leading-relaxed">{{ selectedBook.description }}</p>
                </div>
            </div>
        </Dialog>

        <!-- Author Modal -->
        <Dialog v-model:visible="authorModalVisible" :style="{ width: '50vw' }" :modal="true"
            :header="selectedAuthor?.full_name">
            <div v-if="selectedAuthor" class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="h-20 w-20 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-green-600 font-semibold text-xl">
                            {{ getInitials(selectedAuthor) }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ selectedAuthor.full_name }}</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                            <div v-if="selectedAuthor.nationality">
                                <span class="font-medium text-gray-900">Nationality:</span>
                                <span class="ml-2">{{ selectedAuthor.nationality }}</span>
                            </div>
                            <div v-if="selectedAuthor.birth_date">
                                <span class="font-medium text-gray-900">Born:</span>
                                <span class="ml-2">{{ formatDate(selectedAuthor.birth_date) }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">Books:</span>
                                <span class="ml-2">{{ selectedAuthor.books_count || 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <Divider />
                <div v-if="selectedAuthor.biography">
                    <h4 class="font-medium text-gray-900 mb-2">Biography</h4>
                    <p class="text-gray-700 leading-relaxed">{{ selectedAuthor.biography }}</p>
                </div>
                <div v-if="selectedAuthor.books && selectedAuthor.books.length > 0">
                    <h4 class="font-medium text-gray-900 mb-3">Published Works</h4>
                    <div class="grid gap-2">
                        <div v-for="book in selectedAuthor.books" :key="book.id"
                            class="flex items-center justify-between p-2 bg-gray-50 rounded text-sm">
                            <span class="font-medium">{{ book.title }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import type { Book, Author } from '@/Types';
import { formatDate, formatCurrency } from '@/Utils/helpers';

interface Props {
    canLogin?: boolean;
    books: Book[];
    authors: Author[];
}

const props = defineProps<Props>();

// State
const activeView = ref<'books' | 'authors'>('books');
const searchTerm = ref('');
const loading = ref(false);
const loadingMore = ref(false);
const itemsPerPage = 20;
const currentPage = ref(1);

// Modals
const bookModalVisible = ref(false);
const authorModalVisible = ref(false);
const selectedBook = ref<Book | null>(null);
const selectedAuthor = ref<Author | null>(null);

// Computed
const filteredBooks = computed(() => {
    if (!searchTerm.value) return props.books || [];
    const search = searchTerm.value.toLowerCase();
    return props.books.filter(book =>
        book.title.toLowerCase().includes(search) ||
        book.author?.full_name.toLowerCase().includes(search) ||
        book.isbn?.toLowerCase().includes(search) ||
        book.description?.toLowerCase().includes(search)
    );
});

const filteredAuthors = computed(() => {
    if (!searchTerm.value) return props.authors || [];
    const search = searchTerm.value.toLowerCase();
    return props.authors.filter(author =>
        author.full_name.toLowerCase().includes(search) ||
        author.nationality?.toLowerCase().includes(search) ||
        author.biography?.toLowerCase().includes(search)
    );
});

const currentItems = computed(() => {
    return activeView.value === 'books' ? filteredBooks.value : filteredAuthors.value;
});

const totalItems = computed(() => currentItems.value.length);

const displayedItems = computed(() => {
    return currentItems.value.slice(0, currentPage.value * itemsPerPage);
});

const hasMore = computed(() => {
    return displayedItems.value.length < currentItems.value.length;
});

// Methods
const switchView = (view: 'books' | 'authors') => {
    activeView.value = view;
    currentPage.value = 1;
    searchTerm.value = '';
};

const loadMore = () => {
    loadingMore.value = true;
    // Simulate loading delay
    setTimeout(() => {
        currentPage.value++;
        loadingMore.value = false;
    }, 300);
};

const openBookModal = (book: Book) => {
    selectedBook.value = book;
    bookModalVisible.value = true;
};

const openAuthorModal = (author: Author) => {
    selectedAuthor.value = author;
    authorModalVisible.value = true;
};

const getInitials = (author: Author): string => {
    return `${author.first_name[0]}${author.last_name[0]}`;
};

// Infinite scroll
const handleScroll = () => {
    if (loadingMore.value || !hasMore.value) return;

    const scrollTop = window.pageYOffset;
    const windowHeight = window.innerHeight;
    const docHeight = document.documentElement.offsetHeight;

    if (scrollTop + windowHeight >= docHeight - 200) {
        loadMore();
    }
};

// Watchers
watch(searchTerm, () => {
    currentPage.value = 1;
});

// Lifecycle
onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>