HttpBin
=======

[![Latest Stable Version](https://poser.pugx.org/httpbin/httpbin/v/stable)](https://packagist.org/packages/httpbin/httpbin)
[![Build Status](https://travis-ci.org/gsouf/httpbin-php.svg?branch=master)](https://travis-ci.org/gsouf/httpbin-php)
[![Test Coverage](https://codeclimate.com/github/gsouf/httpbin-php/badges/coverage.svg)](https://codeclimate.com/github/gsouf/httpbin-php/coverage)

That was highly inspired by the python version: https://github.com/Runscope/httpbin but I needed a tool with 
better integration to phpunit and test environment (and the python httpbin had its limits).

This tools aims to test API client, it was not built to use in prod (though I cant imagine such an use case)
You might want to check:

- [PHPUnit integration](httpbin-phpunit-plugin)


Install
-------

Download the sources or install through composer


Start the server...
-------------------

- ...via command line: ``php -S localhost:8000 -t www`` 
- ...via php: 

```php
    use HttpBin\Server\ServerInstance;
    $server = new ServerInstance("localhost", 8080);
    $server->start();
    // Additionally you can use the server internal call method
    echo $server->call("/ping")->getBody(); // outputs "pong"
```

and check that ``http://localhost:8000/ping`` outputs ``pong``


**PSR-7 compliant**

If you use PSR-7 requests and responses then you can use httpbin without starting the webserver:

```php
    
    use HttpBin\Application;

    $application = new Application();
    $psr7Response = $application->dispatch($psr7ServerRequest);
```

Default Routes
--------------

### /post 

returns a json that contains some info about the request including get parameters. Requires the method to be ``post``

### /get

returns a json that contains some info about the request including get parameters. Requires the method to be ``get``

### /ping

returns ``pong`` everytime

Custom routes
-------------

You can write custom routes or override an existing one:

```php
    
    use HttpBin\DefaultApplication;

    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/customRoute",
        "output" => "customOutput"
    ]);
```

Now when you call ``/customRoute`` on your server you will have ``customOutput`` as response.

You can add route independently the way you started your server. The following sections explain how to:
- [Add route to programatically created ``Application``](#add-route-to-application)
- [Add route to programatically created ``ServerInstance``](#add-route-to-server-instance)
- [Add route to server created by command line](#use-additional-route-with-phpini-command-line)




### Add route to application

#### Control the status code

The option status allows to control the status code of the response:

```php
    
    use HttpBin\DefaultApplication;

    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/customRoute",
        "output" => "I'm not here",
        "status" => "404"
    ]);
```

Now when you call ``/customRoute`` on your server you will have ``I'm not here`` as response with status code ``404``


#### Control the headers

The option headers allows to add some headers to the response

```php
    
    use HttpBin\DefaultApplication;

    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/customRoute",
        "output" => "customOutput",
        "headers" => ["someHeader" => "somevalue"]
    ]);
```

Now when you call ``/customRoute`` on your server you will have ``customOutput`` as response with the header ``someHeader: somevalue``

#### Control matching http methods

Defaultly the option will match for every request method. 
But you can change this behaviours with the option ``methods``.
In this example the request will only match ``POST`` requests:


```php
    
    use HttpBin\DefaultApplication;

    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/postOnly",
        "output" => "customOutput",
        "methods" => ["POST"]
    ]);
```

``/postOnly`` will only answer to request with ``POST`` method


#### Setup redirect

You can create a redirect response with the ``responseType`` set to ``redirect``, ``output`` will contain the location:

```php
    
    use HttpBin\DefaultApplication;

    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/redirectRoute",
        "output" => "/redirectTo",
        "responseType"=> "redirect"
    ]);
```

``RedirectRoute`` will return a http response that redirects to ``/redirectTo``

#### Json Response

You can create a json response with the ``responseType`` set to ``json``

```php
    
    use HttpBin\DefaultApplication;

    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/someJson",
        "output" => ["key" => "value"],
        "responseType"=> "json"
    ]);
```

``/someJson`` will return the jsonized output:``{"key":"value"}`` with the http header ``content-Type: application/json``



### Add route to server instance


```php

    use HttpBin\Server\ServerInstance;
    
    $server = new ServerInstance("localhost", 8080);
    $server->start();
    $server->getRoutes()->addRoute("/foobar", "foo bar");
```

Now you can call ``http://localhost:8080/foobar`` and the will output ``foo bar``



### Use additional route with php.ini (command line)

*Be aware that this feature is already managed by the ``ServerInstance`` class and in most of case you wont need it.*

When you start the server you can pass a custom ini file. This ini file allow to gain more control on the application:

``php -S localhost:8000 -t www -c myphp.ini`` 


php.ini example:

```ini
; Disable default routes
httpbin.skipDefaultRoutes=true

; A json file that contains additional routes of the application
httpbin.handler=/path/to/file.json

```

An example of the handler file (json):

```json
    [
        {
            "path": "/myRoute",
            "output": "Some output",
            "methods": ["POST"]
        }
    ]
```
