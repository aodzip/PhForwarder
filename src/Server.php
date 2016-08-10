<?php
use utils\Config;
use utils\MainLogger;
class Server{

    public static $addr;
    public static $port;
    public static $target;
    public static $targetport;
    public static $password;

    private $clients = [];

    public function __construct(){
        new MainLogger($this);
        if(!file_exists('server.properties')){
            MainLogger::getInstance()->warning('无服务端配置文件');
            Config::createConf('server.properties');
            MainLogger::getInstance()->info('创建默认配置文件');
        }
        Config::loadConf('server.properties');
        $server = socket_create_listen(static::$port);
        socket_set_nonblock($server);
        MainLogger::getInstance()->success("PhProxy 监听于 " . static::$addr . ":".static::$port." 映射到 ".static::$target.":".static::$targetport);
        while(true){
            if(($client = socket_accept($server))){
                $this->clients[] = new Client($client, static::$password);
            }
            foreach($this->clients as $key => $client){
                if($client->isClosed()){
                    socket_close($client->getsocket());
                    unset($this->clients[$key]);
                }
            }
            usleep(1);
        }
    }

    public function getLogFile(){
        return $this->getBaseDir().DIRECTORY_SEPARATOR.'server.log';
    }

    public function getBaseDir(){
        return '.';
    }
}

