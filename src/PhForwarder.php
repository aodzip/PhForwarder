<?php
include "utils/ClassLoader.php";
if(!extension_loaded("pthreads")){
	echo "PHP运行环境缺失必要的pthread扩展" . PHP_EOL;
	exit(1);
}
if(!extension_loaded("sockets")){
	echo "PHP运行环境缺失必要的sockets扩展" . PHP_EOL;
	exit(1);
}
date_default_timezone_set('Asia/Shanghai');
$loader = (new utils\ClassLoader(__DIR__))->register();
$server = new Server();