<?php

namespace App\Providers;

use Anthropic\Client as AnthropicClient;
use App\Models\Setting;
use App\Support\GlobalMonth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
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
        // Define gates for custom permissions
        Gate::define('accessValueDistribution', function ($user) {
            // Only operations roles can access value distribution
            return $user->hasRole(['admin', 'operation-manager', 'project-manager', 'team-leader', 'team-member']) || ! $user->isSales();
        });

        // Share project statuses with all views
        View::composer('*', function ($view) {
            $view->with('projectStatuses', Setting::getProjectStatuses());
            $view->with('taskStatuses', Setting::getTaskStatuses());

            // The header's month picker needs to know the active month and
            // options on every page, not just dashboard pages.
            if (auth()->check()) {
                $view->with('globalSelectedMonth', GlobalMonth::key());
                $view->with('globalAvailableMonths', GlobalMonth::available());
            }
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
