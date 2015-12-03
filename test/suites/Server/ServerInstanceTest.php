<?php

namespace HttpBin\Test\Server;

use HttpBin\Server\ServerInstance as Server;
use HttpBin\Server\ServerStartupException;

class ServerInstanceTest extends \PHPUnit_Framework_TestCase {
    public function testStartAndStop()
    {
        $server = new Server("localhost", 8082);
        $server->start();
        $response = $server->call("/ping");
        $this->assertEquals("pong", $response->getBody());
        $server->stop();
        $response = $server->call("/ping");
        $this->assertEmpty($response->getBody());
        $server->start();
        $response = $server->call("/ping");
        $this->assertEquals("pong", $response->getBody());
        $server->stop();
    }
    public function testStart2Servers()
    {
        $server1 = new Server("localhost", 8082);
        $server1->start();
        $server2 = new Server("localhost", 8084);
        $server2->start();
        $this->assertEquals("pong", $server1->call("ping"));
        $this->assertEquals("pong", $server2->call("ping"));
        $server1->stop();
        $server2->stop();
    }
    public function testOtherServerIsAlreadyRunning()
    {
        $server1 = new Server("localhost", 8082);
        $server1->start();
        $this->assertEquals("pong", $server1->call("ping"));
        $server2 = new Server("localhost", 8082);
        try {
            $server2->start();
            $server2->stop();
            $server1->stop();
            $this->fail("Exception was not thrown");
        } catch (ServerStartupException $e) {
            $this->assertTrue(true);
            $server1->stop();
        }
    }
    public function testCustomRoute()
    {
        $server = new Server("localhost", 8082);
        $server->start();
        $server->getRoutes()->addRoute("/foobar", "foobarbaz");
        $response = $server->call("/foobar");
        $this->assertEquals("foobarbaz", $response->getBody());
        $server->stop();
    }
}
