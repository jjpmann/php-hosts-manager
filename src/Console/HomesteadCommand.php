<?php

namespace HostsManager\Console;

use HostsManager\homesteadYamlParser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HomesteadCommand extends BaseCommand
{
    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this
            ->setName('homestead')
            ->setDescription('Reads sites/domains from homestead.yaml file and updates hosts file.')
            ->addArgument(
                'folders',
                InputArgument::IS_ARRAY,
                'Folders with valid homestead files');

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
        $folders = $input->getArgument('folders');
        if (count($folders) === 0) {
            $folders = ['.'];
        }

        $h = new homesteadYamlParser($folders);

        $this->hostFile->replace($h->pattern(), $h->getHosts());

        $output->writeLn("Updated.\n\n".$h->getHosts()."\n\n");
    }
}
