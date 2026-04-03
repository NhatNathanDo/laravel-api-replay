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

    /*
     * Custom header for dry run (safe simulation).
     */
    'dry_run_header' => env('API_REPLAY_DRY_RUN_HEADER', 'X-Api-Replay-Dry-Run'),

    /*
     * Environments where replay is allowed.
     * Leave empty to allow all (not recommended).
     */
    'allow_replay_environments' => ['local', 'staging', 'development'],

    /*
     * Enable automatic DB rollback for dry run requests.
     */
    'enable_db_rollback' => env('API_REPLAY_ENABLE_ROLLBACK', true),
];
