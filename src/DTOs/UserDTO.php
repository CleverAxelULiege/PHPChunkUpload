<?php

namespace Surveys\DTOs;

class UserDTO{
    public function __construct(
        public int $id,
        public string $username,
        public string $password,
        public array $roles,
    )
    {
        
    }
}