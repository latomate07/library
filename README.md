# Library System

A comprehensive library management system built with Laravel, Inertia.js, Vue.js 3, TypeScript, and PrimeVue. This application provides a basic solution for managing books, authors, and user permissions with a modern, responsive interface.

## 🛠 Tech Stack

### Backend
- **Laravel 12**: PHP framework with modern features
- **SQLite**: Lightweight database for development
- **Spatie Laravel Permission**: Role and permission management
- **Inertia.js**: Modern monolith architecture

### Frontend
- **Vue.js 3**: Progressive JavaScript framework
- **TypeScript**: Type-safe JavaScript
- **PrimeVue**: Professional UI component library
- **Tailwind CSS**: Utility-first CSS framework
- **Vite**: Fast build tool and development server

### Testing
- **Pest PHP**: Modern testing framework

## 📋 Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js 18+ and npm
- SQLite extension for PHP

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/latomate07/library.git
cd library
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install JavaScript Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database (SQLite)
touch database/database.sqlite
```

### 5. Configure Environment Variables
Update your `.env` file:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/your/project/database/database.sqlite
```

### 6. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### 7. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Start the Application
```bash
# Start Laravel development server
php artisan serve

# In another terminal, watch for changes (development only)
npm run dev
```

Visit `http://localhost:8000` to access the application.

## 👥 Default Users

The seeder creates three default users:

| Role | Email | Password | Permissions |
|------|-------|----------|------------|
| Admin | admin@library.com | password | All permissions |
| Librarian | librarian@library.com | password | View, Create, Update books and authors |
| Viewer | viewer@library.com | password | View only |

## 🏗 Architecture

### Directory Structure
```
app/
├── Http/
│   ├── Controllers/          # Application controllers
│   │   ├── AuthorController.php
│   │   ├── BookController.php
│   │   └── HomeController.php
│   │   └── ProfileController.php
│   ├── Requests/            # Form request validation
│   │   ├── Author/StoreAuthorRequest.php
│   │   ├── Author/UpdateAuthorRequest.php
│   │   ├── Book/StoreBookRequest.php
│   │   └── Book/UpdateBookRequest.php
│   ├── Resources/           # API resources
│   │   ├── AuthorResource.php
│   │   └── BookResource.php
│   └── Middleware/          # Custom middleware
├── Models/                  # Eloquent models
│   ├── Author.php
│   ├── Book.php
│   └── User.php
└── Providers/              # Service providers

resources/
├── js/
│   ├── Components/         # Reusable Vue components
│   ├── Layouts/           # Layout components
│   ├── Pages/             # Inertia.js pages
│   │   ├── Authors/       # Author management pages
│   │   ├── Books/         # Book management pages
│   │   ├── Dashboard.vue  # Admin dashboard
│   │   └── Welcome.vue    # Public catalog
│   ├── types/             # TypeScript type definitions
│   └── utils/             # Utility functions
└── css/
    └── app.css            # Custom styles and PrimeVue overrides

database/
├── factories/             # Model factories
├── migrations/            # Database migrations
└── seeders/              # Database seeders

tests/
├── Feature/              # Feature tests
└── Unit/                # Unit tests
```

### Database Schema

#### Authors Table
- `id` - Primary key
- `first_name` - Author's first name
- `last_name` - Author's last name
- `biography` - Author's biography (optional)
- `birth_date` - Birth date (optional)
- `nationality` - Nationality (optional)
- `created_at`, `updated_at` - Timestamps

#### Books Table
- `id` - Primary key
- `title` - Book title
- `isbn` - ISBN number (optional, unique)
- `price` - Book price
- `publication_date` - Publication date
- `description` - Book description (optional)
- `pages` - Number of pages (optional)
- `language` - Book language
- `author_id` - Foreign key to authors table
- `created_at`, `updated_at` - Timestamps

### API Endpoints

#### Authors
- `GET /authors` - List all authors
- `POST /authors` - Create new author
- `GET /authors/{id}` - Get specific author
- `PUT /authors/{id}` - Update author
- `DELETE /authors/{id}` - Delete author

#### Books
- `GET /books` - List all books
- `POST /books` - Create new book
- `GET /books/{id}` - Get specific book
- `PUT /books/{id}` - Update book
- `DELETE /books/{id}` - Delete book

### Permissions System

The application uses Spatie Laravel Permission for role-based access control:

#### Roles
- **Admin**: Full system access
- **Librarian**: Can manage books and authors (no delete)
- **Viewer**: Read-only access

#### Permissions
- `view authors`, `create authors`, `update authors`, `delete authors`
- `view books`, `create books`, `update books`, `delete books`
- `manage library`

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run with Pest
./vendor/bin/pest

# Run specific test files
./vendor/bin/pest tests/Feature/AuthorTest.php
./vendor/bin/pest tests/Feature/BookTest.php

# Run tests with coverage
./vendor/bin/pest --coverage
```

## 🔗 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [PrimeVue Documentation](https://primevue.org/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [TypeScript Documentation](https://www.typescriptlang.org/)
- [API Documentation](http://localhost:8000/docs) (Make sure that the application runs on port 8000 and configured as mentionned at the top)