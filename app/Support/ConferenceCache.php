<?php

namespace App\Support;

use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;

class ConferenceCache
{
    public static function remember(
        string $key,
               $ttl,
        Closure $callback
    ) {
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags(['conferences'])
                ->remember($key, $ttl, $callback);
        }

        return Cache::remember($key, $ttl, $callback);
    }
    public static function flush(): void
    {
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(['conferences'])->flush();
        }
    }
}
