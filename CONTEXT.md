# MIRAI Tournament Hub - Development Context

## Project Overview

**Nama Aplikasi:** MIRAI Tournament Hub  
**Tipe:** Internal Tools untuk MIRAI Indonesia (bukan SaaS)  
**Deskripsi:** Aplikasi berbasis web untuk membuat, mengelola, dan mempublikasikan turnamen esports.

---

## Tech Stack

-   **Language:** PHP 8.4+
-   **Framework:** Laravel 12.x
-   **Fullstack Framework:** Livewire 3.x (Class-based components)
-   **Frontend Logic:** Alpine.js 3.x
-   **Styling:** Tailwind CSS 4.0 (utility classes only)
-   **Database:** PostgreSQL (dengan JSONB column)
-   **Icons:** Lucide Icons (blade-lucide-icons)

---

## UI/UX Guidelines (Dark Mode Neon)

-   **Global Background:** `bg-[#050505]` (hitam pekat)
-   **Card Background:** `bg-[#1a1a1a]` dengan `border border-gray-800`
-   **Accent Color:** `cyan-500` (text/border) dan `bg-cyan-600` (button)
-   **Input:** `bg-[#222]` atau `bg-black` dengan `border border-gray-700`
-   **Layout:** Form di kiri (lebar), Sidebar Menu di kanan (sticky)

---

## Firebase Configuration

```env
FIREBASE_API_KEY=AIzaSyB9RYy87jN4yt6RNoRB3pNxIkh8-dWA5qI
FIREBASE_AUTH_DOMAIN=mirai-hub-cdda6.firebaseapp.com
FIREBASE_PROJECT_ID=mirai-hub-cdda6
```

---

## Database Schema

### tournaments

```php
Schema::create('tournaments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('title');
    $table->string('slug')->unique();
    $table->string('game_type');
    $table->dateTime('event_date');
    $table->decimal('fee', 12, 2)->default(0);
    $table->integer('max_slots')->default(32);
    $table->text('description')->nullable();
    $table->string('status')->default('open');
    $table->jsonb('form_schema')->nullable();
    $table->timestamps();
});
```

### participants

```php
Schema::create('participants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
    $table->string('team_name');
    $table->string('captain_name');
    $table->string('whatsapp');
    $table->jsonb('submission_data')->nullable();
    $table->string('payment_status')->default('pending');
    $table->timestamp('registered_at');
    $table->timestamps();
});
```

---

## Key Livewire Components

### CreateTournament.php / EditTournament.php Methods

| Method                                            | Parameter               | Description                           |
| ------------------------------------------------- | ----------------------- | ------------------------------------- |
| `addField(string $type)`                          | type only (1 param)     | Adds new field                        |
| `removeField(int $index)`                         | index                   | Removes field                         |
| `moveFieldUp(int $index)`                         | index                   | Moves field up                        |
| `moveFieldDown(int $index)`                       | index                   | Moves field down                      |
| `duplicateField(int $index)`                      | index                   | Duplicates field                      |
| `toggleImageInput(int $index)`                    | index                   | Shows/hides image input               |
| `toggleLinkInput(int $index)`                     | index                   | Shows/hides link input                |
| `clearImage(int $index)`                          | index                   | Clears embedded image                 |
| `clearLink(int $index)`                           | index                   | Clears embedded link                  |
| `addOption(int $fieldIndex)`                      | fieldIndex              | Adds option for radio/checkbox/select |
| `removeOption(int $fieldIndex, int $optionIndex)` | fieldIndex, optionIndex | Removes option                        |
| `toggleRestrictTypes(int $index)`                 | index                   | Toggles file type restriction         |
| `toggleFileType(int $index, string $fileType)`    | index, fileType         | Toggles specific file type            |

---

## Completed Tasks

### 1. Copy Function for Game Profile

-   Fixed copy function in `public-registration.blade.php`
-   Separated x-data for game profile copy into its own function

### 2. Cursor Pointer for All Clickable Elements

-   Added `cursor-pointer` class to all clickable elements across all view files
-   Edit/delete buttons always visible (not hover-only) for mobile support

### 3. Admin Create/Edit Tournament Page Fixes

-   Fixed close embedded image/link buttons (changed to Alpine.js)
-   Added fullscreen modal with x-teleport for images
-   Added banner upload preview with loading indicator
-   Fixed `addField()` calls to use single parameter
-   Removed `wire:ignore.self` that was blocking updates

### 4. Image-View Upload in Edit Tournament

-   Added missing image upload feature for `image-view` type in edit-tournament.blade.php

### 5. Performance Optimization

-   Removed `autosave()` from all small actions (toggle, clear, add, remove, move, duplicate)
-   Converted toggle actions to Alpine.js for instant response
-   Added loading indicator for server-side operations

### 6. Alpine.js Duplicate Instance Fix

-   Removed duplicate Alpine import from `resources/js/app.js`
-   Livewire 3 already includes Alpine

---

## Known Limitations

### Livewire vs React Performance

| Aspect           | React            | Livewire                 |
| ---------------- | ---------------- | ------------------------ |
| State location   | Client (browser) | Server (PHP)             |
| Update mechanism | Instant (memory) | HTTP request             |
| Re-render        | Virtual DOM diff | Full component re-render |
| Latency          | 0ms              | 50-200ms (network)       |

**Actions that are now instant (Alpine-only):**

-   Toggle image/link input
-   Clear image/link
-   Required toggle
-   File settings toggle

**Actions that still need server (unavoidable delay):**

-   Add/Remove field
-   Move field up/down
-   Duplicate field
-   Add/Remove option

---

## File Structure

```
app/
├── Exports/
├── Http/
│   └── Controllers/
│       └── GoogleAuthController.php
├── Livewire/
│   └── Admin/
│       ├── CreateTournament.php
│       ├── EditTournament.php
│       ├── Dashboard.php
│       ├── TournamentList.php
│       └── ParticipantManager.php
├── Models/
└── Providers/

resources/views/
├── layouts/
│   └── admin.blade.php
├── livewire/
│   ├── admin/
│   │   ├── create-tournament.blade.php
│   │   ├── edit-tournament.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── tournament-list.blade.php
│   │   └── participant-manager.blade.php
│   ├── public-registration.blade.php
│   ├── user-profile.blade.php
│   └── partials/
│       └── form-field-input.blade.php
├── components/
│   └── mirai-logo.blade.php
├── home.blade.php
├── login.blade.php
└── register.blade.php
```

---

## Development Notes

1. **Bahasa:** Komunikasi dalam Bahasa Indonesia
2. **MIRAI Hub adalah internal tools** untuk MIRAI Indonesia, bukan SaaS platform
3. **Semua elemen yang bisa diklik harus punya `cursor-pointer`**
4. **Edit/delete buttons harus selalu visible** (tidak hover-only) untuk mobile
5. **Method `addField(string $type)` hanya menerima 1 parameter** - type string saja
6. **Gunakan `@click.stop="$wire.method()"` atau `wire:click.stop`** untuk button di dalam nested elements
7. **Untuk instant UI response**, gunakan Alpine.js dengan `$wire.set(property, value, false)` - parameter ketiga `false` mencegah re-render
