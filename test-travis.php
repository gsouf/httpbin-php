<?php

    include __DIR__ . "/vendor/autoload.php";

    use HttpBin\Server\ServerInstance;
    $server = new ServerInstance("localhost", 9094);
    $server->start();

    use GuzzleHttp\Client;
    $client = new Client(["base_uri" => "http://127.0.0.1:9094/"]);
    $response = $client->request("GET", "ping");


    var_dump((string)$response->getBody());
