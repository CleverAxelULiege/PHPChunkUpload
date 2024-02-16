<?php

use Surveys\SurveyResponseMessage\FileManager;
use Surveys\Http\HeaderManager;
use Surveys\Mapper;
use Surveys\SurveyResponseMessage\FileStatusCodeManager;
use Surveys\Traduction\Traduction;

require(__DIR__ . "/../../../vendor/autoload.php");

HeaderManager::contentTypeToJson();
JSONResponsePreventLargeContentLengthOrFileTooBig();

$payload = $_POST["payload"] ?? null;

$requestUpload = Mapper::JSONtoRequestUpload(Mapper::jsonDecode($payload));
$fileManager = new FileManager(Traduction::retrieveSurveyResponseMessage()->traductions, $requestUpload);

if (!$fileManager->validateCSRFToken($requestUpload)) {
    HeaderManager::unauthorizedStatus();

    echo json_encode([
        "msg" => "Failed to validate the CSRF token.",
        "status" => FileStatusCodeManager::INVALID_CSRF_TOKEN
    ]);
    exit;
}

$successToMoveFile = $fileManager->moveUploadedFileToTemp();

if ($successToMoveFile === false) {

    switch ($fileManager->statusUploadedFile) {

        case FileStatusCodeManager::FAILED_TO_MOVE_FILE:
            HeaderManager::serviceUnavailableStatus();
            echo json_encode([
                "msg" => "Resend file.",
                "CSRFToken" => $fileManager->refreshCSRFToken(),
                "status" => FileStatusCodeManager::FAILED_TO_MOVE_FILE
            ]);
            break;

        case FileStatusCodeManager::FILE_TOO_BIG:
            HeaderManager::badRequestStatus();
            echo json_encode([
                "msg" => "Chunk received too big. Must be at or below :" . FileManager::MAX_CHUNK_SIZE_BYTES,
                "status" => FileStatusCodeManager::FILE_TOO_BIG
            ]);
            break;

        case FileStatusCodeManager::NO_FILE_SENT:
            HeaderManager::badRequestStatus();
            echo json_encode([
                "msg" => "No file/chunk file sent.",
                "status" => FileStatusCodeManager::NO_FILE_SENT
            ]);
            break;

        default:
            HeaderManager::badRequestStatus();
            echo json_encode([
                "msg" => "An unknown error occured.",
                "status" => FileStatusCodeManager::UNKNOWN_ERROR
            ]);
            break;
    }

    exit;
}

if ($fileManager->getUploadState()->currentFileSize > FileManager::MAX_FILE_SIZE_BYTES) {
    $fileManager->removeUploadTempDir($requestUpload->sessionTokenUpload);
    HeaderManager::badRequestStatus();
    echo json_encode([
        "msg" => "Your file has exceeded the max size allowed. Somehow you passed through the first validation while doing some shenanigans with the JS. Congrats.",
        "status" => FileStatusCodeManager::TOTAL_SIZE_EXCEEDED,
    ]);
    exit;
}

//refresh the session
$fileManager->setSessionTokenUpload($requestUpload->sessionTokenUpload);

echo json_encode([
    "msg" => "success",
    "CSRFToken" => $fileManager->refreshCSRFToken(),
    "status" => FileStatusCodeManager::OK
]);
