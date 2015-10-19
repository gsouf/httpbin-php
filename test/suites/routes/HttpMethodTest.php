<?php
/**
 * @license see LICENSE
 */

namespace HttpBin\Test\Route;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use HttpBin\Application;
use HttpBin\Routes\HttpMethod;
use HttpBin\Test\HttpbinTestCase;
use Symfony\Component\Process\Process;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Response\EmitterInterface;

/**
 * @codeCoverageIgnore
 */
class HttpMethodTest extends HttpbinTestCase
{

    /**
     * @var Client
     */
    protected static $httpClient;

    /**
     * @var Process
     */
    protected static $serverProcess;


    public static function setUpBeforeClass()
    {
        $process = new Process("exec php -S localhost:9094 -t " . __DIR__ . "/../../../www");
        $process->start();

        self::$httpClient = new Client(["base_uri" => "http://127.0.0.1:9094/"]);

        $tryout = 50;
        $try = 0;

        do {
            try {
                $response = self::$httpClient->request("GET", "ping");
                $responseText = (string)$response->getBody();
            } catch (ConnectException $e) {
                $responseText = "";
            }

            usleep(50000);
            $try++;
        } while ($try < $tryout && $responseText != "pong");

        if ($responseText == "pong") {
            if (!$process->isRunning()) {
                $message = "Unable to start http server for test. "
                    . "It seems that an other server is already using the same port (9094) detailled error bellow: ";
                $message .= $process->getErrorOutput();
                throw new \Exception($message);
            }
        } else {
            $message = "Unable to start http server. See detailled error: ";
            $message .= $process->getErrorOutput();
            throw new \Exception($message);
        }

    }

    protected function getData()
    {
        return [
            "someParam" => "params",
            "foo"       => "bar"
        ];
    }

    public function getQueryData()
    {
        return [
            "query" => 1,
            "foo"   => "bar"
        ];
    }

    protected function makeAssertion($path, $responseJson, $ignoreDataBody = false)
    {
        if (!$ignoreDataBody) {
            $this->assertEquals($this->getData(), $responseJson["data"]);
        }
        $this->assertEquals($path, $responseJson["path"]);
        $this->assertEquals("query=1&foo=bar", $responseJson["query"]);
        $this->assertEquals("http://127.0.0.1:9094$path?query=1&foo=bar", $responseJson["url"]);
        $this->assertEquals("127.0.0.1:9094", $responseJson["authority"]);
    }

    public function testPostPsr7()
    {
        $request = $this->generateServerRequest(
            "http://127.0.0.1:9094/post",
            "POST",
            $this->getQueryData(),
            $this->getData()
        );

        $application = new Application();
        $application->getRouter()->fromClassAnnotation(new HttpMethod());

        $response = $application->dispatch($request);
        $responseJson = json_decode($response->getBody(), true);

        $this->makeAssertion("/post", $responseJson);
    }

    public function testPostReal()
    {
        $response = self::$httpClient->request("POST", "post?query=1&foo=bar", [
            'form_params' => $this->getData(),
        ]);

        $responseJson = json_decode($response->getBody(), true);
        $this->makeAssertion("/post", $responseJson);
    }

    public function testGetPsr7()
    {
        $request = $this->generateServerRequest(
            "http://127.0.0.1:9094/get",
            "GET",
            $this->getQueryData()
        );

        $application = new Application();
        $application->getRouter()->fromClassAnnotation(new HttpMethod());

        $response = $application->dispatch($request);
        $responseJson = json_decode($response->getBody(), true);

        $this->makeAssertion("/get", $responseJson, true);
    }

    public function testGetReal()
    {
        $response = self::$httpClient->request("GET", "get?query=1&foo=bar");

        $responseJson = json_decode($response->getBody(), true);
        $this->makeAssertion("/get", $responseJson, true);
    }


    public function testPutPsr7()
    {
        $request = $this->generateServerRequest(
            "http://127.0.0.1:9094/put",
            "PUT",
            $this->getQueryData()
        );

        $application = new Application();
        $application->getRouter()->fromClassAnnotation(new HttpMethod());

        $response = $application->dispatch($request);
        $responseJson = json_decode($response->getBody(), true);

        $this->makeAssertion("/put", $responseJson, true);
    }

    public function testPutReal()
    {
        $response = self::$httpClient->request("PUT", "put?query=1&foo=bar");
        $responseJson = json_decode($response->getBody(), true);
        $this->makeAssertion("/put", $responseJson, true);
    }

    public function testPatchPsr7()
    {
        $request = $this->generateServerRequest(
            "http://127.0.0.1:9094/patch",
            "PATCH",
            $this->getQueryData()
        );

        $application = new Application();
        $application->getRouter()->fromClassAnnotation(new HttpMethod());

        $response = $application->dispatch($request);
        $responseJson = json_decode($response->getBody(), true);

        $this->makeAssertion("/patch", $responseJson, true);
    }

    public function testPatchReal()
    {
        $response = self::$httpClient->request("PATCH", "patch?query=1&foo=bar");
        $responseJson = json_decode($response->getBody(), true);
        $this->makeAssertion("/patch", $responseJson, true);
    }

    public function testDeletePsr7()
    {
        $request = $this->generateServerRequest(
            "http://127.0.0.1:9094/delete",
            "DELETE",
            $this->getQueryData()
        );

        $application = new Application();
        $application->getRouter()->fromClassAnnotation(new HttpMethod());

        $response = $application->dispatch($request);
        $responseJson = json_decode($response->getBody(), true);

        $this->makeAssertion("/delete", $responseJson, true);
    }

    public function testDeleteReal()
    {
        $response = self::$httpClient->request("DELETE", "delete?query=1&foo=bar");
        $responseJson = json_decode($response->getBody(), true);
        $this->makeAssertion("/delete", $responseJson, true);
    }
}
