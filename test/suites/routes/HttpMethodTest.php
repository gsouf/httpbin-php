<?php
/**
 * @license see LICENSE
 */

namespace HttpBin\Test\Route;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use HttpBin\Application;
use HttpBin\Routes\HttpMethod;
use HttpBin\Server\ServerInstance;
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
     * @var ServerInstance
     */
    protected static $server;


    public static function setUpBeforeClass()
    {

        self::$server = new ServerInstance("localhost", "9094");
        self::$server->start();

        echo PHP_EOL;
        echo "=======";
        echo PHP_EOL;
        echo "check startup";
        echo PHP_EOL;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:9094/ping");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $pong = curl_exec($ch);
        var_dump($pong);
        echo "=======";
        echo PHP_EOL;

        self::$httpClient = new Client(["base_uri" => "http://127.0.0.1:9094/"]);
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


    public function testPing(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:9094/ping");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $pong = curl_exec($ch);
        var_dump($pong);

        var_dump("is running: ");
        var_dump(self::$server->isRunning());
        var_dump("output: ");
        var_dump(self::$server->serverProcess->getErrorOutput());
        var_dump(self::$server->serverProcess->getOutput());
        var_dump(self::$server->serverProcess->getExitCode());

        $response = self::$httpClient->request("GET", "ping");
        $response = $response->getBody();
        $this->assertEquals("pong", $response);
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
