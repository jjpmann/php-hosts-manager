<?php

namespace HostsManager\Console;

use HostsManager\HostsFile;
use HostsManager\SudoProcess;
use HostsManager\SudoProcessException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
{
    protected $hostProcess;

    protected $sudo = false;

    protected $validate = true;

    protected $hostsfile = '/etc/hosts';

    /**
     * Constructor.
     *
     * @param string|null name of Command
     */
    public function __construct($name = null)
    {
        $this->hostFile = new HostsFile($this->hostsfile);
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        parent::run($input, $output);
    }

    /**
     * shortcut to run hostFile validation with all inputs.
     */
    protected function validate(InputInterface $input)
    {
        $host = $input->getArgument('host') ? $input->getArgument('host') : null;
        $ip = $input->hasArgument('ip') ? $input->getArgument('ip') : null;

        $this->hostFile->validate($host, $ip);
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        if ($this->validate) {
            $this->validate($input);
        }

        if ($this->sudo && exec('whoami') !== 'root') {
            $cmd = implode(' ', $_SERVER['argv']);

            try {
                SudoProcess::runAsRoot($cmd, function ($obj, $data) use ($output) {
                    $output->write($data);
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
