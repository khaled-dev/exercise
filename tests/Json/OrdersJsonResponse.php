<?php


namespace Tests\Json;


trait OrdersJsonResponse
{


    private function listJsonResponse(): array
    {
        return (new JsonResponseBuilder())
            ->setData(['orders' => []])
            ->build();
    }

    private function createJsonResponse(): array
    {
        return (new JsonResponseBuilder())
            ->setData(['products' => []])
            ->build();
    }

    private function validationErrorJsonResponse(): array
    {
        return (new JsonResponseBuilder())
            ->setError([
                "products.0.product_id" => [
                    "The selected products.0.product_id is invalid.",
                    "The products.0.product_id field has a duplicate value."
                ],
                "products.1.product_id" => [
                    "The selected products.1.product_id is invalid.",
                    "The products.1.product_id field has a duplicate value."
                ],
                "products.0.quantity" => [
                    "The products.0.quantity must be an integer.",
                    "The products.0.quantity must not have more than 3 digits.",
                    "The products.0.quantity must have at least 1 digits."
                ],
                "products.1.quantity" => [
                    "The products.1.quantity must be an integer.",
                    "The products.1.quantity must not have more than 3 digits.",
                    "The products.1.quantity must have at least 1 digits."
                ]
            ])->build();
    }

    private function outOfStockErrorJsonResponse(): array
    {
        return (new JsonResponseBuilder())
            ->setError([
                '[ Beef ] out of stock.'
            ])->build();
    }
}
