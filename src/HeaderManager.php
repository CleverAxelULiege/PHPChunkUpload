<?php

namespace Surveys;

class HeaderManager{
    public static function setBadRequestStatus(){
        http_response_code(400);
    }
    
    public static function setUnauthorizedStatus(){
        http_response_code(401);
    }

    public static function setServiceUnavailableStatus(){
        http_response_code(503);
    }

    public static function setCreatedStatus(){
        http_response_code(201);
    }
    
    public static function setUnprocessableEntityStatus(){
        http_response_code(422);
    }

    public static function setContentTypeToJson(){
        header('Content-Type: application/json');
    }
}