<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Cache an expensive dashboard computation. Caching is skipped while
     * running tests so assertions always read fresh data.
     */
    protected function remember(string $key, Closure $callback, int $ttl = 60): mixed
    {
        if (app()->runningUnitTests()) {
            return $callback();
        }

        return Cache::remember($key, $ttl, $callback);
    }
}
