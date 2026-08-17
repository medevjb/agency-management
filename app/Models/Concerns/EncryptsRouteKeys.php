<?php

namespace App\Models\Concerns;

use App\Support\IdEncrypter;

trait EncryptsRouteKeys
{
    /**
     * Use an encrypted id as the route key so sequential database ids are
     * never exposed in URLs.
     */
    public function getRouteKey(): string
    {
        return IdEncrypter::encrypt($this->getKey());
    }

    /**
     * Resolve the encrypted route key back to a model instance. Anything
     * that is not a valid encrypted id resolves to null (a 404).
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        if ($field) {
            return parent::resolveRouteBinding($value, $field);
        }

        $id = IdEncrypter::decrypt($value);

        if ($id === null) {
            return null;
        }

        return $this->newQuery()->where($this->getRouteKeyName(), $id)->first();
    }
}
