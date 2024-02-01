<?php

namespace Migrations\Utilities\Column;

interface ColumnBaseInterface
{

    /**
     * @return $this
     */
    public function bool();
    /**
     * @return $this
     */
    public function smallint();
    /**
     * @return $this
     */
    public function int();
    /**
     * @return $this
     */
    public function bigint();
    /**
     * @return $this
     */
    public function float();
    /**
     * @return $this
     */
    public function double();
    /**
     * @return $this
     */
    public function decimal(int $precision, int $scale);
    /**
     * @return $this
     */
    public function char(int $length);
    /**
     * @return $this
     */
    public function varchar(?int $length = null);
    /**
     * @return $this
     */
    public function text();
    /**
     * @return $this
     */
    public function timestamp();
    /**
     * @return $this
     */
    public function date();
    /**
     * @return $this
     */
    public function time();
    /**
     * @return $this
     */
    public function nullable(bool $nullable = true);
    /**
     * @return $this
     */
    public function default(string $default);
    /**
     * @return $this
     */
    public function uuid();
    
    /**
     * @return $this
     */
    public function json();
}
