<?php

namespace HostsManager;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;

class NewHostsProcess
{
 
    protected $cmd;
    protected $sudo   = true;
    protected $host   = null;
    protected $ip     = null;
    protected $message;
    
    protected $output;
    protected $callback;
    protected $previousCallback;
    protected $passthru = false;

    protected $file;

    public function __construct()
    {
    
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return false;
    }

    private function test()
    {
        
    }



    protected function validHost($host)
    {
        if ($host === null) {
            return;
        }
        if (false === $this->isValidDomainName($host)) {
            throw new \RuntimeException('Domain invalid');
        }
    }

    protected function validIp($ip)
    {
        if ($ip === null) {
            return;
        }
        if (false === filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new \RuntimeException('IP invalid');
        }
    }

    protected function validCallback($callback)
    {
        if ($callback == null) {
            return;
        }
        if (!is_callable($callback)) {
            throw new \RuntimeException('IP invalid');
        }
    }

    protected function isValidDomainName($domain_name)
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
    }
}
