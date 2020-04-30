<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Tests\Console\Command;

use Slim\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleCommand extends AbstractCommand
{
    /**
    * {@inheritdoc}
    */
    protected function configure(): void
    {
        $this->setName('example')
            ->setDescription('Example Test Command Slim Application');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
