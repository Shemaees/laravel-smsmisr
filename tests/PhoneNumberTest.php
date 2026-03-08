<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\PhoneNumber;

class PhoneNumberTest extends TestCase
{
    // --- normalize ---

    public function test_normalize_local_format(): void
    {
        $this->assertEquals('201012345678', PhoneNumber::normalize('01012345678'));
    }

    public function test_normalize_with_country_code(): void
    {
        $this->assertEquals('201012345678', PhoneNumber::normalize('201012345678'));
    }

    public function test_normalize_with_plus(): void
    {
        $this->assertEquals('201012345678', PhoneNumber::normalize('+201012345678'));
    }

    public function test_normalize_with_double_zero(): void
    {
        $this->assertEquals('201012345678', PhoneNumber::normalize('00201012345678'));
    }

    public function test_normalize_strips_spaces(): void
    {
        $this->assertEquals('201012345678', PhoneNumber::normalize('010 1234 5678'));
    }

    public function test_normalize_strips_dashes(): void
    {
        $this->assertEquals('201012345678', PhoneNumber::normalize('010-1234-5678'));
    }

    public function test_normalize_strips_parentheses(): void
    {
        $this->assertEquals('201012345678', PhoneNumber::normalize('(010) 12345678'));
    }

    public function test_normalize_all_prefixes(): void
    {
        $this->assertEquals('201012345678', PhoneNumber::normalize('01012345678'));
        $this->assertEquals('201112345678', PhoneNumber::normalize('01112345678'));
        $this->assertEquals('201212345678', PhoneNumber::normalize('01212345678'));
        $this->assertEquals('201512345678', PhoneNumber::normalize('01512345678'));
    }

    // --- isValid ---

    public function test_valid_local_numbers(): void
    {
        $this->assertTrue(PhoneNumber::isValid('01012345678'));
        $this->assertTrue(PhoneNumber::isValid('01112345678'));
        $this->assertTrue(PhoneNumber::isValid('01212345678'));
        $this->assertTrue(PhoneNumber::isValid('01512345678'));
    }

    public function test_valid_international_numbers(): void
    {
        $this->assertTrue(PhoneNumber::isValid('201012345678'));
        $this->assertTrue(PhoneNumber::isValid('+201112345678'));
        $this->assertTrue(PhoneNumber::isValid('00201212345678'));
    }

    public function test_invalid_prefix(): void
    {
        $this->assertFalse(PhoneNumber::isValid('01312345678'));
        $this->assertFalse(PhoneNumber::isValid('01412345678'));
        $this->assertFalse(PhoneNumber::isValid('01612345678'));
    }

    public function test_invalid_length_too_short(): void
    {
        $this->assertFalse(PhoneNumber::isValid('0101234567'));
    }

    public function test_invalid_length_too_long(): void
    {
        $this->assertFalse(PhoneNumber::isValid('010123456789'));
    }

    public function test_invalid_non_numeric(): void
    {
        $this->assertFalse(PhoneNumber::isValid('abcdefghijk'));
    }

    public function test_invalid_empty(): void
    {
        $this->assertFalse(PhoneNumber::isValid(''));
    }
}
