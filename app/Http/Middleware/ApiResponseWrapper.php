<?php

namespace App\Http\Middleware;

use Illuminate\Http\JsonResponse;

/**
 * Professional API Response Wrapper
 * Membuat semua API response konsisten dan profesional
 */
class ApiResponseWrapper
{
    /**
     * Success Response
     */
    public static function success($data = null, $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String()
        ], $statusCode);
    }

    /**
     * Error Response
     */
    public static function error($message = 'Error', $statusCode = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'status_code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String()
        ], $statusCode);
    }

    /**
     * Paginated Response
     */
    public static function paginated($items, $total, $perPage, $currentPage, $message = 'Data retrieved successfully')
    {
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => $message,
            'data' => $items,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'last_page' => ceil($total / $perPage),
                'from' => ($currentPage - 1) * $perPage + 1,
                'to' => min($currentPage * $perPage, $total)
            ],
            'timestamp' => now()->toIso8601String()
        ], 200);
    }

    /**
     * Cached Response with metadata
     */
    public static function cached($data, $cacheExpiresAt, $message = 'Cached data')
    {
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => $message,
            'data' => $data,
            'cache' => [
                'cached_at' => now()->toIso8601String(),
                'expires_at' => $cacheExpiresAt->toIso8601String(),
                'ttl_seconds' => $cacheExpiresAt->diffInSeconds(now())
            ],
            'timestamp' => now()->toIso8601String()
        ], 200);
    }
}
