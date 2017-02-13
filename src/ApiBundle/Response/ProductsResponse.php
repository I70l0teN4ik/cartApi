<?php

namespace ApiBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ProductsResponse extends JsonResponse
{
    public function __construct(array $list = [], array $pagination = [])
    {
        $response = array('products' => $list);

        if (sizeof($pagination)) {
            $response['pagination'] = $pagination;
        }

        parent::__construct($response);
    }
}
