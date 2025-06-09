<?php

namespace App\Helpers;

class BaseResponse
{
    public static function success($data = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ], $code);
    }

    public static function error($message = 'Terjadi kesalahan', $code = 500, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}
