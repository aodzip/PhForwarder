<?php
namespace client;

class UDPThread extends \Thread
{

    const DYNAMIC_SPEED = 10000;
    private $src_addr;
    private $src_port;
    private $dest_addr;
    private $dest_port;
    private $isrunning;
    private $session;

    public function __construct($src_addr, $src_port, $dest_addr, $dest_port)
    {
        $this->isrunning = true;
        $this->src_addr = $src_addr;
        $this->src_port = $src_port;
        $this->dest_addr = $dest_addr;
        $this->dest_port = $dest_port;
        $this->session = [];
        $this->start();
    }

    public function run()
    {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        if (socket_bind($socket, $this->src_addr, $this->src_port)) {
            echo "UDP listen at: $this->src_addr:$this->src_port forward to $this->dest_addr:$this->dest_port" . PHP_EOL;
        } else {
            echo "UDP listen at: $this->src_addr:$this->src_port FAILED" . PHP_EOL;
        }
        $count = 0;
        $lastcount = 0;
        $stat = 0;
        while ($this->isrunning) {
            $status = socket_recvfrom($socket, $buffer, 1024, 64, $client_ip, $client_port);
            $time = time();
            if (!($status === false)) {
                if ((time() - (int) $time) > 1) {
                    $stat = time();
                    if ($count > self::DYNAMIC_SPEED && abs($lastcount - $count) < self::DYNAMIC_SPEED) {
                        $nextcount = $count;
                    } else {
                        $nextcount = 0;
                    }
                    $lastcount = $count;
                    $count = $nextcount;
                } else {
                    $count ++;
                }
                if (!isset($this->session["$client_ip:$client_port"])) {
                    $this->session["$client_ip:$client_port"] = [socket_create(AF_INET, SOCK_DGRAM, SOL_UDP), 0];
                }
                $this->session["$client_ip:$client_port"][1] = $time;
                $session = $this->session["$client_ip:$client_port"];
                socket_sendto($session[0], $buffer, strlen($buffer), 64, $this->dest_addr, $this->dest_port);
            } else {
                if ($count <= self::DYNAMIC_SPEED) {
                    $count = 0;
                } else {
                    $count -= self::DYNAMIC_SPEED;
                }
            }
            foreach ($this->session as $info => $session) {
                if ((time() - (int)$session[1]) > 5) {
                    socket_close($session[0]);
                    unset($this->session[$info]);
                    continue;
                }
                socket_recv($session[0], $buffer, 1024, 64);
                if (strlen($buffer) > 0) {
                    $info = explode(':', $info);
                    $client_ip = $info[0];
                    $client_port = $info[1];
                    socket_sendto($socket, $buffer, strlen($buffer), 64, $client_ip, $client_port);
                }
            }
            $sleep = 100000 / ($count + 1);
            usleep($sleep > 1 ? $sleep : 1);
        }
    }
}
