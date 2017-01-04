<?php
$msg = 'hello';
$len = strlen($msg);
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
while(1){
    socket_sendto($sock, $msg, $len, 64, '127.0.0.1', 8023);
    socket_recv($sock, $buffer, 1024, 0);
    echo $buffer;
}
socket_close($sock);
