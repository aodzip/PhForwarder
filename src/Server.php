<?php
use utils\Config;
use utils\MainLogger;
use client\TCPClient;
use client\UDPClient;
class Server{

    public static $addr;
    public static $port;
    public static $target;
    public static $targetport;
    public static $password;

    private $tcp = [];
    private $udp = [];

    public function __construct(){
        new MainLogger($this);
        MainLogger::getInstance()->info('PhForwarder 0.1 启动中');
        if(!file_exists('server.properties')){
            MainLogger::getInstance()->warning('未找到服务端配置文件');
            Config::createConf('server.properties');
            MainLogger::getInstance()->info('创建默认配置文件');
        }
        $conf = Config::loadConf('server.properties');
        foreach($conf as $server){
            $this->createListener($server);
        }
        while(true){
            foreach($this->tcp as $key => $connection){
                if(($client = socket_accept($connection[0]))){
                    $this->tcp[$key][3][] = new TCPClient($client, $connection[1], $connection[2]);
                }
                foreach($connection[3] as $ckey => $client){
                    if($client->isClosed()){
                        socket_close($client->getsocket());
                        unset($this->tcp[$key][3][$ckey]);
                    }
                }
            }
            usleep(1);
        }
    }

    private function createListener($server){
        if($server[0] == 'TCP'){
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
            if(@socket_bind($socket, $server[1], $server[2])){
                socket_listen($socket);
                socket_set_nonblock($socket);
                MainLogger::getInstance()->success("TCP 监听于 " . $server[1] . ":" . $server[2] . " 映射到 " . $server[3] . ":" . $server[4]);
                $this->tcp[] = [$socket, $server[3], $server[4], []];
            }else{
                MainLogger::getInstance()->warning("TCP 监听于 " . $server[1] . ":" . $server[2] . " 失败");
            }
        }
        if($server[0] == 'UDP'){
            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if(socket_bind($socket, $server[1], $server[2])){
                socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 0);
                MainLogger::getInstance()->success("UDP 监听于 " . $server[1] . ":" . $server[2] . " 映射到 " . $server[3] . ":" . $server[4]);
                $this->udp[] = new UDPClient($socket, $server[3], $server[4]);
            }else{
                MainLogger::getInstance()->warning("UDP 监听于 " . $server[1] . ":" . $server[2] . " 失败");
            }
        }
    }

    public function getLogFile(){
        return $this->getBaseDir().DIRECTORY_SEPARATOR.'server.log';
    }

    public function getBaseDir(){
        return '.';
    }
}

