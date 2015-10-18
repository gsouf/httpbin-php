<?php
/**
 * @license see LICENSE
 */

namespace HttpBin\Test;

use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;

class HttpbinTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @param $url
     * @param string $method
     * @param array $data
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function generateServerRequest($url, $method = "GET", $data = [])
    {


        $serverParams = ["REMOTE_ADDR" => "127.0.0.1"];
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
