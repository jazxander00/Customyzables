# Customyzables

A full-stack Laravel web app that connects clothing donors with local upcyclers in
Lapu-Lapu City, Cebu. Donors post textile bundles; verified upcyclers request them;
both sides arrange a handover and track the donation from **listed** to **completed**.

Built from the *Customyzables Project Proposal (Team Tynamo)* and its UI mockups.

---

## Stack

| Layer | Choice |
| --- | --- |
| Framework | Laravel 10.50 (PHP 8.1) |
| Database | MariaDB 10.4 (XAMPP) |
| Styling | Tailwind CSS 3.4 with a custom design-token system |
| Motion | anime.js 4.5 (bundled via Vite) |
| Auth | Hand-built session auth (no Breeze/Jetstream) |
| Build | Vite 4 |

---

## Running it locally

Requirements: PHP 8.1+, Composer, Node 18+, MySQL/MariaDB running (XAMPP is fine).

```bash
# 1. Install PHP dependencies
composer install

# 2. Environment
copy .env.example .env        # Windows
# cp .env.example .env        # macOS/Linux
php artisan key:generate

# 3. Create the database (adjust user/password to your setup)
mysql -u root -e "CREATE DATABASE customyzables CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Schema + demo data
php artisan migrate:fresh --seed

# 5. Uploads (donation photos live in storage/app/public)
php artisan storage:link

# 6. Front-end build
npm install
npm run build      # production build
# npm run dev      # or: hot-reload while developing

# 7. Serve
php artisan serve
```

Open <http://localhost:8000>.

> The app also works from XAMPP's Apache at
> `http://localhost/customyzable/public`, but `php artisan serve` is simpler for demos.

### Database

The schema is defined in `database/migrations/`. To get it into MySQL you can either:

```bash
php artisan migrate:fresh --seed   # build from migrations + demo data
```

or import the ready-made dump (schema + demo data, no `CREATE DATABASE` statement so it
fits any host):

```
database/customyzables.sql
```

