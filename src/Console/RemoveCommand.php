<?php

namespace HostsManager\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveCommand extends BaseCommand
{

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('remove')
            ->setDescription('Remove domain/host from hosts file.')
            ->addArgument('host', InputArgument::REQUIRED, 'Single or Mutliple domains to be removed');
            
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
        

        // $host = $input->getArgument('host');

        // $this->hostProcess->remove($host)->run();

        // "<info> DONE Done</info>\n";

    }
}
