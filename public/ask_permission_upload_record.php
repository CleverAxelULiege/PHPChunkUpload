<?php

use Upload\FileManager;
use Upload\HeaderManager;
use Upload\Mapper;
use Upload\Traduction\Traduction;

require(__DIR__ . "/../vendor/autoload.php");

HeaderManager::setContentTypeToJson();

$payload = $_POST["payload"] ?? null;
$requestUpload = Mapper::toRequestUpload(Mapper::jsonDecode($payload));

$fileManager = new FileManager(Traduction::retrieve());

if(!$fileManager->fileRespectRule($requestUpload)){
    exit;
}

echo json_encode([
    "msg" => $fileManager->createTempFolder()
]);