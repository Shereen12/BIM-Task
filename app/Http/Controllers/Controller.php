<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected function apiResponse($message = null, $meta = null, $items = null, $error = null, $code = 200)
    {
        $array = [
            'meta' => $meta,
            'items' => $items,
            'status' => in_array($code, $this->successCode()) ? true : false,
            'message' => $message,
            'errors' => $error,
        ];
        return response($array, $code);
    }
    protected function successCode()
    {
        return [
            200, 201, 202,
        ];
    }
}
