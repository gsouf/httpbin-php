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
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\ServerRequest;

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
                function () use ($routesArray) {
                    $status = isset($routesArray["status"]) ? $routesArray["status"] : 200;
                    return new HtmlResponse($routesArray["output"], $status);
                }
            );

        if (isset($routesArray["methods"])) {
            $route->allows($routesArray["methods"]);
        }

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

    public function match(ServerRequest $request)
    {
        return $this->routerContainer->getMatcher()->match($request);
    }
}
