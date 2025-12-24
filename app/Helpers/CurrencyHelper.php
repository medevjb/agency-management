<?php

namespace App\Helpers;

use App\Models\Setting;

class CurrencyHelper
{
    /**
     * Get currency code from settings.
     */
    public static function getCode(): string
    {
        return Setting::get('currency_code', 'USD');
    }

    /**
     * Get currency symbol from settings.
     */
    public static function getSymbol(): string
    {
        return Setting::get('currency_symbol', '$');
    }

    /**
     * Get currency position (before or after).
     */
    public static function getPosition(): string
    {
        return Setting::get('currency_position', 'before');
    }

    /**
     * Get decimal separator.
     */
    public static function getDecimalSeparator(): string
    {
        return Setting::get('decimal_separator', '.');
    }

    /**
     * Get thousand separator.
     */
    public static function getThousandSeparator(): string
    {
        return Setting::get('thousand_separator', ',');
    }

    /**
     * Get number of decimal places.
     */
    public static function getDecimalPlaces(): int
    {
        return (int) Setting::get('decimal_places', 2);
    }

    /**
     * Format a number as currency.
     */
    public static function format(float $amount): string
    {
        $symbol = self::getSymbol();
        $position = self::getPosition();
        $decimalPlaces = self::getDecimalPlaces();
        $decimalSeparator = self::getDecimalSeparator();
        $thousandSeparator = self::getThousandSeparator();

        // Format the number
        $formatted = number_format(
            $amount,
            $decimalPlaces,
            $decimalSeparator,
            $thousandSeparator
        );

        // Add symbol based on position
        if ($position === 'before') {
            return $symbol.$formatted;
        }

        return $formatted.' '.$symbol;
    }

    /**
     * Get all currency settings as an array.
     */
    public static function getSettings(): array
    {
        return [
            'code' => self::getCode(),
            'symbol' => self::getSymbol(),
            'position' => self::getPosition(),
            'decimal_separator' => self::getDecimalSeparator(),
            'thousand_separator' => self::getThousandSeparator(),
            'decimal_places' => self::getDecimalPlaces(),
        ];
    }
}
