<?php

namespace App\Helper;

Trait Message 
{
    protected function success(string $message, $data = [], string $data_key, int $status_code)
    {
        return response()->json([
            'isSuccess' => true,
            'message'     => $message,
            'data'      => [
                $data_key => $data
            ]
        ],$status_code);
    }

    protected function successWithData($data = [], string $data_key)
    {
        return response()->json([
            'isSuccess' => true,
            'data'      => [
                $data_key => $data
            ]
        ],200);
    }
    
    
    protected function error(string $message, $data = [], int $status_code)
    {
        return response()->json([
            'isSuccess' => false,
            'error'     => $message,
            'data'      => $data
        ],$status_code);
    }

    protected function successWithToken($token, $user)
    {
        return response()->json([
           'isSuccess'         => true,
           'message'           => 'Authentication successful',
           'data'              => [
               'user' => $user
           ],
            'headers' => [
                "Content-Type" => "application/json",
                "token"        => $token
            ],
        ], 200);
    }
}
