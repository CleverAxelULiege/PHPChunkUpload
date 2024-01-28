<?php

namespace Upload;

use DateTime;
use Upload\DTOs\RequestUploadDTO;
use Upload\DTOs\UploadStateDTO;

class FileManager
{
    private UploadStateDTO|null $uploadState;

    const STATUS_FAILED_TO_MOVE_FILE = 0;
    const STATUS_DIRECTORY_DOESNT_EXIST = 1;
    public int $statusUploadedFile = -1;

    public function __construct(private array $traduction, private RequestUploadDTO $requestUpload)
    {
        $this->uploadState = $this->getUploadStateFromRequest($requestUpload);
        $this->buildUploadTempDirectoryIfDoesntExist();
    }

    const PATH_TO_UPLOAD_TEMP = __DIR__ . "/../upload/temp";

    const COOKIE_NAME = "session_token_upload";

    /**
     * Mettre à NULL pour ne pas en tenir compte
     */
    const MAX_FILE_SIZE_BYTES = 1000 * 1000 * 250;

    /**
     * Mettre à NULL pour ne pas en tenir compte
     */
    const MAX_RECORDING_TIME_SECOND = 60 * 5;

    /**
     * Mettre à NULL pour ne pas en tenir compte
     */
    const MIN_RECORDING_TIME_SECOND = 0;

    const ALLOWED_EXTENSIONS = ["mp4", "webm"];

    /**
     * Le temps qu'un dossier temporaire peut-être actif et recevoir des fichiers
     */
    const TEMP_FOLDER_LIFESPAN_SECOND = 60 * 60 * 8;

    const FILE_FIELD_NAME = "file";

    private function buildUploadTempDirectoryIfDoesntExist(): void
    {
        if (!is_dir(FileManager::PATH_TO_UPLOAD_TEMP)) {
            mkdir(FileManager::PATH_TO_UPLOAD_TEMP, 0777, true);
        }
    }

