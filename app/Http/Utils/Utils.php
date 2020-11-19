<?php


namespace App\Http\Utils;


class Utils
{
    public static function makeResponse($data = [], $message = 'OK')
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function genOTP($digits = 4)
    {
        return '1234';
//        return '' . rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }
}
