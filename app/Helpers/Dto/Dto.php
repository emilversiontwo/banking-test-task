<?php

namespace App\Helpers\Dto;

use App\Helpers\Dto\Interface\DtoInterface;

class Dto implements DtoInterface
{
    public bool $test = false;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;

                continue;
            }
            $camelKey = $this->snakeToCamelCase($key);
            if (property_exists($this, $camelKey)) {
                $this->$camelKey = $value;
            }
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    private function snakeToCamelCase(string $key): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
    }
}
