export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    roles?: string[];
    permissions?: string[];
}

export interface Author {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    biography?: string;
    birth_date?: string;
    nationality?: string;
    books_count?: number;
    books?: Book[];
    created_at: string;
    updated_at: string;
}

export interface Book {
    id: number;
    title: string;
    isbn?: string;
    price: number;
    publication_date: string;
    description?: string;
    pages?: number;
    language: string;
    author_id: number;
    author?: Author;
    created_at: string;
    updated_at: string;
}

export interface PaginatedData<T> {
    data: T[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
    current_page: number;
    from: number;
    last_page: number;
    per_page: number;
    to: number;
    total: number;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    flash: {
        success?: string;
        error?: string;
        warning?: string;
        info?: string;
    };
};