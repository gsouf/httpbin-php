<?php
/**
 * @license see LICENSE
 */

namespace HttpBin\Routes;

use \Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Request;
use HttpBin\JsonPrettyResponse;

class HttpMethod
{

    protected function createResponse(ServerRequestInterface $request, array $data = [])
    {

        $server = $request->getServerParams();
        $ip = isset($server["HTTP_CLIENT_IP"]) ? $server["HTTP_CLIENT_IP"] : null;

        $defaultData = [
            "data"      => $request->getParsedBody(),
            "queryData" => $request->getQueryParams(),
            "headers"   => $request->getHeaders(),
            "body"      => (string)$request->getBody(),
            "path"      => $request->getUri()->getPath(),
            "method"    => $request->getMethod(),
            "query"     => $request->getUri()->getQuery(),
            "scheme"    => $request->getUri()->getScheme(),
            "authority" => $request->getUri()->getAuthority(),
            "url"       => $request->getUri()->__toString(),
            "ip"        =>  $ip

        ];

        return new JsonPrettyResponse($data + $defaultData);

    }

    /**
     * @route.name post
     * @route.path /post
     * @route.methods POST
     */
    public function routePost(ServerRequestInterface $request)
    {
        return $this->createResponse($request);
    }


    /**
     * @route.name get
     * @route.path /get
     * @route.methods GET
     */
    public function routeGet(ServerRequestInterface $request)
    {
        return $this->createResponse($request);
    }

    /**
     * @route.name put
     * @route.path /put
     * @route.methods PUT
     */
    public function routePut(ServerRequestInterface $request)
    {
        return $this->createResponse($request);
    }

    /**
     * @route.name patch
     * @route.path /patch
     * @route.methods PATCH
     */
    public function routePatch(ServerRequestInterface $request)
    {
        return $this->createResponse($request);
    }

    /**
     * @route.name delete
     * @route.path /delete
     * @route.methods DELETE
     */
    public function routeDelete(ServerRequestInterface $request)
    {
        return $this->createResponse($request);
    }
}
