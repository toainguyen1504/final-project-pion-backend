<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BasicApiController extends Controller
{
    protected function success($data, $message = null)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function error($message, $code = 500)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
}
