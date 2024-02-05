<?php

use Surveys\Mapper;
use Surveys\Http\HeaderManager;
use Surveys\Traduction\Traduction;
use Surveys\ResponseMessage\FileManager;

require(__DIR__ . "/../../../vendor/autoload.php");

HeaderManager::contentTypeToJson();
JSONResponsePreventLargeContentLengthOrFileTooBig();

$payload = $_POST["payload"] ?? null;

$requestUpload = Mapper::JSONtoRequestUpload(Mapper::jsonDecode($payload));
$fileManager = new FileManager(Traduction::retrieveResponseMessage(), $requestUpload);

if (!$fileManager->fileRespectRules()) {
    exit;
}

$CSRFToken = $fileManager->createTempFolderAndGetCSRFToken();

echo json_encode([
    "msg" => "success",
    "CSRFToken" => $CSRFToken,
]);
