<?php

namespace Surveys\DTOs;

class RequestUploadDTO{
    public string|null $fileName = null;

    /**Doit être en byte */
    public int|null $fileSize = null;

    /**Doit être en seconde */
    public int|null $recordDuration = null;

    public string|null $CSRFtoken = null;
    public string|null $sessionTokenUpload = null;
}