<?php


namespace Tests\Json;


trait ProductsJsonResponse
{

    /**
     *
     */
    private function listJsonResponse(): array
    {
        return (new JsonResponseBuilder())
            ->setData(['products' => []])
            ->build();
    }
}
