<?php
namespace utils;
use \Server;
class Config{

    public static function loadConf($file){
        $conf = file_get_contents($file);
        preg_match_all('/(.*)=(.*)$/m', $conf, $conf);
        foreach($conf[1] as $key => $value){
            $config[trim($value)] = trim($conf[2][$key]);
        }
        Server::$addr = $config['listen-ip'];
        Server::$port = $config['listen-port'];
        Server::$target = $config['server-ip'];
        Server::$targetport = $config['server-port'];
        Server::$password = $config['auth-key'];
    }

    public static function createConf($file){
        $conf .= 'listen-port=13000' . PHP_EOL;
        $conf .= 'listen-ip=0.0.0.0' . PHP_EOL;
        $conf .= 'server-port=25565' . PHP_EOL;
        $conf .= 'server-ip=127.0.0.1' . PHP_EOL;
        $conf .= 'auth-key=PhProxy' . PHP_EOL;
        file_put_contents($file, $conf);
    }

}