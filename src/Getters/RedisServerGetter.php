<?php
namespace TwinePM\Getters;

use \Predis\Client;
class RedisServerGetter implements IGetter {
    public static function get(array $context = null): Client {
        $server = new Client(RedisServerUrlGetter::get());
        return $server;
    }
}