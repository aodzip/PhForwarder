<?php
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
if ($socket === false) {
    echo "socket_create() failed:reason:" . socket_strerror(socket_last_error()) . "\n";
}
$ok = socket_bind($socket, '127.0.0.1', 11109);
if ($ok === false) {
    echo "socket_bind() failed:reason:" . socket_strerror(socket_last_error($socket));
}
while (true) {
    socket_recvfrom($socket, $buf, 1024, 0, $from, $port);
    echo $buf;
    socket_sendto($socket, "hello", 5, 64, $from, $port);
    //usleep( 1000 );
}
