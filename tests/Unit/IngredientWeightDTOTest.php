<?php

namespace Tests\Unit;

use App\DataTransferObjects\IngredientWeightDTO;
use PHPUnit\Framework\TestCase;

class IngredientWeightDTOTest extends TestCase
{
    /**
     * @test
     */
    public function test_create_object()
    {
        $object = new IngredientWeightDTO(7, 'foodics', 200);

        $this->assertEquals($object->getId(), 7);
        $this->assertEquals($object->getName(), 'foodics');
        $this->assertEquals($object->getWeight(), 200);
    }

}
