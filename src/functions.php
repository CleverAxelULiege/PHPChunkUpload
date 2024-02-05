<?php

use Surveys\DTOs\UserDTO;
use Surveys\Http\HeaderManager;
use Surveys\Http\Redirect;
use Surveys\ResponseMessage\FileManager;

function sessionStart()
{
    session_start([
        "cookie_secure" => true,
        "cookie_httponly" => true,
        "cookie_samesite" => "Strict"
    ]);
}

function needsToBeLogged(UserDTO|null $user)
{
    if ($user === null) {
        Redirect::to("/login.php");
    }
}

function needsAuthorization(UserDTO $user, array $requiredAuthorizations)
{
    foreach ($requiredAuthorizations as $requiredAuthorization) {
        foreach ($user->roles as $role) {
            if ($role == $requiredAuthorization) {
                return true;
            }
        }
    }

    Redirect::to("/login.php");
}

function requestMethodExpected(...$requestMethods){

    if(is_array($requestMethods[0])){
        $requestMethods = [...$requestMethods[0]];
    }

    foreach($requestMethods as $requestMethod){
        if(strtoupper($requestMethod) === strtoupper($_SERVER['REQUEST_METHOD'])){
            return true;
        }
    }

    Redirect::toUnauthorized();
}

function JSONResponsePreventLargeContentLengthOrFileTooBig()
{
    if (
        isset($_SERVER["CONTENT_LENGTH"]) &&
        (int) $_SERVER["CONTENT_LENGTH"] > (1024 * 1024 * (int) ini_get('post_max_size'))
    ) {
        HeaderManager::unprocessableEntityStatus();
        echo json_encode([
            "msg" => "The content length is too big."
        ]);
        exit;
    }


    if (isset($_FILES[FileManager::FILE_FIELD_NAME]) && ($_FILES[FileManager::FILE_FIELD_NAME]["error"] === UPLOAD_ERR_INI_SIZE || $_FILES[FileManager::FILE_FIELD_NAME]["size"] > FileManager::MAX_FILE_SIZE_BYTES)) {
        HeaderManager::unprocessableEntityStatus();
        echo json_encode([
            "msg" => "The file or chunk file sent is too big."
        ]);
        exit;
    }
}
