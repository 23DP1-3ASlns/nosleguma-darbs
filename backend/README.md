# School Website - Laravel Backend

## Setup Instructions

### Requirements
- PHP 8.1+
- Composer
- MySQL 8.0+
- Laravel 10

### Installation

```bash
# 1. Navigate to backend directory
cd backend

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure your database in .env
#    DB_DATABASE=school_db
#    DB_USERNAME=your_username
#    DB_PASSWORD=your_password

# 6. Create database
mysql -u root -p -e "CREATE DATABASE school_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 7. Run migrations
php artisan migrate

# 8. Seed the database (creates admin user and sample data)
php artisan db:seed

# 9. Create storage symlink for file uploads
php artisan storage:link

# 10. Start the development server
php artisan serve --port=8000
```

### Default Admin Credentials
- **Email:** admin@skola.lv
- **Password:** Admin1234!

### Register Middleware
Add `AdminMiddleware` to your `app/Http/Kernel.php` under `$routeMiddleware`:
```php
'admin' => \App\Http\Middleware\AdminMiddleware::class,
```

### API Endpoints

#### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/login | Login |
| POST | /api/logout | Logout (auth required) |
| GET | /api/user | Get current user (auth required) |

#### Public
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/news | Get all news |
| GET | /api/schedule | Get schedule (filter: ?group=10.A) |
| GET | /api/schedule/groups | Get all group names |
| GET | /api/pages/{slug} | Get page content |

#### Admin (auth + admin role required)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/admin/news | Create news post |
| PUT | /api/admin/news/{id} | Update news post |
| DELETE | /api/admin/news/{id} | Delete news post |
| POST | /api/admin/schedule | Create schedule entry |
| PUT | /api/admin/schedule/{id} | Update schedule entry |
| DELETE | /api/admin/schedule/{id} | Delete schedule entry |
| PUT | /api/admin/pages/{slug} | Update page content |
| GET | /api/admin/users | List all users |
| POST | /api/admin/users | Create user |
| DELETE | /api/admin/users/{id} | Delete user |

### File Uploads
- News images are stored in `storage/app/public/news/`
- Access via: `http://localhost:8000/storage/news/filename.jpg`
- Max image size: 5MB
- Supported formats: jpeg, png, jpg, gif, webp
