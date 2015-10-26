<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 10/16/15
 * Time: 12:12 AM
 */

namespace HttpBin\Test;

use HttpBin\Router;
use Zend\Diactoros\ServerRequest;

class RouterTest extends \PHPUnit_Framework_TestCase
{

    public function testFromArray()
    {
        $router = new Router();
        $router->fromArray([
            "path" => "/foo",
            "output" => "bar",
            "status" => 200,
            "headers"=> ["x-custom" => "baz"],
        ]);

        $this->assertCount(1, $router->getRoutes());
        /* @var $response \Psr\Http\Message\ResponseInterface */
        $response = call_user_func($router->getRoutes()[0]->handler, new ServerRequest());
        $this->assertEquals("bar", (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(2, $response->getHeaders());
        $this->assertArrayHasKey( "content-type", $response->getHeaders());
        $this->assertEquals( ["text/html"], $response->getHeaders()["content-type"]);
        $this->assertArrayHasKey( "x-custom", $response->getHeaders());
        $this->assertEquals( ["baz"], $response->getHeaders()["x-custom"]);



        // TEST RAW RESPONSE
        $router = new Router();
        $router->fromArray([
            "path" => "/rawResponse",
            "output" => "rawResponse",
            "status" => 200,
            "responseType" => "raw"
        ]);

        $this->assertCount(1, $router->getRoutes());
        /* @var $response \Psr\Http\Message\ResponseInterface */
        $response = call_user_func($router->getRoutes()[0]->handler, new ServerRequest());
        $this->assertCount(0, $response->getHeaders());
        $this->assertEquals("rawResponse", (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());


        // TEST REDIRECT RESPONSE
        $router = new Router();
        $router->fromArray([
            "path" => "/redirectResponse",
            "output" => "/redirectTo",
            "responseType" => "redirect"
        ]);

        $this->assertCount(1, $router->getRoutes());
        /* @var $response \Psr\Http\Message\ResponseInterface */
        $response = call_user_func($router->getRoutes()[0]->handler, new ServerRequest());
        $this->assertEquals("", (string)$response->getBody());
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertCount(1, $response->getHeaders());
        $this->assertArrayHasKey( "location", $response->getHeaders());
        $this->assertEquals( ["/redirectTo"], $response->getHeaders()["location"]);


        // TEST JSON RESPONSE
        $router = new Router();
        $router->fromArray([
            "path" => "/jsonResponse",
            "output" => ["data" => "value"],
            "responseType" => "json"
        ]);

        $this->assertCount(1, $router->getRoutes());
        /* @var $response \Psr\Http\Message\ResponseInterface */
        $response = call_user_func($router->getRoutes()[0]->handler, new ServerRequest());
        $this->assertEquals(["data" => "value"], json_decode($response->getBody(), true));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $response->getHeaders());
        $this->assertArrayHasKey( "content-type", $response->getHeaders());
        $this->assertEquals( ["application/json"], $response->getHeaders()["content-type"]);

    }
}
