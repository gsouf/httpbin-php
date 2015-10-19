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
    protected function generateServerRequest($url, $method = "GET", array $queryData = [], array $data = null)
    {


        $serverParams = ["REMOTE_ADDR" => "127.0.0.1"];
        $fileParams = [];
        $body = new Stream("php://memory", "r+");
        $headers = [];

        if (count($queryData) > 0) {
            $url .= "?" . http_build_query($queryData);
        }
        $request = new ServerRequest(
            $serverParams,
            $fileParams,
            $url,
            $method,
            $body,
            $headers
        );

        if ($data) {
            $request = $request->withQueryParams($queryData);
            if ($data) {
                $request = $request->withParsedBody($data);
            }
        }

        return $request;
    }
}
