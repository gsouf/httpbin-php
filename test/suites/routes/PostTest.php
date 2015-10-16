<?php
/**
 * @license see LICENSE
 */

namespace HttpBin\Test\Route;

use HttpBin\Application;
use HttpBin\Routes\HttpMethod;
use HttpBin\Test\HttpbinTestCase;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Response\EmitterInterface;

/**
 * @codeCoverageIgnore
 */
class PostTest extends HttpbinTestCase
{



    public function testPost()
    {

        $data = [
            "someParam" => "params",
            "foo"       => "bar"
        ];

        $request = $this->generateServerRequest(
            "http://127.0.0.1:8000/post?query=1&foo=bar",
            "POST",
            $data
        );

        $application = new Application();
        $application->getRouter()->fromClassAnnotation(new HttpMethod());

        $response = $application->dispatch($request);

        $responseJson = json_decode($response->getBody(), true);

        $this->assertEquals($data, $responseJson["data"]);
        $this->assertEquals("/post", $responseJson["path"]);
        $this->assertEquals("query=1&foo=bar", $responseJson["query"]);
        $this->assertEquals("http://127.0.0.1:8000/post?query=1&foo=bar", $responseJson["url"]);
        $this->assertEquals("127.0.0.1:8000", $responseJson["authority"]);

    }
}