Step-by-step phpMyAdmin and command-line instructions — including importing into a hosting
database with a different name — are in **[DEPLOYMENT.md](DEPLOYMENT.md#part-1--putting-the-database-into-mysql)**.

### Deployment

`DEPLOYMENT.md` covers the full process: a production checklist, Docker deployment on
Railway, Render with an external MySQL, shared hosting/cPanel, and a demo-day tunnel.
The repo includes:

- `Dockerfile` + `docker/entrypoint.sh` — builds PHP 8.1 + Apache, runs migrations and
  caches config/routes/views on boot,
- `docker-compose.yml` — the same image plus MySQL locally, one command,
- `.env.production.example` — the variables a server needs,
- `database/customyzables.sql` — the database to import when there is no SSH.

```bash
docker compose up --build     # after copying .env.production.example to .env.docker
```


### Environment notes

`.env` needs the usual DB values:

```
APP_NAME=Customyzables
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customyzables
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
```

---

## Demo accounts

All passwords are `password`.

| Role | Email | Notes |
| --- | --- | --- |
| Admin | `admin@customyzables.test` | Verification, moderation, audit trail |
| Donor | `elaine@customyzables.test` | Has donations in several states |
| Donor | `maria@customyzables.test` | Hotel linen donor, completed handovers |
| Donor | `kyra@customyzables.test` | Resort uniform bundles |
| Upcycler (verified) | `johnriel@customyzables.test` | Basak Upcycling Co-op |
| Upcycler (verified) | `ben@customyzables.test` | Gun-ob Parish Craft Circle |
| Upcycler (unverified) | `marisol@customyzables.test` | Can browse, cannot request until verified |

---

## What is built

### Roles and permissions

- **Donor** — create and withdraw donations, review requests, accept/decline, schedule
  handovers, complete donations, chat, view impact.
- **Upcycler** — browse the public feed, request bundles (verified only), withdraw
  requests, mark items received, chat. Unverified accounts are read-only until an admin
  approves them.
- **Admin** — verify/unverify upcyclers, suspend/reinstate accounts, moderate and remove
  donations, review the audit trail.

Route groups are protected by a `role:` middleware; the request workflow re-checks
authorization server-side (an unverified upcycler cannot POST a request directly).

### Donation lifecycle

```
listed → requested → accepted → scheduled → received → completed
                                 ↘ cancelled
```

Every transition goes through `app/Services/DonationWorkflow.php`, which is the single
source of truth for:

- legal transitions and guards,
- auto-declining competing requests when one is accepted,
- dropping a bundle back to `listed` when the last pending request is declined,
- writing a `donation_events` row and an `audit_logs` entry.

### Pages

| Route | Purpose |
| --- | --- |
| `/` | Landing: hero, impact counters, how-it-works, live feed preview |
| `/browse`, `/browse/{donation}` | Public feed with fibre/barangay/condition filters + detail page |
| `/register`, `/login` | Role-picker registration, sign-in with rate limiting |
| `/dashboard/donor`, `/dashboard/upcycler` | Role-specific dashboards |
| `/donations`, `/donations/create`, `/donations/{id}` | Donor listings, 3-step posting form, request management |
| `/my-requests` | Upcycler request list |
| `/chat`, `/chat/{donation}` | One conversation per donation |
| `/profile`, `/impact` | Profile settings, password change, personal impact |
| `/admin/*` | Overview, users, donation moderation, audit trail |

### Design system

`resources/css/app.css` + `tailwind.config.js` define:

- **Surfaces** — `sand` (warm paper), white cards, soft radii (`rounded-card`).
- **Accent** — `clay` (terracotta) for primary actions, `forest` for progress/verified states.
- **Type** — Plus Jakarta Sans, tight display tracking.
- **Components** — `.card`, `.btn-primary/-secondary/-forest/-ghost`, `.field`, `.chip`,
  `.badge-*`, `.stat-card`, `.role-option`, `.nav-link`.

### Motion (anime.js)

`resources/js/app.js` exposes a small `motion` module:

- `reveal` / `cards` — staggered entrance for sections and card grids,
- `countUp` — dashboard and impact statistics,
- `stepper` — donation progress bar + step pop-in,
- `bars` — horizontal progress bars,
- `flash` — toast slide-in and auto-dismiss,
- `press` — button press feedback.

Everything checks `prefers-reduced-motion` and is progressive: content stays visible if
JavaScript fails (`.js [data-reveal]` is only hidden once the bundle has run).

---

## Tests

```bash
php artisan test
```

14 feature tests cover public pages, role isolation, registration/publishing, the full
listed → completed workflow, verification, and chat access control.

The suite uses a **separate database** (`customyzables_testing`, configured in
`phpunit.xml`) so `RefreshDatabase` never touches your demo data.

---

## Project layout

```
app/
  Enums/            Role, DonationStatus, RequestStatus, HandoffMethod
  Services/         DonationWorkflow (all status transitions)
  Http/Controllers/ Auth, Dashboard, Browse, Donation, Request, Chat, Profile, Admin
  Http/Requests/    RegisterRequest, StoreDonationRequest, StoreMessageRequest
  Http/Middleware/  EnsureRole
  Models/           User, Donation, DonationImage, DonationRequest, Message, DonationEvent, AuditLog
database/
  migrations/       users, donations, images, requests, messages, events, audit logs
  seeders/          DatabaseSeeder — 7 users, 10 donations, 5 requests, 2 messages
  customyzables.sql  importable dump (schema + demo data) for hosts without SSH
resources/
  css/app.css       design tokens + component classes
  js/app.js         anime.js motion module
  views/            layouts, components, auth, dashboards, donations, browse, chat, admin
routes/web.php      full route map
Dockerfile          PHP 8.1 + Apache image, builds Vite assets in stage 1
docker/entrypoint.sh  migrate → seed (optional) → cache → start Apache
docker-compose.yml  app + MySQL 8 for local production-parity testing
.env.production.example  variables every host needs
DEPLOYMENT.md       database import + four deployment paths + troubleshooting
```

---

## Scope decisions for v1

- **Google sign-in** is shown disabled on the login screen; no OAuth app is configured.
- **Naming:** the proposal uses **"Upcycler"**, so that is what the UI and code use.
  The early mockups said "Artisan" — one rename if that wording is preferred.
- No drop-off map, verification tiers, or urgent-needs board yet; these were listed as
  later-phase features in the proposal.
- Notifications are in-app only (flash messages + chat); no email/SMS delivery.
- Donations are public to browse; requesting is restricted to verified upcyclers.
