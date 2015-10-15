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
            "data"    => $request->getQueryParams(),
            "headers" => $request->getHeaders(),
            "path"    => $request->getUri()->getPath(),
            "query"   => $request->getUri()->getQuery(),
            "scheme"  => $request->getUri()->getScheme(),
            "authority"     => $request->getUri()->getAuthority(),
            "url"     => $request->getUri()->__toString(),
            "ip"      =>  $ip

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
}
