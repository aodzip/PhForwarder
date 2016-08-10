<?php
namespace client;
use utils\MainLogger;
class UDPClient extends \Thread {

    private $isclosed;
    private $socket;

	public function __construct($socket, $password){
        $this->isclosed = false;
		$this->socket = $socket;
        $this->target = Server::$target;
        $this->targetport = Server::$targetport;
        $this->logger = MainLogger::getInstance();
		$this->start();
	}

	public function run(){
        date_default_timezone_set('Asia/Shanghai');
        $this->logger->info("$caddress:$cport 发起链接");
		$client = $this->socket;
        socket_getpeername($client, $caddress, $cport);
        $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(!socket_connect($server, $this->target, $this->targetport)){
            $this->logger->alert("后端服务器{$this->target}:{$this->targetport}无法连接");
            $this->isclosed = true;
        }
		while(!$this->isclosed){
            $time = time();
            $srecv = socket_recv($server, $sb, 1024, 64);
            if($srecv === 0) $this->isclosed = true;
            if(strlen($sb) !== 0){
                socket_write($client, $sb);
            }
            $crecv = socket_recv($client, $cb, 10240, 64);
            if($crecv === 0) $this->isclosed = true;
            if(strlen($cb) !== 0){
                socket_write($server, $cb);
            }
            usleep(1);
		}
        @socket_close($server);
        $this->isclosed = true;
        $this->logger->info("$caddress:$cport 断开链接");
	}

    public function isClosed(){
        return $this->isclosed;
    }

    public function getSocket(){
        return $this->socket;
    }

}