<?php

namespace Migrations\Utilities\Column;

interface ColumnCreateInterface extends ColumnBaseInterface
{
    /**
     * @return $this
     */
    public function primaryKey(?string $constraint = null);
    /**
     * @return $this
     */
    public function autoIncrement();
    /**
     * @return $this
     */
    public function foreignKey(string $reference, string $column, ?string $constraint = null);
    /**
     * @return $this
     */
    public function onDeleteCascade();
    /**
     * @return $this
     */
    public function onUpdateCascade();
    /**
     * @return $this
     */
    public function withTimeZone();
    /**
     * @return $this
     */
    public function unique();
}
