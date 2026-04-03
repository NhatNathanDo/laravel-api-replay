<?php

return [
    /*
     * Whether request recording is enabled.
     */
    'enabled' => env('API_REPLAY_ENABLED', true),

    /*
     * Whether response body should be logged.
     */
    'log_response' => env('API_REPLAY_LOG_RESPONSE', true),

    /*
     * Maximum body size (bytes) to store.
     */
    'max_body_size' => env('API_REPLAY_MAX_BODY_SIZE', 64000),

    /*
     * Routes to ignore logging.
     * Use asterisk for wildcards, e.g. 'api/debug/*'
     */
    'ignored_routes' => [
        'api/replay/*',
        'telescope/*',
        'horizon/*',
    ],

    /*
     * Storage driver selection.
     * Supported: 'database' (Default)
     */
    'storage_driver' => env('API_REPLAY_STORAGE_DRIVER', 'database'),

    /*
     * Whether to use the queue for logging.
     */
    'queue_enabled' => env('API_REPLAY_QUEUE_ENABLED', false),

    /*
     * Headers that should be masked.
     */
    'sensitive_headers' => [
        'Authorization',
        'Cookie',
        'X-CSRF-TOKEN',
    ],

    /*
     * JSON body fields that should be masked.
     */
    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'token',
        'access_token',
        'client_secret',
    ],
];
