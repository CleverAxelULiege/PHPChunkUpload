<?php

namespace Upload;

use stdClass;
use Exception;
use Upload\DTOs\RequestUploadDTO;

class Mapper{
    public static function toRequestUpload(stdClass $payload){
        $requestUpload = new RequestUploadDTO();
        $requestUpload->fileName = $payload->fileName ?? null;
        $requestUpload->fileSize = $payload->fileSize ?? null;
        $requestUpload->recordDuration = $payload->recordDuration ?? null;
        $requestUpload->CSRFtoken = $payload->CSRFtoken ?? null;
        $requestUpload->tokenSessionUpload = $_COOKIE["token_session_upload"] ?? null;

        return $requestUpload;
    }

    public static function jsonDecode(string|null $jsonPayload){
        if($jsonPayload === null){
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => "No payload received."
            ]);
            exit;
        }

        try{
            return json_decode($jsonPayload, false, 512, JSON_THROW_ON_ERROR);
        }catch(Exception $e){
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => "Failed to decode the JSON payload sent."
            ]);
            exit;
        }
    }
}