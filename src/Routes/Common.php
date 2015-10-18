<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 10/14/15
 * Time: 5:34 AM
 */

namespace HttpBin\Routes;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

class Common
{

    /**
     * @route.path /ping
     * @route.name ping
     */
    public function ping()
    {
        return new HtmlResponse("pong");
    }

    /**
     * @route.path /ip
     * @route.name ip
     */
    public function ip(ServerRequestInterface $request)
    {

        $params= $request->getServerParams();
        $ip = isset($params["REMOTE_ADDR"]) ? (string) $params["REMOTE_ADDR"] : "";
        return new HtmlResponse($ip);
    }
}
