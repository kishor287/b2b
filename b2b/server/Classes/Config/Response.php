<?php

namespace Panel\Server\Classes\Config;


final class Response {

    public static function json(array $data, int $statusCode ){
        http_response_code($statusCode);
        echo json_encode($data);
    }

    public static function success(string $message){
        http_response_code(200);
        echo json_encode(['status' => 1,'statusCode' => 200,'message' => $message]);
    }
    public static function error(string $message){
        http_response_code(500);
        echo json_encode(['status' =>0,'statusCode' => 500,'message' => $message]);
    }

    public static function badRequest(string $message){
        http_response_code(400);
        echo json_encode(['status' => 0,'statusCode' => 400,'responseMessage'=>'Bad Request','message' => $message]);
    }
}