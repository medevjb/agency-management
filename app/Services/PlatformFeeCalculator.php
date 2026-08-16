<?php

namespace App\Services;

use App\Models\Profile;

class PlatformFeeCalculator
{
    /**
     * Platform cut, keyed by Profile::platform.
     */
    private const RATES = [
        'fiverr' => 0.20,
        'upwork' => 0.10,
    ];

    /**
     * The default used when an order has no freelance profile, or the
     * profile's platform isn't recognised — preserves the app's original
     * fixed 80% net assumption.
     */
    private const DEFAULT_RATE = 0.20;

    public static function rateFor(?int $freelanceProfileId): float
    {
        if ($freelanceProfileId === null) {
            return self::DEFAULT_RATE;
        }

        $platform = Profile::query()->whereKey($freelanceProfileId)->value('platform');

        return self::rateForPlatform($platform);
    }

    /**
     * The platform cut for a platform name, without touching the database.
     * Callers looping over many orders can resolve the platform→rate mapping
     * once (e.g. a single pluck keyed by profile id) and avoid one query per
     * order.
     */
    public static function rateForPlatform(?string $platform): float
    {
        return self::RATES[strtolower((string) $platform)] ?? self::DEFAULT_RATE;
    }

    /**
     * The order's value after its platform's cut — what actually gets
     * distributed to whoever delivered it.
     */
    public static function netFor(float $grossValue, ?int $freelanceProfileId): float
    {
        return round($grossValue * (1 - self::rateFor($freelanceProfileId)), 2);
    }
}
