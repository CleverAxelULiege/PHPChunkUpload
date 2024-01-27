<?php

use Upload\FileManager;
use Upload\HeaderManager;
function preventLargeContentLengthOrFileTooBig()
    {
        if (
            isset($_SERVER["CONTENT_LENGTH"]) &&
            (int) $_SERVER["CONTENT_LENGTH"] > (1024 * 1024 * (int) ini_get('post_max_size'))
        ) {
            HeaderManager::setUnprocessableEntityStatus();
            echo json_encode([
                "msg" => "The content length is too big."
            ]);
            exit;
        }


        if ($_FILES[FileManager::FILE_FIELD_NAME]["error"] === UPLOAD_ERR_INI_SIZE || $_FILES[FileManager::FILE_FIELD_NAME]["size"] > FileManager::MAX_FILE_SIZE_BYTES ) {
            HeaderManager::setUnprocessableEntityStatus();
            echo json_encode([
                "msg" => "The file or chunk file sent is too big."
            ]);
            exit;
        }
    }