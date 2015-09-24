<?php

namespace HostsManager\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use HostsManager\HostsProcess;
use HostsManager\HostsFile;
use HostsManager\SudoProcess;
use HostsManager\SudoProcessException;

class BaseCommand extends Command
{

    protected $hostProcess;

    protected $sudo     = false;
    protected $bypass   = false;
    protected $check    = true;

    protected $hostsfile = '/etc/hosts';

    public function __construct($name = null)
    {
        $this->hostProcess = new HostsProcess;
        $this->hostFile = new HostsFile($this->hostsfile);
        parent::__construct($name);
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        parent::run($input, $output);
    }


    protected function validate(InputInterface $input)
    {
        $host   = $input->getArgument('host');
        $ip     = $input->getArgument('ip');

        $this->hostFile->validate($host, $ip);

    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {

        $this->validate($input);

        if ($this->sudo && exec('whoami') !== 'root') {

            $cmd = implode(' ', $_SERVER['argv']);

            echo "<pre>"; var_dump( $_SERVER, $cmd ); exit;
            
            

            try {
                SudoProcess::runAsRoot($cmd, function ($obj, $data) use ($output) {
                    //$output->write($data);
                });
            } catch (SudoProcessException $e) {
                $arr = explode("\n", $e->getMessage());
                $str = implode("\n", array_slice(array_slice($arr, 4), 0, -4));
                throw new \RuntimeException(trim($str));
            }
            
            exit;
        }
    }
}
