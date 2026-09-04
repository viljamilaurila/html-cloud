<?php

namespace App\Providers;

use App\Models\Document;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Document ids are short alphanumerics (see Document::generateId).
        Route::pattern('document', '[A-Za-z0-9]{1,12}');

        // Footer counter: served from cache, refreshed in the background once an
        // hour and kept for a day, so no request ever waits on the COUNT.
        View::composer('partials.footer', function (\Illuminate\View\View $view) {
            $view->with('documentCount', Cache::flexible(
                'document_count', [3600, 86400], fn () => Document::count(),
            ));
        });
    }
}
