<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Console\Mocks;

use Slim\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MockCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'mock';

    /**
     * @var string
     */
    private $output = 'output';

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getMockOutput());
        return 1;
    }

    /**
     * @return string
     */
    public function getMockOutput(): string
    {
        return $this->output;
    }
}
