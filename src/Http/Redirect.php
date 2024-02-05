<?php

namespace Surveys\Http;

class Redirect{
    public static function to(string $path){
        header("Location: " . $path);
        exit;
    }

    public static function toNotFound(){
        self::to("/not_found.php");
    }
    
    public static function toUnauthorized(){
        self::to("/unauthorized.php");
    }
}