<?php
/**
 * @license see LICENSE
 */
namespace HttpBin\Test;

use HttpBin\Application;

class ApplicationTest extends HttpbinTestCase
{

    /**
     * @var Application
     */
    protected $app;

    public function setUp()
    {
        $this->app = new Application();
    }

    public function testApplication()
    {

    }

    /**
     * using int value as output was causing issue
     */
    public function testIntOutput()
    {

        $this->app->getRouter()->fromArray([
            "path" => "/testInt",
            "output" => 5
        ]);

        $request = $this->generateServerRequest("/testInt", "GET", []);

        $response = $this->app->dispatch($request);

        $this->assertEquals(5, (string) $response->getBody());

    }
}
