<?php

use Surveys\Upload\FileManager;
use Surveys\HeaderManager;
use Surveys\Mapper;
use Surveys\Upload\FileStatusCodeManager;
use Surveys\Traduction\Traduction;

require(__DIR__ . "/../../../vendor/autoload.php");


HeaderManager::setContentTypeToJson();
JSONResponsePreventLargeContentLengthOrFileTooBig();

$payload = $_POST["payload"] ?? null;

$requestUpload = Mapper::JSONtoRequestUpload(Mapper::jsonDecode($payload));
$fileManager = new FileManager(Traduction::retrieveResponseMessage(), $requestUpload);


if (!$fileManager->validateCSRFToken($requestUpload)) {
    HeaderManager::setUnauthorizedStatus();

    echo json_encode([
        "msg" => "Failed to validate the CSRF token.",
        "status" => FileStatusCodeManager::INVALID_CSRF_TOKEN
    ]);
    exit;
}


$fileManager->buildFinaleVideo();
$fileManager->removeUploadTempDir($requestUpload->sessionTokenUpload);

echo json_encode([
    "msg" => "success"
]);