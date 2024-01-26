<?php

namespace Upload\DTOs;

class UploadStateDTO
{
    public function __construct(
        public int $lastChunkFileReceivedAt,
        public int $currentChunkFile,
        public int $currentFileSize,
        public string $extension,
        public string $CSRFToken
    ) {
    }
}