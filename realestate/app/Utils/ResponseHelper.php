<?php

namespace app\Utils;

class ResponseHelper
{
    public static function success($data = [], $message = 'Success')
    {
        echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
        exit;
    }

    public static function error($message, $code = 400)
    {
        http_response_code($code);
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
}
