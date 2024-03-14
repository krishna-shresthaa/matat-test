<?php

namespace App\Resource;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Create a JSON response.
     *
     * @param mixed  $data
     * @param string $message
     * @param int    $statusCode
     * @return JsonResponse
     */
    public static function make($data = null, $message = null, $statusCode = 200)
    {
        $response = [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'message' => $message,
            'data'    => $data,
        ];

        return new JsonResponse($response, $statusCode);
    }
}
