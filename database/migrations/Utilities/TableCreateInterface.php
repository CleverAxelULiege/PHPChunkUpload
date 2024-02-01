<?php

namespace Migrations\Utilities;

use Migrations\Utilities\Column\ColumnCreateInterface;

interface TableCreateInterface
{
    public function addColumn(string $name): ColumnCreateInterface;
}
