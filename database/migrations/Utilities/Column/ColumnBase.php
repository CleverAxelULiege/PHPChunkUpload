<?php

namespace Migrations\Utilities\Column;

use Migrations\Utilities\ColumnType;

class ColumnBase implements ColumnBaseInterface{
    const PK_PREFIX = "pk_";
    const FK_PREFIX = "fk_";
    const UNIQUE_PREFIX = "uniq_";
    public ?string $type = null;
    public ?int $length = null;
    public ?int $precision = null;
    public ?int $scale = null;

    public ?bool $nullable = null;
    public bool $autoIncrement = false;
    public bool $cascadeOnDelete = false;
    public bool $cascadeOnUpdate = false;
    public bool $withTimeZone = false;
    public bool $dropColumn = false;
    public bool $dropPk = false;
    public bool $dropFk = false;
    public bool $dropUnique = false;

    public ?string $primaryKeyConstraint = null;
    public ?string $foreignKeyConstraint = null;
    public ?string $foreignKeyTableReference = null;
    public ?string $foreignKeyColumnReference = null;
    public ?string $uniqueConstraint = null;

    public ?string $default = null;

    public ?string $newName = null;
    public ?string $tableName = null;


    public function __construct(public string $name)
    {
    }

    public function bool()
    {
        $this->type = ColumnType::$bool;
        return $this;
    }

    public function smallint()
    {
        $this->type = ColumnType::$smallint;
        return $this;
    }

    public function int()
    {
        $this->type = ColumnType::$int;
        return $this;
    }

    public function bigint()
    {
        $this->type = ColumnType::$bigint;
        return $this;
    }

    public function float()
    {
        $this->type = ColumnType::$float;
        return $this;
    }

    public function double()
    {
        $this->type = ColumnType::$double;
        return $this;
    }

    public function decimal(int $precision, int $scale)
    {
        $this->type = ColumnType::$decimal;
        $this->precision = $precision;
        $this->scale = $scale;
        return $this;
    }

    public function char(int $length)
    {
        $this->type = ColumnType::$char;
        $this->length = $length;
        return $this;
    }

    public function varchar(?int $length = null)
    {
        $this->type = ColumnType::$varchar;
        $this->length = $length;
        return $this;
    }

    public function text()
    {
        $this->type = ColumnType::$text;
        return $this;
    }

    public function timestamp()
    {
        $this->type = ColumnType::$timestamp;
        return $this;
    }

    public function date()
    {
        $this->type = ColumnType::$date;
        return $this;
    }

    public function time()
    {
        $this->type = ColumnType::$time;
        return $this;
    }

    public function nullable(bool $nullable = true)
    {
        $this->nullable = $nullable;
        return $this;
    }

    public function default(string $default)
    {
        $this->default = $default;
        return $this;
    }

    public function uuid(){
        $this->type = ColumnType::$uuid;
        return $this;
    }

    public function json(){
        $this->type = ColumnType::$json;
        return $this;
    }
}