<?php

namespace Migrations\Utilities;

class Instruction
{
    public array $instructions = [];
    public array $constraints = [];
    public array $removeConstraints = [];
    public int $operation = -1;
    public function __construct(public string $tableName)
    {
        
    }

    public function setOperation(int $operation){
        $this->operation = $operation;
    }

    public function set(string $instruction)
    {
        array_push($this->instructions, $instruction);
    }

    public function setConstraint(string $instruction)
    {
        array_push($this->constraints, $instruction);
    }

    public function setConstraintToRemove(string $instruction)
    {
        array_push($this->removeConstraints, $instruction);
    }
}
