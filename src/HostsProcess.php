<?php 

namespace HostsManager;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;

class HostsProcess
{   
 
    const DOMAINREGEX   = '/^[a-zA-Z0-9][a-zA-Z0-9\-\_]+[a-zA-Z0-9]$/';
    const IPREGEX       = '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/';
    const URLREGEX      = '/^https?:\/\/[a-zA-Z0-9]{1}[a-zA-Z0-9\/\.\-]+$/';
    const SCRIPT        = __DIR__.'/update-hosts.sh';

    private static $output;
    private static $callback;

    public static function add($host, $ip) 
    {
        self::validHost($host);
        self::validIp($ip);

        self::runScript("add '$host' $ip");
    }

    public static function remove($host) 
    {
        self::validHost($host);

        self::runScript("remove '$host'");
    }

    public static function update($host, $ip) 
    {
        self::validHost($host);
        self::validIp($ip);

        self::runScript("update '$host', $ip");
    }

    public static function check($host) 
    {
        self::validHost($host);

        self::runScript("check '$host'", false);
    }

    public static function rollback() 
    {
        self::runScript("rollback");
    }

    protected static function validHost($host)
    {
        if (false === self::is_valid_domain_name($host)) {
            throw new \RuntimeException('Domain invalid');
        }
    }

    protected static function validIp($ip)
    {
        if (false === filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new \RuntimeException('IP invalid');
        }
    }

    public static function output(OutputInterface $output)
    {
        self::$output =& $output;
        return new self;
    }

    public static function callback($callback)
    {
        self::$callback = $callback;
        return new self;
    }
    

    private static function runScript($cmd, $sudo = true)
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

        call_user_func(self::$callback,$process->getOutput());
        
    }

    private static function is_valid_domain_name($domain_name)
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
    }

}
