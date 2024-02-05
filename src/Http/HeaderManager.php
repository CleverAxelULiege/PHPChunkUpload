<?php

namespace Surveys\Http;

class HeaderManager{
    public static function badRequestStatus(){
        http_response_code(400);
    }
    
    public static function unauthorizedStatus(){
        http_response_code(401);
    }

    public static function serviceUnavailableStatus(){
        http_response_code(503);
    }

    public static function createdStatus(){
        http_response_code(201);
    }
    
    public static function unprocessableEntityStatus(){
        http_response_code(422);
    }

    public static function contentTypeToJson(){
        header('Content-Type: application/json');
    }
}