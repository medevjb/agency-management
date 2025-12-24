<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        // Blade directive for currency symbol
        Blade::directive('currencySymbol', function () {
            return '<?php echo \App\Helpers\CurrencyHelper::getSymbol(); ?>';
        });

        // Blade directive for currency code
        Blade::directive('currencyCode', function () {
            return '<?php echo \App\Helpers\CurrencyHelper::getCode(); ?>';
        });

        // Blade directive for formatting currency
        Blade::directive('currency', function ($expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::format({$expression}); ?>";
        });
    }
}
