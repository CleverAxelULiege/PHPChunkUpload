<?php

use Surveys\ResponseMessage\FileManager;
use Surveys\Http\HeaderManager;
use Surveys\Mapper;
use Surveys\ResponseMessage\FileStatusCodeManager;
use Surveys\Traduction\Traduction;

require(__DIR__ . "/../../../vendor/autoload.php");


HeaderManager::contentTypeToJson();
JSONResponsePreventLargeContentLengthOrFileTooBig();

$payload = $_POST["payload"] ?? null;

$requestUpload = Mapper::JSONtoRequestUpload(Mapper::jsonDecode($payload));
$fileManager = new FileManager(Traduction::retrieveResponseMessage(), $requestUpload);


if (!$fileManager->validateCSRFToken($requestUpload)) {
    HeaderManager::unauthorizedStatus();

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