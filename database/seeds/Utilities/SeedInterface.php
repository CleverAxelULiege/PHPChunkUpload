<?php

namespace Seeds\Utilities;

interface SeedInterface
{
    /**@return $this */
    public function table(string $table);

    /**
     * @param array{columnName: string, value: string}[] $data
     */
    public function with(array $data);
}
