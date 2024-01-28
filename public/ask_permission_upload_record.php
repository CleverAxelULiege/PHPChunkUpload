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

if (!$fileManager->fileRespectRules()) {
    exit;
}

$fileManager->tryToRemoveOldTempDir();

$CSRFToken = $fileManager->createTempFolderAndGetCSRFToken();

echo json_encode([
    "msg" => "success",
    "CSRFToken" => $CSRFToken,
]);
