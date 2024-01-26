<?php

namespace Upload;

use DateTime;
use Exception;
use Upload\DTOs\RequestUploadDTO;
use Upload\DTOs\UploadStateDTO;

class FileManager
{
    private UploadStateDTO|null $uploadState;
    public function __construct(private array $traduction, RequestUploadDTO $requestUpload)
    {
        $this->uploadState = $this->getUploadState($requestUpload);
    }

    const PATH_TO_UPLOAD_TEMP = __DIR__ . "/../upload/temp";

    const COOKIE_NAME = "session_token_upload";

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
        $errorMessages = [];
        if ($requestUpload->fileName === null || trim($requestUpload->fileName) === "") {
            $errorMessages[] = $this->traduction["file"]["no_file_name"];
        }

        $fileExtension = pathinfo($requestUpload->fileName)['extension'] ?? "";

        if (!in_array($fileExtension, FileManager::ALLOWED_EXTENSIONS)) {
            $errorMessages[] = str_replace(":PLACEHOLDER", implode(", ", FileManager::ALLOWED_EXTENSIONS), $this->traduction["file"]["unallowed_extension"]);
        }

        if ($requestUpload->fileSize === null) {
            $errorMessages[] = $this->traduction["file"]["no_file_size"];
        }

        if (FileManager::MAX_FILE_SIZE_BYTES !== null && $requestUpload->fileSize > FileManager::MAX_FILE_SIZE_BYTES) {
            $errorMessages[] = str_replace(":PLACEHOLDER", FileManager::MAX_FILE_SIZE_BYTES, $this->traduction["file"]["file_too_big"]);
        }

        if ($requestUpload->fileSize <= 0 && $requestUpload->fileSize !== null) {
            $errorMessages[] = $this->traduction["file"]["empty_file"];
        }

        if ($requestUpload->recordDuration === null) {
            $errorMessages[] = $this->traduction["file"]["no_duration"];
        }

        if (FileManager::MAX_RECORDING_TIME_SECOND !== null && $requestUpload->recordDuration !== null && $requestUpload->recordDuration > FileManager::MAX_RECORDING_TIME_SECOND) {
            $errorMessages[] = str_replace(":PLACEHOLDER", FileManager::MAX_RECORDING_TIME_SECOND, $this->traduction["file"]["recording_too_long"]);
        }

        if (FileManager::MIN_RECORDING_TIME_SECOND !== null && $requestUpload->recordDuration !== null && $requestUpload->recordDuration <= FileManager::MIN_RECORDING_TIME_SECOND) {
            $errorMessages[] = str_replace(":PLACEHOLDER", FileManager::MIN_RECORDING_TIME_SECOND, $this->traduction["file"]["recording_too_short"]);
        }

        if ($errorMessages !== []) {
            HeaderManager::setBadRequestStatus();
            echo json_encode([
                "msg" => $errorMessages
            ]);
            return false;
        }

        return true;
    }

    public function doesTempFolderExistAndActive(RequestUploadDTO $requestUpload)
    {

        $uploadState = $this->getUploadState($requestUpload);

        if($uploadState === null){
            return false;
        }

        if ($this->isTempFolderActive($uploadState, $requestUpload)) {
            return true;
        }

        return false;
    }

    private function getUploadState(RequestUploadDTO $requestUpload){
        $uploadStateJson = null;

        if (file_exists(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $requestUpload->sessionTokenUpload . "/upload_state.json")) {
            $uploadStateJson = file_get_contents(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $requestUpload->sessionTokenUpload . "/upload_state.json");
        }

        if ($uploadStateJson === null) {
            return null;
        }

        return Mapper::toUploadState(json_decode($uploadStateJson, false));
    }

    public function createTempFolderAndGetState(RequestUploadDTO $requestUpload)
    {
        $directories = array_filter(
            scandir(FileManager::PATH_TO_UPLOAD_TEMP),
            fn ($dir) => is_dir(FileManager::PATH_TO_UPLOAD_TEMP . $dir)
        );

        $directoryToken = $this->createRandomToken();

        while (in_array($directoryToken, $directories)) {
            $directoryToken = $this->createRandomToken();
        }

        mkdir(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $directoryToken);
        $this->setSessionTokenUpload($directoryToken);

        $fileExtension =  $fileExtension = pathinfo($requestUpload->fileName)['extension'] ?? "";
        $uploadState = new UploadStateDTO(
            (new DateTime())->getTimestamp(),
            0,
            0,
            $fileExtension,
            $this->createRandomToken()
        );

        file_put_contents(
            FileManager::PATH_TO_UPLOAD_TEMP . "/" . $directoryToken . "/" . "upload_state.json",
            json_encode($uploadState, JSON_UNESCAPED_UNICODE)
        );

        return $uploadState;
    }

    public function createRandomToken()
    {
        return bin2hex(random_bytes(16));
    }

    private function isTempFolderActive(UploadStateDTO $uploadState, RequestUploadDTO $requestUpload)
    {
        $currentTimestamp = (new DateTime())->getTimestamp();

        if ($currentTimestamp - $uploadState->lastChunkFileReceivedAt > (60 * 60 * 8)) {
            $this->removeUploadTempDir($requestUpload->sessionTokenUpload);
            return false;
        }

        return true;
    }

    public function removeUploadTempDir(string $sessionTokenUpload)
    {
        $files = array_diff(scandir(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $sessionTokenUpload), [".", ".."]);
        foreach ($files as $file) {
            unlink(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $sessionTokenUpload . "/" . $file);
        }

        return rmdir(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $sessionTokenUpload);
    }

    private function setSessionTokenUpload(string $token)
    {
        setcookie(FileManager::COOKIE_NAME, $token, [
            'expires' => time() + 3600 * 24,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    public function removeSessionTokenUpload()
    {
        setcookie(FileManager::COOKIE_NAME, "", [
            'expires' => time() - 3600 * 24,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    public function validateCSRFToken(RequestUploadDTO $requestupload){
        if($this->uploadState === null){
            return false;
        }

        return $this->uploadState->CSRFToken === $requestupload->CSRFtoken;
    }
}
