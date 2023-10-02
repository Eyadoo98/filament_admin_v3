<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
// return unified response for mobile
trait AppResponse
{
    public function genRes($result, $status, $message = "", $errors = []): JsonResponse
    {
        return response()->json([
            "errors" => $errors,
            "message" => $message,
            "result" => $result
        ], $status);
    }

    public function success($result, $message = ""): JsonResponse
    {
        return $this->genRes($result, Response::HTTP_OK, $message);
    }

    public function forbidden($message = ""): JsonResponse
    {
        return $this->genRes(null, Response::HTTP_FORBIDDEN, $message);
    }

    public function notFound($message = ""): JsonResponse
    {
        return $this->genRes(null, Response::HTTP_NOT_FOUND, $message);
    }

    public function  unauthorized($message = ""): JsonResponse
    {
        return $this->genRes(null, Response::HTTP_UNAUTHORIZED, $message);
    }
}
