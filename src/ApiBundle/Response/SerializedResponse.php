<?php

namespace ApiBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class SerializedResponse extends JsonResponse
{
    public function __construct($serializedResponse = null, $status = self::HTTP_OK)
    {
        if (!$serializedResponse) {
            $serializedResponse = json_encode(new \ArrayObject());
        }

        parent::__construct($serializedResponse, $status, [], true);
    }
}
