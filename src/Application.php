<?php
/**
 * @license see LICENSE
 */

namespace HttpBin;

use HttpBin\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\SapiEmitter;

class Application extends SapiEmitter
{

    /**
     * @var Router
     */
    protected $router;


    public function __construct()
    {
        $this->router = new Router();
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }


    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function dispatch(ServerRequestInterface $request)
    {

        $route = $this->getRouter()->match($request);

        if (!$route) {
            return new HtmlResponse("Not found", 404);
        }

        $attributes = (array) $route->attributes;
        $response = call_user_func_array($route->handler, [$request] + $attributes);

        if (! ($response instanceof ResponseInterface)) {
            throw new \Exception("Invalid response");
        }

        return $response;
    }
}
