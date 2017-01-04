<?php
namespace client;

use utils\MainLogger;

class TCPClient extends \Thread
{

    private $isclosed;
    private $client;

    public function __construct($client, $target, $targetport)
    {
        $this->isclosed = false;
        $this->client = $client;
        $this->target = $target;
        $this->targetport = $targetport;
        $this->logger = MainLogger::getInstance();
        $this->start();
    }

    public function run()
    {
        $client = $this->client;
        socket_getpeername($client, $caddress, $cport);
        echo("TCP $caddress:$cport 发起链接" . PHP_EOL);
        $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!socket_connect($server, $this->target, $this->targetport)) {
            echo("TCP 后端服务器{$this->target}:{$this->targetport}无法连接" . PHP_EOL);
            $this->isclosed = true;
        }
        while (!$this->isclosed) {
            $srecv = socket_recv($server, $sb, 8192, 64);
            if ($srecv === 0) {
                $this->isclosed = true;
            }
            if (strlen($sb) !== 0) {
                socket_write($client, $sb);
            }
            $crecv = socket_recv($client, $cb, 8192, 64);
            if ($crecv === 0) {
                $this->isclosed = true;
            }
            if (strlen($cb) !== 0) {
                socket_write($server, $cb);
            }
            usleep(1);
        }
        @socket_close($server);
        $this->isclosed = true;
        echo("TCP $caddress:$cport 断开链接" . PHP_EOL);
    }

    public function isClosed()
    {
        return $this->isclosed;
    }

    public function getSocket()
    {
        return $this->client;
    }
}
