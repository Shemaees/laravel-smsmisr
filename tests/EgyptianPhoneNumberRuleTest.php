<?php

namespace Ghanem\LaravelSmsmisr\Tests;

use Ghanem\LaravelSmsmisr\Rules\EgyptianPhoneNumber;
use Illuminate\Support\Facades\Validator;

class EgyptianPhoneNumberRuleTest extends TestCase
{
    public function test_valid_number_passes(): void
    {
        $validator = Validator::make(
            ['phone' => '01012345678'],
            ['phone' => [new EgyptianPhoneNumber]],
        );

        $this->assertTrue($validator->passes());
    }

    public function test_valid_international_passes(): void
    {
        $validator = Validator::make(
            ['phone' => '+201112345678'],
            ['phone' => [new EgyptianPhoneNumber]],
        );

        $this->assertTrue($validator->passes());
    }

    public function test_invalid_number_fails(): void
    {
        $validator = Validator::make(
            ['phone' => '01312345678'],
            ['phone' => [new EgyptianPhoneNumber]],
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('phone', $validator->errors()->toArray());
    }

    public function test_empty_string_fails(): void
    {
        $validator = Validator::make(
            ['phone' => ''],
            ['phone' => ['required', new EgyptianPhoneNumber]],
        );

        $this->assertTrue($validator->fails());
    }

    public function test_non_string_fails(): void
    {
        $validator = Validator::make(
            ['phone' => 12345],
            ['phone' => [new EgyptianPhoneNumber]],
        );

        $this->assertTrue($validator->fails());
    }

    public function test_too_short_fails(): void
    {
        $validator = Validator::make(
            ['phone' => '0101234'],
            ['phone' => [new EgyptianPhoneNumber]],
        );

        $this->assertTrue($validator->fails());
    }
}
