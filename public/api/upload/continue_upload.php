<?php

use Surveys\Mapper;
use Surveys\Http\HeaderManager;
use Surveys\Traduction\Traduction;
use Surveys\SurveyResponseMessage\FileManager;

require(__DIR__ . "/../../../vendor/autoload.php");

HeaderManager::contentTypeToJson();
JSONResponsePreventLargeContentLengthOrFileTooBig();

$payload = $_POST["payload"] ?? null;

$requestUpload = Mapper::JSONtoRequestUpload(Mapper::jsonDecode($payload));
$fileManager = new FileManager(Traduction::retrieveSurveyResponseMessage()->traductions, $requestUpload);

if (!$fileManager->isTempFolderActive()) {
    $fileManager->removeUploadTempDir($requestUpload->sessionTokenUpload);
    HeaderManager::badRequestStatus();
    echo json_encode([
        "msg" => "Upload is no longer active or doesn't exist. Restart to upload from the beginning."
    ]);
    
    exit;
}

if($fileManager->hashedFileCorrespond()){
    echo json_encode([
        "msg" => "File corresponds. Continue the upload.",
        "nextChunk" => $fileManager->getNextChunk(),
        "CSRFToken" => $fileManager->refreshCSRFToken(),
    ]);
} else {
    HeaderManager::badRequestStatus();
    echo json_encode([
        "msg" => "File doesn't correspond. Restart to upload from the beginning.",
    ]);
}