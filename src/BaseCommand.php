<?php

namespace HostsManager;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
{

    protected $hostProcess;

    protected $sudo     = false;
    protected $bypass   = false;

    public function __construct($name = null)
    {
        $this->hostProcess = new HostsProcess;

        parent::__construct($name);
    }

    public function run(InputInterface $input, OutputInterface $output)
    {

        $this->hostProcess->callback(function ($obj, $data) use ($output) {
            $output->write($data);
        });

        parent::run($input, $output);
    }


    public function initialize(InputInterface $input, OutputInterface $output)
    {

        if ($this->sudo && exec('whoami') !== 'root') {
  
            $cmd = implode(' ', $_SERVER['argv']);

            SudoProcess::runAsRoot($cmd, function ($obj, $data) use ($output) {
                
                $output->write($data);
            });
            
            $this->bypass = true;

        }

    }

    
}
