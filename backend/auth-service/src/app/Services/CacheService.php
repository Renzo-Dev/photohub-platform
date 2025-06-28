<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function storeRefreshTokenInCache($refreshToken, $userId, $accessToken, $ttl)
    {
        try {
            // Store the refresh token in the cache with the user ID and access token
            Cache::put("refresh_token:{$refreshToken}", [
                'user_id' => $userId,
                'access_token' => $accessToken,
            ], $ttl);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during caching
            throw new \Exception('Failed to store refresh token in cache: ' . $e->getMessage());
        }
    }
}
