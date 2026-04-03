<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiLog extends Model
{
    protected $table = 'api_logs';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'method',
        'url',
        'path',
        'headers',
        'query_params',
        'request_body',
        'response_status',
        'response_body',
        'duration_ms',
        'ip',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'query_params' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    public function replays()
    {
        return $this->hasMany(ApiLogReplay::class, 'api_log_id');
    }
}
