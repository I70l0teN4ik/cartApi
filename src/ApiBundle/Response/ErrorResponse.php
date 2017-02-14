<?php

namespace ApiBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponse extends JsonResponse
{
    public function __construct($e = null)
    {
        $status = self::HTTP_BAD_REQUEST;
        $response = ['message' => 'Error', 'code' => $status];

        if (is_string($e)) {
            $response['message'] = $e;
        } elseif (is_object($e) && $e instanceof \Exception) {
            $response['message'] = $e->getMessage();
            if (method_exists($e, 'getStatusCode') && $e->getStatusCode() >= 400 && $e->getStatusCode() < 500) {
                $response['code'] = $status = $e->getStatusCode();
            }
        }

        parent::__construct(['error' => $response], $status);
    }
}
