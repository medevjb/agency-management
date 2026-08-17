<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;

class IdEncrypter
{
    /**
     * Encrypt a numeric id for use in URLs so sequential database ids are
     * never exposed to clients.
     */
    public static function encrypt(int $id): string
    {
        return Crypt::encryptString((string) $id);
    }

    /**
     * Decrypt a URL key back to its numeric id. Returns null when the value
     * is not a valid encrypted id (plain ids included), so callers fall back
     * to a 404.
     */
    public static function decrypt(string $key): ?int
    {
        try {
            $decrypted = Crypt::decryptString($key);

            return is_numeric($decrypted) ? (int) $decrypted : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
