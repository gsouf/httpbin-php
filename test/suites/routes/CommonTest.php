<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 10/18/15
 * Time: 8:05 PM
 */

namespace HttpBin\Test\Route;


use HttpBin\Application;
use HttpBin\DefaultApplication;
use HttpBin\Test\HttpbinTestCase;

class CommonTest extends HttpbinTestCase
{

    public function testIp(){
        $data = [];
        $request = $this->generateServerRequest(
            "http://127.0.0.1:8000/ip"
        );


        $application = new DefaultApplication();
        $response = $application->dispatch($request);

        $response = $response->getBody();

        $this->assertEquals("127.0.0.1", (string)$response);
    }

}
