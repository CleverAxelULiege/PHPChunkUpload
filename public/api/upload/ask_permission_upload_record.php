<?php

use Surveys\Upload\FileManager;
use Surveys\HeaderManager;
use Surveys\Mapper;
use Surveys\Traduction\Traduction;

require(__DIR__ . "/../../../vendor/autoload.php");

HeaderManager::setContentTypeToJson();
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
