<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Base TestCase for Pest tests.
 *
 * Provide method annotations to help static analyzers (Intelephense) recognize
 * common Laravel testing helper methods used in tests (e.g., actingAs, get, post).
 *
 * @method self actingAs(\Illuminate\Contracts\Auth\Authenticatable $user, string|null $guard = null)
 * @method \Illuminate\Testing\TestResponse get(string $uri, array $headers = [])
 * @method \Illuminate\Testing\TestResponse post(string $uri, array $data = [], array $headers = [])
 * @mixin \Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication
 */
abstract class TestCase extends BaseTestCase
{
    //
}
