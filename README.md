# Laravel API Replay

Record incoming HTTP requests and responses, store them efficiently, and replay any request accurately for debugging and comparison.

## 🚀 Features

*   **Request/Response Logging**: Captured via middleware.
*   **Sensitive Data Masking**: Automatically masks passwords and sensitive headers.
*   **Accurate Replay**: Reconstructs requests using the Laravel HTTP client.
*   **Artisan Command**: Replay requests directly from the terminal.
*   **Extensible Storage**: Supports the Repo pattern for custom storage drivers.

## 📦 Installation

```bash
composer require nhatdo/laravel-api-replay
```

Publish configuration and migrations:

```bash
php artisan vendor:publish --tag="api-replay-config"
php artisan vendor:publish --tag="api-replay-migrations"
```

Run migrations:

```bash
php artisan migrate
```

## 🛠 Usage

### 1. Register Middleware

Add the `RecordApiRequest` middleware to your `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'api' => [
        \\Storage\\ApiReplay\\Http\\Middleware\\RecordApiRequest::class,
        // ...
    ],
];
```

### 2. Replay a Request

Get the UUID of a recorded log from the `api_logs` table, then:

```bash
php artisan replay:request {uuid}
```

Options:
*   `--override-header="Authorization: Bearer my-token"`
*   `--base-url="http://new-domain.test"`

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
