<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NotificationService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\ContactUs;
use App\Models\Banner;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // defaults (important)
        $contact_detail = null;
        $upperBanners   = collect();
        $middleBanners  = collect();

        // Contact Us table check
        if (Schema::hasTable('contact_us')) {
            $contact_detail = ContactUs::select(
                'phone_number',
                'whatsapp_number',
                'email',
                'address'
            )->first();
        }

        // Banner table check
        if (Schema::hasTable('banners')) {
            $upperBanners  = Banner::where('type', 1)->get();
            $middleBanners = Banner::where('type', 2)->get();
        }

        View::share([
            'contact_detail' => $contact_detail,
            'upperBanners'   => $upperBanners,
            'middleBanners'  => $middleBanners,
        ]);
    }
}
