<?php
// app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\BatchParticipant;          
use App\Observers\BatchParticipantObserver; 

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
        // ✅ Register Observer
        BatchParticipant::observe(BatchParticipantObserver::class);
    }
}