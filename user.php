<?php
class Client extends Thread {

	public function __construct($socket, $target, $port, $key){
        $this->isclosed = false;
		$this->socket = $socket;
        $this->target = $target;
        $this->targetport = $port;
        $this->key = $key;
		$this->start();
	}

	public function run(){
		$client = $this->socket;
        $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($server, $this->target, $this->targetport);
		do{
            $time = time();
            $srecv = socket_recv($server, $sb, 1024, MSG_DONTWAIT);
            if($srecv === 0) $this->isclosed = true;
            if(strlen($sb) !== 0){
                socket_write($client, $sb);
            }
            $crecv = socket_recv($client, $cb, 1024, MSG_DONTWAIT);
            if($crecv === 0) $this->isclosed = true;
            if(strlen($cb) !== 0){
                socket_write($server, $this->key.$cb);
            }
            usleep(1);
		}while(!$this->isclosed);
        socket_close($server);
	}

}

$server = socket_create_listen(25565);
while(($client = socket_accept($server))){
    $clients[]=new Client($client, '127.0.0.1', '13000', 'hello');
}