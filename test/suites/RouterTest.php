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

    public function testFromArray(){
        $router = new Router();

        $this->assertCount(0, $router->getRoutes());

        $router->fromArray([
            "path" => "/foo",
            "output" => "bar"
        ]);

        $this->assertCount(1, $router->getRoutes());
        $response = call_user_func($router->getRoutes()[0]->handler, new ServerRequest());
        $this->assertEquals("bar", (string)$response->getBody());
    }

}
