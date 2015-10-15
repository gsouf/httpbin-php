<?php
/**
 * @license see LICENSE
 */

namespace HttpBin\Test\Route;

use HttpBin\Application;
use HttpBin\Routes\HttpMethod;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Response\EmitterInterface;

/**
 * @codeCoverageIgnore
 */
class PostTest extends \PHPUnit_Framework_TestCase
{

    public function testPost()
    {

        $method = "POST";

        $serverParams = [];

        $postData = [
            "someParam" => "params",
            "foo"       => "bar"
        ];
        $fileParams = [];
        $uri = "http://127.0.0.1:8000/post?query=1&foo=bar";
        $body = new Stream("php://memory", "r+");
        $headers = [
            "Content-Type" => "application/x-www-form-urlencoded"
        ];

        $request = new ServerRequest(
            $serverParams,
            $fileParams,
            $uri,
            $method,
            $body,
            $headers
        );
        $request = $request->withQueryParams($postData);

        $application = new Application();
        $application->getRouter()->fromClassAnnotation(new HttpMethod());

        $response = $application->dispatch($request);

        $responseJson = json_decode($response->getBody(), true);

        $this->assertEquals($postData, $responseJson["data"]);
        $this->assertEquals("/post", $responseJson["path"]);
        $this->assertEquals("query=1&foo=bar", $responseJson["query"]);
        $this->assertEquals("http://127.0.0.1:8000/post?query=1&foo=bar", $responseJson["url"]);
        $this->assertEquals("127.0.0.1:8000", $responseJson["authority"]);

    }
}
