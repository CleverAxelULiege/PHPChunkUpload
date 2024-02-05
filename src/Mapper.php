<?php

namespace Surveys;

use stdClass;
use Exception;
use Surveys\ResponseMessage\FileManager;
use Surveys\DTOs\UploadStateDTO;
use Surveys\DTOs\RequestUploadDTO;
use Surveys\DTOs\UserDTO;

class Mapper{

    public static function toUserDTO(stdClass $user){
        return new UserDTO(
            $user->id,
            $user->username,
            $user->password,
            json_decode($user->roles),
        );
    }

    public static function JSONtoUploadState(stdClass $payload){
        return new UploadStateDTO(
            $payload->currentChunkFile,
            $payload->currentFileSize,
            $payload->extension,
            $payload->CSRFToken,
            $payload->hashedFile
        );
    }

    public static function JSONtoRequestUpload(stdClass $payload){
        $requestUpload = new RequestUploadDTO();
        $requestUpload->fileName = $payload->fileName ?? null;
        $requestUpload->fileSize = $payload->fileSize ?? null;
        $requestUpload->recordDuration = (int)ceil($payload->recordDuration ?? 0);
        $requestUpload->CSRFtoken = $payload->CSRFtoken ?? null;
        $requestUpload->sessionTokenUpload = $_COOKIE[FileManager::COOKIE_NAME] ?? "UNKOWN";

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