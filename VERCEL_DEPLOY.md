# Vercel deployment (demo branch)

## Setup

1. Install Vercel CLI and link the project: `vercel link`
2. In the Vercel project **Environment Variables**, set:
   - `APP_KEY` – from your `.env` (e.g. `php artisan key:generate --show`)
   - `APP_URL` – your deployment URL (e.g. `https://your-app.vercel.app`)

`vercel.json` already sets SQLite and `/tmp` cache paths for production.

## First deploy and database

SQLite uses `/tmp/database.sqlite` on Vercel. `/tmp` is empty on each cold start, so the database file and tables are recreated when needed.

To create tables and the demo user on the **first** request, run migrations and the seeder once after deploy:

- **Option A:** In Vercel Dashboard → your project → Settings → General → **Build Command** set to:
  ```bash
  composer install --no-dev --optimize-autoloader
  ```
  Then in **Root Directory** leave blank. For the database, use **Option B** or a one-off run.

- **Option B:** After the first deploy, trigger a one-off run that executes migrations and seed (e.g. a temporary route or script that runs `Artisan::call('migrate', ['--force' => true])` and `Artisan::call('db:seed', ['--class' => 'DemoUserSeeder', '--force' => true])` then remove it). Or run locally against the deployed app if you expose a way to run artisan.

- **Option C:** Rely on a bootstrap in `api/index.php` that runs migrate + seed when the SQLite file is missing (add this only if your runtime has `php` in PATH and the project root as cwd).

For a quick demo, **Option B** is often simplest: add a one-time setup route (e.g. `/setup-demo`) that runs migrate and seed, visit it once after deploy, then remove or protect the route.

## Login

- Email: `siena@admin.com`
- Password: `admin123`

## Notes

- Data in `/tmp` is **ephemeral** (lost on cold start). For persistent data, use Vercel Blob or an external database.
- Cron/scheduler does not run on Vercel; no background jobs.
