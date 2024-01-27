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

if (!$fileManager->isTempFolderActive()) {
    HeaderManager::setBadRequestStatus();
    echo json_encode([
        "msg" => "Upload is no longer active. Restart upload from the beginning."
    ]);
    
    exit;
}

if($fileManager->hashedFileCorrespond()){
    echo json_encode([
        "msg" => "Hash file corresponds.",
        "nextChunk" => $fileManager->getNextChunk(),
        "CSRFToken" => $fileManager->refreshCSRFToken(),
    ]);
} else {
    HeaderManager::setBadRequestStatus();
    echo json_encode([
        "msg" => "Hash file doesn't correspond. Restart upload from the beginning.",
    ]);
}