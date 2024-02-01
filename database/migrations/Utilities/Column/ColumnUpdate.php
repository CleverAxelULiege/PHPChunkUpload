<?php

namespace Migrations\Utilities\Column;

class ColumnUpdate extends ColumnCreate implements ColumnUpdateInterface{
    public function addPrimaryKey(?string $constraint = null)
    {
        $this->primaryKey($constraint);
        return $this;
    }

    public function addForeignKey(string $reference, string $column, ?string $constraint = null)
    {
        $this->foreignKey($reference, $column, $constraint);
        return $this;
    }

    public function dropPrimaryKey(?string $constraint = null)
    {
        $this->dropPk = true;
        if ($constraint == null) {
            $this->primaryKeyConstraint = ColumnBase::PK_PREFIX . $this->name;
        } else {
            $this->primaryKeyConstraint = $constraint;
        }
        return $this;
    }

    public function dropForeignKey(?string $constraint = null)
    {
        $this->dropFk = true;
        if ($constraint == null) {
            $this->foreignKeyConstraint = ColumnBase::FK_PREFIX . $this->name;
        }else{
            $this->foreignKeyConstraint = $constraint;
        }
        return $this;
    }

    public function drop(): void
    {
        $this->dropColumn = true;
    }

    public function dropUnique()
    {
        $this->dropUnique = true;
        $this->uniqueConstraint = ColumnBase::UNIQUE_PREFIX . $this->name;
        return $this;
    }

    public function addUnique()
    {
        $this->unique();
        return $this;
    }

    public function rename(string $name)
    {
        $this->newName = $name;
    }
}