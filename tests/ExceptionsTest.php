<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\Exceptions\SmsmisrApiException;
use Ghanem\LaravelSmsmisr\Exceptions\SmsmisrAuthenticationException;
use Ghanem\LaravelSmsmisr\Exceptions\SmsmisrException;
use Ghanem\LaravelSmsmisr\Exceptions\SmsmisrInsufficientBalanceException;

class ExceptionsTest extends TestCase
{
    public function test_base_exception_holds_response(): void
    {
        $response = ['code' => 1906, 'message' => 'Error'];
        $exception = new SmsmisrException('Error', 1906, $response);

        $this->assertEquals('Error', $exception->getMessage());
        $this->assertEquals(1906, $exception->getCode());
        $this->assertEquals($response, $exception->getResponse());
    }

    public function test_base_exception_with_null_response(): void
    {
        $exception = new SmsmisrException('Error');

        $this->assertNull($exception->getResponse());
    }

    public function test_api_exception_from_response_with_known_code(): void
    {
        $exception = SmsmisrApiException::fromResponse(['code' => 1904, 'message' => 'Bad sender']);

        $this->assertEquals('Invalid sender', $exception->getMessage());
        $this->assertEquals(1904, $exception->getCode());
    }

    public function test_api_exception_from_response_with_unknown_code(): void
    {
        $exception = SmsmisrApiException::fromResponse(['code' => 9999, 'message' => 'Unknown']);

        $this->assertEquals('Unknown', $exception->getMessage());
        $this->assertEquals(9999, $exception->getCode());
    }

    public function test_api_exception_from_response_with_no_message(): void
    {
        $exception = SmsmisrApiException::fromResponse(['code' => 8888]);

        $this->assertStringContainsString('8888', $exception->getMessage());
    }

    public function test_authentication_exception_from_response(): void
    {
        $exception = SmsmisrAuthenticationException::fromResponse(['code' => 1902, 'message' => 'Bad creds']);

        $this->assertInstanceOf(SmsmisrApiException::class, $exception);
        $this->assertInstanceOf(SmsmisrException::class, $exception);
        $this->assertEquals('Bad creds', $exception->getMessage());
    }

    public function test_insufficient_balance_exception_from_response(): void
    {
        $exception = SmsmisrInsufficientBalanceException::fromResponse(['code' => 1903, 'message' => 'No balance']);

        $this->assertInstanceOf(SmsmisrApiException::class, $exception);
        $this->assertEquals('No balance', $exception->getMessage());
    }

    public function test_known_errors_returns_array(): void
    {
        $errors = SmsmisrApiException::getKnownErrors();

        $this->assertIsArray($errors);
        $this->assertArrayHasKey(1902, $errors);
        $this->assertArrayHasKey(1903, $errors);
        $this->assertArrayHasKey(4902, $errors);
    }

    public function test_exception_hierarchy(): void
    {
        $this->assertTrue(is_subclass_of(SmsmisrApiException::class, SmsmisrException::class));
        $this->assertTrue(is_subclass_of(SmsmisrAuthenticationException::class, SmsmisrApiException::class));
        $this->assertTrue(is_subclass_of(SmsmisrInsufficientBalanceException::class, SmsmisrApiException::class));
    }
}
