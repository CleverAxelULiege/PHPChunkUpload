<?php

namespace Migrations\Utilities;

use Migrations\Utilities\Column\ColumnCreate;
use Migrations\Utilities\Column\ColumnUpdate;
use Migrations\Utilities\Column\ColumnCreateInterface;
use Migrations\Utilities\Column\ColumnUpdateInterface;

class Table implements TableUpdateInterface{

    

    /**
     * @var \Migrations\Utilities\Column\ColumnBase[]
     */
    public array $columns = [];
    const ADD = 0;
    const UPDATE = 1;
    const DELETE = 2;

    public ?string $newName = null;

    public function __construct(public string $name, public int $operation)
    {
        
    }

    public function addColumn(string $name): ColumnCreateInterface
    {
        $column = new ColumnCreate($this->getEscapedColumnName($name));
        array_push($this->columns, $column);
        return $column;
    }

    public function updateColumn(string $name): ColumnUpdateInterface
    {
        $column = new ColumnUpdate($this->getEscapedColumnName($name));
        array_push($this->columns, $column);
        return $column;
    }

    private function getEscapedColumnName(string $name){
        if(in_array($name, $this->reservedKeyWords)){
            return '"'. $name .'"';
        }

        return $name;
    }

    private array $reservedKeyWords = [
        "all",
        "analyse",
        "analyze",
        "and",
        "any",
        "array",
        "as",
        "asc",
        "asymmetric",
        "both",
        "case",
        "cast",
        "check",
        "collate",
        "column",
        "constraint",
        "create",
        "current_catalog",
        "current_date",
        "current_role",
        "current_time",
        "current_timestamp",
        "current_user",
        "default",
        "deferrable",
        "desc",
        "distinct",
        "do",
        "else",
        "end",
        "except",
        "false",
        "fetch",
        "for",
        "foreign",
        "from",
        "grant",
        "group",
        "having",
        "in",
        "initially",
        "intersect",
        "into",
        "lateral",
        "leading",
        "limit",
        "localtime",
        "localtimestamp",
        "not",
        "null",
        "offset",
        "on",
        "only",
        "or",
        "order",
        "placing",
        "primary",
        "references",
        "returning",
        "select",
        "session_user",
        "some",
        "symmetric",
        "table",
        "then",
        "to",
        "trailing",
        "true",
        "union",
        "unique",
        "user",
        "using",
        "variadic",
        "when",
        "where",
        "window",
        "with",
    ];
}