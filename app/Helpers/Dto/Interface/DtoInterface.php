<?php

namespace App\Helpers\Dto\Interface;

interface DtoInterface
{
    public function __construct();

    public function toArray(): array;
}
