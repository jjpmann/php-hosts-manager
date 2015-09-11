<?php

namespace HostsManager;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;



class TestCommand extends BaseCommand
{



    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Testing stuff.');

        $this->sudo = true;

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
        if ($this->bypass) {
            return;
        }
        
        $fs = new Filesystem();
        $hostsfile = '/etc/hosts';
        
        try {
            $fs->touch($hostsfile);
        } catch (RuntimeException $e) {
            //echo "<pre>"; var_dump( $e ); exit;  
        }

        echo "<info>Done</info>\n";
        

        // $host   = $input->getArgument('host');
        // $ip     = $input->getArgument('ip');

        // $this->hostProcess->update($host, $ip)->run();
    }
}
