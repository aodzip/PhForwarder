<?php
namespace utils;
use \Server;
class Config{

    public static function loadConf($file){
        $conf = file_get_contents($file);
        preg_match_all('/^(.*)$/m', $conf, $conf);
        foreach($conf[1] as $oneline){
            $cfg = explode(' ', $oneline);
            if(!(count($cfg) == 5)) continue;
            if(!($cfg[0] == 'TCP' or $cfg[0] == 'UDP')) continue;
            if(!(preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/',$cfg[1]))) continue;
            if(!((int)$cfg[2] and (int)$cfg[2] > 0 and (int)$cfg[2] < 65536)) continue;
            if(!(preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/',$cfg[3]))) continue;
            if(!((int)$cfg[4] and (int)$cfg[4] > 0 and (int)$cfg[4] < 65536)) continue;
            $config[] = [$cfg[0], $cfg[1], (int)$cfg[2], $cfg[3], (int)$cfg[4]];
        }
        return $config;
    }

    public static function createConf($file){
        $conf .= 'TCP 0.0.0.0 12001 127.0.0.1 22' . PHP_EOL;
        file_put_contents($file, $conf);
    }

}