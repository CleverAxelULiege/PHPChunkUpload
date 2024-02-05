<?php

namespace Surveys\ResponseMessage;

/**
 * Doit être en accord avec UploadManager.js
 */
class FileStatusCodeManager
{
    const UNKNOWN_ERROR = -9999;
    const NO_FILE_SENT = 0;
    const FILE_TOO_BIG = 1;
    const FAILED_TO_MOVE_FILE = 2;
    const DIRECTORY_DOESNT_EXIST = 3;
    const INVALID_CSRF_TOKEN = 4;
    const TOTAL_SIZE_EXCEEDED = 5;
    const OK = 6;
}
