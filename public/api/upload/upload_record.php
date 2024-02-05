<?php

use Upload\FileManager;
use Upload\HeaderManager;
use Upload\Mapper;
use Upload\StatusCodeManager;
use Upload\Traduction\Traduction;

require(__DIR__ . "/../../../vendor/autoload.php");

HeaderManager::setContentTypeToJson();
preventLargeContentLengthOrFileTooBig();

$payload = $_POST["payload"] ?? null;

$requestUpload = Mapper::toRequestUpload(Mapper::jsonDecode($payload));
$fileManager = new FileManager(Traduction::retrieve(), $requestUpload);

if (!$fileManager->validateCSRFToken($requestUpload)) {
    HeaderManager::setUnauthorizedStatus();

    echo json_encode([
        "msg" => "Failed to validate the CSRF token.",
        "status" => StatusCodeManager::INVALID_CSRF_TOKEN
    ]);
    exit;
}

$successToMoveFile = $fileManager->moveUploadedFileToTemp();

if ($successToMoveFile === false) {

    switch ($fileManager->statusUploadedFile) {

        case FileManager::STATUS_FAILED_TO_MOVE_FILE:
            HeaderManager::setServiceUnavailableStatus();
            echo json_encode([
                "msg" => "Resend file.",
                "CSRFToken" => $fileManager->refreshCSRFToken(),
                "status" => StatusCodeManager::FAILED_TO_MOVE_FILE
            ]);
            break;

        case FileManager::STATUS_CHUNK_TOO_BIG:
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => "Chunk received too big. Must be at or below :" . FileManager::MAX_CHUNK_SIZE_BYTES,
                "status" => StatusCodeManager::FILE_TOO_BIG
            ]);
            break;

        case FileManager::STATUS_NO_FILE_SENT:
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => "No file/chunk file sent.",
                "status" => StatusCodeManager::NO_FILE_SENT
            ]);
            break;
    }

    exit;
}

if ($fileManager->getUploadState()->currentFileSize > FileManager::MAX_FILE_SIZE_BYTES) {
    $fileManager->removeUploadTempDir($requestUpload->sessionTokenUpload);
    HeaderManager::setBadRequestStatus();
    echo json_encode([
        "msg" => "Your file has exceeded the max size allowed. Somehow you passed through the first validation while doing some shenanigans with the JS. Congrats.",
        "status" => StatusCodeManager::TOTAL_SIZE_EXCEEDED,
    ]);
    exit;
}

//refresh the session
$fileManager->setSessionTokenUpload($requestUpload->sessionTokenUpload);

echo json_encode([
    "msg" => "success",
    "CSRFToken" => $fileManager->refreshCSRFToken(),
    "status" => StatusCodeManager::OK
]);
