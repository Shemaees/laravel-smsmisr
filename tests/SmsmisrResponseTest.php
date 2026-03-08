<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\SmsmisrResponse;

class SmsmisrResponseTest extends TestCase
{
    public function test_from_array_with_success_code(): void
    {
        $response = SmsmisrResponse::fromArray(['code' => 1901, 'message' => 'Success']);

        $this->assertEquals(1901, $response->code);
        $this->assertEquals('Success', $response->message);
        $this->assertTrue($response->success);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isFailed());
    }

    public function test_from_array_with_verify_success_code(): void
    {
        $response = SmsmisrResponse::fromArray(['code' => 4901, 'message' => 'OTP sent']);

        $this->assertTrue($response->isSuccessful());
    }

    public function test_from_array_with_balance_success_code(): void
    {
        $response = SmsmisrResponse::fromArray(['code' => 6000, 'message' => 'Success', 'balance' => 100]);

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(100, $response->raw['balance']);
    }

    public function test_from_array_with_failure_code(): void
    {
        $response = SmsmisrResponse::fromArray(['code' => 1906, 'message' => 'Invalid message']);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isFailed());
        $this->assertEquals(1906, $response->code);
    }

    public function test_from_array_with_missing_fields(): void
    {
        $response = SmsmisrResponse::fromArray([]);

        $this->assertEquals(0, $response->code);
        $this->assertEquals('', $response->message);
        $this->assertFalse($response->isSuccessful());
    }

    public function test_to_array_returns_raw_data(): void
    {
        $data = ['code' => 1901, 'message' => 'Success', 'extra' => 'value'];
        $response = SmsmisrResponse::fromArray($data);

        $this->assertEquals($data, $response->toArray());
    }

    public function test_raw_contains_all_original_data(): void
    {
        $data = ['code' => 6000, 'balance' => 500, 'currency' => 'EGP'];
        $response = SmsmisrResponse::fromArray($data);

        $this->assertEquals(500, $response->raw['balance']);
        $this->assertEquals('EGP', $response->raw['currency']);
    }
}
