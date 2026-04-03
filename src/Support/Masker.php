<?php

declare(strict_types=1);

namespace Storage\ApiReplay\Support;

class Masker
{
    public static function maskHeaders(array $headers, array $sensitiveHeaders): array
    {
        return array_map(function ($key, $values) use ($sensitiveHeaders) {
            if (in_array(strtolower((string) $key), array_map('strtolower', $sensitiveHeaders))) {
                return ['********'];
            }
            return $values;
        }, array_keys($headers), $headers);
    }

    public static function maskData(array $data, array $sensitiveFields): array
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                $value = self::maskData($value, $sensitiveFields);
            } elseif (in_array(strtolower((string) $key), array_map('strtolower', $sensitiveFields))) {
                $value = '********';
            }
        }
        return $data;
    }
}
