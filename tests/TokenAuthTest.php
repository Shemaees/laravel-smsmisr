<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Illuminate\Support\Facades\Http;

class TokenAuthTest extends TestCase
{
    public function test_uses_username_password_by_default(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '201012345678');

        Http::assertSent(function ($request) {
            $url = $request->url();

            return str_contains($url, 'username=test_user')
                && str_contains($url, 'password=test_pass')
                && !str_contains($url, 'token=');
        });
    }

    public function test_uses_token_when_configured(): void
    {
        config(['smsmisr.token' => 'my-api-token']);

        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '201012345678');

        Http::assertSent(function ($request) {
            $url = $request->url();

            return str_contains($url, 'token=my-api-token')
                && !str_contains($url, 'username=')
                && !str_contains($url, 'password=');
        });
    }

    public function test_token_auth_for_verify(): void
    {
        config(['smsmisr.token' => 'my-api-token']);

        Http::fake(['*' => Http::response(['code' => 4901, 'message' => 'Success'])]);

        app('smsmisr')->sendVerify('1234', '201012345678');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'token=my-api-token');
        });
    }

    public function test_token_auth_for_balance(): void
    {
        config(['smsmisr.token' => 'my-api-token']);

        Http::fake(['*' => Http::response(['code' => 6000, 'message' => 'Success', 'balance' => 100])]);

        app('smsmisr')->balance();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'token=my-api-token');
        });
    }

    public function test_token_auth_for_bulk(): void
    {
        config(['smsmisr.token' => 'my-api-token']);

        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->sendBulk('Hello', ['201012345678']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'token=my-api-token');
        });
    }
}
