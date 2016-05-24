<?php

namespace FileStorageClientTest;

use Dez\Http\Client\Provider\Curl;
use FileStorage\Client;

error_reporting(1);
ini_set('display_errors', 1);

include_once 'vendor/autoload.php';

$curl = new Curl();
$client = new Client($curl, 'http://static.fs.com/', 'token');

//var_dump($client->generateToken([
//    'email' => 'admin@root.local',
//    'password' => '123qwe',
//]));

try {
    if($client->checkToken()) {
        var_dump(
            $client->getDirectLink($client->uploadFile(__DIR__ . '/stewie.jpg', 'Remote upload test'))
        );
    } else {
        die('bad token');
    }
} catch (\Exception $e) {
    die($e->getMessage());
}