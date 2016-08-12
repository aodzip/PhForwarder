<?php
namespace client;
class UDPSession extends \Thread{
    private $client;
    private $from;
    private $fromport;
    private $target;
    private $targetport;
    public $server;
    public $lastrecv;
    public $isclosed;

    public function __construct($client, $from = null, $fromport = null, $target = null, $targetport = null, $timeout = 30){
        if($client == null) return;
        $this->lastrecv = time();
        $this->client = $client;
        $this->from = $from;
        $this->fromport = $fromport;
        $this->target = $target;
        $this->targetport = $targetport;
        $this->isclosed = false;
        $this->server = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $this->timeout = $timeout;
        $this->start();
    }

    public function run(){
        while(!$this->isclosed){
            socket_recv($this->server, $sb, 8192, 64);
            if(strlen($sb) !== 0){
                socket_sendto($this->client, $sb, strlen($sb), 0, $this->from, $this->fromport);
            }
            if((time() - $this->lastrecv) > $this->timeout){
                $this->isclosed = true;
                break;
            }
            usleep(1);
        }
    }

    public function onRecv($cb){
        $this->lastrecv = time();
        socket_sendto($this->server, $cb, strlen($cb), 0, $this->target, $this->targetport);
    }

}