<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Illuminate\Support\Facades\Http;

class NormalizationTest extends TestCase
{
    public function test_send_normalizes_phone_by_default(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '01012345678');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'mobile=201012345678');
        });
    }

    public function test_send_verify_normalizes_phone(): void
    {
        Http::fake(['*' => Http::response(['code' => 4901, 'message' => 'Success'])]);

        app('smsmisr')->sendVerify('1234', '01012345678');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'mobile=201012345678');
        });
    }

    public function test_auto_normalize_can_be_disabled(): void
    {
        config(['smsmisr.auto_normalize' => false]);

        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '01012345678');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'mobile=01012345678');
        });
    }

    public function test_already_normalized_number_unchanged(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '201012345678');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'mobile=201012345678');
        });
    }

    public function test_plus_prefix_normalized(): void
    {
        Http::fake(['*' => Http::response(['code' => 1901, 'message' => 'Success'])]);

        app('smsmisr')->send('Hello', '+201012345678');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'mobile=201012345678');
        });
    }
}
