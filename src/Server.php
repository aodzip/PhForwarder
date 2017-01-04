<?php
use utils\Config;
use client\TCPClient;
use client\UDPThread;

class Server
{

    public static $addr;
    public static $port;
    public static $target;
    public static $targetport;
    public static $password;

    private $tcp = [];
    private $udp = [];

    public function __construct()
    {
        echo 'PhForwarder 0.2 Starting...' . PHP_EOL;
        if (!file_exists('server.properties')) {
            echo 'No config file' . PHP_EOL;
            Config::createConf('server.properties');
            echo 'Creating default config file' . PHP_EOL;
        }
        $conf = Config::loadConf('server.properties');
        foreach ($conf as $server) {
            $this->createListener($server);
        }
        while (true) {
            if(fgets(STDIN) == 'stop'){
                continue;
            }
        }
    }

    private function createListener($server)
    {
        if ($server[0] == 'TCP') {
            //TODO new TCP Handler
        }
        if ($server[0] == 'UDP') {
            $this->udp[] = new UDPThread($server[1], $server[2], $server[3], $server[4]);
        }
    }

    public function getBaseDir()
    {
        return '.';
    }
}
