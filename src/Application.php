<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class Application extends SymfonyApplication {
    private const VERSION = '0.1';

    public function __construct()
    {
        parent::__construct('Slim Console', self::VERSION);
    }

    /**
     * @param InputInterface  $input  An Input instance
     * @param OutputInterface $output An Output instance
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws Throwable
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        /**
         * Always show the version information except when the user invokes --help
         * The help command always shows the long version
         */
        if (($input->hasParameterOption(['--help', '-h']) !== false) || ($input->getFirstArgument() !== null && $input->getFirstArgument() !== 'list')) {
            $output->writeln($this->getLongVersion());
            $output->writeln('');
        }

        return parent::doRun($input, $output);
    }
}
