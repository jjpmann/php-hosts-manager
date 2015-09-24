<?php

namespace HostsManager;

use Symfony\Component\Process\Process;
use HostsManager\SudoProcessException;

class SudoProcess
{
    
    protected static $cmd;

    public static function runAsRoot($cmd, \Closure $callback)
    {
        
        self::$cmd = $cmd = "sudo $cmd";
        
        $process = new Process($cmd);
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
            throw new SudoProcessException($process->getErrorOutput());
        }

        call_user_func($callback, false, $process->getOutput());

    }
}
