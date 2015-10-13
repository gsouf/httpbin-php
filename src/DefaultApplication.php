<?php
/**
 * @license see LICENSE
 */

namespace HttpBin;


use HttpBin\Routes\HttpMethod;

class DefaultApplication extends Application{

    public function __construct()
    {
        parent::__construct();
        $router = $this->getRouter();
        $router->fromClassAnnotation(new HttpMethod());
    }
}
