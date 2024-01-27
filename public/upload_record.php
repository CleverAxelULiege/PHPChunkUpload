<?php

use Upload\FileManager;
use Upload\HeaderManager;
use Upload\Mapper;
use Upload\Traduction\Traduction;

require(__DIR__ . "/../vendor/autoload.php");


HeaderManager::setContentTypeToJson();
preventLargeContentLengthOrFileTooBig();

$payload = $_POST["payload"] ?? null;

$requestUpload = Mapper::toRequestUpload(Mapper::jsonDecode($payload));
$fileManager = new FileManager(Traduction::retrieve(), $requestUpload);


if (!$fileManager->validateCSRFToken($requestUpload)) {
    HeaderManager::setUnauthorizedStatus();

    echo json_encode([
        "msg" => "Failed to validate the CSRF token.",
    ]);
    exit;
}

$successToMoveFile = $fileManager->moveUploadedFileToTemp();

if ($successToMoveFile === false) {

    switch ($fileManager->statusUploadedFile) {

        case FileManager::STATUS_FAILED_TO_MOVE_FILE:
            HeaderManager::setServiceUnavailableStatus();
            echo json_encode([
                "msg" => "Resend file",
                "CSRFToken" => $fileManager->refreshCSRFToken()
            ]);
            break;

        case FileManager::STATUS_DIRECTORY_DOESNT_EXIST:
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => "Failed to upload. Your session is missing.",
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
    ]);
    exit;
}

//refresh the session
$fileManager->setSessionTokenUpload($requestUpload->sessionTokenUpload);

echo json_encode([
    "msg" => "success",
    "CSRFToken" => $fileManager->refreshCSRFToken()
]);
