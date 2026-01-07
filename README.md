# 🎮 MIRAI Hub – Esports Tournament Platform

**MIRAI Hub** adalah aplikasi web full-stack berbasis **Laravel 12** yang dirancang sebagai portal pendaftaran dan manajemen turnamen esports.  
Project ini dikembangkan untuk menunjukkan kemampuan dalam membangun **sistem dinamis, scalable, dan maintainable** menggunakan modern Laravel ecosystem.

> **Role:** Full-Stack Laravel Developer  
> **Status:** Functional Prototype  
> **Tech Focus:** Laravel • Livewire • Dynamic Form • Auth System  

---

## 🧠 Problem Statement

Sebagian besar platform turnamen esports:
- Memiliki form pendaftaran statis
- Sulit dikustomisasi untuk setiap turnamen
- Tidak fleksibel terhadap jenis game & kebutuhan organizer

**MIRAI Hub** menyelesaikan masalah tersebut dengan:
- **Dynamic form builder** berbasis JSON
- **Multi-step registration system**
- **Multi-authentication** (Manual, Google OAuth, Firebase)
- **Admin dashboard** terintegrasi

---

## ✨ Key Features

### 🧩 Dynamic Tournament Registration Form
- Admin dapat membuat form pendaftaran **tanpa coding**
- Setiap turnamen memiliki schema form sendiri
- Mendukung:
  - Text, textarea, select, radio, checkbox
  - File upload (dengan validasi tipe & ukuran)
  - Section, note, image, link (static block)
- Data disimpan sebagai **JSON schema**

### 🏆 Tournament Management
- Create, edit, publish, close tournament
- Slot peserta & status otomatis (open / closed)
- Export data peserta ke Excel

### 👥 Authentication System
- Manual login & register
- Google OAuth (Laravel Socialite)
- Firebase Authentication (client-side Google Sign-In)
- Role-based access (Admin / User)

### 🧑‍💼 Admin Dashboard
- Statistik turnamen & peserta
- Manajemen peserta
- Export data pendaftaran

---

## 🛠️ Tech Stack

### Backend
- **Laravel 12**
- **PHP 8.5+**
- **Livewire 3** (full-stack reactive components)
- Laravel Socialite (OAuth)
- Maatwebsite Excel (export)

### Frontend
- **Tailwind CSS 4**
- **Alpine.js**
- Vite
- Dark gaming UI theme

### Database
- PostgreSQL

---

## 🧱 Architecture Overview

```

User ──< Tournament ──< Participant
└──< UserGameProfile
Tournament >── Game

````

- **Tournament** menyimpan `form_schema` (JSON)
- **Participant** menyimpan `submission_data` (JSON)
- Validasi dilakukan per-step di Livewire

---

## 🧩 Core Components

### Livewire Components
- `Admin/CreateTournament`  
  → Dynamic form builder (≈ 380 LOC)
- `PublicRegistration`  
  → Multi-step form + validation (≈ 330 LOC)
- `UserProfile`  
  → Game profile & auto-fill form

### Key Technical Highlights
- JSON-driven UI rendering
- State management di Livewire
- Conditional validation per step
- File upload handling
- Role-based middleware

---

## 🔐 Security & Access Control

- CSRF protection
- Auth & admin middleware
- Role-based routing
- Secure file upload validation
- OAuth token verification

---

## 🧪 Development & Testing

```bash
composer install
npm install
php artisan migrate --seed
composer dev
````

Testing:

```bash
php artisan test
```

---

## 📊 Codebase Summary

| Category            | Count |
| ------------------- | ----- |
| Models              | 5     |
| Livewire Components | 7     |
| Controllers         | 5     |
| Migrations          | 12    |
| Seeders             | 2     |

**Notable Files**

* `CreateTournament.php` → ~380 LOC
* `PublicRegistration.php` → ~330 LOC
* Dynamic Blade views → 1000+ LOC

---

## 🚀 Skills & Experience Demonstrated

✅ Advanced Laravel architecture

✅ Livewire full-stack development

✅ Dynamic form & JSON-based schema

✅ Authentication & authorization

✅ Clean code & separation of concerns

✅ Real-world use case (esports platform)

---

## 🔮 Planned Improvements

* Email verification
* Password reset
* API endpoints
* Unit & feature tests
* Caching & performance optimization
