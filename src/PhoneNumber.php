<?php

namespace Ghanem\LaravelSmsmisr;

class PhoneNumber
{
    /**
     * Valid Egyptian mobile prefixes (after country code 20).
     */
    protected const MOBILE_PREFIXES = ['10', '11', '12', '15'];

    /**
     * Normalize an Egyptian phone number to international format (2XXXXXXXXXX).
     * Accepts: 01012345678, 2010012345678, +201012345678, 201012345678
     */
    public static function normalize(string $phone): string
    {
        // Remove whitespace, dashes, parentheses, and plus sign
        $phone = preg_replace('/[\s\-\(\)\+]/', '', $phone);

        // Remove leading 00 (international dialing prefix)
        if (str_starts_with($phone, '00')) {
            $phone = substr($phone, 2);
        }

        // If starts with 0 (local format), replace leading 0 with 20
        if (str_starts_with($phone, '0')) {
            $phone = '20' . substr($phone, 1);
        }

        // If doesn't start with 20, prepend it
        if (!str_starts_with($phone, '20')) {
            $phone = '20' . $phone;
        }

        return $phone;
    }

    /**
     * Validate that a phone number is a valid Egyptian mobile number.
     */
    public static function isValid(string $phone): bool
    {
        $normalized = static::normalize($phone);

        // Egyptian mobile: 20 + 2-digit prefix + 8 digits = 12 digits total
        if (strlen($normalized) !== 12) {
            return false;
        }

        // Must start with 20 followed by a valid mobile prefix
        $prefix = substr($normalized, 2, 2);

        return in_array($prefix, self::MOBILE_PREFIXES);
    }
}
