# 🚀 Laravel API Replay

Record incoming HTTP requests and responses, store them efficiently, and replay any request accurately for debugging and comparison via a modern Dashboard.

## ✨ Features

*   **⚡ Middleware Recording**: Automatically capture Request/Response details.
*   **🖥️ Dashboard UI**: Visual interface to browse logs and trigger replays (default at `/api-replay`).
*   **🛡️ Production Safety**: **Automatic DB Rollback** for dry-run replays to keep your data safe.
*   **🔐 Sensitive Data Masking**: Automatically masks passwords and sensitive headers.
*   **🎯 Accurate Replay**: Reconstructs requests using the Laravel HTTP client.
*   **🏗️ Artisan Command**: Still supports `php artisan replay:request {uuid}`.

## 📦 Installation

1. Install via composer:
```bash
composer require nhatdo/laravel-api-replay
```

2. Publish configuration, migrations, and UI views:
```bash
php artisan vendor:publish --tag="api-replay-config"
php artisan vendor:publish --tag="api-replay-migrations"
php artisan vendor:publish --tag="api-replay-views"
```

3. Run migrations:
```bash
php artisan migrate
```

## 🛠 Usage

### 1. Register Middleware

Add the middlewares to your `app/Http/Kernel.php` (or `bootstrap/app.php` for Laravel 11+):

```php
protected $middlewareGroups = [
    'api' => [
        \Storage\ApiReplay\Http\Middleware\RecordApiRequest::class,
        // Optional: Add this to enable automatic DB rollback during Dry Run replays
        \Storage\ApiReplay\Http\Middleware\SimulationMiddleware::class,
    ],
];
```

### 2. Dashboard UI

Access the dashboard at `/api-replay` in your browser. You can:
*   View all recorded API calls.
*   Compare original response vs replay response.
*   Enable **Dry Run Mode** to simulate the request without persisting DB changes.

### 3. Programmatic Safety

Use the `ApiReplay` helper to skip side-effects (like sending real emails) during a replay:

```php
use Storage\ApiReplay\Support\ApiReplay;

if (ApiReplay::isDryRun()) {
    // Skip sending real SMS or calling external 3rd party APIs
    return;
}
```

## ⚙️ Configuration

Check `config/api-replay.php` for options:
*   `enabled`: Toggle recording.
*   `log_response`: Enable/disable response body logging.
*   `sensitive_headers`: List of headers to mask.
*   `sensitive_fields`: List of JSON fields to mask.

## 🧪 Testing

```bash
./vendor/bin/phpunit
```
