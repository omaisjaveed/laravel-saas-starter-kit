<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Consistent JSON response envelope for the API.
 */
trait ApiResponse
{
    /**
     * Success response.
     *
     * @param  mixed  $data
     */
    protected function success($data = null, string $message = 'OK', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Error response.
     *
     * @param  mixed  $errors
     */
    protected function error(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
