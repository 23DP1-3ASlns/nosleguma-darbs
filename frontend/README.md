# School Website - Vue.js Frontend

## Setup Instructions

### Requirements
- Node.js 18+
- npm 8+
- Backend running on http://localhost:8000

### Installation

```bash
# 1. Navigate to frontend directory
cd frontend

# 2. Install dependencies
npm install

# 3. Start development server
npm run dev
```

The app will be available at **http://localhost:5173**

### Build for Production

```bash
npm run build
```

The built files will be in the `dist/` folder. Deploy to any static host (Nginx, Apache, Vercel, Netlify).

---

## Project Structure

```
src/
├── main.js                    # App entry - Axios config, auth interceptors
├── App.vue                    # Root component, dark mode, global styles
├── stores/
│   └── auth.js               # Pinia auth store (login, logout, user state)
├── router/
│   └── index.js              # Vue Router - public + admin routes + guards
└── views/
    ├── LoginPage.vue          # Admin login form
    ├── public/
    │   ├── PublicLayout.vue   # Navbar + Footer wrapper
    │   ├── HomePage.vue       # Landing page with hero, stats, news preview
    │   ├── NewsPage.vue       # Full news listing
    │   ├── HistoryPage.vue    # School history (DB-backed)
    │   ├── StudentsPage.vue   # Students info (DB-backed)
    │   ├── TeachersPage.vue   # Teachers info (DB-backed)
    │   ├── SchedulePage.vue   # Schedule with group filter
    │   └── ContactsPage.vue   # Contact information
    └── admin/
        ├── AdminLayout.vue    # Sidebar + topbar layout
        ├── Dashboard.vue      # Admin dashboard with stats
        ├── NewsManagement.vue # CRUD news posts + image upload
        ├── ScheduleManagement.vue # CRUD schedule + conflict detection
        ├── PageEditor.vue     # Edit page content (HTML editor)
        └── UserManagement.vue # Create/delete admin users
```

## Features

### Public Website
- **Responsive navbar** with dropdown menu and slide-hover effect
- **Dark mode toggle** with localStorage persistence
- **Smooth page transitions** via Vue Router + CSS
- **Scroll animations** on homepage
- **News listing** with images
- **Schedule page** with group/class filter
- **DB-driven content** for History, Students, Teachers pages
- **Full-width footer** with contact info, library hours

### Admin Panel
- **Protected routes** with JWT via Laravel Sanctum
- **Sidebar navigation** with collapsible state
- **News management**: create/edit/delete with image upload
- **Schedule management**: CRUD + teacher conflict detection + conflict highlighting
- **Page editor**: HTML editor with preview, toolbar for common tags
- **User management**: create/delete admin accounts
- **Loading states + confirmation modals** before destructive actions

### UI/UX
- Clean typography (Playfair Display + DM Sans)
- Forest green + gold color palette
- Smooth CSS transitions and animations
- Mobile-friendly responsive design
- Error and success alerts
- Empty state displays
