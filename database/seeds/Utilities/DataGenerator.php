<?php

namespace Seeds\Utilities;

class DataGenerator
{
    public static function generate(callable $callback, int $times = 1)
    {
        $data = [];
        for($i = 0; $i < $times; $i++){
            array_push($data, $callback());
        }
        return $data;
    }
}
