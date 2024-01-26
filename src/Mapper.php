<?php

namespace Upload;

use stdClass;
use Exception;
use Upload\DTOs\RequestUploadDTO;
use Upload\DTOs\UploadStateDTO;

class Mapper{

    public static function toUploadState(stdClass $payload){
        return new UploadStateDTO(
            $payload->lastChunkFileReceivedAt,
            $payload->currentChunkFile,
            $payload->currentFileSize,
            $payload->extension,
            $payload->CSRFToken
        );
    }

    public static function toRequestUpload(stdClass $payload){
        $requestUpload = new RequestUploadDTO();
        $requestUpload->fileName = $payload->fileName ?? null;
        $requestUpload->fileSize = $payload->fileSize ?? null;
        $requestUpload->recordDuration = $payload->recordDuration ?? null;
        $requestUpload->CSRFtoken = $payload->CSRFtoken ?? null;
        $requestUpload->sessionTokenUpload = $_COOKIE[FileManager::COOKIE_NAME] ?? "UNKNOWN";

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