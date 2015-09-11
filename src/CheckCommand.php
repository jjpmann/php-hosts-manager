<?php

namespace HostsManager;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends BaseCommand
{

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('check')
            ->setDescription('Check to see if domain/host exists in host file')
            ->addArgument('host', InputArgument::REQUIRED, 'Single domain');

    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $hostsfile = '/etc/hosts';

        $handle = fopen($hostsfile, 'r');
        $valid = false; // init as false
        while (($buffer = fgets($handle)) !== false) {
            if (strpos($buffer, $id) !== false) {
                $valid = TRUE;
                break; // Once you find the string, you should break out the loop.
            }      
        }
        fclose($handle);



        // if ($this->bypass) {
        //     return;
        // }
        // $host = $input->getArgument('host');

        // $this->hostProcess->check($host)->run();

    }

    protected function inFile($str, $file)
    {
        
    }

}
