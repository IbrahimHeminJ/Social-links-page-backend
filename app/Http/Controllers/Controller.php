<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageReasource;

abstract class Controller
{
    protected function success($message = null, $data = null, $status = null)
    {
        return new MessageReasource([
            'message' => $message,
            'data' => $data,
            'status' => $status,
        ]);
    }

    protected function error($message = null, $status = null)
    {
        return new MessageReasource([
            'message' => $message,
            'status' => $status,
        ]);
    }
}
