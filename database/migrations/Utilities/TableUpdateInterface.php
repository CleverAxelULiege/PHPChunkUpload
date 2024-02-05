<?php

namespace Migrations\Utilities;

use Migrations\Utilities\Column\ColumnUpdateInterface;

interface TableUpdateInterface extends TableCreateInterface
{
    public function updateColumn(string $name): ColumnUpdateInterface;
}
