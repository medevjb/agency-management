<?php

namespace Tests;

use App\Support\IdEncrypter;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    /**
     * Assert the response is a redirect to a model's show route. Because
     * encrypted route keys use a fresh IV on every encryption, the redirect
     * URL cannot be compared against a locally generated one — instead the
     * id embedded in the redirect is decrypted and compared against the model.
     */
    protected function assertRedirectToModel(TestResponse $response, string $routeName, $model): void
    {
        $response->assertRedirect();

        $location = $response->headers->get('Location');

        $this->assertStringContainsString('/'.$routeName.'/', $location);
        $this->assertSame($model->id, IdEncrypter::decrypt(Str::afterLast($location, '/')));
    }
}
