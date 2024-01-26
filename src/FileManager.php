<?php

namespace Upload;

use Upload\DTOs\RequestUploadDTO;

class FileManager
{

    public function __construct(private array $traduction)
    {
    }

    /**
     * Mettre à NULL pour ne pas en tenir compte
     */
    const MAX_FILE_SIZE_BYTES = 1000 * 1000 * 150;

    /**
     * Mettre à NULL pour ne pas en tenir compte
     */
    const MAX_RECORDING_TIME_SECOND = 60 * 5;

    /**
     * Mettre à NULL pour ne pas en tenir compte
     */
    const MIN_RECORDING_TIME_SECOND = 0;

    const ALLOWED_EXTENSIONS = ["mp4", "webm"];

    public function fileRespectRule(RequestUploadDTO $requestUpload)
    {
        
        if ($requestUpload->fileName === null || trim($requestUpload->fileName) === "") {
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => $this->traduction["file"]["no_file_name"]
            ]);
            return false;
        }

        $fileExtension = pathinfo($requestUpload->fileName)['extension'] ?? "";

        if (!in_array($fileExtension, FileManager::ALLOWED_EXTENSIONS)) {
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => str_replace(":PLACEHOLDER", implode(", ", FileManager::ALLOWED_EXTENSIONS), $this->traduction["file"]["unallowed_extension"])
            ]);
            return false;
        }

        if ($requestUpload->fileSize === null) {
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => $this->traduction["file"]["no_file_size"]
            ]);
            return false;
        }

        if (FileManager::MAX_FILE_SIZE_BYTES !== null && $requestUpload->fileSize > FileManager::MAX_FILE_SIZE_BYTES) {
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => str_replace(":PLACEHOLDER", FileManager::MAX_FILE_SIZE_BYTES, $this->traduction["file"]["file_too_big"])
            ]);
            return false;
        }

        if ($requestUpload->fileSize <= 0) {
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => $this->traduction["file"]["empty_file"]
            ]);
            return false;
        }

        if ($requestUpload->recordDuration === null) {
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => $this->traduction["file"]["no_duration"]
            ]);
            return false;
        }

        if (FileManager::MAX_RECORDING_TIME_SECOND !== null && $requestUpload->recordDuration > FileManager::MAX_RECORDING_TIME_SECOND) {
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => str_replace(":PLACEHOLDER", FileManager::MAX_RECORDING_TIME_SECOND, $this->traduction["file"]["recording_too_long"])
            ]);
            return false;
        }

        if (FileManager::MIN_RECORDING_TIME_SECOND !== null && $requestUpload->recordDuration <= FileManager::MIN_RECORDING_TIME_SECOND) {
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => str_replace(":PLACEHOLDER", FileManager::MIN_RECORDING_TIME_SECOND, $this->traduction["file"]["recording_too_short"])
            ]);
            return false;
        }

        return true;
    }

    public function createTempFolder()
    {
        $directories = array_filter(
            scandir(__DIR__ . "/../upload/temp"),
            fn ($dir) => is_dir(__DIR__ . "/../upload/temp/" . $dir)
        );

        $directoryToken = $this->createRandomToken();

        while (in_array($directoryToken, $directories)) {
            $directoryToken = $this->createRandomToken();
        }

        return $directoryToken;
    }

    public function createRandomToken()
    {
        return bin2hex(random_bytes(16));
    }
}
