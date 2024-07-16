<?php

use Illuminate\Validation\ValidationException;

function apiResponseWithSuccess(string $message, $data = [], int $code = 200)
{
    if ($data) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    } else {
        return response()->json([
            'success' => true,
            'message' => $message
        ], $code);
    }
}

function apiResponseWithError(mixed $message, $code = 400)
{
    if ($message instanceof ValidationException) {
        return response()->json([
            'success' => false,
            'message' => $message->getMessage(),
            'errors' => $message->errors()
        ], $code);
    } elseif (is_object($message)) {
        return response()->json([
            'success' => false,
            'message' => $message->first(),
            'errors' => $message
        ], $code);
    }

    return response()->json([
        'success' => false,
        'message' => $message
    ], $code);
}
