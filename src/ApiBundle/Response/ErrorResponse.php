<?php

namespace ApiBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponse extends JsonResponse
{
    public function __construct($e = null)
    {
        $status = self::HTTP_BAD_REQUEST;
        $response = ['message' => 'Error', 'code' => 0];

        if (is_string($e)) {
            $response['message'] = $e;
        } elseif (is_object($e) && $e instanceof \Exception) {
            $response = array(
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            );
            if (method_exists($e, 'getStatusCode') && $e->getStatusCode() >= 400 && $e->getStatusCode() < 500) {
                $status = $e->getStatusCode();
            }
        }

        parent::__construct($response, $status);
    }
}
