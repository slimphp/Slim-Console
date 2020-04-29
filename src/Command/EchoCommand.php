<?php

namespace Slim\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class EchoCommand extends Command
{
    protected function configure()
    {
        $this->setName('echo')
            ->setDescription('Echos back any input')
            ->setHelp('Demonstration of custom commands created by Symfony Console component.')
            ->addArgument('echo-string', InputArgument::REQUIRED, 'Pass any string.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($input->getArgument('echo-string'));
        return 0;
    }
}
