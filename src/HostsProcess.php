<?php

namespace HostsManager;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;

class HostsProcess
{
 
    const DOMAINREGEX   = '/^[a-zA-Z0-9][a-zA-Z0-9\-\_]+[a-zA-Z0-9]$/';
    const SCRIPT        = __DIR__ . '/update-hosts.sh';

    private static $output;

    private $cmd;
    private $sudo   = true;
    private $host   = null;
    private $ip     = null;
    private $callback;

    public function __construct($cmd, $host = null, $ip = null, $sudo = true, $callback = null)
    {
        $this->cmd  = $cmd;
        $this->host = $host;
        $this->ip   = $ip;
        $this->sudo = $sudo;
        $this->callback = $callback;
    }

    private static function create($cmd, $host = null, $ip = null, $sudo = true, $callback = null)
    {
        self::validIp($ip);
        self::validHost($host);
        self::validCallback($callback);
        return new static($cmd, $host, $ip, $sudo, $callback);
        //return $class->process();
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return false;
    }

    public static function add($host, $ip, $callback = null)
    {
        return self::create("add $host $ip", $host, $ip, true, $callback);
    }

    public static function remove($host, $callback = null)
    {
        return self::create("remove $host", $host, null, true, $callback);
    }

    public static function update($host, $ip, $callback = null)
    {
        return self::create("update $host $ip", $host, $ip, true, $callback);
    }

    public static function check($host, $callback = null)
    {
        return self::create("check $host", $host, null, false, $callback);
    }

    public static function rollback($callback = null)
    {
        return self::create('rollback', null, null, true, $callback);
    }

    protected static function validHost($host)
    {
        if ($host === null) {
            return;
        }
        if (false === self::isValidDomainName($host)) {
            throw new \RuntimeException('Domain invalid');
        }
    }

    protected static function validIp($ip)
    {
        if ($ip === null) {
            return;
        }
        if (false === filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new \RuntimeException('IP invalid');
        }
    }

    protected static function validCallback($callback)
    {
        if ($callback == null) {
            return;
        }
        if (!is_callable($callback)) {
            throw new \RuntimeException('IP invalid');
        }
    }


    public static function output(OutputInterface $output)
    {
        self::$output =& $output;

    }

    public static function callback($callback)
    {
        self::$callback = $callback;
    }
    
    public function run()
    {
        $this->runScript($this->cmd, $this->sudo);
        return $this;
    }

    private function runScript($cmd, $sudo = true)
    {

        $script = self::SCRIPT;

        if ($sudo) {
            $script = "sudo $script";
        }

        $process = new Process("$script $cmd");
        $process->start();

        $process->wait(function ($type, $buffer) {
            if (Process::ERR === $type) {
                //echo 'ERR > '.$buffer;
            } else {
                //echo 'OUT > '.$buffer;
            }
        });
         
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $this->doCallback($cmd, trim($process->getOutput()));

    }

    private function doCallback($cmd, $output)
    {

        call_user_func($this->callback, $this, $output);
        // if (is_array(self::$callback)) {
        //     call_user_func_array(self::$callback[0], self::$callback[1], $args);
        // } else {
            
        // }
    }

    private static function isValidDomainName($domain_name)
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
    }
}
