<?php
namespace utils;
class MainLogger{

    private $server;
    private $file;

    public static $instance;

    public function __construct(\Server $server){
        $this->server = $server;
        $this->file = $server->getLogFile();
        MainLogger::$instance = $this;
    }

    public static function getInstance(){
        return MainLogger::$instance;
    }

    public function info($log){
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::WHITE . "[INFO] $log" . PHP_EOL;
        echo $log;
        file_put_contents($this->file, TextFormat::clean($log), FILE_APPEND);
    }

	public function warning($log){
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::YELLOW . "[WARN] $log" . PHP_EOL;
        echo $log;
        file_put_contents($this->file, TextFormat::clean($log), FILE_APPEND);
	}

	public function alert($log){
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::RED . "[ALER] $log" . PHP_EOL;
        echo $log;
        file_put_contents($this->file, TextFormat::clean($log), FILE_APPEND);
	}

	public function success($log){
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::GREEN . "[INFO] $log" . PHP_EOL;
        echo $log;
        file_put_contents($this->file, TextFormat::clean($log), FILE_APPEND);
	}

}