<?php

namespace Upload\DTOs;

class UploadStateDTO
{
    public function __construct(
        public int $currentChunkFile,
        public int $currentFileSize,
        public string $extension,
        public string $CSRFToken,
        public string|null $hashedFile = null
    ) {
    }
}
