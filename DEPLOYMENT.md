# Deploying Customyzables

Two separate jobs:

1. **Get the database into MySQL** — locally (XAMPP) or on the hosting server.
2. **Put the app online** — pick one of the four paths in Part 2.

Everything below assumes PHP **8.1+** and MySQL 5.7+ / MariaDB 10.3+. Laravel 10 will not
run on older PHP.

---

## Part 1 — Putting the database into MySQL

### What the database needs

| Setting | Value |
| --- | --- |
| Engine | MySQL 5.7+ or MariaDB 10.3+ |
| Database name | `customyzables` (any name works on a host — see 1.6) |
| Charset | `utf8mb4` / `utf8mb4_unicode_ci` |
| Tables | 11 (users, donations, donation_images, donation_requests, messages, donation_events, audit_logs, plus Laravel's own) |

You have **three ways** to create it. Pick one.

---

### 1.1 Option A — Import the ready-made dump (fastest)

A full dump with the schema **and** the demo accounts is included at:

```
database/customyzables.sql
```

**Using phpMyAdmin (XAMPP):**

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Open <http://localhost/phpmyadmin>.
3. Left sidebar → **New**.
4. Database name: `customyzables` · Collation: `utf8mb4_unicode_ci` → **Create**.
5. Select `customyzables` in the left sidebar → **Import** tab.
6. **Choose File** → `database/customyzables.sql` → scroll down → **Import**.
7. You should see “Import has been successfully finished” and 11 tables in the sidebar.

**Using the command line (same result, no clicking):**

```bash
# Windows (XAMPP)
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS customyzables CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
C:\xampp\mysql\bin\mysql.exe -u root customyzables < database\customyzables.sql

# macOS / Linux
mysql -u root -e "CREATE DATABASE IF NOT EXISTS customyzables CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root customyzables < database/customyzables.sql
```

The dump contains no `CREATE DATABASE` statement on purpose, so it imports cleanly into
whatever database name your host gives you.

---

### 1.2 Option B — Build the schema from migrations (no demo data)

Use this if you want a clean, empty database.

```bash
php artisan migrate
```

This creates the tables by running `database/migrations/*.php` in order. It does **not**
create the demo users or donations.

---

### 1.3 Option C — Migrations + demo data (what the demo uses)

```bash
php artisan migrate:fresh --seed
```

- `migrate:fresh` drops every table and rebuilds it.
- `--seed` runs `database/seeders/DatabaseSeeder.php`: 7 users, 10 donations, 5 requests,
  2 chat messages.

**Never run `migrate:fresh` against the deployed database** — it deletes real data. On a
server use `php artisan migrate --force`, which only applies new migrations.

---

### 1.4 Point the app at the database

Edit `.env` in the project root:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customyzables
DB_USERNAME=root
DB_PASSWORD=
```

On a real host the values change (usually `localhost`, a long username, and a strong
password). If you change `.env` while the app is cached, run:

```bash
php artisan config:clear
```

### 1.5 Check that it worked

```bash
php artisan tinker
>>> \App\Models\User::count();      // 7 with the seeded dump
>>> \App\Models\Donation::count();  // 10
>>> exit
```

Or in phpMyAdmin: `SELECT COUNT(*) FROM users;` → `7`.

Then open <http://localhost:8000> and sign in with `elaine@customyzables.test` /
`password`. If the dashboard loads, the connection is correct.

### 1.6 Importing into a hosting database (cPanel / InfinityFree / Hostinger)

The host creates the database for you and prefixes its name (for example
`epiz_12345678_customyzables`).

1. In the host panel → **MySQL Databases** → create a database and a user, and give the
   user **All Privileges** on it. Write down the full database name, username, password,
   and host (usually `localhost` or `sqlXXX.infinityfree.com`).
2. Open **phpMyAdmin** from the panel, select that database.
3. **Import** → `database/customyzables.sql`.
4. Put the same four values into the server's `.env`:

```env
DB_HOST=sqlXXX.infinityfree.com
DB_DATABASE=epiz_12345678_customyzables
DB_USERNAME=epiz_12345678
DB_PASSWORD=the-password-you-set
```

If the import fails with “access denied to CREATE DATABASE”, you imported while no
database was selected — select the database first, then import.

### 1.7 Exporting the database later (backups, submission)

```bash
# Windows
C:\xampp\mysql\bin\mysqldump.exe -u root --single-transaction --routines --result-file="database\customyzables.sql" customyzables

# macOS / Linux
mysqldump -u root --single-transaction --routines customyzables > database/customyzables.sql
```

Or phpMyAdmin → select the database → **Export** → **Go** (SQL format).

Keep the dump out of public access — it lives in `database/`, not `public/`, so it is not
downloadable from the browser.

---

## Part 2 — Deploying the app

### 2.0 Before you deploy anything

| Check | Why |
| --- | --- |
| `APP_KEY` is set | Without it, sessions and CSRF tokens break (every form gives “419 Page Expired”). |
| `APP_DEBUG=false` | With `true`, any error shows your `.env`, database password, and file paths to the public. |
| `APP_URL` is the real URL | Links, redirects, and asset URLs are generated from it. |
| `public/build/` is uploaded | The CSS/JS is pre-built, so **Node is not needed on the server**. |
| `storage:link` was run | Otherwise donation photos return 404. |
| `.env` is **not** in Git / the ZIP you submit | It contains credentials. |

Production commands (run once after upload):

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

The project already avoids closure routes, so `route:cache` works.

---

### 2.1 Option A — Railway with Docker (recommended)

The repo ships a `Dockerfile`, so Railway builds the image, runs migrations automatically,
and starts Apache. No manual server setup.

**Steps**

1. Push the project to GitHub (see the note at the end of this section if you have never
   used Git).
2. Go to <https://railway.app> → **New Project** → **Deploy from GitHub repo** →
   select the repository. Railway detects the `Dockerfile` and builds it.
3. In the same project → **New** → **Database** → **Add MySQL**.
4. Open the **app** service → **Variables** → add:

| Variable | Value |
| --- | --- |
| `APP_NAME` | `Customyzables` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | run `php artisan key:generate --show` locally and paste the `base64:...` output |
| `APP_URL` | `https://<the-domain-railway-gives-you>` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `${{MySQL.MYSQLHOST}}` |
| `DB_PORT` | `${{MySQL.MYSQLPORT}}` |
| `DB_DATABASE` | `${{MySQL.MYSQLDATABASE}}` |
| `DB_USERNAME` | `${{MySQL.MYSQLUSER}}` |
| `DB_PASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |
| `SESSION_DRIVER` | `file` |
| `LOG_CHANNEL` | `stderr` |
| `SEED_DATABASE` | `true` for the first deploy only |

5. App service → **Settings** → **Networking** → **Generate Domain**, and set the target
   port to **80**.
6. Redeploy. Watch the deploy log for `Running migrations` and `Routes cached successfully`.
7. Once it is up, change `SEED_DATABASE` to `false` and redeploy (so a restart can never
   reseed demo accounts onto a live database).

**Uploaded photos:** container storage is wiped on every deploy. Railway volumes can be
mounted at `/var/www/html/storage/app/public` if you want uploads to survive; otherwise
treat photos as demo-only.

> Railway is no longer free forever — it gives a one-time trial credit, which is plenty
> for a defense. For an always-free host, use Option B.

**If you have never used Git:**

```bash
cd C:\xampp\htdocs\customyzable
git init
git add .
git commit -m "Customyzables"
git branch -M main
git remote add origin https://github.com/<you>/customyzables.git
git push -u origin main
```

`.env` is already ignored by `.gitignore`; commit `.env.production.example` instead.

---

### 2.2 Option B — Render (free Docker web service) + free MySQL

Render can host the Docker image for free (the service sleeps after ~15 minutes idle and
takes ~50 seconds to wake — **open the URL five minutes before your defense**). Render has
no free MySQL, so use a free MySQL provider.

1. Create a free MySQL database at <https://aiven.io> (Free plan, 1 GB). Note the host,
   port, database, user, and password. Download the CA certificate if Aiven requires TLS.
2. Push the project to GitHub.
3. Render → **New** → **Web Service** → connect the repo → Runtime: **Docker** → Free plan.
4. Add the same environment variables as the Railway table above, using the Aiven values
   for `DB_*`. Set `APP_URL` to the Render URL (`https://customyzables.onrender.com`).
5. Deploy. The entrypoint runs migrations and caches automatically.
6. First deploy only: `SEED_DATABASE=true`; then set it back to `false`.

Free Render services have ephemeral disks — uploaded photos disappear on redeploy. Fine for
a demonstration where the panel browses existing seeded data.

---

### 2.3 Option C — Shared hosting / cPanel (InfinityFree, Hostinger, Namecheap)

**Ready-made ZIP:** `customyzables-deploy.zip` sits in the project root (6.8 MB) for
upload-based hosts. It already contains:

- the app code and `vendor/` **built without dev packages** (no PHPUnit, no Pint — that
  alone removed a 15 MB file),
- compiled CSS/JS in `public/build` (no Node needed on the server),
- `database/customyzables.sql`.

It excludes `node_modules/`, `.opencode/`, and `.env`. The largest file inside is 0.57 MB,
so it fits hosts with a 10 MB per-file limit.

To use it: upload → extract on the server → create `.env` → import the SQL dump →
create the `storage` link → visit the site.

Shared hosts usually **have no SSH and no Composer**, so you upload a fully prepared copy.

**Prepare the ZIP locally:**

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build          # or reuse the existing public/build
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Then upload **everything except** `node_modules/` and `.env` (create a fresh `.env` on the
server instead). Keep `vendor/` — you cannot run Composer there.

**Two common layouts:**

*If you can choose the document root*, point the domain to `customyzables/public` and
upload the whole project one level above `public_html`. This is the clean way.

*If the document root is fixed at `public_html`*, upload the project into
`public_html/customyzables/`, then copy everything from `public/` into `public_html/`.
Edit `public_html/index.php`:

```php
// before
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// after
require __DIR__.'/customyzables/vendor/autoload.php';
$app = require_once __DIR__.'/customyzables/bootstrap/app.php';
```

Do the same rewrite in `public_html/.htaccess` if your host copies it, or simply create:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ customyzables/public/$1 [L]
</IfModule>
```

**On the server:**

1. Import the SQL dump through phpMyAdmin (Part 1.6) — you cannot run `php artisan migrate`
   without SSH, and this is exactly why the dump exists.
2. Create `.env` in `customyzables/` with the host's DB values, `APP_ENV=production`,
   `APP_DEBUG=false`, a generated `APP_KEY`, and `APP_URL=https://yourdomain`.
3. Permissions: `storage/` and `bootstrap/cache/` must be writable (usually `755`, or
   `775` on some hosts).
4. **Storage link:** cPanel's File Manager → right-click `public/storage` → create a
   symlink to `../storage/app/public`. If symlinks are disabled, upload the contents of
   `storage/app/public` into `public/storage` after every photo upload.
5. Visit the site. If you get a 500, set `APP_DEBUG=true` temporarily, reload, read the
   error, then set it back to `false`.

InfinityFree notes: free SSL, PHP 8.2 available, 10 MB per-file upload limit (fine — vendor
files are small), and `symlink()` is blocked, so use the copy fallback.

---

### 2.4 Option D — Demo-day tunnel (no hosting account)

If deployment fails the night before, this still gives the panel a real HTTPS URL while
your laptop runs the app. It is a demonstration setup, not a production deployment.

1. Start MySQL (XAMPP) and the app:

   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

2. In a second terminal, install and run Cloudflare Tunnel:

   ```bash
   winget install --id Cloudflare.cloudflared
   cloudflared tunnel --url http://localhost:8000
   ```

3. It prints a URL like `https://random-words-1234.trycloudflare.com`. That URL is public
   and works on phones on any network.
4. Set `APP_URL` to that URL in `.env` and run `php artisan config:clear` so generated links
   match.

Limits: the URL changes each run, the laptop must stay awake and online, and the free
tunnel is rate-limited. Use it as a backup, not as the plan.

---

## 3. Post-deploy smoke test

Run this on the deployed URL before you present. It takes two minutes and catches almost
every deployment mistake.

1. **Home page** loads with styling (fonts, terracotta buttons). If it is unstyled, the
   `public/build` folder did not upload.
2. **Register** a throwaway account → you land on the correct dashboard (no 419 error).
3. As the donor, **post a donation** with a photo → the photo displays (if it 404s,
   `storage:link` was not run).
4. Log in as `admin@customyzables.test` → **Users** → verify the new upcycler account.
5. Log in as that upcycler → request the new donation → accept it as the donor → the
   stepper advances and the status badges change.
6. **Messages** → send a chat message both ways.

If steps 2–6 work, the database, sessions, file storage, and authorization are all wired
correctly.

---

## 4. Troubleshooting

| Symptom | Cause | Fix |
| --- | --- | --- |
| `419 Page Expired` on every form | Missing/incorrect `APP_KEY`, or cookies blocked | `php artisan key:generate` on the server, `SESSION_DRIVER=file`, `APP_URL` must match the real scheme (`https`) |
| 500 on every page | Wrong DB credentials, missing `vendor/`, or permissions | Set `APP_DEBUG=true`, reload, read the message, then set it back to `false` |
| `SQLSTATE[HY000] [2002] Connection refused` | Wrong `DB_HOST`, or MySQL not running | On shared hosting use the host's DB hostname, not `127.0.0.1` |
| `Access denied for user` | DB user lacks privileges on that database | Grant **All Privileges** in cPanel |
| Page looks unstyled | `public/build` missing or `APP_URL`/asset path wrong | Upload `public/build`, run `php artisan config:clear` |
| Photos show a broken image | `storage:link` missing, or symlinks blocked | Run `php artisan storage:link`, or copy files into `public/storage` |
| `Target class [role] does not exist` | Cached config from another machine | `php artisan optimize:clear` then `php artisan config:cache` |
| White page, no error | `APP_DEBUG=false` hiding the error | Check `storage/logs/laravel.log` or the platform's log stream (`LOG_CHANNEL=stderr`) |
| Changes don't appear after editing `.env` | Config is cached | `php artisan config:clear` |

---

## 5. Before you submit

- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] `APP_KEY` unique to the deployment (never reuse the local one in a public repo)
- [ ] `.env` not uploaded, not committed
- [ ] Demo passwords changed, or demo accounts disabled after grading
- [ ] `database/customyzables.sql` is the version you want graded
- [ ] HTTPS active (free on Railway/Render/InfinityFree)
- [ ] The smoke test in section 3 passed on the deployed URL
- [ ] You can restore the database from the dump if something breaks during the defense
