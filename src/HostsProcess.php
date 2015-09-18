<?php

namespace HostsManager;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class HostsProcess
{
    protected $script;

    protected $cmd;
    protected $sudo = true;
    protected $host = null;
    protected $ip = null;
    protected $message;

    protected $output;
    protected $callback;
    protected $previousCallback;
    protected $passthru = false;

    public function __construct($callback = null)
    {
        $this->script = __DIR__.'/update-hosts.sh';
        // $this->cmd  = $cmd;
        // $this->host = $host;
        // $this->ip   = $ip;
        // $this->sudo = $sudo;
        $this->callback = $callback;
    }

    private function create($cmd, $host = null, $ip = null, $sudo = true, $callback = null)
    {
        $this->validIp($ip);
        $this->validHost($host);
        $this->validCallback($callback);

        $this->cmd = $cmd;
        $this->host = $host;
        $this->ip = $ip;
        $this->sudo = $sudo;

        if ($callback) {
            $this->callback = $callback;
        }

        return $this;
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return false;
    }

    public function add($host, $ip, $callback = null)
    {
        $this->create("check $host", $host, $ip, true, $callback);

        $this->previousCallback = $this->callback;
        $this->callback = [$this, 'checkCallback'];

        return $this;
    }

    public function remove($host, $callback = null)
    {
        $this->create("remove $host", $host, null, true, $callback);

        $this->message = "You have removed {$host} from your Hosts file.\n";

        return $this;
    }

    public function update($host, $ip, $callback = null)
    {
        $this->create("check $host", $host, $ip, true, $callback);

        $this->previousCallback = $this->callback;
        $this->callback = [$this, 'checkCallback'];

        return $this;
    }

    public function check($host, $callback = null)
    {
        $this->create("check $host", $host, null, false, $callback);

        return $this;
    }

    public function rollback($callback = null)
    {
        $this->create('rollback', null, null, true, $callback);

        $this->message = "You have rolled back your Hosts file.\n";

        return $this;
    }

    public function output(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    public function callback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    public function run()
    {
        $this->runScript($this->cmd, $this->sudo);

        return $this;
    }

    private function runCheck()
    {
        $this->previousCallback = $this->callback;
        $this->callback = [$this, 'checkCallback'];

        $this->runScript("check {$this->host}", false);
    }

    protected function checkCallback($obj, $output)
    {
        $this->callback = $this->previousCallback;

        if ($output === "The host {$this->host} was not found in the host file.") {
            $this->message = "Adding {$this->host} {$this->ip}\n";
            $this->runScript("add {$this->host} {$this->ip}");
        } else {
            $this->message = "Updating  {$this->host} to {$this->ip}\n";
            $this->runScript("update {$this->host} {$this->ip}");
        }
    }

    protected function runScript($cmd, $sudo = true)
    {
        $script = $this->script;

        if ($sudo) {
            $script = "sudo $script";
            $this->passthru = true;
        }

        $process = new Process("$script $cmd");
        $process->start();

        $this->passthru = false;
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

    protected function doCallback($cmd, $output)
    {
        $message = $this->message;
        if (is_array($this->callback) && $this->callback[1] === 'checkCallback') {
            $message = $output;
        }
        if ($this->passthru) {
            $message = $output;
        }

        call_user_func($this->callback, $this, $message);
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
            && preg_match('/^.{1,253}$/', $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)); //length of each label
    }
}
