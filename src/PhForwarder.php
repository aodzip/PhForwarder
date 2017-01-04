<?php
include "utils/ClassLoader.php";
if (!extension_loaded("pthreads")) {
    echo "!!!require php-pthreads!!!" . PHP_EOL;
    exit(1);
}
if (!extension_loaded("sockets")) {
    echo "!!!require php-socket!!!" . PHP_EOL;
    exit(1);
}
$loader = (new utils\ClassLoader(__DIR__))->register();
$server = new Server();
