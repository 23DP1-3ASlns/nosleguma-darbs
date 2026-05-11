# 🏫 Vidusskola — Full-Stack School Website

A complete, production-ready school website built with **Vue.js** (frontend) and **Laravel/PHP** (backend) with **MySQL** database.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | Vue.js 3, Vue Router, Pinia, Axios |
| Backend | Laravel 10, PHP 8.1+, Laravel Sanctum |
| Database | MySQL 8.0+ |
| Auth | Token-based (Sanctum Bearer tokens) |
| Styles | Custom CSS with CSS variables, no framework |

---

## Quick Start

### 1. Database
```sql
CREATE DATABASE school_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Backend (Laravel)
```bash
cd backend
composer install
cp .env.example .env
# Edit .env — set DB_USERNAME, DB_PASSWORD
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve --port=8000
```

### 3. Frontend (Vue.js)
```bash
cd frontend
npm install
npm run dev
# Open http://localhost:5173
```

### Default Admin Login
- **URL:** http://localhost:5173/login
- **Email:** admin@skola.lv
- **Password:** Admin1234!

---

## Project Structure

```
school-website/
├── backend/                    # Laravel PHP API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/    # Auth, News, Schedule, Page, User
│   │   │   └── Middleware/     # AdminMiddleware
│   │   └── Models/             # User, NewsPost, ScheduleEntry, Page
│   ├── database/
│   │   ├── migrations/         # 4 migration files
│   │   └── seeders/            # Default admin + sample data
│   ├── routes/api.php          # All API routes
│   └── config/cors.php         # CORS for Vue dev server
│
└── frontend/                   # Vue.js SPA
    ├── src/
    │   ├── stores/auth.js      # Pinia auth store
    │   ├── router/index.js     # Routes + guards
    │   ├── views/
    │   │   ├── public/         # 7 public pages
    │   │   ├── admin/          # 5 admin views
    │   │   └── LoginPage.vue
    │   └── App.vue             # Root + global styles
    └── vite.config.js          # Dev proxy to Laravel
```

---

## Public Pages

| Route | Page | Description |
|-------|------|-------------|
| `/` | Sākums | Hero, stats, quick links, news preview |
| `/jaunumi` | Jaunumi | Full news listing |
| `/vesture` | Vēsture | School history (editable from admin) |
| `/kontakti` | Kontakti | Contact info |
| `/stundu-saraksts` | Stundu saraksts | Schedule with class filter |
| `/skoleniem` | Skolēniem | Students info (editable from admin) |
| `/skolotajiem` | Skolotājiem | Teachers info (editable from admin) |

## Admin Panel Routes

| Route | Feature |
|-------|---------|
| `/admin` | Dashboard with stats |
| `/admin/news` | Create/edit/delete news + image upload |
| `/admin/schedule` | Manage schedule, conflict detection |
| `/admin/pages` | Edit History, Students, Teachers content |
| `/admin/users` | Create/delete admin users |

---

## API Endpoints

### Public
```
GET  /api/news                    → All news posts
GET  /api/schedule?group=10.A     → Schedule (optional filter)
GET  /api/schedule/groups         → All group names
GET  /api/pages/{slug}            → Page content (vesture/skoleniem/skolotajiem)
```

### Auth
```
POST /api/login                   → { email, password } → { user, token }
POST /api/logout                  → (requires Bearer token)
GET  /api/user                    → Current user
```

### Admin (Bearer token required)
```
POST   /api/admin/news            → Create news (multipart/form-data)
POST   /api/admin/news/{id}       → Update news (_method=PUT, multipart)
DELETE /api/admin/news/{id}       → Delete news

POST   /api/admin/schedule        → Create schedule entry
PUT    /api/admin/schedule/{id}   → Update schedule entry
DELETE /api/admin/schedule/{id}   → Delete schedule entry

PUT    /api/admin/pages/{slug}    → Update page content

GET    /api/admin/users           → List admins
POST   /api/admin/users           → Create admin
DELETE /api/admin/users/{id}      → Delete admin
```

---

## Features Checklist

### Public Website ✅
- [x] Sticky navbar with hover slide effect
- [x] "Vairāk" dropdown (Skolēniem / Skolotājiem)
- [x] Admin link (visible to all, redirects to login if unauthenticated)
- [x] Dark mode toggle with persistence
- [x] Hero section with scroll animation
- [x] Quick-link cards with hover scale
- [x] Fade-in scroll animations
- [x] News page with images
- [x] Schedule page with group/class filter
- [x] DB-driven content pages (Vēsture, Skolēniem, Skolotājiem)
- [x] Contact page
- [x] Full-width footer (contact, library hours, links)
- [x] Mobile-responsive design

### Admin Panel ✅
- [x] Secure JWT login with error messages
- [x] Protected routes with navigation guards
- [x] Admin middleware on all admin API endpoints
- [x] Collapsible sidebar
- [x] Dashboard with live stats
- [x] News CRUD with optional image upload
- [x] Schedule CRUD with teacher conflict detection
- [x] Conflict highlighted in red in the table
- [x] Page editor with HTML preview + quick-insert toolbar
- [x] User management (create/delete)
- [x] Confirmation modal before deleting
- [x] Loading states throughout
- [x] Success/error alerts

### Security ✅
- [x] Passwords hashed with bcrypt (Laravel default)
- [x] Bearer token auth (Sanctum)
- [x] AdminMiddleware on protected routes
- [x] CORS configured for frontend origin
- [x] Validation on all inputs
- [x] Cannot delete own account
- [x] Token invalidated on logout

---

## Database Schema

```sql
users              → id, name, email, password, role, timestamps
news_posts         → id, title, content, image_path, published_at, timestamps
schedule_entries   → id, group_name, subject, teacher, day_of_week, start_time, end_time, timestamps
pages              → id, slug, title, content, timestamps
```

---

## Production Deployment

### Backend
1. Set `APP_ENV=production`, `APP_DEBUG=false` in `.env`
2. Run `composer install --optimize-autoloader --no-dev`
3. Run `php artisan config:cache && php artisan route:cache`
4. Point web server (Nginx/Apache) to `backend/public`

### Frontend
1. Set `VITE_API_BASE_URL=https://your-api.com` if needed
2. Run `npm run build`
3. Deploy `dist/` to static host or configure web server

### Nginx config example for Vue SPA:
```nginx
location / {
    try_files $uri $uri/ /index.html;
}
```
