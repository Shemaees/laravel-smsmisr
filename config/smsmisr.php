<?php

return [
    /*
     * Environment: 1 = Live, 2 = Test
     */
    'environment' => env('SMSMISR_ENVIRONMENT', 1),

    /*
     * API Endpoint
     */
    'endpoint' => env('SMSMISR_ENDPOINT', 'https://smsmisr.com/api/'),

    /*
     * Account Username
     */
    'username' => env('SMSMISR_USERNAME'),

    /*
     * Account Password
     */
    'password' => env('SMSMISR_PASSWORD'),

    /*
     * Default Sender Name
     */
    'sender' => env('SMSMISR_SENDER'),

    /*
     * M Signature
     */
    'm_signature' => env('SMSMISR_M_SIGNATURE'),

    /*
     * API Token (alternative to username/password)
     */
    'token' => env('SMSMISR_TOKEN'),

    /*
     * SMS Template ID
     */
    'sms_id' => env('SMSMISR_SMS_ID', 4945703),

    /*
     * SMS Verify Template ID
     */
    'sms_verify_id' => env('SMSMISR_SMS_VERIFY_ID', 72973),

    /*
     * Automatically normalize Egyptian phone numbers to international format
     */
    'auto_normalize' => env('SMSMISR_AUTO_NORMALIZE', true),

    /*
     * HTTP request timeout in seconds
     */
    'timeout' => env('SMSMISR_TIMEOUT', 30),

    /*
     * Number of retry attempts on failure (0 = no retries)
     */
    'retries' => env('SMSMISR_RETRIES', 0),

    /*
     * Delay between retries in milliseconds
     */
    'retry_delay' => env('SMSMISR_RETRY_DELAY', 100),

    /*
     * Rate limit: max messages per minute (null = no limit)
     */
    'rate_limit' => env('SMSMISR_RATE_LIMIT'),

    /*
     * Queue name for async sending (null = default queue)
     */
    'queue' => env('SMSMISR_QUEUE'),

    /*
     * Log channel for SMS operations (null = disabled)
     * Set to a valid log channel name (e.g. 'stack', 'single', 'smsmisr')
     */
    'log_channel' => env('SMSMISR_LOG_CHANNEL'),

    /*
     * Low balance threshold for smsmisr:check-balance command
     */
    'low_balance_threshold' => env('SMSMISR_LOW_BALANCE_THRESHOLD', 100),
];
