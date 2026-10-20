<?php

namespace src\Models\Neighbourhood;

interface MyNbhSetInterface
{
    public function getCount(): int;

    public function getResults($limit = 10, $offset = 0): array;
}
