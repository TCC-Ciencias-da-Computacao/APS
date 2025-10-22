<?php

namespace App\Services;

class ShardManager
{
    /**
     * Decide the shard connection name for a given key.
     * Uses a simple consistent hash: crc32(key) % shardCount
     *
     * @param string|int $key
     * @return string
     */
    public static function connectionFor($key): string
    {
        // Prefer config() when available (Laravel runtime), fall back to env() for standalone scripts
        if (function_exists('config')) {
            $count = (int) config('database.shard_count', env('DB_SHARD_COUNT', 0));
        } else {
            $count = (int) env('DB_SHARD_COUNT', 0);
        }

        if ($count <= 0) {
            return config('database.default');
        }

        $hash = crc32((string) $key);
        $idx = $hash % $count;

        if (function_exists('config')) {
            $driver = config('database.shard_driver', env('DB_SHARD_DRIVER', env('DB_CONNECTION', 'sqlite')));
        } else {
            $driver = env('DB_SHARD_DRIVER', env('DB_CONNECTION', 'sqlite'));
        }

        if ($driver === 'sqlite') {
            return "sqlite_shard_{$idx}";
        }

        return "{$driver}_shard_{$idx}";
    }
}
