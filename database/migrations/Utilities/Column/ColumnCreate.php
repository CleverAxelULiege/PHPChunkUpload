<?php

namespace Migrations\Utilities\Column;

class ColumnCreate extends ColumnBase implements ColumnCreateInterface{
    public function primaryKey(?string $constraint = null)
    {
        if ($constraint == null) {
            $this->primaryKeyConstraint = ColumnBase::PK_PREFIX . $this->name;
        } else {
            $this->primaryKeyConstraint = $constraint;
        }

        return $this;
    }

    public function autoIncrement()
    {
        $this->autoIncrement = true;
        return $this;
    }

    public function foreignKey(string $reference, string $column, ?string $constraint = null)
    {
        $this->foreignKeyTableReference = $reference;
        $this->foreignKeyColumnReference = $column;

        if ($constraint == null) {
            $this->foreignKeyConstraint = ColumnBase::FK_PREFIX . $this->name;
        }else{
            $this->foreignKeyConstraint = $constraint;
        }

        return $this;
    }

    public function onDeleteCascade()
    {
        $this->cascadeOnDelete = true;
        return $this;
    }

    public function onUpdateCascade()
    {
        $this->cascadeOnUpdate = true;
        return $this;
    }

    public function withTimeZone()
    {
        $this->withTimeZone = true;
        return $this;
    }

    public function unique()
    {
        $this->uniqueConstraint = ColumnBase::UNIQUE_PREFIX . $this->name;
        return $this;
    }
}