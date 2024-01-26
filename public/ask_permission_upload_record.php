<?php

use Upload\FileManager;
use Upload\HeaderManager;
use Upload\Mapper;
use Upload\Traduction\Traduction;

require(__DIR__ . "/../vendor/autoload.php");

HeaderManager::setContentTypeToJson();

$payload = $_POST["payload"] ?? null;

$requestUpload = Mapper::toRequestUpload(Mapper::jsonDecode($payload));
$fileManager = new FileManager(Traduction::retrieve(), $requestUpload);

if (!$fileManager->fileRespectRule($requestUpload)) {
    exit;
}

if ($fileManager->doesTempFolderExistAndActive($requestUpload)) {
    echo json_encode([
        "msg" => "to do"
    ]);

    exit;
}

$uploadState = $fileManager->createTempFolderAndGetState($requestUpload);

echo json_encode([
    "msg" => "success",
    "CSRFToken" => $uploadState->CSRFToken
]);
