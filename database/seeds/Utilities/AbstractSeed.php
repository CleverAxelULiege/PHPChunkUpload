<?php

namespace Seeds\Utilities;

abstract class AbstractSeed{
    public abstract function seed(SeedInterface $seed);
}