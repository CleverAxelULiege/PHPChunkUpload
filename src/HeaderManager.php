<?php

namespace Upload;

class HeaderManager{
    public static function setBadRequestStatus(){
        http_response_code(400);
    }

    public static function setContentTypeToJson(){
        header('Content-Type: application/json');
    }
}