<?php
use utils\MainLogger;
class Client extends Thread {

    private $isclosed;
    private $socket;

	public function __construct($socket, $password){
        $this->isclosed = false;
		$this->socket = $socket;
        $this->target = Server::$target;
        $this->targetport = Server::$targetport;
        $this->password = $password;
        $this->passwordlenth = strlen($password);
        $this->logger = MainLogger::getInstance();
		$this->start();
	}

	public function run(){
        date_default_timezone_set('Asia/Shanghai');
		$client = $this->socket;
        socket_getpeername($client, $caddress, $cport);
        $this->logger->info("$caddress:$cport 发起链接");
        $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(!socket_connect($server, $this->target, $this->targetport)){
            
        }
		do{
            $time = time();
            $srecv = socket_recv($server, $sb, 1024, 64);
            if($srecv === 0) $this->isclosed = true;
            if(strlen($sb) !== 0){
                socket_write($client, $sb);
            }
            $crecv = socket_recv($client, $cb, 10240, 64);
            if($crecv === 0) $this->isclosed = true;
            if(strlen($cb) !== 0){
                if(substr($cb, 0, $this->passwordlenth) !== $this->password){
                    $this->logger->alert("$caddress:$cport 非法客户端链接");
                    break;
                }
                $cb = substr($cb, $this->passwordlenth, 10240);
                socket_write($server, $cb);
            }
            usleep(1);
		}while(!$this->isclosed);
        //socket_close($client);
        socket_close($server);
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