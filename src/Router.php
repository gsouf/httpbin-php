<?php
/**
 * @license see LICENSE
 */

namespace HttpBin;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Minime\Annotations\Cache\ArrayCache;
use Minime\Annotations\Parser;
use Minime\Annotations\Reader;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

class Router
{

    private $annotationReader;
    /**
     * @var RouterContainer
     */
    private $routerContainer;

    public function __construct($basePath = null)
    {
        $this->routerContainer = new RouterContainer($basePath);
    }

    public function fromClassAnnotation($classInstance)
    {

        $reflection = new \ReflectionClass($classInstance);

        foreach ($reflection->getMethods() as $method) {
            if ($method->isPublic()) {
                $this->tryAddRouteForMethod($classInstance, $method);
            }
        }
    }

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routerContainer->getMap()->getRoutes();
    }

    /**
     * Adds a route to the application.
     *
     * Here is a sample or the array format:
     *
     * [
     *  "path" => "/foo",
     *  "output" => "someOutput produced when the route is called"
     *  "status" => 200
     *  "methods" => ["GET","POST"] // list of accepted http methods (leave null or ignore it to accept all methods),
     *  "headers" => ["headerNamer" => "headerValue"],
     *  "responseType" => "html"  // accepts one of: html, json, raw, redirect
     * ]
     *
     *
     * @param array $routesArray the route to add
     * @throws \Exception
     */
    public function fromArray($routesArray)
    {

        if (!isset($routesArray["path"])) {
            throw new \Exception("Invalid route from array. No path was provided");
        }

        if (!isset($routesArray["output"])) {
            throw new \Exception("Invalid route from array. No output was provided");
        }

        $name = isset($routesArray["name"]) ? $routesArray["name"] : null;

        $route = $this
            ->routerContainer
            ->getMap()
            ->route(
                $name,
                $routesArray["path"],
                $this->fromArrayHandler($routesArray)
            );

        if (isset($routesArray["methods"])) {
            $route->allows($routesArray["methods"]);
        }

    }

    private function fromArrayHandler($routesArray)
    {
        return function () use ($routesArray) {
            $status = isset($routesArray["status"]) ? $routesArray["status"] : 200;
            $headers = isset($routesArray["headers"]) ? $routesArray["headers"] : [];
            $responseType = isset($routesArray["responseType"]) ? $routesArray["responseType"] : "html";

            switch ($responseType) {
                case "html":
                    $response = new HtmlResponse((string) $routesArray["output"], $status, $headers);
                    break;
                case "json":
                    $response = new JsonPrettyResponse($routesArray["output"], $status, $headers);
                    break;
                case "redirect":
                    $status = isset($routesArray["status"]) ? $routesArray["status"] : 301;
                    $response = new RedirectResponse((string) $routesArray["output"], $status, $headers);
                    break;
                case "raw":
                    $response = new HtmlResponse((string) $routesArray["output"], $status, $headers);
                    $response = $response->withoutHeader("content-type");
                    break;

            }

            return $response;
        };
    }

    /**
     * @return Reader
     */
    private function getAnnotationReader()
    {
        if (null == $this->annotationReader) {
            $this->annotationReader = new Reader(new Parser(), new ArrayCache());
        }

        return $this->annotationReader;
    }

    private function tryAddRouteForMethod($class, \ReflectionMethod $method)
    {
        $annotations = $this->getAnnotationReader()->getAnnotations($method);
        $annotations = $annotations->useNamespace("route");

        // Annotations :
        // @route.path null mandatory
        // @route.name null
        // @route.methods []

        if ($annotations->has("path")) {
            $routePath = $annotations->get("path");

            if (!$routePath || !is_string($routePath)) {
                $classMethod = get_class($class) . "::" . $method->getName();
                throw new \Exception("invalid value for routePath in class method $classMethod");
            }

            $httpMethods = $annotations->getAsArray("methods");

            $route = $this
                ->routerContainer
                ->getMap()
                ->route(
                    $annotations->get("name", null),
                    $routePath,
                    [$class, $method->getName()]
                );

            if (!empty($httpMethods)) {
                $route->allows($httpMethods);
            }
        }

    }

    public function match(ServerRequestInterface $request)
    {
        return $this->routerContainer->getMatcher()->match($request);
    }
}
