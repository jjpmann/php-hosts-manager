<?php

namespace HostsManager;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
{

    protected $hostProcess;

    public function __construct($name = null)
    {
        $this->hostProcess = new HostsProcess;

        parent::__construct($name);
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->hostProcess->callback(function($data) use ($output) {
            $output->write($data);
        });

        parent::run($input, $output);
    }
}
