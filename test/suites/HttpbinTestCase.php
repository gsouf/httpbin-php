<?php
/**
 * @license see LICENSE
 */

namespace HttpBin\Test;

use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;

class HttpbinTestCase extends \PHPUnit_Framework_TestCase
{

    protected function generateServerRequest($url, $method, $data)
    {


        $serverParams = [];
        $fileParams = [];
        $body = new Stream("php://memory", "r+");
        $headers = [];

        $request = new ServerRequest(
            $serverParams,
            $fileParams,
            $url,
            $method,
            $body,
            $headers
        );

        if ($data) {
            $request = $request->withQueryParams($data);
        }

        return $request;
    }
}
