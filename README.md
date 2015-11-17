HttpBin
=======

That was highly inspired by the python version: https://github.com/Runscope/httpbin


Install
-------

Download the sources or install through composer


Start the server
----------------

Many options to start a server

- ``php -S localhost:8000 -t path/to/sources/www/``


PSR-7 compliant
---------------

If you use PSR-7 requests and responses then you can test use httpbin without starting the webserver:

```php

    $application = new Application();
    $psr7Response = $application->dispatch($psr7ServerRequest);

```

Default Routes
--------------

### /post 

- accepted methods: POST

### /get

- accepted methods: GET

### /ping

Will everytime return pong

Output example: 

```
pong
```

Custom routes
-------------

You can write custom routes or override an existing one:

```php
    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/customRoute",
        "output" => "customOutput"
    ]);
```

Now when you call ``/customRoute`` on your server you will have ``customOutput`` as response.

#### Control the status code

The option status allows to control the status code of the response:

```php
    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/customRoute",
        "output" => "I'm not here",
        "status" => "404"
    ]);
```


#### Control the headers

The option headers allows to add some headers to the response

```php
    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/customRoute",
        "output" => "customOutput",
        "headers" => ["someHeader" => "somevalue"]
    ]);
```

#### Control matching http methods

Defaultly the option will match for every request method. 
But you can change this behaviours with the option ``methods``.
In this example the request will only match ``POST`` requests:


```php
    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/postOnly",
        "output" => "customOutput",
        "methods" => ["POST"]
    ]);
```


#### Setup redirect

You can create a redirect response with the ``responseType`` set to ``redirect``, ``ouput`` will contain the location:

```php
    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/redirectRoute",
        "output" => "/redirectTo",
        "responseType"=> "redirect"
    ]);
```

#### Json Response

You can create a json response with the ``responseType`` set to ``json``

```php
    $application = new DefaultApplication();
    $application->getRouter()->fromArray([
        "path" =>  "/redirectRoute",
        "output" => ["key" => "value"],
        "responseType"=> "json"
    ]);
```



## Use additional route with php.ini

When you start the server you can pass a custom ini file. This ini file allow to gain more control on the application.

Available options:

```ini
; Disable default routes
httpbin.skipDefaultRoutes=true

; A json file that contains additional routes of the application
httpbin.handler

```

An example of the handler file:

```json
    [
        {
            "path": "/myRoute",
            "output": "Some output",
            "methods": ["POST"]
        }
    ]
```
