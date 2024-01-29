<?php

use Upload\FileManager;
use Upload\HeaderManager;
use Upload\Mapper;
use Upload\StatusCodeManager;
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
        "status" => StatusCodeManager::INVALID_CSRF_TOKEN
    ]);
    exit;
}


$fileManager->buildFinaleVideo();
$fileManager->removeUploadTempDir($requestUpload->sessionTokenUpload);

echo json_encode([
    "msg" => "success"
]);