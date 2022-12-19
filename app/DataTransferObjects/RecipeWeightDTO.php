<?php

namespace App\DataTransferObjects;

class RecipeWeightDTO
{

    public function __construct(private int $id, private string $name, private int $weight)
    {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }
}
