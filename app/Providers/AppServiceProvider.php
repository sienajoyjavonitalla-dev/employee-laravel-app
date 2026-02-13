<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootstrapVercel();
    }

    /**
     * On Vercel: ensure SQLite DB and view compile path exist (read-write /tmp).
     */
    protected function bootstrapVercel(): void
    {
        if (env('VERCEL') !== '1') {
            return;
        }

        $viewPath = config('view.compiled');
        if ($viewPath && ! is_dir($viewPath)) {
            @mkdir($viewPath, 0755, true);
        }

        $dbPath = env('DB_DATABASE');
        if (env('DB_CONNECTION') !== 'sqlite' || $dbPath !== '/tmp/database.sqlite') {
            return;
        }

        if (! file_exists($dbPath)) {
            @touch($dbPath);
            try {
                \Artisan::call('migrate', ['--force' => true]);
                \Artisan::call('db:seed', ['--class' => 'DemoUserSeeder', '--force' => true]);
            } catch (\Throwable $e) {
                report($e);
            }
            return;
        }

        // Ensure demo user exists with correct password (siena@admin.com / admin123)
        try {
            \Artisan::call('db:seed', ['--class' => 'DemoUserSeeder', '--force' => true]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
