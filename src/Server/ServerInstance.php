<?php
/**
 * @license see LICENSE
 */
namespace HttpBin\Server;

use gsouf\SimpleCurl\HttpRequest;
use Symfony\Component\Process\Process;
use HttpBin\Server\ServerRoutes;

class ServerInstance
{
    protected $root;
    /**
     * @var Process
     */
    public $serverProcess;
    /**
     * @var ServerRoutes
     */
    protected $serverRoutes;
    protected $iniFile;
    protected $host;
    protected $port;

    public function __construct($host = null, $port = null, $root = null)
    {

        if (null === $root) {
            $root = __DIR__ . "/../../www";
        }

        if (null === $host) {
            $host = "localhost";
        }

        if (null === $port) {
            $port = "9850";
        }

        $this->root = $root;
        $this->port = $port;
        $this->host = $host;
        $this->serverRoutes = new ServerRoutes(tempnam(sys_get_temp_dir(), "httpbin"));
        $this->iniFile = tempnam(sys_get_temp_dir(), "httpbin");
    }
    public function isRunning()
    {
        return $this->serverProcess && $this->serverProcess->isRunning();
    }

    public function getPid()
    {
        if ($this->isRunning()) {
            return $this->serverProcess->getPid();
        }
        return null;
    }
    /**
     * Starts the server at the given host and port and waits for the server to be fully started
     * @throws \Exception
     */
    public function start()
    {
        if ($this->isRunning()) {
            throw new ServerStartupException("Server process is already running");
        }
        $this->generateIniFile();
        $scriptString = sprintf(
            'exec php -S %s:%s -t %s -c %s',
            $this->host,
            $this->port,
            $this->root,
            $this->iniFile
        );
        $this->serverProcess = new Process($scriptString);
        $this->serverProcess->start();
        $this->serverRoutes->setPid($this->getPid());
        $this->startWait();
    }
    /**
     * Make an http call to the server
     * @param string $uri uri to call with a leading slash
     * @param string $method http method
     * @param array $data data to send
     * @return string|bool the response body as a string or false in case of error
     */
    public function call($uri, $method = "GET", $data = [])
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->buildUrl($uri));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        switch ($method) {
            case 'GET':
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_setopt($ch, CURLOPT_NOBODY, false);
                break;
            case 'HEAD':
                curl_setopt($ch, CURLOPT_HTTPGET, false);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                break;
            case 'POST':
            case 'CONNECT':
            case 'DELETE':
            case 'PATCH':
            case 'PUT':
            case 'TRACE':
                curl_setopt($ch, CURLOPT_HTTPGET, false);
                curl_setopt($ch, CURLOPT_NOBODY, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }

        $response = curl_exec($ch);

        return $response;
    }

    /**
     * Wait until the serve ris started or the timeout is reached
     */
    private function startWait()
    {
        $tryout = 15;
        $timePerTry = 8000;
        $try = 0;
        while ($try < $tryout && $this->serverProcess->isRunning()) {
            $serverResponse = $this->call("/_mypid");

            if ($serverResponse == $this->getPid()) {
                return true;
            }
            usleep($timePerTry);
            $try++;
        }
        $message = "Unable to start httpbin server";
        if (!$this->serverProcess->isRunning()) {
            $message .= ": " . $this->serverProcess->getErrorOutput();
        } else {
            $this->serverProcess->stop();
        }
        throw new ServerStartupException($message);
    }

    /*
     * Build an url to call this server
     */
    private function buildUrl($uri)
    {
        $uri = ltrim($uri, "/");
        $url = sprintf(
            'http://%s:%s/%s',
            $this->host,
            $this->port,
            $uri
        );
        return $url;
    }
    /**
     * Stop the server
     */
    public function stop()
    {
        unlink($this->iniFile);
        $this->serverProcess->stop();
    }
    /**
     * @return ServerRoutes
     */
    public function getRoutes()
    {
        return $this->serverRoutes;
    }
    private function generateIniFile()
    {
        $iniContent = "httpbin.handler = " . $this->serverRoutes->getFilePath();
        file_put_contents($this->iniFile, $iniContent);
    }
}
