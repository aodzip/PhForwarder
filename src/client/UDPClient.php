<?php
namespace client;
use utils\MainLogger;
class UDPClient extends \Thread {

	public function __construct($client, $target, $targetport){
        $this->isclosed = false;
		$this->client = $client;
        $this->clientport = $clientport;
        $this->target = $target;
        $this->targetport = $targetport;
        $this->logger = MainLogger::getInstance();
        new UDPSession(null);
		$this->start();
	}

	public function run(){
        date_default_timezone_set('Asia/Shanghai');
        $session = [];
		$client = $this->client;
		while(true){
            socket_recvfrom($client, $cb, 8192, 0, $from, $port);
            if(isset($session["$from:$port"])){
                $session["$from:$port"]->onRecv($cb);
            }else{
                $session["$from:$port"] = new UDPSession($client, $from, $port, $this->target, $this->targetport, 30);
                $session["$from:$port"]->onRecv($cb);
                $this->logger->info("UDP $from:$port 发起链接");
            }
            foreach($session as $key => $cs){
                if($cs->isclosed){
                    socket_close($cs->server);
                    unset($session[$key]);
                    $this->logger->info("UDP $key 断开链接");
                }
            }
		}
	}

    public function isClosed(){
        return $this->isclosed;
    }

    public function getSocket(){
        return $this->client;
    }

}