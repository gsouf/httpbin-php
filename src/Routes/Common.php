<?php
/**
 * Created by PhpStorm.
 * User: bob
 * Date: 10/14/15
 * Time: 5:34 AM
 */

namespace HttpBin\Routes;


use Zend\Diactoros\Response\HtmlResponse;

class Common
{

    /**
     * @route.path /ping
     * @route.name ping
     */
    public function ping(){
        return new HtmlResponse("pong");
    }

}