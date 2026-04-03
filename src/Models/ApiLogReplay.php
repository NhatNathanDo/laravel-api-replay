<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiLogReplay extends Model
{
    protected $table = 'api_log_replays';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'api_log_id',
        'response_status',
        'response_body',
        'duration_ms',
        'created_at',
    ];

    protected $casts = [
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

    public function apiLog()
    {
        return $this->belongsTo(ApiLog::class, 'api_log_id');
    }
}
