<?php

namespace Migrations\Utilities\Column;

interface ColumnUpdateInterface extends ColumnBaseInterface
{
    /**
     * @return $this
     */
    public function addPrimaryKey(?string $constraint = null);
    /**
     * @return $this
     */
    public function addForeignKey(string $reference, string $column, ?string $constraint = null);
    /**
     * @return $this
     */
    public function dropPrimaryKey(?string $constraint = null);
    /**
     * @return $this
     */
    public function dropForeignKey(?string $constraint = null);

    public function drop(): void;
    /**
     * @return $this
     */
    public function dropUnique();
    
    /**
     * @return $this
     */
    public function addUnique();

    public function rename(string $name);
}
