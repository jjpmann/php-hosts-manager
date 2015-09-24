<?php

namespace HostsManager\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends BaseCommand
{
    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this
            ->setName('add')
            ->setDescription('Add domain/host to hosts file.')
            ->addArgument('host', InputArgument::REQUIRED, 'Single or Mutliple domains')
            ->addArgument('ip', InputArgument::REQUIRED, 'IP address to be used');

        $this->sudo = true;
    }

    /**
     * Execute the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getArgument('host');
        $ip = $input->getArgument('ip');

        $this->hostFile->add($host, $ip);

        $output->writeLn("\"$ip $host\" was added to file.");
    }
}
