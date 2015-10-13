<?php

if (!class_exists('Phar')) {
    echo "Phar extension not avaiable\n";
    exit(255);
}

$web = 'www/index.php';


function rewritePath($path)
{
    global $web;
    return $web;
}


//Phar::interceptFileFuncs();
Phar::webPhar("httpbin.phar", $web, null, array(), 'rewritePath');
require 'phar://' . __FILE__ . '/www/index.php';
__HALT_COMPILER();