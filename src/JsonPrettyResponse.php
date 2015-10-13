<?php
/**
 * @license see LICENSE
 */

namespace HttpBin;

use Zend\Diactoros\Response\JsonResponse;



class JsonPrettyResponse extends JsonResponse{

    public function __construct($data, $status = 200, array $headers = [])
    {
        parent::__construct($data, $status, $headers, JSON_PRETTY_PRINT);
    }

}
