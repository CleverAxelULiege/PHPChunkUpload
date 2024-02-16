<?php

namespace Surveys\DTOs;

class TraductionDTO{
    public function __construct(
        public string $lng,
        public array $traductions
    )
    {
        
    }
}