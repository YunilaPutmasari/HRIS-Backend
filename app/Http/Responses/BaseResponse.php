<?php

namespace App\Http\Responses;

class BaseResponse
{
    public static function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'meta' => [
                'success' => true,
                'message' => $message,
                'code' => $code
            ],
            'data' => $data
        ], $code);
    }

    public static function error($data = null, $message = 'Error', $code = 500)
    {
        return response()->json([
            'meta' => [
                'success' => false,
                'message' => $message,
                'code' => $code
            ],
            'data' => $data
        ], $code);
    }

    public static function redirect(string $url, int $status = 302)
    {
        return redirect()->away($url, $status);
    }
}
