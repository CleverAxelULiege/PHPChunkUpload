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

if(!$fileManager->validateCSRFToken($requestUpload)){
    HeaderManager::setUnauthorizedStatus();

    echo json_encode([
        "msg" => "Failed to validate the CSRF token."
    ]);
    exit;
}

echo json_encode([
    "msg" => " it"
]);
