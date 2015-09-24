<?php

namespace HostsManager\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends BaseCommand
{
    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Update domain/host in hosts file.')
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

        $this->hostFile->update($host, $ip);

        $output->writeLn("\"$ip $host\" was updated in file.");
    }
}