    /**
     * Valide les informations concernant le fichier.
     * Pour le moment aucun fichier n'a été upload. Seulement les informations de base envoyées par AJAX :
     * - Durée de l'enregistrement
     * - Taille de l'enregistrement
     * - Vérifie si l'extension est autorisée depuis le nom du fichier. (S'intéresser à Exiftool)
     */
    public function fileRespectRules(): bool
    {
        $errorMessages = [];
        if ($this->requestUpload->fileName === null || trim($this->requestUpload->fileName) === "") {
            $errorMessages[] = $this->traduction["file"]["no_file_name"];
        }

        $fileExtension = pathinfo($this->requestUpload->fileName)['extension'] ?? "";

        if (!in_array($fileExtension, FileManager::ALLOWED_EXTENSIONS)) {
            $errorMessages[] = str_replace(":PLACEHOLDER", implode(", ", FileManager::ALLOWED_EXTENSIONS), $this->traduction["file"]["unallowed_extension"]);
        }

        if (FileManager::MAX_FILE_SIZE_BYTES !== null && $this->requestUpload->fileSize === null) {
            $errorMessages[] = $this->traduction["file"]["no_file_size"];
        }

        if (FileManager::MAX_FILE_SIZE_BYTES !== null && $this->requestUpload->fileSize > FileManager::MAX_FILE_SIZE_BYTES) {
            $errorMessages[] = str_replace(":PLACEHOLDER", FileManager::MAX_FILE_SIZE_BYTES, $this->traduction["file"]["file_too_big"]);
        }

        if ($this->requestUpload->fileSize <= 0 && $this->requestUpload->fileSize !== null) {
            $errorMessages[] = $this->traduction["file"]["empty_file"];
        }

        if (FileManager::MAX_RECORDING_TIME_SECOND !== null && $this->requestUpload->recordDuration === null) {
            $errorMessages[] = $this->traduction["file"]["no_duration"];
        }

        if (FileManager::MAX_RECORDING_TIME_SECOND !== null && $this->requestUpload->recordDuration !== null && $this->requestUpload->recordDuration > FileManager::MAX_RECORDING_TIME_SECOND) {
            $errorMessages[] = str_replace(":PLACEHOLDER", FileManager::MAX_RECORDING_TIME_SECOND, $this->traduction["file"]["recording_too_long"]);
        }

        if (FileManager::MIN_RECORDING_TIME_SECOND !== null && $this->requestUpload->recordDuration !== null && $this->requestUpload->recordDuration <= FileManager::MIN_RECORDING_TIME_SECOND) {
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

    /**
     * Essayera de supprimer l'ancien dossier d'upload s'il existe à partir d'un ancien token de session
     */
    public function tryToRemoveOldTempDir()
    {
        if (is_dir(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $this->requestUpload->sessionTokenUpload)) {
            $this->removeUploadTempDir($this->requestUpload->sessionTokenUpload);
        }
    }


    private function getUploadStateFromRequest(RequestUploadDTO $requestUpload): null|UploadStateDTO
    {
        $uploadStateJson = null;

        if (file_exists(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $requestUpload->sessionTokenUpload . "/upload_state.json")) {
            $uploadStateJson = file_get_contents(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $requestUpload->sessionTokenUpload . "/upload_state.json");
        }

        if ($uploadStateJson === null) {
            return null;
        }

        return Mapper::toUploadState(json_decode($uploadStateJson, false));
    }

    public function createTempFolderAndGetCSRFToken(): string
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

        $fileExtension =  $fileExtension = pathinfo($this->requestUpload->fileName)['extension'] ?? "";
        $CSRFToken = $this->createRandomToken();
        $uploadState = new UploadStateDTO(
            0,
            0,
            $fileExtension,
            $CSRFToken
        );

        file_put_contents(
            FileManager::PATH_TO_UPLOAD_TEMP . "/" . $directoryToken . "/" . "upload_state.json",
            json_encode($uploadState, JSON_UNESCAPED_UNICODE)
        );

        return $CSRFToken;
    }

    public function createRandomToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Regarde si le dossier temporaire qui est toujours actif peut recevoir des fichiers et existe.
     */
    public function isTempFolderActive(): bool
    {
        if ($this->uploadState === null) {
            return false;
        }

        $pathToUploadState = FileManager::PATH_TO_UPLOAD_TEMP . "/" . $this->requestUpload->sessionTokenUpload . "/upload_state.json";

        $currentTimestamp = strtotime("now");
        if (($currentTimestamp - filemtime($pathToUploadState)) > FileManager::TEMP_FOLDER_LIFESPAN_SECOND) {
            return false;
        }

        return true;
    }

    /**
     * Renvoie vrai si on a réussi à effacer le dossier temporaire avec tous ses fichiers
     */
    public function removeUploadTempDir(string $sessionTokenUpload): bool
    {
        if (!is_dir(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $sessionTokenUpload)) {
            return false;
        }

        $files = array_diff(scandir(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $sessionTokenUpload), [".", ".."]);
        foreach ($files as $file) {
            unlink(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $sessionTokenUpload . "/" . $file);
        }

        return rmdir(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $sessionTokenUpload);
    }

    public function setSessionTokenUpload(string $token)
    {
        setcookie(FileManager::COOKIE_NAME, $token, [
            'expires' => time() + FileManager::TEMP_FOLDER_LIFESPAN_SECOND * 2,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    public function removeSessionTokenUpload()
    {
        setcookie(FileManager::COOKIE_NAME, "", [
            'expires' => time() - FileManager::TEMP_FOLDER_LIFESPAN_SECOND * 2,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    public function validateCSRFToken(): bool
    {
        if ($this->uploadState === null) {
            return false;
        }

        return $this->uploadState->CSRFToken === $this->requestUpload->CSRFtoken;
    }

    /**
     * Renvoie vrai on a réussi à correctement enregistrer le bout de fichier
     * sinon FAUX et je mets à jour un status pour voir ce qui a raté
     */
    public function moveUploadedFileToTemp(): bool
    {
        if (!is_dir(FileManager::PATH_TO_UPLOAD_TEMP . "/" . $this->requestUpload->sessionTokenUpload)) {
            $this->statusUploadedFile = FileManager::STATUS_DIRECTORY_DOESNT_EXIST;
            return false;
        }

        $newFileName = $this->uploadState->currentChunkFile . ".bin";
        $pathToNewFile = FileManager::PATH_TO_UPLOAD_TEMP . "/" . $this->requestUpload->sessionTokenUpload . "/" . $newFileName;
        $successToMoveFile = move_uploaded_file($_FILES[FileManager::FILE_FIELD_NAME]["tmp_name"], $pathToNewFile);

        if ($successToMoveFile === false) {
            $this->statusUploadedFile = FileManager::STATUS_FAILED_TO_MOVE_FILE;
            return false;
        }

        if ($this->uploadState->currentChunkFile === 0) {
            $this->uploadState->hashedFile = hash_file("sha256", $pathToNewFile);
        }

        $this->uploadState->currentChunkFile++;
        $this->uploadState->currentFileSize += $_FILES[FileManager::FILE_FIELD_NAME]["size"];
        $this->updateUploadState();

        return true;
    }

    public function hashedFileCorrespond(): bool
    {
        return hash_file("sha256", $_FILES[FileManager::FILE_FIELD_NAME]["tmp_name"]) === $this->uploadState->hashedFile;
    }

    public function getUploadState()
    {
        return $this->uploadState;
    }

    private function updateUploadState(): void
    {
        file_put_contents(
            FileManager::PATH_TO_UPLOAD_TEMP . "/" . $this->requestUpload->sessionTokenUpload . "/" . "upload_state.json",
            json_encode($this->uploadState, JSON_UNESCAPED_UNICODE)
        );
    }

    public function refreshCSRFToken(): string
    {
        $this->uploadState->CSRFToken = $this->createRandomToken();
        $this->updateUploadState();
        return $this->uploadState->CSRFToken;
    }

    public function getNextChunk()
    {
        return $this->uploadState->currentFileSize;
    }
}
