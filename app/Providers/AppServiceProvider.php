<?php

namespace App\Providers;

use Anthropic\Client as AnthropicClient;
use App\Models\Setting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AnthropicClient::class, function () {
            return new AnthropicClient(apiKey: config('services.anthropic.api_key'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share project statuses with all views
        View::composer('*', function ($view) {
            $view->with('projectStatuses', Setting::getProjectStatuses());
            $view->with('taskStatuses', Setting::getTaskStatuses());
        });

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
