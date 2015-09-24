<?php

namespace HostsManager\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends BaseCommand
{
    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this
            ->setName('rollback')
            ->setDescription('Reverts the last change.');

        $this->sudo = true;
        $this->validate = false;
    }

    /**
     * Execute the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->hostFile->rollback();

        $output->writeLn('Rolled backed to latest backup.');
    }
}
