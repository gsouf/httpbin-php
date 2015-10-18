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
- TODO: http://php.net/manual/fr/phar.webphar.php
- TODO: Make a callable socket daemon


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

You can write custom routes or override an existing one.

TODO



TODO
----

- Status code for custom routes
- Gziped for custom routes
- Deflate for custom routes
- custom header for custom routes (e.g. for redirect)
