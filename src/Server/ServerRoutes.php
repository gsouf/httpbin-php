<?php
namespace HttpBin\Server;

class ServerRoutes
{
    protected $filePath;
    protected $pid;
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->reset();
    }
    /**
     * Get the path of the handler
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
    /**
     * Reset every custom route
     */
    public function reset()
    {
        file_put_contents($this->filePath, "{}");
        if ($this->pid) {
            $this->addRoute("/_mypid", $this->pid);
        }
    }
    public function setPid($pid)
    {
        $this->pid = $pid;
        $content = $this->readContent();
        foreach ($content as $k => $route) {
            if ($route["path"] == "/_mypid") {
                unset($content[$k]);
            }
        }
        $this->writeContent($content);
        $this->addRoute("/_mypid", $this->pid);
    }
    private function readContent()
    {
        return json_decode(file_get_contents($this->filePath), true);
    }
    private function writeContent($content)
    {
        file_put_contents($this->filePath, json_encode($content));
    }
    /**
     * Add a route to the current server
     * @param string $path   uri of the route with a leading slash
     * @param string $result what the server will return when the route is matched
     * @param null|string|array $methods method or array of methods that the route will match
     */
    public function addRoute($path, $result, $methods = null)
    {
        $content = $this->readContent();
        $content[] = [
            "path" => $path,
            "output" => $result,
            "methods" => $methods
        ];
        $this->writeContent($content);
    }
}
